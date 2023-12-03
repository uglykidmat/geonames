<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use App\Service\AdministrativeDivisionsService;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'Last cry for help',
    description: 'Performs an update geoIDs given. Searches everywhere for a clue, a spark of hope,... an answer.',
    aliases: ['app:wmf']
)]
class AdministrativeDivisionsLastTryCommand extends Command
{
    public function __construct(private AdministrativeDivisionsService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'GeoIDs',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'The geonamesIDs missing a family... (multiple IDs separated by a space).'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Aight lads...' . PHP_EOL);

        if ($serviceResult = $this->service->tryAndFindAFamily($input->getArgument('GeoIDs'))) {
            $io->success($serviceResult);
            return Command::SUCCESS;
        } else $io->error('Something went wrong !');

        return Command::FAILURE;
    }
}
