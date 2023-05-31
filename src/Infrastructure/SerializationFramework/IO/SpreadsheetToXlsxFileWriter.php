<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriter;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;

final readonly class SpreadsheetToXlsxFileWriter implements SerializedWriter
{
    public function __construct(private string $basename, private PhpSpreadsheet $spreadsheet)
    {
    }

    public function describe(): string
    {
        return "Writing {$this->spreadsheet->describe()} to $this->basename.xlsx";
    }

    public function write(): void
    {
        (new Xlsx($this->spreadsheet->getData()))->save("$this->basename.xlsx");
    }
}
