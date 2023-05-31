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
    function testVo()
    {
        $valid = new FluidRate(FluidType::Oil, 100.0);
        $this->assertEquals('oil', $valid->type->value);
        $this->assertEquals(100.0, $valid->value);
        $this->expectException(\InvalidArgumentException::class);
        new FluidRate(FluidType::Oil, -1);
    }
}
