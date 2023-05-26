<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use RigStats\Fluids\FluidRate;
use RigStats\Rig\WellId;

final readonly class ExtractionDay
{
    public function __construct(
        public \DateTimeInterface $day,
        public WellId $well,
        /**
         * @var FluidRate[]
         */
        public array $rates,
        /**
         * @var ExtractionLayer[]
         */
        public array $layers,
    ) {
    }
}
