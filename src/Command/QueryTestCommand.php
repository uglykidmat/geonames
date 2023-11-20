<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use App\Repository\AdministrativeDivisionLocaleRepository;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'Query Test Command',
    description: 'Query Test Command',
    aliases: ['app:query']
)]
class QueryTestCommand extends Command
{
    public function __construct(private AdministrativeDivisionLocaleRepository $repo)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('geonameid', InputArgument::REQUIRED, 'geonameid');
        $this->addArgument('locale', InputArgument::REQUIRED, 'locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('pouet');
        $io->success($this->repo->findLocalesForGeoId(
            $input->getArgument('geonameid'),
            $input->getArgument('locale')
        )[0]);

        return Command::SUCCESS;
    }
}
