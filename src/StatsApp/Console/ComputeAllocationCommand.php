<?php

declare(strict_types=1);

namespace RigStats\StatsApp\Console;

use RigStats\Infrastructure\SerializationFramework\Deserialization\DeserializerFactory;
use RigStats\Infrastructure\SerializationFramework\IO\JsonToFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\PlaintextToSymfonyOutputInterfaceWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToSingleCsvFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\SpreadsheetToXlsxFileWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\Write\SerializedWriterFactory;
use RigStats\Infrastructure\SerializationFramework\IO\XlsxFileToSpreadsheetReaderFactory;
use RigStats\Infrastructure\SerializationFramework\Serialization\SerializerFactory;
use RigStats\Infrastructure\Types\TypeDescriber;
use RigStats\RigModel\Extraction\Extractions;
use RigStats\Infrastructure\SerializationFramework\Types\ClassType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ComputeAllocationCommand extends Command
{
    private OutputInterface $output;

    public function __construct(
        private readonly SerializerFactory $serializers,
        private readonly DeserializerFactory $deserializers,
    ) {
        parent::__construct();
    }

    protected static $defaultName = "compute:allocation";
    protected static $defaultDescription = "Computes layer split allocation from extraction data files.";

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
        $this->output = $output;
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
            $this->output->writeln(
                "There is no `--writer` for " . join(", ", $extras)
                . "; supported `--writer` options are: " . join(", ", array_keys($writers)) . "."
            );
            return Command::INVALID;
        }
        $writers = array_intersect_key($writers, array_combine($chosenWriters, $chosenWriters));
        foreach ($readers as $readerFactory) {
            if ($reader = $readerFactory->readable()) {
                if ($serialized = $reader->read()) {
                    $sourceType = new ClassType(Extractions::class);
                    if ($deserializer = $this->deserializers->deserializable($serialized, $sourceType)) {
                        $loadedModel = $deserializer->deserialize();
                        if ($loadedModel instanceof Extractions) {
                            $computed = $loadedModel->intoAllocationDaysOrInvalidRates();
                            $this->output->writeln("Computation complete into " . TypeDescriber::describe($computed));
                            $this->writeAll($computed, $writers);
                            return Command::SUCCESS;
                        }
                        // todo: test when other data type is possible
                        $this->output->writeln(
                            "This program only supports " . Extractions::class
                            . ", but got " . TypeDescriber::describe($loadedModel)
                        );
                        return Command::INVALID;
                    }
                    $this->output->writeln("{$serialized->describe()} is not deserializable into any known type.");
                    return Command::INVALID;
                }
            }
        }
        $this->output->writeln("Failed to read the input into any known container type.");
        return Command::INVALID;
    }

    /**
     * @param mixed $data
     * @param SerializedWriterFactory[] $writers
     * @return void
     */
    private function writeAll(
        mixed $data,
        array $writers
    ) {
        foreach ($writers as $writer) {
            $atLeastOneFormat = false;
            foreach ($writer->formats() as $writerFormat) {
                if ($serializable = $this->serializers->serializable($data, $writerFormat)) {
                    $serialized = $serializable->serialize();
                    if ($writable = $writer->writable($serialized)) {
                        $this->output->writeln($writable->describe());
                        $writable->write();
                        $atLeastOneFormat = true;
                    }
                }
            }
            if (!$atLeastOneFormat) {
                $this->output->writeln(
                    "No compatible output for " . get_class($writer)
                    . " on " . TypeDescriber::describe($data)
                );
            }
        }
    }
}
