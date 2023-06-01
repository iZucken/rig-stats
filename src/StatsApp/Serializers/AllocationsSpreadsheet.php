<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RigStats\RigModel\Fluids\FluidSplitRate;
use RigStats\RigModel\RateAllocation\Allocations;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

final readonly class AllocationsSpreadsheet implements Serializer
{
    public function __construct(private Allocations $data)
    {
    }

    public function serialize(): PhpSpreadsheet
    {
        ini_set('serialize_precision', 14);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('allocations');
        $reference = $this->data->allocations[0];
        $keys = array_merge(
            [
                'dt',
                'well_id',
                'layer_id',
            ],
            array_reduce(
                $reference->rates,
                fn(array $rates, FluidSplitRate $rate) => [
                    ...$rates,
                    "{$rate->split->type->value}_rate",
                ],
                []
            ),
        );
        foreach ($keys as $index => $key) {
            $sheet->setCellValue([$index + 1, 1], $key);
        }
        foreach ($this->data->allocations as $rowIndex => $row) {
            $serialRow = array_merge(
                [
                    $row->at->format("Y-m-d H:i:s"),
                    $row->layer->well->id,
                    $row->layer->id,
                ],
                array_reduce(
                    $row->rates,
                    fn(array $rates, FluidSplitRate $rate) => [...$rates, $rate->getSplitRateValue()->value],
                    []
                ),
            );
            foreach ($serialRow as $colIndex => $col) {
                $sheet->setCellValue([$colIndex + 1, $rowIndex + 2], $col);
            }
        }
        return new PhpSpreadsheet($spreadsheet);
    }
}
