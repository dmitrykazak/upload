<?php

namespace AppBundle\Services;

use AppBundle\Document\Product;
use AppBundle\Writer\DoctrineMongoDbWriter;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Port\Csv\CsvReader;
use Port\Steps\Step\MappingStep;
use Port\Steps\StepAggregator as Workflow;

class UploadProduct
{
    const DELIMITER = ';';

    const MAPPING = [
        '[Product name]' => '[name]',
        '[Cost]' => '[cost]',
        '[Discontinued]' => '[isDiscontinued]',
        '[Stock]' => '[stock]',
        '[Created]' => '[createdAt]',
        '[Product code]' => '[code]',
    ];

    /**
     * @var ObjectManager $manager
     */
    private $manager;

    /**
     * @var UploadValidate $validatorImport
     */
    private $validatorImport;

    /**
     * UploadProduct constructor.
     *
     * @param DocumentManager $manager
     * @param UploadValidate $validateImport
     */
    public function __construct(DocumentManager $manager, UploadValidate $validateImport)
    {
        $this->manager = $manager;
        $this->validatorImport = $validateImport;
    }

    /**
     * @param string $file
     *
     * @return \Port\Result
     */
    public function upload(string $file)
    {
        $file = new \SplFileObject($file);

        $reader = new CsvReader($file, static::DELIMITER);
        $reader->setHeaderRowNumber(0);

        $workflow = new Workflow($reader);

        $workflow->addWriter($this->writer());

        $workflow->addStep($this->mapping());
        $workflow->addStep($this->validatorImport->stepValidate());

        return $workflow->process();
    }

    /**
     * @return MappingStep
     */
    protected function mapping()
    {
        return new MappingStep(static::MAPPING);
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