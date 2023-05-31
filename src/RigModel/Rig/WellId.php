<?php

declare(strict_types=1);

namespace RigStats\RigModel\Rig;

final readonly class WellId
{
    function __construct(public int $id)
    {
        if ($id < 0) {
            throw new \InvalidArgumentException("Malformed well id $id");
        }
    }
}
