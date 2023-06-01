<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use RigStats\RigModel\Extraction\Extraction;
use RigStats\RigModel\Extraction\Extractions as ExtractionsModel;
use RigStats\RigModel\Extraction\ExtractionLayer;
use RigStats\RigModel\Fluids\FluidRate;
use RigStats\RigModel\Fluids\FluidSplit;
use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\Rig\LayerId;
use RigStats\RigModel\Rig\WellId;
use RigStats\Infrastructure\SerializationFramework\Deserialization\Deserializer;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;

/**
 * @template-extends Deserializer<PhpSpreadsheet, ExtractionsModel>
 */
final readonly class Extractions implements Deserializer
{
    public function __construct(private PhpSpreadsheet $carrier, private float $epsilon)
    {
    }

    public function deserialize(): ExtractionsModel
    {
        $rates = $this->carrier->getData()->getSheetByName("rates")->toArray();
        $splits = $this->carrier->getData()->getSheetByName("splits")->toArray();
        array_shift($rates); // header removal
        array_shift($splits); // header removal
        $merge = [];
        foreach ($rates as $row) {
            $merge[$row[0]][$row[1]] = [
                'rates' => [
                    new FluidRate(FluidType::Oil, floatval($row[2])),
                    new FluidRate(FluidType::Gas, floatval($row[3])),
                    new FluidRate(FluidType::Water, floatval($row[4])),
                ],
                'layers' => [],
            ];
        }
        foreach ($splits as $row) {
            $merge[$row[0]][$row[1]]['layers'][] = [
                'layer' => $row[2],
                'splits' => [
                    new FluidSplit(FluidType::Oil, floatval($row[3])),
                    new FluidSplit(FluidType::Gas, floatval($row[4])),
                    new FluidSplit(FluidType::Water, floatval($row[5])),
                ],
            ];
        }
        $data = [];
        foreach ($merge as $date => $wells) {
            foreach ($wells as $wellId => $well) {
                $wellId = new WellId(intval($wellId));
                $data[] = (new Extraction(
                    \DateTimeImmutable::createFromFormat("Y-m-d", $date),
                    $well['rates'],
                    array_map(fn (array $layer) => new ExtractionLayer(
                        new LayerId($wellId, intval($layer['layer'])),
                        $layer['splits'],
                    ), $well['layers']),
                    $this->epsilon,
                ));
            }
        }
        return new ExtractionsModel($data);
    }
}
