<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO\Read;

/**
 * @template ContainerType
 */
interface SerializedReaderFactory
{
    /**
     * @return null|SerializedReader<ContainerType>
     */
    public function readable(): ?SerializedReader;
}
