<?php

namespace App\Command;

use App\Entity\GeonamesAdministrativeDivision;
use App\Service\GeonamesAPIService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GeonamesDBCachingService;
use Symfony\Component\Console\Command\Command;
use App\Service\AdministrativeDivisionsService;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:adbu',
    description: 'Administrative Divisions Batch Update'
)]
class AdministrativeDivisionsBatchGetCommand extends Command
{
    public function __construct(
        private AdministrativeDivisionsService $service,
        private GeonamesAPIService $apiService,
        private GeonamesDBCachingService $dbService,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('geoids', InputArgument::REQUIRED, 'geoids');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $geoids = explode(',', $input->getArgument('geoids'));
        $newIds = [];
        $progressBar = new ProgressBar($output, count($geoids));

        $io->title('Fetching :');
        $io->text('Running...');
        $progressBar->start();

        foreach ($geoids as $geoid) {
            if (!$this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findOneByGeonameId((int)$geoid)) {
                $newSubDiv = $this->apiService->getJsonSearch((int)$geoid);
                $this->dbService->saveSubdivisionToDatabase($newSubDiv);
                $newIds[] = $geoid;
                $progressBar->advance();
            }
        }

        $this->entityManager->flush();
        $progressBar->finish();
        if (empty($newIds)) {
            $io->success('No new Ids inserted.');
        } else $io->success('Success ! New GeonameIds : ' . implode(',', $newIds));

        return Command::SUCCESS;
    }
}
