<?php

namespace App\Command;
use App\Service\ApiDataScheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchedulerRunCommand extends Command
{
    protected static $defaultName = 'app:scheduler-run';
    private ApiDataScheduler $schedulerService;

    public function __construct(ApiDataScheduler $schedulerService)
    {
        parent::__construct();
        $this->schedulerService = $schedulerService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Uruchamia zadania cykliczne.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->schedulerService->schedule('app:download-class-period-data', 600);
        echo "After" . PHP_EOL;
        return Command::SUCCESS;
    }
}