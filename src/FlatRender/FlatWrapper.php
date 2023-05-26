<?php

namespace RigStats\FlatRender;

use RigStats\FlatData\FlattenableList;

interface FlatWrapper
{
    public function wrapList(FlattenableList $data): FlattenableList;
}
