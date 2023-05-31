<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriter;
use RigStats\Infrastructure\SerializationFramework\Serialized\Json;

final readonly class JsonToFileWriter implements SerializedWriter
{
    public function __construct(private string $basename, private Json $data)
    {
    }

    public function describe(): string
    {
        return "Writing {$this->data->describe()} to json file $this->basename.json";
    }

    public function write(): void
    {
        file_put_contents("$this->basename.json", json_encode($this->data->getData(), JSON_PRETTY_PRINT));
    }
}
