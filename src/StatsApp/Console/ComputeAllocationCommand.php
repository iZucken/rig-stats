<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Console;

use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\IO\JsonToFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\PlaintextToSymfonyOutputInterfaceWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToSingleCsvFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToXlsxFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\XlsxFileToSpreadsheetReaderFactory;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\DataPipe\FirstFitPipe;
use RigStats\DataPipe\Exceptions\PipeNotConvergedException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand("compute:allocation", "Computes layer split allocation from extraction data files.")]
final class ComputeAllocationCommand extends Command
{
    public function __construct(
        /**
         * @var SerializerFactory[]
         */
        private readonly array $serializers,
        /**
         * @var DeserializerFactory[]
         */
        private readonly array $deserializers,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                "inputFilename",
                InputArgument::REQUIRED,
                "Input data stored in xlsx compatible worksheet file.",
            )
            ->addArgument(
                "outputBasename",
                InputArgument::OPTIONAL,
                "Basename for output files when using file writable formats.",
                'output/compute',
            )
            ->addOption(
                "writer",
                "w",
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                "Output writers. Output data is written using every compatible writer. Supports: stdio, csv, xlsx, json.",
                [],
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // todo: ideally do something about this global state, maybe push it into serializer context
        ini_set('serialize_precision', 14);
        $outputBasename = $input->getArgument("outputBasename");
        $writers = [
            'stdio' => new PlaintextToSymfonyOutputInterfaceWriterFactory($output),
            'json' => new JsonToFileWriterFactory($outputBasename),
            'xlsx' => new SpreadsheetToXlsxFileWriterFactory($outputBasename),
            'csv' => new SpreadsheetToSingleCsvFileWriterFactory($outputBasename),
        ];
        $chosenWriters = $input->getOption("writer");
        if (count($extras = array_diff($chosenWriters, array_keys($writers)))) {
            $output->writeln(
                "There is no `--writer` for " . join(", ", $extras)
                . "; supported `--writer` options are: " . join(", ", array_keys($writers)) . "."
            );
            return Command::INVALID;
        }
        try {
            (new FirstFitPipe(
                [
                    new XlsxFileToSpreadsheetReaderFactory($input->getArgument("inputFilename"))
                ],
                array_intersect_key($writers, array_combine($chosenWriters, $chosenWriters)),
                $this->serializers,
                $this->deserializers,
            ))->run();
            $output->writeln("Computation complete");
            return Command::SUCCESS;
        } catch (PipeNotConvergedException $exception) {
            $output->writeln($exception->getMessage());
            return Command::INVALID;
        }
    }
}
