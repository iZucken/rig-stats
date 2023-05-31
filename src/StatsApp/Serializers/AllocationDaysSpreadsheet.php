<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RigStats\RigModel\Fluids\FluidRate;
use RigStats\RigModel\RateAllocation\AllocationDays;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

final readonly class AllocationDaysSpreadsheet implements Serializer
{
    public function __construct(private AllocationDays $data)
    {
    }

    public function serialize(): PhpSpreadsheet
    {
        ini_set('serialize_precision', 14);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('allocations');
        foreach (
            [
                'dt',
                'well_id',
                'layer_id',
                'oil_rate',
                'gas_rate',
                'water_rate',
            ] as $index => $key
        ) {
            $sheet->setCellValue([$index + 1, 1], $key);
        }
        foreach ($this->data->days as $rowIndex => $row) {
            $serialRow = array_merge(
                [
                    $row->day->format("Y-m-d H:i:s"),
                    $row->layer->well->id,
                    $row->layer->id,
                ],
                array_reduce(
                    $row->rates,
                    fn(array $rates, FluidRate $rate) => [...$rates, $rate->value],
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
