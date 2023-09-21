<?php

namespace App\Command;

use App\Service\GeonamesCountryLocaleService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
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

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $io->title('Fetching :');
        $io->text('Running...');
        $result = $this->service->updateCountryBatch($file);
        $io->success('Success. Translations for geonameIds in file ' . $file . ' : ' . $result);

        return Command::SUCCESS;
    }
}
