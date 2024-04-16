<?php

declare(strict_types=1);

namespace App\Command;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\VesselPosition;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;

#[AsCommand(
    name: 'app:import-vessel-positions',
    description: 'Imports vessel positions from JSON file into the database'
)]
class ImportVesselPositionsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface   $entityManager,
        private readonly LoggerInterface $logger,
        private Client $client
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to import vessel positions from JSON file into the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // streaming can be implemented for large files
            $response = $this->client->request(
                'GET',
                'https://kpler.github.io/kp-recruitment/a2d6f4f07f5aca0f0a49c076f6a36cede56ce76e/ship_positions.json'
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $batchSize = 100; // Adjust batch size based on performance and memory

            foreach (array_chunk($data, $batchSize) as $chunk) {
                $this->importBatch($chunk);
            }

            $output->writeln('Vessel positions imported successfully.');
            return Command::SUCCESS;

        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch data: ' . $e->getMessage());
            $output->writeln('Failed to fetch data: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function importBatch(array $data): void
    {
        foreach ($data as $item) {
            if (!isset($item['mmsi'], $item['status'], $item['stationId'], $item['speed'], $item['lon'], $item['lat'], $item['course'], $item['heading'], $item['timestamp'])) {
                $this->logger->warning('Skipping entry: Required fields are missing.');
                continue;
            }
            $vesselPosition = new VesselPosition();
            $vesselPosition->setMmsi($item['mmsi']);
            $vesselPosition->setStatus($item['status']);
            $vesselPosition->setStationId($item['stationId']);
            $vesselPosition->setSpeed($item['speed']);
            $vesselPosition->setLon($item['lon']);
            $vesselPosition->setLat($item['lat']);
            $vesselPosition->setCourse($item['course']);
            $vesselPosition->setHeading($item['heading']);
            $vesselPosition->setTimestamp($item['timestamp']);
            $vesselPosition->setRot($item['rot'] ?? null);

            $this->entityManager->persist($vesselPosition);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->logger->info(count($data) . ' vessel positions imported successfully.');
    }

}