<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriter;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterProbe;
use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use RigStats\Infrastructure\SerializationFramework\Serialized\Serialized;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @template-extends SerializedWriterProbe<Plaintext>
 */
final readonly class PlaintextToSymfonyOutputInterfaceWriterProbe implements SerializedWriterProbe
{
    public function __construct(private OutputInterface $output)
    {
    }

    public function formats(): array
    {
        return [Plaintext::getFormat()];
    }

    public function writable(Serialized $data): ?SerializedWriter
    {
        if ($data instanceof Plaintext) {
            return new PlaintextToSymfonyOutputInterfaceWriter($this->output, $data);
        }
        return null;
    }
}
