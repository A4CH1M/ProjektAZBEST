<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\ClassPeriodDownloadMessage;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Console\Application;

#[AsMessageHandler]

class ClassPeriodDownloadMessageHandler
{
    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function __invoke(ClassPeriodDownloadMessage $message)
    {
        $commandName = $message->getCommand();
        $output = new ConsoleOutput();

        $command = $this->application->find($commandName);
        $command->run(new ArrayInput([]), $output);
    }
}