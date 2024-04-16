<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\VesselPositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: VesselPositionRepository::class),
    ApiResource(
        paginationEnabled: true,
        paginationItemsPerPage: 10
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'id'   => SearchFilter::STRATEGY_EXACT,
            'mmsi' => SearchFilter::STRATEGY_EXACT,
        ]
    ),
    ApiFilter(
        RangeFilter::class,
        properties: ['lat', 'lon', 'timestamp']
    ),
    ApiFilter(
        OrderFilter::class,
        properties: ['id']
    )
]
class VesselPosition
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column,
        ApiProperty(description: 'The ID of the vessel.')
    ]
    private ?int $id = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'A unique vessel identifier.')
    ]
    private ?int $mmsi = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: ' AIS vessel status.')
    ]
    private ?int $status = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'Receiving station ID.')
    ]
    private ?int $stationId = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'Speed in knots multiplied by 10.')
    ]
    private ?int $speed = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'Longitude.')
    ]
    private ?float $lon = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'Latitude.')
    ]
    private ?float $lat = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'Vessel’s course over ground.')
    ]
    private ?int $course = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'Vessel’s true heading.')
    ]
    private ?int $heading = null;

    #[
        ORM\Column(length: 20, nullable: true),
        ApiProperty(description: 'Vessel’s rate of turn.')
    ]
    private ?string $rot = null;

    #[
        ORM\Column,
        Assert\NotBlank,
        ApiProperty(description: 'Position timestamp.')
    ]
    private ?int $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMmsi(): ?int
    {
        return $this->mmsi;
    }

    public function setMmsi(int $mmsi): static
    {
        $this->mmsi = $mmsi;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStationId(): ?int
    {
        return $this->stationId;
    }

    public function setStationId(int $stationId): static
    {
        $this->stationId = $stationId;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): static
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getCourse(): ?int
    {
        return $this->course;
    }

    public function setCourse(int $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getHeading(): ?int
    {
        return $this->heading;
    }

    public function setHeading(int $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getRot(): ?string
    {
        return $this->rot;
    }

    public function setRot(?string $rot): static
    {
        $this->rot = $rot;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
