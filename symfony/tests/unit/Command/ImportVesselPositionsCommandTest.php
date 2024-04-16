<?php

namespace App\Test\Unit\Command;

use App\Command\ImportVesselPositionsCommand;
use App\Entity\VesselPosition;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use GuzzleHttp\Client;

#[CoversClass(ImportVesselPositionsCommand::class)]
#[UsesClass(VesselPosition::class)]
class ImportVesselPositionsCommandTest extends TestCase
{
    private const MOCK_JSON_DATA = '[
        {"mmsi":247039300,"status":0,"stationId":81,"speed":180,"lon":15.4415,"lat":42.75178,"course":144,"heading":144,"rot":"","timestamp":1372683960},
        {"mmsi":247039300,"status":0,"stationId":82,"speed":154,"lon":16.21578,"lat":42.03212,"course":149,"heading":150,"rot":"","timestamp":1372700340}
    ]';

    private MockObject & Client $httpClient;
    private $entityManager;
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(Client::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testExecuteSuccess(): void
    {
        $this->httpClient
            ->method('request')
            ->willReturn(new Response(200, [], self::MOCK_JSON_DATA));

        $this->entityManager
            ->expects($this->exactly(2))
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->entityManager
            ->expects($this->once())
            ->method('clear');

        $this->logger
            ->expects($this->once())
            ->method('info');

        $command = new ImportVesselPositionsCommand($this->entityManager, $this->logger, $this->httpClient);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Vessel positions imported successfully.', $output);
    }

    public function testExecuteFailure(): void
    {
        $this->httpClient
            ->method('request')
            ->willThrowException($this->createMock(GuzzleException::class));

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->entityManager
            ->expects($this->never())
            ->method('clear');

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Failed to fetch data: ');

        $command = new ImportVesselPositionsCommand($this->entityManager, $this->logger, $this->httpClient);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Failed to fetch data: ', $output);
    }

    public function testExecuteMissingFields(): void
    {
        $missingFieldsData = [
            [
                // Missing 'mmsi' field
                'status' => 0,
                'stationId' => 81,
                'speed' => 180,
                'lon' => 15.4415,
                'lat' => 42.75178,
                'course' => 144,
                'heading' => 144,
                'timestamp' => 1372683960
            ]
        ];

        $this->httpClient
            ->method('request')
            ->willReturn(new Response(200, [], json_encode($missingFieldsData)));


        $this->logger
            ->expects($this->exactly(1))
            ->method('warning')
            ->with('Skipping entry: Required fields are missing.');

        $command = new ImportVesselPositionsCommand($this->entityManager, $this->logger, $this->httpClient);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}