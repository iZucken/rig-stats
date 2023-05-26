<?php

declare(strict_types=1);

namespace RigStats\Rig;

final readonly class LayerId
{
    public function __construct(public WellId $well, public int $id)
    {
        if ($id < 0) {
            throw new \InvalidArgumentException("Malformed well id value $id");
        }
    }
}
