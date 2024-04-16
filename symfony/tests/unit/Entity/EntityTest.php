<?php

namespace App\Test\Unit\Entity;

use App\Entity\VesselPosition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VesselPosition::class)]
class EntityTest extends TestCase
{
    private VesselPosition $vesselPosition;

    protected function setUp(): void
    {
        $this->vesselPosition = new VesselPosition();
    }

    public function testId(): void
    {
        $this->assertNull($this->vesselPosition->getId());
    }

    public function testMmsi(): void
    {
        $this->assertNull($this->vesselPosition->getMmsi());

        $this->vesselPosition->setMmsi(123456789);
        $this->assertSame(123456789, $this->vesselPosition->getMmsi());
    }

    public function testStatus(): void
    {
        $this->assertNull($this->vesselPosition->getStatus());

        $this->vesselPosition->setStatus(1);
        $this->assertSame(1, $this->vesselPosition->getStatus());
    }

    public function testStationId(): void
    {
        $this->assertNull($this->vesselPosition->getStationId());

        $this->vesselPosition->setStationId(123);
        $this->assertSame(123, $this->vesselPosition->getStationId());
    }

    public function testSpeed(): void
    {
        $this->assertNull($this->vesselPosition->getSpeed());

        $this->vesselPosition->setSpeed(100);
        $this->assertSame(100, $this->vesselPosition->getSpeed());
    }

    public function testLon(): void
    {
        $this->assertNull($this->vesselPosition->getLon());

        $this->vesselPosition->setLon(45.6789);
        $this->assertSame(45.6789, $this->vesselPosition->getLon());
    }

    public function testLat(): void
    {
        $this->assertNull($this->vesselPosition->getLat());

        $this->vesselPosition->setLat(12.3456);
        $this->assertSame(12.3456, $this->vesselPosition->getLat());
    }

    public function testCourse(): void
    {
        $this->assertNull($this->vesselPosition->getCourse());

        $this->vesselPosition->setCourse(180);
        $this->assertSame(180, $this->vesselPosition->getCourse());
    }

    public function testHeading(): void
    {
        $this->assertNull($this->vesselPosition->getHeading());

        $this->vesselPosition->setHeading(90);
        $this->assertSame(90, $this->vesselPosition->getHeading());
    }

    public function testRot(): void
    {
        $this->assertNull($this->vesselPosition->getRot());

        $this->vesselPosition->setRot('10');
        $this->assertSame('10', $this->vesselPosition->getRot());
    }

    public function testTimestamp(): void
    {
        $this->assertNull($this->vesselPosition->getTimestamp());

        $this->vesselPosition->setTimestamp(1618303270);
        $this->assertSame(1618303270, $this->vesselPosition->getTimestamp());
    }
}