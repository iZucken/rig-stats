<?php

namespace Unit\RigModel\Rig;

use RigStats\RigModel\Rig\WellId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \RigStats\RigModel\Rig\WellId
 */
class WellIdTest extends TestCase
{
    public function test__construct()
    {
        $this->assertEquals(1, (new WellId(1))->id);
        $this->expectException(\InvalidArgumentException::class);
        new WellId(-1);
    }

    public function testEquals()
    {
        $this->assertTrue((new WellId(1))->equals(new WellId(1)));
    }
}
