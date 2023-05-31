<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\RigModel\RateAllocation\AllocationDaySeries;
use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;

final class AllocationSeriesFactory implements SerializerFactory
{
    public function serializable(mixed $data, Format $format): null|AllocationSeriesJson|AllocationSeriesSpreadsheet
    {
        if ($data instanceof AllocationDaySeries) {
            if ($format->equals(Json::getFormat())) {
                return new AllocationSeriesJson($data);
            } elseif ($format->equals(PhpSpreadsheet::getFormat())) {
                return new AllocationSeriesSpreadsheet($data);
            } else {
                return null;
            }
        }
        return null;
    }
}
