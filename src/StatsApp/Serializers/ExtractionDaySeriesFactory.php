<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\Extraction\ExtractionDaySeries as ExtractionDaySeriesModel;
use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;
use RigStats\Infrastructure\SerializationFramework\Types\ClassType;
use RigStats\Infrastructure\SerializationFramework\Types\Type;

/**
 * @template-extends DeserializerFactory<PhpSpreadsheet, ExtractionDaySeriesModel>
 */
final class ExtractionDaySeriesFactory implements DeserializerFactory
{
    public function deserializable(Serialized $data, Type $type): ?ExtractionDaySeries
    {
        if ($data instanceof PhpSpreadsheet
            && $type->equals(new ClassType(ExtractionDaySeriesModel::class))
            && $data->getData()->getSheetByName("rates")
            && $data->getData()->getSheetByName("splits")) {
            return new ExtractionDaySeries($data, $type);
        }
        return null;
    }
}
