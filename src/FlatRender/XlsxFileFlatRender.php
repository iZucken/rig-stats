<?php

declare(strict_types=1);

namespace RigStats\FlatRender;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RigStats\FlatData\FlattenableList;

final readonly class XlsxFileFlatRender implements FlatRender
{
    public function __construct(private string $basename, private string $worksheetTitle)
    {
    }

    public function disclaimer(): string
    {
        return "Writing to spreadsheet $this->basename.xlsx";
    }

    public function renderList(FlattenableList $data): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($this->worksheetTitle);
        $all = $data->all();
        foreach (array_keys($all[0]->flatten()) as $index => $key) {
            $sheet->setCellValue([$index + 1, 1], $key);
        }
        foreach ($all as $rowIndex => $row) {
            foreach (array_values($row->flatten()) as $colIndex => $col) {
                $sheet->setCellValue([$colIndex + 1, $rowIndex + 2], $col);
            }
        }
        (new Xlsx($spreadsheet))->save("$this->basename.xlsx");
    }
}
