<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriter;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterProbe;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template-extends SerializedWriterProbe<Json>
 */
final readonly class JsonToFileWriterProbe implements SerializedWriterProbe
{
    public function __construct(private string $basename)
    {
    }

    public function formats(): array
    {
        return [Json::getFormat()];
    }

    public function writable(Serialized $data): ?SerializedWriter
    {
        if ($data instanceof Json) {
            return new JsonToFileWriter($this->basename, $data);
        }
        return null;
    }
}
