<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\RigModel\RateAllocation\AllocationDays;
use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;

final class AllocationDaysFactory implements SerializerFactory
{
    public function serializable(mixed $data, Format $format): null|AllocationDaysJson|AllocationDaysSpreadsheet
    {
        if ($data instanceof AllocationDays) {
            if ($format->equals(Json::getFormat())) {
                return new AllocationDaysJson($data);
            } elseif ($format->equals(PhpSpreadsheet::getFormat())) {
                return new AllocationDaysSpreadsheet($data);
            } else {
                return null;
            }
        }
        return null;
    }
}
