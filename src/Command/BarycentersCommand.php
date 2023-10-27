<?php

namespace App\Command;

use App\Controller\GeonamesCountryController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'BarycenterUpdate',
    description: 'Performs an update of the Geojson data on the country entities.',
    aliases: ['app:cbu']
)]
class BarycentersCommand extends Command
{
    public function __construct(private GeonamesCountryController $controller)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Starting...');
        $io->text($this->controller->updateBarycenters());
        $io->success('Success. Countries barycenters have been successfully updated.');

        return Command::SUCCESS;
    }
}
