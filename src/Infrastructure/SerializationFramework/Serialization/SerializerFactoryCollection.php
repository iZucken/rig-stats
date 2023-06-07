<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialization;

use RigStats\Infrastructure\SerializationFramework\Format;

/**
 * @template-implements SerializerFactory<mixed, mixed, mixed>
 */
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
        $mapBy = fn(SerializerFactory $factory): ?Serializer => $factory->serializable($data, $format);
        $supported = array_values(array_filter(array_map($mapBy, $this->factories)));
        return $supported[0] ?? null;
    }
}
