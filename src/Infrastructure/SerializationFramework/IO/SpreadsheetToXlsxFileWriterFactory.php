<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriter;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;
use RigStats\Infrastructure\SerializationFramework\Serialized\PhpSpreadsheet;

/**
 * @template-implements SerializedWriterFactory<PhpSpreadsheet>
 */
final readonly class SpreadsheetToXlsxFileWriterFactory implements SerializedWriterFactory
{
    public function __construct(private string $basename)
    {
    }

    public function formats(): array
    {
        return [PhpSpreadsheet::getFormat()];
    }

    public function writable(Serialized $data): ?SerializedWriter
    {
        if ($data instanceof PhpSpreadsheet) {
            return new SpreadsheetToXlsxFileWriter($this->basename, $data);
        }
        return null;
    }
}
