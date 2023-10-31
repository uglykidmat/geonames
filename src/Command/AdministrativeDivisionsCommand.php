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
    name: 'app:adu',
    description: 'Administrative Divisions Update'
)]
class AdministrativeDivisionsCommand extends Command
{
    public function __construct(private AdministrativeDivisionsService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('country', InputArgument::REQUIRED, 'country');
        $this->addArgument('fcode', InputArgument::REQUIRED, 'fcode');
        $this->addArgument('startrow', InputArgument::REQUIRED, 'startrow');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $country = $input->getArgument('country');
        $fcode = $input->getArgument('fcode');
        $startrow = $input->getArgument('startrow');
        $io->title('Fetching :');
        $io->text('Running...');

        if ($serviceResult = $this->service->addAdminDivisions($fcode, $startrow, $country)) {
            $io->success($serviceResult->getContent());

            return Command::SUCCESS;
        } else $io->error('Something went wrong !');

        return Command::FAILURE;
    }
}
