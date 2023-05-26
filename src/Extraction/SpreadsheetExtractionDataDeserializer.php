<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SpreadsheetExtractionDataDeserializer
{
    public function deserialize(Spreadsheet $inputSpreadsheet): array
    {
        $rates = $inputSpreadsheet->getSheetByName("rates")->toArray();
        $splits = $inputSpreadsheet->getSheetByName("splits")->toArray();
        array_shift($rates); // header removal
        array_shift($splits); // header removal
        $data = [];
        foreach ($rates as $row) {
            $data[$row[0]][intval($row[1])] = [
                'oil' => floatval($row[2]),
                'gas' => floatval($row[3]),
                'water' => floatval($row[4]),
                'splits' => [],
            ];
        }
        foreach ($splits as $row) {
            $data[$row[0]][intval($row[1])]['splits'][intval($row[2])] = [
                'oil' => floatval($row[3]),
                'gas' => floatval($row[4]),
                'water' => floatval($row[5]),
            ];
        }
        return $data;
    }
}
