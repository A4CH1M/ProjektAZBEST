<?php

namespace App\Command;

use App\Service\ApiDataManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:download-class-period-data')]
class ClassPeriodDownloadCommand extends Command
{
    private ApiDataManager $apiDataManager;

    public function __construct(ApiDataManager $apiDataManager)
    {
        parent::__construct();
        $this->apiDataManager = $apiDataManager;
        $this->addArgument('is_whole_semester', InputArgument::REQUIRED, 'If true, download data for the whole semester, one week otherwise');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {

            $arg = $input->getArgument('is_whole_semester');

            $this->apiDataManager->classPeriodDownloadWrapper(!($arg == 'false' || $arg == '0'));
            $output->writeln('Dane zostały pobrane.');

            return Command::SUCCESS;
        }
        catch (\Exception $e) {
            $output->writeln("Błąd: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}