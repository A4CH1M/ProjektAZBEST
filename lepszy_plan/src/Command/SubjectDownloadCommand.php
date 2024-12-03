<?php

namespace App\Command;

use App\Service\ApiDataManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:download-subject-data')]
class SubjectDownloadCommand extends Command
{
    private ApiDataManager $apiDataManager;

    public function __construct(ApiDataManager $apiDataManager)
    {
        parent::__construct();
        $this->apiDataManager = $apiDataManager;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->apiDataManager->subjectDownload();

            $output->writeln('Dane zostały pobrane.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("Błąd: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}