<?php

declare(strict_types=1);

namespace RigStats\FlatRender;

use RigStats\FlatData\FlattenableList;

final readonly class JsonFileFlatRender implements FlatRender
{
    public function __construct(private string $basename, private ?FlatWrapper $wrapper)
    {
    }

    public function disclaimer(): string
    {
        return "Writing to json file $this->basename.json";
    }

    public function renderList(FlattenableList $data): void
    {
        if ($this->wrapper) {
            $data = $this->wrapper->wrapList($data);
        }
        file_put_contents("$this->basename.json", json_encode($data, JSON_PRETTY_PRINT));
    }
}
