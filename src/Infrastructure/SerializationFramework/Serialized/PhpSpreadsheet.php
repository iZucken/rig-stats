<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialized;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use RigStats\Infrastructure\SerializationFramework\Format;

final readonly class PhpSpreadsheet implements Serialized
{
    public function __construct(private Spreadsheet $spreadsheet)
    {
    }

    public function describe(): string
    {
        return "php-spreadsheet";
    }

    public static function getFormat(): Format
    {
        return new Format(self::class);
    }

    public function getData(): Spreadsheet
    {
        return $this->spreadsheet;
    }
}
