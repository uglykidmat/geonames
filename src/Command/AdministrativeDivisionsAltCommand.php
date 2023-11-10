<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use App\Controller\AdministrativeDivisionsController;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'Altcodes Update',
    description: 'Performs an update of the Administrative divisions alternative codes.',
    aliases: ['app:adaltu']
)]
class AdministrativeDivisionsAltCommand extends Command
{
    public function __construct(private AdministrativeDivisionsController $controller)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Starting...');
        $io->info($this->controller->updateAlternativeCodes());
        $io->success('Success. Administrative Altcodes have been successfully updated.');

        return Command::SUCCESS;
    }
}
