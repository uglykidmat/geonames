<?php

namespace App\Command;

use App\Service\GeonamesCountryLocaleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:clu',
    description: 'Adds all the geonames translations for a country. The list of geonames IDs is in the folder all_countries_data.'
)]
class CountryLocaleUpdateCommand extends Command
{
    public function __construct(private GeonamesCountryLocaleService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $progressBar = new ProgressBar($output, 5);

        $io->text("🔜 Running...\r 💀 Please be patient, the hydration process can take up to 4 minutes !");
        $progressBar->start();
        $fileNum = 0;

        while (++$fileNum <= 5) {
            if ($this->service->updateCountryBatch($fileNum)) {
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $io->success('Success : country locales updated successfully.');

        return Command::SUCCESS;
    }
}
