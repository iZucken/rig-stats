<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO\Write;

use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template Formats
 */
interface SerializedWriterFactory
{
    /**
     * @return Format<Formats>[]
     */
    public function formats(): array;

    /**
     * @param Serialized<Format<Formats>> $data
     * @return SerializedWriter|null
     */
    public function writable(Serialized $data): ?SerializedWriter;
}
