<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Controller\GeonamesCountryLevelController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'CountryLevelUpdate',
    description: 'Performs an update of the "levels" data on the country entities.',
    aliases: ['app:clvu']
)]
class CountryLevelUpdateCommand extends Command
{
    public function __construct(private GeonamesCountryLevelController $controller)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Starting...');
        $io->text($this->controller->update());
        $io->success('Success. Countries levels have been successfully updated.');

        return Command::SUCCESS;
    }
}
