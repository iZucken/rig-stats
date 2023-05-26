<?php

namespace RigStats\FlatData;

/**
 * @template T
 */
interface FlattenableList
{
    /**
     * @return array<int, T & Flattenable>
     */
    public function all(): array;
}