<?php

declare(strict_types=1);

namespace RigStats\FlatRender;

use RigStats\FlatData\FlattenableList;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class ConsoleFlatRender implements FlatRender
{
    public function __construct(private OutputInterface $output)
    {
    }

    public function disclaimer(): string
    {
        return "Writing to console";
    }

    public function renderList(FlattenableList $data): void
    {
        $all = $data->all();
        $this->output->writeln(join("; ", array_keys($all[0]->flatten())));
        foreach ($all as $datum) {
            $this->output->writeln(join("; ", array_values($datum->flatten())));
        }
    }
}
