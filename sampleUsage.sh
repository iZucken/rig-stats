#!/bin/bash

echo "Display command help:"
./rigstats.php compute:allocation --help
echo ""

echo "Running with invalid data source:"
./rigstats.php c:a -w stdio tests/examples/rates_splits/invalid.xlsx output/invalid
echo ""

echo "Running with valid data source:"
./rigstats.php c:a -w json -w xlsx tests/examples/rates_splits/valid.xlsx
echo ""

echo "Run tests with coverage:"
XDEBUG_MODE=coverage vendor/bin/phpunit tests --coverage-text
echo ""
