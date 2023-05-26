<?php

namespace RigStats\FlatData;

interface Flattenable
{
    /**
     * @return array<string, scalar>
     */
    public function flatten(): array;
}