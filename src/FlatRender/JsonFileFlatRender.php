<?php

declare(strict_types=1);

namespace RigStats\FlatRender;

use RigStats\FlatData\FlattenableList;

final class JsonFileFlatRender implements FlatRender
{
    /**
     * @var callable
     */
    private $wrapper;

    public function __construct(private readonly string $basename, callable $wrapper, private readonly FlatWrapper $remapper)
    {
        $this->wrapper = $wrapper;
    }

    public function disclaimer(): string
    {
        return "Writing to json file $this->basename.json";
    }

    public function renderList(FlattenableList $data): void
    {
        file_put_contents("$this->basename.json", json_encode(($this->wrapper)(
            array_map(fn($element) => $element->flatten(), $this->remapper->wrapList($data)->all())
        ), JSON_PRETTY_PRINT));
    }
}
