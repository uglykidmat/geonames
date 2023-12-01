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
    description: 'Administrative Divisions Update by country code and featurecode'
)]
class AdministrativeDivisionsCommand extends Command
{
    public function __construct(private AdministrativeDivisionsService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('country', InputArgument::REQUIRED, 'A 2-letter country code');
        $this->addArgument('fcode', InputArgument::REQUIRED, 'FeatureCode (ADM1, ADM2, ADM3)');
        $this->addArgument('startrow', InputArgument::REQUIRED, 'The start row in geonames search. Run this command with "1" and see the "Max entries for this level" info when it is done. Run it again with 1000 if there are more than 1000 entries, since the script gets the information by batch of 1000.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $country = $input->getArgument('country');
        $fcode = $input->getArgument('fcode');
        $startrow = $input->getArgument('startrow');

        if (strlen($country) > 2) {
            $country = explode(',', $input->getArgument('country'));
        }

        $io->title('Fetching :');
        $io->text('Running...');

        if ($serviceResult = $this->service->addAdminDivisionsBatch($fcode, $startrow, $country)) {
            $io->success($serviceResult->getContent());

            return Command::SUCCESS;
        } else $io->error('Something went wrong !');

        return Command::FAILURE;
    }
}
