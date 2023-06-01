<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\RigModel\Extraction\WellFluidErrors;

final readonly class WellFluidErrorsFactory implements SerializerFactory
{
    public function serializable(mixed $data, Format $format): null|WellFluidErrorsPlaintext|WellFluidErrorsSpreadsheet
    {
        if ($data instanceof WellFluidErrors) {
            if ($format->equals(Plaintext::getFormat())) {
                return new WellFluidErrorsPlaintext($data);
            }
            if ($format->equals(PhpSpreadsheet::getFormat())) {
                return new WellFluidErrorsSpreadsheet($data);
            }
        }
        return null;
    }
}
