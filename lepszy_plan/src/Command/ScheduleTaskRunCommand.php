<?php

namespace App\Command;

use Cron\CronExpression;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleTaskRunCommand extends Command
{
    protected static $defaultName = 'app:schedule-task';

    protected function configure()
    {
        $this
            ->setDescription('Continuously runs and evaluates cron tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        date_default_timezone_set('Europe/Warsaw');
        while (true) {
            $this->checkAndRunTasks($output);
            sleep(3600);
        }
    }

    private function checkAndRunTasks(OutputInterface $output): void
    {
        $now = new \DateTime();
        shell_exec('php bin/console app:download-class-period-data false');
        //$output->writeln($now->format('Y-m-d H:i:s') . ' - download');
    }
}