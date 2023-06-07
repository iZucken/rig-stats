<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use DateTimeImmutable;
use RigStats\RigModel\Extraction\Extraction;
use RigStats\RigModel\Extraction\Extractions as ExtractionsModel;
use RigStats\RigModel\Extraction\ExtractionStats;
use RigStats\RigModel\Fluids\LayerSplit;
use RigStats\RigModel\Fluids\PerFluidMap;
use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\Fluids\Rate;
use RigStats\RigModel\Fluids\Split;
use RigStats\RigModel\Rig\LayerId;
use RigStats\RigModel\Rig\WellId;
use RigStats\Infrastructure\SerializationFramework\Deserialization\Deserializer;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RuntimeException;
use SplFixedArray;

/**
 * @template-implements Deserializer<PhpSpreadsheet, ExtractionsModel>
 */
final readonly class Extractions implements Deserializer
{
    public function __construct(private PhpSpreadsheet $carrier, private float $epsilon)
    {
    }

    public function deserialize(): ExtractionsModel
    {
        /**
         * @var array<int, array<int, string>> $rates
         */
        $rates = $this->carrier->getData()->getSheetByName("rates")?->toArray() ?? [];
        /**
         * @var array<int, array<int, string>> $splits
         */
        $splits = $this->carrier->getData()->getSheetByName("splits")?->toArray() ?? [];
        array_shift($rates); // header removal
        array_shift($splits); // header removal
        $layers = [];
        foreach ($splits as $row) {
            $layerId = new LayerId(new WellId(intval($row[1])), intval($row[2]));
            $layers[$row[0]][$row[1]]['oil'][] = new LayerSplit($layerId, new Split(floatval($row[3])));
            $layers[$row[0]][$row[1]]['gas'][] = new LayerSplit($layerId, new Split(floatval($row[4])));
            $layers[$row[0]][$row[1]]['water'][] = new LayerSplit($layerId, new Split(floatval($row[5])));
        }
        $data = [];
        $rateFluidColumns = ['oil' => 2, 'gas' => 3, 'water' => 4];
        foreach ($rates as $row) {
            $statsMap = new PerFluidMap(ExtractionStats::class);
            foreach ($rateFluidColumns as $fluid => $column) {
                $statsMap->add(
                    FluidType::from($fluid),
                    new ExtractionStats(
                        new Rate(floatval($row[$column])),
                        SplFixedArray::fromArray($layers[$row[0]][$row[1]][$fluid]),
                    )
                );
            }
            if (!$dt = DateTimeImmutable::createFromFormat("!Y-m-d", $row[0])) {
                throw new RuntimeException("Value $row[0] must be a valid date conforming to `!Y-m-d`");
            }
            $data[] = new Extraction(
                $dt,
                $statsMap,
                $this->epsilon,
            );
        }
        return new ExtractionsModel(SplFixedArray::fromArray($data, false));
    }
}
