#!/bin/bash

echo "Display command help:"
./cli compute:allocation --help
echo ""

echo "Running with invalid data source:"
./cli c:a -w stdio tests/examples/rates_splits/invalid.xlsx output/invalid
echo ""

echo "Running with valid data source:"
./cli c:a -w json -w xlsx tests/examples/rates_splits/valid.xlsx
echo ""

echo "Run strict psalm:"
vendor/bin/psalm
echo ""

echo "Run tests with coverage:"
XDEBUG_MODE=coverage vendor/bin/phpunit tests --coverage-text --coverage-html .coverage --path-coverage
echo ""
