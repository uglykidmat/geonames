<?php

namespace App\Command;

use App\Controller\GeojsonController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'CountryGeojsonUpdate',
    description: 'Performs an update of the Geojson data on the country entities.',
    aliases: ['app:cgu']
)]
class GeojsonCommand extends Command
{
    public function __construct(private GeojsonController $controller)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Starting...');
        if ($geojsonupdate = $this->controller->update()) {
            $io->success('Success. GeoJson data has been successfully updated. Output : ' . $geojsonupdate->getContent());
        } else $io->error('Error. GeoJson data has not been updated !');

        return Command::SUCCESS;
    }
}
