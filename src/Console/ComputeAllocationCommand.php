<?php

declare(strict_types=1);

namespace RigStats\Console;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RigStats\Extraction\SpreadsheetExtractionDataDeserializer;
use RigStats\FlatRender\ConsoleFlatRender;
use RigStats\FlatRender\FlatRender;
use RigStats\FlatRender\JsonFileFlatRender;
use RigStats\FlatRender\XlsxFileFlatRender;
use RigStats\RateAllocation\AllocationComputer;
use RigStats\RateAllocation\InvalidDataException;
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
            // todo: output basename options
            // todo: output format options (toggles)
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
        $outputRenders = [
            new XlsxFileFlatRender("output/computation", 'allocation'),
            new JsonFileFlatRender("output/computation", new AllocationJsonDataWrapper),
        ];
        /** @var FlatRender[] $errorRenders */
        $errorRenders = [
            new XlsxFileFlatRender('output/errors', 'errors'),
            new ConsoleFlatRender($output),
        ];
        try {
            $computed = (new AllocationComputer)->compute(
                (new SpreadsheetExtractionDataDeserializer)->deserialize(IOFactory::load($inputFilename))
            );
            $output->writeln("Computation complete.");
            foreach ($outputRenders as $outputRender) {
                $output->writeln($outputRender->disclaimer());
                $outputRender->renderList($computed);
            }
            return Command::SUCCESS;
        } catch (InvalidDataException $exception) {
            $output->writeln("Input data contains errors.");
            foreach ($errorRenders as $outputRender) {
                $output->writeln($outputRender->disclaimer());
                $outputRender->renderList($exception->errors);
            }
            return Command::INVALID;
        }
    }
}
