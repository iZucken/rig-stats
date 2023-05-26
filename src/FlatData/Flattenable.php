<?php

namespace RigStats\FlatData;

interface Flattenable
{
    public function flatten(): array;
}