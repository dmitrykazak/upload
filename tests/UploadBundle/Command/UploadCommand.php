<?php

namespace UploadBundle\Tests\Controller;

use UploadBundle\Command\UploadCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UploadCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new UploadCommand());

        $command = $application->find('app:upload');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('All record: 6' . PHP_EOL . 'Load: 3' . PHP_EOL . 'Test mode:', $output);

    }

    public function testArgumentTestExecute()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new UploadCommand());

        $command = $application->find('app:upload');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'test' => 'test',
        ));

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertContains('All record: 6' . PHP_EOL . 'Load: 3' . PHP_EOL . 'Test mode: test', $output);

    }
}