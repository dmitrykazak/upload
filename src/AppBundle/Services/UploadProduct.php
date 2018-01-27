<?php
declare(strict_types=1);

namespace AppBundle\Services;

use AppBundle\Document\Product;
use AppBundle\Writer\DoctrineMongoDbWriter;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Port\Csv\CsvReader;
use Port\Result;
use AppBundle\Aggregator\StepAggregatorMongo as Workflow;

class UploadProduct
{
    const DELIMITER = ';';

    /**
     * @var ObjectManager $manager
     */
    private $manager;

    /**
     * @var UploadValidate $validatorImport
     */
    private $validatorImport;

    /**
     * @var UploadMapper $mapper
     */
    private $mapper;

    /**
     * UploadProduct constructor.
     *
     * @param DocumentManager $manager
     * @param UploadValidate $validateImport
     * @param UploadMapper $mapper
     */
    public function __construct(
        UploadValidate $validateImport,
        UploadMapper $mapper,
        DocumentManager $manager
    )
    {
        $this->manager = $manager;
        $this->validatorImport = $validateImport;
        $this->mapper = $mapper;
    }

    public function upload(string $file, ?bool $isTest): Result
    {
        $file = new \SplFileObject($file);

        $reader = new CsvReader($file, static::DELIMITER);
        $reader->setHeaderRowNumber(0);

        $workflow = new Workflow($reader);

        $writer = $this->writer();

        $workflow->addWriter($writer);

        $workflow->addStep($this->mapper->getMapper());
        $workflow->addStep($this->validatorImport->stepValidate());

        if ($isTest) {
            $writer->setIsTest(true);
        }

        return $workflow->process();
    }

    protected function writer(): DoctrineMongoDbWriter
    {
        $writer = new DoctrineMongoDbWriter($this->manager, Product::class);
        $writer->disableTruncate();

        return $writer;
    }
}
