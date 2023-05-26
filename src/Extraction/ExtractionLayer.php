<?php

declare(strict_types=1);

namespace RigStats\Extraction;

use RigStats\Fluids\FluidSplit;
use RigStats\Rig\LayerId;

final readonly class ExtractionLayer
{
    public function __construct(
        public LayerId $layer,
        /**
         * @var FluidSplit[]
         */
        public array $splits,
    ) {
        // todo: maybe duplicate split types
    }
}
