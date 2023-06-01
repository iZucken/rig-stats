<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\Extraction\Extractions as ExtractionsModel;
use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template-extends DeserializerFactory<PhpSpreadsheet, ExtractionsModel>
 */
final readonly class ExtractionsFactory implements DeserializerFactory
{
    public function __construct(private float $epsilon)
    {
    }

    public function deserializable(Serialized $data): ?Extractions
    {
        if ($data instanceof PhpSpreadsheet
            && $data->getData()->getSheetByName("rates")
            && $data->getData()->getSheetByName("splits")) {
            // todo: does not check table structure/signature
            return new Extractions($data, $this->epsilon);
        }
        return null;
    }
}
