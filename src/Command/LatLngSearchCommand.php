<?php

namespace App\Command;

use App\Service\GeonamesAPIService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'Latlngsearch',
    description: 'Launches a Geonames search with Latitude and Longitude data. This will return information about a GeonameId found close to the coordinates provided.',
    aliases: ['lls']
)]
class LatLngSearchCommand extends Command
{
    public function __construct(private GeonamesAPIService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('lat', InputArgument::REQUIRED, 'Latitude')
            ->addArgument('lng', InputArgument::REQUIRED, 'Longitude');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $lat = $input->getArgument('lat');
        $lng = $input->getArgument('lng');

        $latLngSearchOutput = $this->service->latLngSearch($lat, $lng);
        $getJsonSearchOutput = json_encode($this->service->getJsonSearch($latLngSearchOutput));

        $io->writeln([
            'Json Result :',
            '=============',
            '',
        ]);
        $io->success($getJsonSearchOutput);

        return Command::SUCCESS;
    }
}
