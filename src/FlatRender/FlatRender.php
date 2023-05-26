<?php

namespace RigStats\FlatRender;

use RigStats\FlatData\FlattenableList;

interface FlatRender
{
    function disclaimer(): string;

    function renderList(FlattenableList $data): void;
}
