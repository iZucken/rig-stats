<?php

declare(strict_types=1);

namespace RigStats\Console;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RigStats\Extraction\SpreadsheetExtractionDataDeserializerVioletRed;
use RigStats\FlatRender\ConsoleFlatRender;
use RigStats\FlatRender\FlatRender;
use RigStats\FlatRender\JsonFileFlatRender;
use RigStats\FlatRender\XlsxFileFlatRender;
use RigStats\Extraction\ExtractionDataCorruptionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ComputeAllocationCommand extends Command
{
    protected static $defaultName = "compute:allocation";
    protected static $defaultDescription = "Computes layer split allocation from extraction data files.";

    protected function configure(): void
    {
        $this
            // todo: options to set output file
            ->addArgument(
                "inputFilename",
                InputArgument::REQUIRED,
                "Input data stored in xlsx compatible worksheet file."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('serialize_precision', 14);
        $inputFilename = $input->getArgument("inputFilename");
        if (!(is_file($inputFilename) && is_readable($inputFilename))) {
            $output->writeln("Cannot read from $inputFilename");
            return Command::INVALID;
        }
        /** @var FlatRender[] $outputRenders */
        // todo: options to toggle output types
        $outputRenders = [
            new XlsxFileFlatRender("output/computation", 'allocation'),
            new JsonFileFlatRender("output/computation", fn($data) => [
                'allocation' => ['data' => $data]
            ], new AllocationRemapperOrchidGreen),
        ];
        /** @var FlatRender[] $errorRenders */
        $errorRenders = [
            new XlsxFileFlatRender('output/errors', 'errors'),
            new ConsoleFlatRender($output),
        ];
        try {
            $computed = (new SpreadsheetExtractionDataDeserializerVioletRed)->deserialize(
                IOFactory::load($inputFilename)
            )->toAllocations();
            $output->writeln("Computation complete.");
            foreach ($outputRenders as $outputRender) {
                $output->writeln($outputRender->disclaimer());
                $outputRender->renderList($computed);
            }
            return Command::SUCCESS;
        } catch (ExtractionDataCorruptionException $exception) {
            $output->writeln("Input data contains errors.");
            foreach ($errorRenders as $outputRender) {
                $output->writeln($outputRender->disclaimer());
                $outputRender->renderList($exception->errors);
            }
            return Command::INVALID;
        }
    }
}
