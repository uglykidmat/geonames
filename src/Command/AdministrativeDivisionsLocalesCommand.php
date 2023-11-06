<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use App\Service\AdministrativeDivisionLocaleService;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:adlu',
    description: 'Administrative Divisions Locales Update'
)]
class AdministrativeDivisionsLocalesCommand extends Command
{
    public function __construct(private AdministrativeDivisionLocaleService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('country', InputArgument::REQUIRED, 'country');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $country = $input->getArgument('country');
        $io->title('Fetching :');
        $io->text('Running...');

        if ($serviceResult = $this->service->updateSubdivisionsLocales($country)) {
            $io->success($serviceResult->getContent());

            return Command::SUCCESS;
        } else $io->error('Something went wrong !');

        return Command::FAILURE;
    }
}
