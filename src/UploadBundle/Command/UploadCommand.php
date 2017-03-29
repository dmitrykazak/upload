<?php

namespace UploadBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UploadBundle\Document\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

use Ddeboer\DataImport\Reader\CsvReader;

class UploadCommand extends ContainerAwareCommand
{
    const TEST_MODE = "test";

    protected function configure()
    {
        $this
            ->setName('app:upload')
            ->setDescription('Upload a new file.')
            ->setHelp('This command allows you to upload a file...')
            ->addArgument('test', InputArgument::OPTIONAL, 'Unable test mode.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $file = new \SplFileObject(dirname(__DIR__) . '/../../web/file/products.csv');

        $reader = new CsvReader($file, ';');

        // If one of your rows contains column headers.
        $reader->setHeaderRowNumber(0);

        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $validator = $this->getContainer()->get('validator');

        $load = 0;
        foreach ($reader as $row) {

            $product = new Product();

            $product->setName($row["product"]);
            $product->setCost($row["cost"]);
            $product->setStock($row["stock"]);
            $product->setIsDiscontinued($row["isDiscontinued"]);
            $product->setCreatedAt($row["createdAt"]);

            $errors = $validator->validate($product);

            if (!count($errors)) {
                $dm->persist($product);

                $load++;
            }
        }

        if (self::TEST_MODE != $input->getArgument('test')) {
            $dm->flush();
        }

        $output->writeln([
            'All record: ' . $reader->count(),
            'Load: ' . $load,
            'Test mode: ' . $input->getArgument('test')
        ]);
    }
}