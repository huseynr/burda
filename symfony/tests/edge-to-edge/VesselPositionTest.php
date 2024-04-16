<?php

namespace App\Test\EdgeToEdge;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Command\ImportVesselPositionsCommand;
use App\Entity\VesselPosition;
use App\EventListener\RateLimitingListener;
use App\EventListener\RequestLoggingListener;
use App\Repository\VesselPositionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(ImportVesselPositionsCommand::class)]
#[UsesClass(VesselPosition::class)]
#[UsesClass(RateLimitingListener::class)]
#[UsesClass(RequestLoggingListener::class)]
#[UsesClass(VesselPositionRepository::class)]
class VesselPositionTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/vessel_positions');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            '@context'         => '/api/contexts/VesselPosition',
            '@id'              => '/api/vessel_positions',
        ]);

        $this->assertCount(10, $response->toArray()['hydra:member']);
    }

    public function testPagination(): void
    {
        static::createClient()->request('GET', '/api/vessel_positions?page=2');

        $this->assertJsonContains([
            'hydra:view'       => [
                '@id'            => '/api/vessel_positions?page=2',
            ],
        ]);
    }

    public function testCreateVesselPosition(): void
    {
        static::createClient()->request('POST', '/api/vessel_positions', [
            'json' => [
                'mmsi'      => 247039301,
                'status'    => 0,
                'stationId' => 81,
                'speed'     => 180,
                'lon'       => 15.4415,
                'lat'       => 42.75178,
                'course'    => 144,
                'heading'   => 144,
                'rot'       => '',
                'timestamp' => 1372683960
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            'mmsi'      => 247039301,
            'status'    => 0,
            'stationId' => 81,
            'speed'     => 180,
            'lon'       => 15.4415,
            'lat'       => 42.75178,
            'course'    => 144,
            'heading'   => 144,
            'rot'       => '',
            'timestamp' => 1372683960
        ]);
    }
}










