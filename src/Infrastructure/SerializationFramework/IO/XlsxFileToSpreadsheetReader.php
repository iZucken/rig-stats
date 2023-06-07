<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Read\SerializedReader;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;
use RuntimeException;

/**
 * @template-implements SerializedReader<PhpSpreadsheet>
 */
final readonly class XlsxFileToSpreadsheetReader implements SerializedReader
{
    public function __construct(private string $filename)
    {
    }

    public function read(): PhpSpreadsheet
    {
        if (is_file($this->filename) && is_readable($this->filename)) {
            return new PhpSpreadsheet(IOFactory::load($this->filename));
        }
        throw new RuntimeException("Failed to read $this->filename");
    }
}
