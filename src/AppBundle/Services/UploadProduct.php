<?php

namespace AppBundle\Services;

use AppBundle\Document\Product;
use AppBundle\Writer\DoctrineMongoDbWriter;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Port\Csv\CsvReader;
use Port\Steps\StepAggregator as Workflow;

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

    /**
     * @param string $file
     * @param bool|null $isTest
     *
     * @return \Port\Result
     */
    public function upload(string $file, bool $isTest = null)
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

    /**
     * @return DoctrineMongoDbWriter
     */
    protected function writer()
    {
        $writer = new DoctrineMongoDbWriter($this->manager, Product::class);
        $writer->disableTruncate();

        return $writer;
    }
}
