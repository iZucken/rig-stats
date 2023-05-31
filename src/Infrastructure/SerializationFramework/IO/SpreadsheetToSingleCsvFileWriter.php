<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use PhpOffice\PhpSpreadsheet\Writer\Csv;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriter;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;

final readonly class SpreadsheetToSingleCsvFileWriter implements SerializedWriter
{
    public function __construct(private string $basename, private PhpSpreadsheet $spreadsheet)
    {
    }

    private function filename(): string
    {
        return $this->basename . "." . $this->spreadsheet->getData()->getActiveSheet()->getTitle() . ".csv";
    }

    public function describe(): string
    {
        return "Writing {$this->spreadsheet->describe()} to {$this->filename()}";
    }

    public function write(): void
    {
        (new Csv($this->spreadsheet->getData()))->save($this->filename());
    }
}
