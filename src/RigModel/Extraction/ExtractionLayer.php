<?php

declare(strict_types=1);

namespace RigStats\RigModel\Extraction;

use RigStats\RigModel\Fluids\FluidSplit;
use RigStats\RigModel\Rig\LayerId;

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