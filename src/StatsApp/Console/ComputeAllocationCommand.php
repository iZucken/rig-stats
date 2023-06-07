<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Console;

use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\IO\JsonToFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\PlaintextToSymfonyOutputInterfaceWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Read\SerializedReaderFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToSingleCsvFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToXlsxFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\XlsxFileToSpreadsheetReaderFactory;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\Infrastructure\Types\TypeDescriber;
use RigStats\RigModel\Extraction\Extractions;
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
        private readonly SerializerFactory $serializers,
        private readonly DeserializerFactory $deserializers,
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
        /**
         * @var SerializedReaderFactory[] $readers
         */
        $readers = [
            new XlsxFileToSpreadsheetReaderFactory($input->getArgument("inputFilename"))
        ];
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
        /** @var array<string, SerializedWriterFactory> $writers */
        $writers = array_intersect_key($writers, array_combine($chosenWriters, $chosenWriters));
        foreach ($readers as $readerFactory) {
            if ($serialized = $readerFactory->readable()?->read()) {
                if ($loadedModel = $this->deserializers->deserializable($serialized)?->deserialize()) {
                    if ($loadedModel instanceof Extractions) {
                        $computed = $loadedModel->intoAllocationDaysOrInvalidRates();
                        $output->writeln("Computation complete into " . TypeDescriber::describe($computed));
                        foreach ($writers as $writer) {
                            $atLeastOneFormat = false;
                            foreach ($writer->formats() as $writerFormat) {
                                if ($serializable = $this->serializers->serializable($computed, $writerFormat)) {
                                    if ($writable = $writer->writable($serializable->serialize())) {
                                        $output->writeln($writable->describe());
                                        $writable->write();
                                        $atLeastOneFormat = true;
                                    }
                                }
                            }
                            if (!$atLeastOneFormat) {
                                $output->writeln(
                                    "No compatible output for " . get_class($writer)
                                    . " on " . TypeDescriber::describe($computed)
                                );
                            }
                        }
                        return Command::SUCCESS;
                    }
                    // todo: test when other data type is possible
                    $output->writeln(
                        "This program only supports " . Extractions::class
                        . ", but got " . TypeDescriber::describe($loadedModel)
                    );
                    return Command::INVALID;
                }
                $output->writeln("{$serialized->describe()} is not deserializable into any known type.");
                return Command::INVALID;
            }
        }
        $output->writeln("Failed to read the input into any known container type.");
        return Command::INVALID;
    }
}
