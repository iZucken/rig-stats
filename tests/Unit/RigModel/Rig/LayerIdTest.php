<?php

namespace Unit\RigModel\Rig;

use RigStats\RigModel\Rig\LayerId;
use PHPUnit\Framework\TestCase;
use RigStats\RigModel\Rig\WellId;

/**
 * @covers \RigStats\RigModel\Rig\LayerId
 */
class LayerIdTest extends TestCase
{
    public function test__construct()
    {
        $well = new WellId(0);
        $this->assertEquals(1, (new LayerId($well, 1))->id);
        $this->expectException(\InvalidArgumentException::class);
        new LayerId($well, -1);
    }

    public function testEquals()
    {
        $well = new WellId(0);
        $this->assertTrue((new LayerId($well, 1))->equals(new LayerId($well, 1)));
    }
}
