<?php

declare(strict_types=1);

namespace RigStats\RigModel\Fluids;

use RigStats\RigModel\Rig\LayerId;

/**
 * @codeCoverageIgnore
 */
final readonly class LayerSplit
{
    public function __construct(public LayerId $layer, public Split $split)
    {
    }
}
