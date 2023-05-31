<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\IO\Write;

interface SerializedWriter
{
    function describe(): string;

    public function write(): void;
}
