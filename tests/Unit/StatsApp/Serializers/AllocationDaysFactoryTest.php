<?php

namespace Unit\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\StatsApp\Serializers\AllocationDaysJson;
use RigStats\StatsApp\Serializers\AllocationDaysFactory;
use PHPUnit\Framework\TestCase;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\RigModel\RateAllocation\AllocationDays;
use RigStats\StatsApp\Serializers\AllocationDaysSpreadsheet;

/**
 * @covers \RigStats\StatsApp\Serializers\AllocationDaysFactory
 */
class AllocationDaysFactoryTest extends TestCase
{
    public function testSerializable()
    {
        $probe = new AllocationDaysFactory();
        $maybeValidData = new AllocationDays([]);
        $this->assertNull($probe->serializable(null, Json::getFormat()));
        $this->assertNull($probe->serializable($maybeValidData, Plaintext::getFormat()));
        $this->assertInstanceOf(
            AllocationDaysJson::class,
            $probe->serializable($maybeValidData, Json::getFormat())
        );
        $this->assertInstanceOf(
            AllocationDaysSpreadsheet::class,
            $probe->serializable($maybeValidData, PhpSpreadsheet::getFormat())
        );
    }
}
