<?php

namespace App\Scheduler\Message;

class ClassPeriodDownloadMessage
{
    private string $command;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}