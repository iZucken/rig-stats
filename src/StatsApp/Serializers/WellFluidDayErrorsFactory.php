<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\RigModel\Extraction\WellFluidDayErrors;

final readonly class WellFluidDayErrorsFactory implements SerializerFactory
{
    public function serializable(mixed $data, Format $format): null|WellFluidDayErrorsPlaintext|WellFluidDayErrorsSpreadsheet
    {
        if ($data instanceof WellFluidDayErrors) {
            if ($format->equals(Plaintext::getFormat())) {
                return new WellFluidDayErrorsPlaintext($data);
            }
            if ($format->equals(PhpSpreadsheet::getFormat())) {
                return new WellFluidDayErrorsSpreadsheet($data);
            }
        }
        return null;
    }
}
