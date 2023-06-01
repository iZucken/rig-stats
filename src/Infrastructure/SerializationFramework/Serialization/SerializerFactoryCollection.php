<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialization;

use RigStats\Infrastructure\SerializationFramework\Format;

final readonly class SerializerFactoryCollection implements SerializerFactory
{
    /**
     * @param SerializerFactory[] $factories
     */
    public function __construct(private array $factories)
    {
    }

    public function serializable(mixed $data, Format $format): ?Serializer
    {
        // todo: strategize around 0, 1, N available probes
        $supported = array_values(
            array_filter(array_map(fn($probe) => $probe->serializable($data, $format), $this->factories))
        );
        return $supported[0] ?? null;
    }
}
