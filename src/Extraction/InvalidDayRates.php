<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use RigStats\FlatData\Flattenable;
use RigStats\Fluids\FluidType;
use RigStats\Rig\WellId;

final readonly class InvalidDayRates implements Flattenable
{
    public function __construct(
        public \DateTimeInterface $day,
        public WellId $well,
        public FluidType $fluid,
        public string $error,
    ) {
    }

    public function flatten(): array
    {
        return [
            'dt' => $this->day->format("Y-m-d H:i:s"),
            'well_id' => $this->well->id,
            'fluid' => $this->fluid->value,
            'error' => $this->error,
        ];
    }
}
