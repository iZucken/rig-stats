<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialized;

use RigStats\Infrastructure\SerializationFramework\Format;

/**
 * @template-implements Serialized<string>
 */
final readonly class Plaintext implements Serialized
{
    public function __construct(private string $text)
    {
    }

    public function describe(): string
    {
        return "plain text (" . strlen($this->text) . " bytes)";
    }

    public static function getFormat(): Format
    {
        return new Format(self::class);
    }

    public function getData(): string
    {
        return $this->text;
    }
}
