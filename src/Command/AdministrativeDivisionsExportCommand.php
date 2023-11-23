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
    name: 'app:ade',
    description: 'Administrative Divisions Export'
)]
class AdministrativeDivisionsExportCommand extends Command
{
    public function __construct(private AdministrativeDivisionsService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('level', InputArgument::REQUIRED, 'level');
        $this->addArgument('locale', InputArgument::REQUIRED, 'locale');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $level = $input->getArgument('level');
        $locale = $input->getArgument('locale');

        $io->title('File building :');
        $io->text('Running...');

        if ($this->service->getSubdivisionsForExport(strtolower($locale), $level)) {
            $io->success('Success ! File exported ');

            return Command::SUCCESS;
        } else $io->error('Something went wrong during subdivisions file export!');

        return Command::FAILURE;
    }
}
