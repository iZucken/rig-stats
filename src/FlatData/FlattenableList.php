<?php

namespace RigStats\FlatData;

interface FlattenableList
{
    /**
     * @return Flattenable[]
     */
    public function all(): array;

    public function count(): int;
}