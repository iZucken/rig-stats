<?php

namespace Unit\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\StatsApp\Serializers\AllocationsJson;
use RigStats\StatsApp\Serializers\AllocationsFactory;
use PHPUnit\Framework\TestCase;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\RigModel\RateAllocation\Allocations;
use RigStats\StatsApp\Serializers\AllocationsSpreadsheet;

/**
 * @covers \RigStats\StatsApp\Serializers\AllocationsFactory
 */
class AllocationsFactoryTest extends TestCase
{
    public function testSerializable()
    {
        $probe = new AllocationsFactory();
        $maybeValidData = new Allocations([]);
        $this->assertNull($probe->serializable(null, Json::getFormat()));
        $this->assertNull($probe->serializable($maybeValidData, Plaintext::getFormat()));
        $this->assertInstanceOf(
            AllocationsJson::class,
            $probe->serializable($maybeValidData, Json::getFormat())
        );
        $this->assertInstanceOf(
            AllocationsSpreadsheet::class,
            $probe->serializable($maybeValidData, PhpSpreadsheet::getFormat())
        );
    }
}
