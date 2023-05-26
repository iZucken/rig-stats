#!/bin/bash

echo "Running with invalid data source:"
./rigstats.php c:a examples/rates_splits/invalid.xlsx
echo ""

echo "Running with valid data source:"
./rigstats.php c:a examples/rates_splits/valid.xlsx
echo ""
