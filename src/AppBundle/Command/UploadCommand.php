<?php

namespace AppBundle\Command;

use AppBundle\Services\UploadProduct;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class UploadCommand extends ContainerAwareCommand
{
    /**
     * @var \AppBundle\Services\UploadProduct
     */
    private $uploadProduct;

    public function __construct(UploadProduct $uploadProduct)
    {
        $this->uploadProduct = $uploadProduct;

        parent::__construct();
    }

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
        $result = $this->uploadProduct->upload();

        $output->writeln([
            $result->getSuccessCount()
        ]);
    }
}