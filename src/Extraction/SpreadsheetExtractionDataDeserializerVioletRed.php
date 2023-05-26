<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RigStats\Fluids\FluidRate;
use RigStats\Fluids\FluidSplit;
use RigStats\Fluids\FluidType;
use RigStats\Rig\LayerId;
use RigStats\Rig\WellId;

class SpreadsheetExtractionDataDeserializerVioletRed
{
    public function deserialize(Spreadsheet $inputSpreadsheet): ExtractionDaySeries
    {
        $rates = $inputSpreadsheet->getSheetByName("rates")->toArray();
        $splits = $inputSpreadsheet->getSheetByName("splits")->toArray();
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
                $data[] = (new ExtractionDay(
                    \DateTimeImmutable::createFromFormat("Y-m-d", $date),
                    $well['rates'],
                    array_map(fn (array $layer) => new ExtractionLayer(
                        new LayerId($wellId, intval($layer['layer'])),
                        $layer['splits'],
                    ), $well['layers']),
                ));
            }
        }
        return new ExtractionDaySeries($data);
    }
}
