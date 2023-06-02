<?php

namespace Unit\RigModel\Fluids;

use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\Fluids\PerFluidMap;
use PHPUnit\Framework\TestCase;

/**
 * @covers \RigStats\RigModel\Fluids\PerFluidMap
 */
class PerFluidMapTest extends TestCase
{
    public function testInvalidArgumentExceptionOnInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $map = new PerFluidMap(FluidType::class);
        $map->add(FluidType::Oil, 1);
    }

    public function testInvalidArgumentExceptionOnDuplicateAdd()
    {
        $this->expectException(\InvalidArgumentException::class);
        $map = new PerFluidMap(FluidType::class);
        $map->add(FluidType::Oil, FluidType::Oil);
        $map->add(FluidType::Oil, FluidType::Water);
    }

    public function testIteration()
    {
        $map = new PerFluidMap(FluidType::class);
        $map->add(FluidType::Oil, FluidType::Oil);
        $map->add(FluidType::Gas, FluidType::Gas);
        foreach ($map as $type => $value) {
            $this->assertEquals($type, $value);
        }
    }

    public function testKeysValues()
    {
        $map = new PerFluidMap(FluidType::class);
        $map->add(FluidType::Oil, FluidType::Gas);
        $map->add(FluidType::Gas, FluidType::Oil);
        $this->assertEquals([FluidType::Oil, FluidType::Gas], $map->keys());
        $this->assertEquals([FluidType::Gas, FluidType::Oil], $map->values());
        $this->assertEquals(FluidType::Gas, $map->get(FluidType::Oil));
    }

    public function testSameDimensions()
    {
        $a = new PerFluidMap(FluidType::class);
        $a->add(FluidType::Oil, FluidType::Oil);
        $this->assertTrue($a->sameDimensions($a), 'Self equality');
        $b = new PerFluidMap(FluidType::class);
        $b->add(FluidType::Oil, FluidType::Oil);
        $b->add(FluidType::Gas, FluidType::Gas);
        $this->assertFalse($a->sameDimensions($b), 'Count mismatch');
        $c = new PerFluidMap(FluidType::class);
        $c->add(FluidType::Gas, FluidType::Gas);
        $c->add(FluidType::Oil, FluidType::Oil);
        // todo: see if ordering should matter for the model
        $this->assertFalse($c->sameDimensions($b), 'Order mismatch');
        $d = new PerFluidMap(\stdClass::class);
        $d->add(FluidType::Oil, new \stdClass);
        $d->add(FluidType::Gas, new \stdClass);
        $this->assertFalse($b->sameDimensions($d), 'Type mismatch');
    }
}
