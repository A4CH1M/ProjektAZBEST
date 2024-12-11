<?php

namespace App\Service;

use App\Scheduler\Message\ClassPeriodDownloadMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class ApiDataScheduler
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function schedule(string $command, int $intervalInSeconds): void
    {
        while (true) {
            $this->messageBus->dispatch(new ClassPeriodDownloadMessage($command));
            echo date('H:i:s', time()) . PHP_EOL;
            sleep($intervalInSeconds);
        }
    }
}