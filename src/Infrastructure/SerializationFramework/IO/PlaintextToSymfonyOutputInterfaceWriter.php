<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO;

use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriter;
use RigStats\Infrastructure\SerializationFramework\Serialized\Plaintext;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class PlaintextToSymfonyOutputInterfaceWriter implements SerializedWriter
{
    public function __construct(private OutputInterface $output, private Plaintext $data)
    {
    }

    public function describe(): string
    {
        return "Writing {$this->data->describe()} to generic output";
    }

    public function write(): void
    {
        $this->output->writeln($this->data->getData());
    }
}
