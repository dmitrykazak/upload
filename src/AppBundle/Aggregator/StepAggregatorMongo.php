<?php
declare(strict_types=1);

namespace AppBundle\Aggregator;

use Port\Reader;
use Port\Exception;
use Port\Result;
use Port\Workflow;
use Port\Writer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Seld\Signal\SignalHandler;
use Psr\Log\NullLogger;
use Port\Steps\Step;

class StepAggregatorMongo implements Workflow, LoggerAwareInterface
{
    use LoggerAwareTrait;

    const BATCH_COUNT = 200;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * Identifier for the Import/Export
     *
     * @var string|null
     */
    private $name = null;

    /**
     * @var boolean
     */
    private $skipItemOnFailure = false;

    /**
     * @var \SplPriorityQueue
     */
    private $steps;

    /**
     * @var Writer[]
     */
    private $writers = [];

    /**
     * @var int
     */
    private $batch = self::BATCH_COUNT;

    /**
     * @param Reader $reader
     * @param string $name
     */
    public function __construct(Reader $reader, $name = null)
    {
        $this->name = $name;
        $this->reader = $reader;

        $this->logger = new NullLogger();
        $this->steps = new \SplPriorityQueue();
    }

    /**
     * Add a step to the current workflow
     *
     * @param Step $step
     * @param integer|null $priority
     *
     * @return StepAggregatorMongo
     */
    public function addStep(Step $step, $priority = null): self
    {
        $priority = null === $priority && $step instanceof PriorityStep ? $step->getPriority() : $priority;
        $priority = null === $priority ? 0 : $priority;

        $this->steps->insert($step, $priority);

        return $this;
    }


    public function addWriter(Writer $writer): self
    {
        array_push($this->writers, $writer);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process(): Result
    {
        $count = 0;
        $exceptions = new \SplObjectStorage();
        $startTime  = new \DateTime;

        $signal = SignalHandler::create(['SIGTERM', 'SIGINT'], $this->logger);

        foreach ($this->writers as $writer) {
            $writer->prepare();
        }

        $pipeline = $this->buildPipeline();

        // Read all items
        foreach ($this->reader as $index => $item) {
            try {
                if ($signal->isTriggered()) {
                    break;
                }

                if (false === $pipeline($item)) {
                    continue;
                }
            } catch(Exception $e) {
                if (!$this->skipItemOnFailure) {
                    throw $e;
                }

                $exceptions->attach($e, $index);
                $this->logger->error($e->getMessage());
            }

            if (($count % $this->batch) === 0) {
                $writer->finish();
            }

            $count++;
        }

        $writer->finish();

        return new Result($this->name, $startTime, new \DateTime, $count, $exceptions);
    }

    /**
     * Sets the value which determines whether the item should be skipped when error occures
     *
     * @param boolean $skipItemOnFailure When true skip current item on process exception and log the error
     *
     * @return StepAggregatorMongo
     */
    public function setSkipItemOnFailure($skipItemOnFailure): self
    {
        $this->skipItemOnFailure = $skipItemOnFailure;

        return $this;
    }

    /**
     * @param int $batch
     *
     * @return StepAggregatorMongo
     */
    public function setBatch(int $batch = self::BATCH_COUNT): self
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Builds the pipeline
     *
     * @return callable
     */
    private function buildPipeline(): callable
    {
        $nextCallable = function ($item) {
            // the final callable is a no-op
        };

        $steps = clone $this->steps;

        $steps->insert(new Step\ArrayCheckStep, -255);

        foreach ($this->writers as $writer) {
            $steps->insert(new Step\WriterStep($writer), -256);
        }

        $steps = iterator_to_array($steps);
        $steps = array_reverse($steps);

        foreach ($steps as $step) {
            $nextCallable = function ($item) use ($step, $nextCallable) {
                return $step->process($item, $nextCallable);
            };
        }

        return $nextCallable;
    }
}
