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
    name: 'app:adapi',
    description: 'Administrative Divisions Api Command'
)]
class AdministrativeDivisionsApiCommand extends Command
{
    public function __construct(
        private AdministrativeDivisionsService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::REQUIRED, 'The LOCALE code to update ("de","it", etc)');
        $this->addArgument('countrycode', InputArgument::REQUIRED, 'The target COUNTRY Code ("FR","US", etc)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $locale = $input->getArgument('locale');
        $countryCode = $input->getArgument('countrycode');

        $io->title('Starting :');
        $io->text('Building file for API levels search...');

        $io->success($this->service->getSubdivisionsForApi($locale, $countryCode));

        return Command::SUCCESS;
    }
}
