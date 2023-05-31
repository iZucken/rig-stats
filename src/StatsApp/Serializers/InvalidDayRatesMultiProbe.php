<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerProbe;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\RigModel\Extraction\ExtractionDataCorruptionException;

final readonly class InvalidDayRatesMultiProbe implements SerializerProbe
{
    public function serializable(mixed $data, Format $format): null|InvalidDayRatesPlaintext|InvalidDayRatesSpreadsheet
    {
        if ($data instanceof ExtractionDataCorruptionException) {
            if ($format->equals(Plaintext::getFormat())) {
                return new InvalidDayRatesPlaintext($data);
            }
            if ($format->equals(PhpSpreadsheet::getFormat())) {
                return new InvalidDayRatesSpreadsheet($data);
            }
        }
        return null;
    }
}
