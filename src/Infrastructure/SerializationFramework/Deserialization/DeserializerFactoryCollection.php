<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Deserialization;

use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;

final readonly class DeserializerFactoryCollection implements DeserializerFactory
{
    /**
     * @param DeserializerFactory[] $factories
     */
    public function __construct(private array $factories)
    {
    }

    public function deserializable(Serialized $data): ?Deserializer
    {
        // todo: strategize around 0, 1, N available probes
        $supported = array_values(
            array_filter(array_map(fn($probe) => $probe->deserializable($data), $this->factories))
        );
        return $supported[0] ?? null;
    }
}
