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
        $this->addArgument('geoids', InputArgument::REQUIRED, 'A comma-separated list of geoname IDs (integers)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);
        $geoids = explode(',', $input->getArgument('geoids'));
        if (!is_array($geoids)) {
            $io->error('An array of integers must be provided.');
            return Command::FAILURE;
        }
        $newIds = [];
        $io->title('Starting :');
        $io->text('Fetching geonames API...');
        $io->progressStart(count($geoids));

        foreach ($geoids as $geoid) {
            if (!$this->entityManager->getRepository(GeonamesAdministrativeDivision::class)->findOneByGeonameId((int)$geoid)) {
                $newSubDiv = $this->apiService->getJsonSearch((int)$geoid);
                $this->dbService->saveSubdivisionToDatabase($newSubDiv);
                $newIds[] = $geoid;
                $io->progressAdvance();
            }
            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();
        if (empty($newIds)) {
            $io->warning('Command successfully executed but no new Ids were inserted.');
        } else {
            $io->success('Success ! ' . count($newIds) . ' new GeonameId(s) : ' . implode(',', $newIds));
        }

        return Command::SUCCESS;
    }
}
