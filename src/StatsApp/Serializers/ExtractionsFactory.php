<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\Extraction\Extractions as ExtractionsModel;
use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;
use RigStats\Infrastructure\SerializationFramework\Types\ClassType;
use RigStats\Infrastructure\SerializationFramework\Types\Type;

/**
 * @template-extends DeserializerFactory<PhpSpreadsheet, ExtractionsModel>
 */
final readonly class ExtractionsFactory implements DeserializerFactory
{
    public function __construct(private float $epsilon)
    {
    }

    public function deserializable(Serialized $data, Type $type): ?Extractions
    {
        if ($data instanceof PhpSpreadsheet
            && $type->equals(new ClassType(ExtractionsModel::class))
            && $data->getData()->getSheetByName("rates")
            && $data->getData()->getSheetByName("splits")) {
            // todo: does not check table structure/signature
            return new Extractions($data, $type, $this->epsilon);
        }
        return null;
    }
}
