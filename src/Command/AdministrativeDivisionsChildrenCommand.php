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
    name: 'Update by children',
    description: 'Performs an update of the "children" Administrative divisions based on a parent ID.',
    aliases: ['app:adcu']
)]
class AdministrativeDivisionsChildrenCommand extends Command
{
    public function __construct(private AdministrativeDivisionsService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'Countrycodes',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'The parent country codes (multiple country codes separated by a space).'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Starting...' . PHP_EOL);

        if ($serviceResult = $this->service->addChildrenDivisions($input->getArgument('Countrycodes'))) {
            $io->success($serviceResult);
            return Command::SUCCESS;
        } else $io->error('Something went wrong !');

        return Command::FAILURE;
    }
}
