<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\RigModel\RateAllocation\AllocationDaySeries;
use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerProbe;

final class AllocationSeriesMultiProbe implements SerializerProbe
{
    public function serializable(mixed $data, Format $format): null|AllocationSeriesJson|AllocationSeriesSpreadsheet
    {
        if ($data instanceof AllocationDaySeries) {
            if ($format->equals(Json::getFormat())) {
                return new AllocationSeriesJson($data);
            }
            if ($format->equals(PhpSpreadsheet::getFormat())) {
                return new AllocationSeriesSpreadsheet($data);
            }
        }
        return null;
    }
}
