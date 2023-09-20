<?php

namespace App\Command;

use App\Service\GeonamesCountryService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'CountryUpdate',
    description: 'Performs a purge of the "geonames_country" table and fills it with information from geonames.',
    aliases: ['countryupdate']
)]
class CountryUpdateCommand extends Command
{
    public function __construct(private GeonamesCountryService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Purge :');
        $io->text('Running...');
        $io->text('Done for countries : ' . $this->service->purgeCountryList()->getContent());
        $io->title('Countries update :');
        $io->text('Running...');
        $io->text('Done for countries : ' . $this->service->getGeoCountryList()->getContent());
        $io->success('Success. Countries have been successfully updated.');

        return Command::SUCCESS;
    }
}
