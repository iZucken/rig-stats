<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\RigModel\RateAllocation\Allocations;
use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;

/**
 * @template-implements SerializerFactory<Allocations, Json|PhpSpreadsheet, Serializer<Allocations, Json>|Serializer<Allocations, PhpSpreadsheet>>
 */
final class AllocationsFactory implements SerializerFactory
{
    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    public function serializable(mixed $data, Format $format): ?Serializer
    {
        if ($data instanceof Allocations) {
            if ($format->equals(Json::getFormat())) {
                return new AllocationsJson($data);
            }
            if ($format->equals(PhpSpreadsheet::getFormat())) {
                return new AllocationsSpreadsheet($data);
            }
        }
        return null;
    }
}
