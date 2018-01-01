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
     * @return \Port\Result
     */
    public function upload()
    {
        $file = new \SplFileObject(dirname(__DIR__) . '/../../web/file/products.csv');

        $reader = new CsvReader($file, self::DELIMITER);

        $reader->setHeaderRowNumber(0);

        $workflow = new Workflow($reader);

        $writer = new DoctrineMongoDbWriter($this->manager, Product::class);
        $writer->disableTruncate();

        $workflow->addWriter($writer);

        $mapStep = new MappingStep(self::MAPPING);

        $workflow->addStep($mapStep);
        $workflow->addStep($this->validatorImport->stepValidate());

        return $workflow->process();
    }
}