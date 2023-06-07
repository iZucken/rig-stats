<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Read\SerializedReader;
use RigStats\Infrastructure\SerializationFramework\IO\Read\SerializedReaderFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;

/**
 * @template-implements SerializedReaderFactory<PhpSpreadsheet>
 */
final readonly class XlsxFileToSpreadsheetReaderFactory implements SerializedReaderFactory
{
    public function __construct(private string $filename)
    {
    }

    public function readable(): ?SerializedReader
    {
        if (is_file($this->filename)
            && is_readable($this->filename)
            && IOFactory::identify($this->filename) === 'Xlsx') {
            return new XlsxFileToSpreadsheetReader($this->filename);
        }
        return null;
    }
}
