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
    name: 'app:clsu',
    description: 'Adds all the geonames translations for a specific country ID.'
)]
class CountryLocaleUpdateSingleCommand extends Command
{
    public function __construct(private GeonamesCountryLocaleService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('geonameid', InputArgument::REQUIRED, 'geonameid');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $geonameid = $input->getArgument('geonameid');
        $io->title('Fetching :');
        $io->text('Running...');
        $result = $this->service->updateCountrySingle($geonameid);
        $io->success('Success. Translations for geonameIds in file ' . $geonameid . ' : ' . $result);

        return Command::SUCCESS;
    }
}
