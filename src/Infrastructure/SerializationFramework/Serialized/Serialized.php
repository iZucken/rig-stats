<?php

declare(strict_types=1);

namespace RigStats\Infrastructure\SerializationFramework\Serialized;

use RigStats\Infrastructure\SerializationFramework\Format;

/**
 * @template Data - intermediate data representation for this serialized format
 */
interface Serialized
{
    public function describe(): string;

    public static function getFormat(): Format;

    /**
     * @return Data
     */
    public function getData(): mixed;
}
