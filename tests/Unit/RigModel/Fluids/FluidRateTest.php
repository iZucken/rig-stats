<?php

namespace Unit\RigModel\Fluids;

use PHPUnit\Framework\TestCase;
use RigStats\RigModel\Fluids\FluidRate;
use RigStats\RigModel\Fluids\FluidType;

/**
 * @covers \RigStats\RigModel\Fluids\FluidRate
 */
class FluidRateTest extends TestCase
{
    function testInstance()
    {
        $valid = new FluidRate(FluidType::Oil, 100.0);
        $this->assertEquals('Oil', $valid->type->name);
        $this->assertEquals(100.0, $valid->value);
        $this->expectException(\InvalidArgumentException::class);
        new FluidRate(FluidType::Oil, -1);
    }
}
