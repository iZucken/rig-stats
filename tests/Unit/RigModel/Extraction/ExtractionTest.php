<?php

namespace Unit\RigModel\Extraction;

use RigStats\RigModel\Extraction\Extraction;
use PHPUnit\Framework\TestCase;
use RigStats\RigModel\Extraction\ExtractionStats;
use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\Fluids\LayerSplit;
use RigStats\RigModel\Fluids\PerFluidMap;
use RigStats\RigModel\Fluids\Rate;
use RigStats\RigModel\Fluids\Split;
use RigStats\RigModel\Rig\LayerId;
use RigStats\RigModel\Rig\WellId;

/**
 * @covers \RigStats\RigModel\Extraction\Extraction
 * @coversDefaultClass \RigStats\RigModel\Extraction\Extraction
 */
class ExtractionTest extends TestCase
{
    /**
     * @covers ::getWellFluidErrors
     */
    public function testGetWellFluidErrors()
    {
        $map = new PerFluidMap(ExtractionStats::class);
        $well = new WellId(0);
        $layer0 = new LayerId($well, 0);
        $layer1 = new LayerId($well, 1);
        $map->add(FluidType::Oil, new ExtractionStats(
            new Rate(10.0),
            \SplFixedArray::fromArray([
                new LayerSplit($layer0, new Split(75.0)),
                new LayerSplit($layer1, new Split(24.9)),
            ])));
        $map->add(FluidType::Gas, new ExtractionStats(
            new Rate(5.0),
            \SplFixedArray::fromArray([
                new LayerSplit($layer0, new Split(100.0)),
                new LayerSplit($layer1, new Split(0.01)),
            ])));
        $at = new \DateTimeImmutable;
        $bigEpsilonSonNoErrors = new Extraction($at, $map, 0.1);
        $this->assertCount(0, $bigEpsilonSonNoErrors->getWellFluidErrors());
        $errors = (new Extraction($at, $map, 0e-5))->getWellFluidErrors();
        $this->assertCount(2, $errors);
        $this->assertEquals($at, $errors[0]->at);
        $this->assertEquals("Split data sum error by -0.10%", $errors[0]->error);
        $this->assertTrue($errors[0]->well->equals($well));
        $this->assertEquals(FluidType::Oil, $errors[0]->fluid);
    }

    /**
     * @covers ::getAllocations
     */
    public function testGetAllocations()
    {
        $at = new \DateTimeImmutable;
        $map = new PerFluidMap(ExtractionStats::class);
        $well = new WellId(0);
        $layer0 = new LayerId($well, 0);
        $layer1 = new LayerId($well, 1);
        $map->add(FluidType::Oil, new ExtractionStats(
            new Rate(10.0),
            \SplFixedArray::fromArray([
                new LayerSplit($layer0, new Split(75.0)),
                new LayerSplit($layer1, new Split(25.0)),
            ])));
        $map->add(FluidType::Gas, new ExtractionStats(
            new Rate(5.0),
            \SplFixedArray::fromArray([
                new LayerSplit($layer0, new Split(100.0)),
                new LayerSplit($layer1, new Split(0.0)),
            ])));
        $sut = new Extraction($at, $map, 0e-5);
        $allocations = $sut->getAllocations();
        $this->assertCount(2, $allocations);
        $this->assertEquals($at, $allocations[0]->at);
        $this->assertTrue($allocations[0]->layer->equals($layer0));
        $this->assertCount(2, $allocations[0]->rates);
        $this->assertEquals(7.5, $allocations[0]->rates->get(FluidType::Oil)->value);
        $this->assertEquals(5.0, $allocations[0]->rates->get(FluidType::Gas)->value);
        $this->assertEquals($at, $allocations[1]->at);
        $this->assertTrue($allocations[1]->layer->equals($layer1));
        $this->assertCount(2, $allocations[1]->rates);
        $this->assertEquals(2.5, $allocations[1]->rates->get(FluidType::Oil)->value);
        $this->assertEquals(0.0, $allocations[1]->rates->get(FluidType::Gas)->value);
    }

    /**
     * @covers ::sameDimensions
     */
    public function testSameDimensions()
    {
        $a = new Extraction(new \DateTimeImmutable, new PerFluidMap(ExtractionStats::class), 0.1);
        $b = new Extraction(new \DateTimeImmutable, new PerFluidMap(ExtractionStats::class), 0.1);
        $this->assertTrue($a->sameDimensions($b), 'Same');
        $c = new Extraction(new \DateTimeImmutable, new PerFluidMap(ExtractionStats::class), 0.01);
        $this->assertFalse($a->sameDimensions($c), 'Epsilon mismatch');
    }
}
