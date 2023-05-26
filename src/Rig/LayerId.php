<?php

declare(strict_types=1);

namespace RigStats\Rig;

final readonly class LayerId
{
    public WellId $well;
    public int $id;

    public function __construct(WellId $well, int $id)
    {
        if ($id < 0) {
            throw new \InvalidArgumentException("Malformed well id value $id");
        }
        $this->well = $well;
        $this->id = $id;
    }
}
