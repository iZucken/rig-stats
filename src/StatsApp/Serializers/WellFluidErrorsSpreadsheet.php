<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Serializers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RigStats\RigModel\Extraction\WellFluidErrors;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RigStats\Infrastructure\SerializationFramework\Serialization\Serializer;

/**
 * @template-implements Serializer<WellFluidErrors, PhpSpreadsheet>
 */
final readonly class WellFluidErrorsSpreadsheet implements Serializer
{
    public function __construct(private WellFluidErrors $error)
    {
    }

    public function serialize(): PhpSpreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("errors");
        foreach (['dt', 'well_id', 'fluid', 'error'] as $index => $key) {
            $sheet->setCellValue([$index + 1, 1], $key);
        }
        foreach ($this->error->errors as $rowIndex => $error) {
            $rowData = [
                $error->at->format("Y-m-d H:i:s"),
                $error->well->id,
                $error->fluid->value,
                $error->error,
            ];
            foreach ($rowData as $colIndex => $col) {
                $sheet->setCellValue([$colIndex + 1, $rowIndex + 2], $col);
            }
        }
        return new PhpSpreadsheet($spreadsheet);
    }
}
