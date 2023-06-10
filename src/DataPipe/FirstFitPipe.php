<?php

declare(strict_types=1);

namespace RigStats\DataPipe;

use RigStats\DataPipe\Exceptions\PipeNotConvergedException;
use RigStats\Infrastructure\SerializationFramework\Deserialization\Deserializer;
use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Read\SerializedReader;
use RigStats\Infrastructure\SerializationFramework\IO\Read\SerializedReaderFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterFactory;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;
use RigStats\Infrastructure\Types\TypeDescriber;
use RigStats\RigModel\Extraction\Extractions;

final readonly class FirstFitPipe
{
    public function __construct(
        /**
         * @var SerializedReaderFactory[]
         */
        private array $readers,
        /**
         * @var SerializedWriterFactory[]
         */
        private array $writers,
        /**
         * @var SerializerFactory[]
         */
        private array $serializers,
        /**
         * @var DeserializerFactory[]
         */
        private array $deserializers,
    ) {
    }

    /**
     * @throws PipeNotConvergedException
     */
    private function reader(): SerializedReader
    {
        foreach ($this->readers as $readerFactory) {
            if ($maybe = $readerFactory->readable()) {
                return $maybe;
            }
        }
        throw new PipeNotConvergedException("Failed to read the input into any known container type.");
    }

    /**
     * @throws PipeNotConvergedException
     */
    private function deserializer(Serialized $serialized): Deserializer
    {
        foreach ($this->deserializers as $deserializer) {
            if ($maybe = $deserializer->deserializable($serialized)) {
                return $maybe;
            }
        }
        throw new PipeNotConvergedException("{$serialized->describe()} is not deserializable into any known type.");
    }

    /**
     * @throws PipeNotConvergedException
     */
    private function transform(mixed $from): mixed
    {
        if ($from instanceof Extractions) {
            return $from->intoAllocationDaysOrInvalidRates();
        }
        throw new PipeNotConvergedException(
            "This pipeline only supports " . Extractions::class . ", but got " . TypeDescriber::describe($from)
        );
    }

    /**
     * @throws PipeNotConvergedException
     */
    private function maybeWrite(SerializedWriterFactory $writer, mixed $data): void
    {
        $found = false;
        foreach ($writer->formats() as $writerFormat) {
            foreach ($this->serializers as $serializer) {
                if ($serial = $serializer->serializable($data, $writerFormat)?->serialize()) {
                    if ($writable = $writer->writable($serial)) {
                        $writable->write();
                        $found = true;
                    }
                }
            }
        }
        if (!$found) {
            throw new PipeNotConvergedException(
                "No compatible output for " . get_class($writer) . " on " . TypeDescriber::describe($data)
            );
        }
    }

    /**
     * @throws PipeNotConvergedException
     */
    function run(): void
    {
        $computed = $this->transform($this->deserializer($this->reader()->read())->deserialize());
        foreach ($this->writers as $writer) {
            $this->maybeWrite($writer, $computed);
        }
    }
}
