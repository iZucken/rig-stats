<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO\Write;

use RigStats\Infrastructure\SerializationFramework\Format;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

/**
 * @template Format
 */
interface SerializedWriterProbe
{
    /**
     * @return Format<Format>[]
     */
    public function formats(): array;

    /**
     * @param Serialized<Format> $data
     * @return SerializedWriter|null
     */
    public function writable(Serialized $data): ?SerializedWriter;
}
