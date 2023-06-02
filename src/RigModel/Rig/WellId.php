<?php

declare(strict_types=1);

namespace RigStats\RigModel\Rig;

use InvalidArgumentException;

final readonly class WellId
{
    function __construct(public int $id)
    {
        if ($id < 0) {
            throw new InvalidArgumentException("Malformed well id $id");
        }
    }

    public function equals(self $well): bool
    {
        return $this->id === $well->id;
    }
}
