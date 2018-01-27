<?php
declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Services\UploadProduct;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class UploadCommand extends ContainerAwareCommand
{
    /**
     * @var UploadProduct $uploadProduct
     */
    private $uploadProduct;

    /**
     * UploadCommand constructor.
     *
     * @param UploadProduct $uploadProduct
     */
    public function __construct(UploadProduct $uploadProduct)
    {
        $this->uploadProduct = $uploadProduct;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:upload')
            ->setDescription('Upload a new file.')
            ->setHelp('This command allows you to upload a file...')
            ->addArgument('file', InputArgument::REQUIRED, 'File for upload.')
            ->addArgument('test', InputArgument::OPTIONAL, 'Unable test mode.');
    }

    protected function execute(InputInterface $input, OutputInterface $output):  void
    {
        $file = $input->getArgument('file');
        $isTest = (bool) $input->getArgument('test') ?? false;

        if (file_exists($file)) {
            $result = $this->uploadProduct->upload($file, $isTest);

            $output->writeln([
                $result->getSuccessCount()
            ]);
        } else {
            $output->writeln('File not exists.');
        }
    }
}