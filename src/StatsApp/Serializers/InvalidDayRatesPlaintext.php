<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\Extraction\ExtractionDataCorruptionException;
use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

final readonly class InvalidDayRatesPlaintext implements Serializer
{
    public function __construct(private ExtractionDataCorruptionException $error)
    {
    }

    public function serialize(): Plaintext
    {
        return new Plaintext(
            join(
                "\n",
                array_map(
                    fn($error) => "At {$error->day->format('Y-m-d')} "
                        . "#{$error->well->id} for {$error->fluid->value}: $error->error",
                    $this->error->errors
                )
            )
        );
    }
}
