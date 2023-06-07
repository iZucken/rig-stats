<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RigStats\RigModel\Fluids\FluidType;
use RigStats\RigModel\Fluids\Rate;
use RigStats\RigModel\RateAllocation\Allocations;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

/**
 * @template-implements Serializer<Allocations, PhpSpreadsheet>
 */
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
        /** @var array<int, string> $keys */
        $keys = [
            'dt',
            'well_id',
            'layer_id',
            ...array_map(fn (FluidType $type) => "{$type->value}_rate", $reference->rates->keys()),
        ];
        foreach ($keys as $index => $key) {
            $sheet->setCellValue([$index + 1, 1], $key);
        }
        foreach ($this->data->allocations as $rowIndex => $row) {
            /** @var array<int, string> $serialRow */
            $serialRow = [
                $row->at->format("Y-m-d H:i:s"),
                $row->layer->well->id,
                $row->layer->id,
                ...array_map(fn (Rate $v) => $v->value, $row->rates->values()),
            ];
            foreach ($serialRow as $colIndex => $col) {
                $sheet->setCellValue([$colIndex + 1, $rowIndex + 2], $col);
            }
        }
        return new PhpSpreadsheet($spreadsheet);
    }
}
