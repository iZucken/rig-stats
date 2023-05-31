<?php

namespace Unit\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\StatsApp\Serializers\AllocationSeriesJson;
use RigStats\StatsApp\Serializers\AllocationSeriesFactory;
use PHPUnit\Framework\TestCase;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\RigModel\RateAllocation\AllocationDaySeries;
use RigStats\StatsApp\Serializers\AllocationSeriesSpreadsheet;

/**
 * @covers \RigStats\StatsApp\Serializers\AllocationSeriesFactory
 */
class AllocationSeriesFactoryTest extends TestCase
{
    public function testSerializable()
    {
        $probe = new AllocationSeriesFactory();
        $maybeValidData = new AllocationDaySeries([]);
        $this->assertNull($probe->serializable(null, Json::getFormat()));
        $this->assertNull($probe->serializable($maybeValidData, Plaintext::getFormat()));
        $this->assertInstanceOf(
            AllocationSeriesJson::class,
            $probe->serializable($maybeValidData, Json::getFormat())
        );
        $this->assertInstanceOf(
            AllocationSeriesSpreadsheet::class,
            $probe->serializable($maybeValidData, PhpSpreadsheet::getFormat())
        );
    }
}
