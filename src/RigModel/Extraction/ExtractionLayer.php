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
        $types = array_reduce($splits, fn ($a, $r) => [$r->type->name => $r->type->name, ...$a], []);
        if (count($splits) - count($types) !== 0) {
            throw new \InvalidArgumentException("Unexpected duplicate rate readings.");
        }
    }
}
