### About

Command to compute rig layer split rates from rig stat input files. Not optimised for high volume inputs.

### Setup

Setup when using docker compose assuming current working directory in package root:

1. `cp .env.example .env` and change according to your needs
2. Run `docker-compose run composer install`
3. Run samples with `./example.sh` to verify your setup works

### Input\Output

Refer to `--help` to find out how to pass input\output file names and choose write methods.
File writers save files with appropriate suffixes, so you only have to specify base names.
Results are also written to console when appropriate.
The command will try to output results in all selected formats which have corresponding implementation.

### Validation

Data structure is assumed to be correct by the contract, invalid inputs are likely to fail.
Modelled errors are output if they have corresponding implementation.

### Computations

Supported computations are baked into the command.

#### "Extraction rates\splits series"

For allocation compute source files, layer split data represents rate percentage per layer and must add up to 100% with
at least 1e-5 accuracy.

Considered inconsequential or up to the user (a lot but the again this is just a test):

- time slice sequencing
- whether time slice refers to a day
- well layers stability between days
- duplicate time slices or wells will be merged
- whether layer data is consistent between time slices

##### "Extraction rates\splits series" into "Split allocation series"

For allocation computation layer rates are computed using `layer_rate = well_rate * layer_split / 100`.

Sample xlsx output:

| dt                  | well_id | layer_id | oil_rate         | gas_rate        | water_rate      |
|---------------------|---------|----------|------------------|-----------------|-----------------|
| 2022-12-01 00:00:00 | 0       | 4        | 15.295271785934  | 80.932299594197 | 47.767335473972 |
| 2022-12-01 00:00:00 | 1       | 0        | 0.94284054455412 | 50.215626301672 | 31.577161553345 |
| 2022-12-01 00:00:00 | 1       | 1        | 1.6315786095135  | 8.3386609029143 | 10.661237418533 |
| 2022-12-01 00:00:00 | 1       | 3        | 0.79066889201148 | 4.5989026702649 | 24.787653168743 |

Sample json output:

```json
{
  "allocation": {
    "data": [
      {
        "wellId": 0,
        "dt": "2022-12-01T00:00:00",
        "layerId": 4,
        "oilRate": 15.29527178593368,
        "gasRate": 80.93229959419733,
        "waterRate": 47.76733547397225
      }
    ]
  }
}
```

##### "Extraction rates\splits series" into "Well extraction error series"

Output if extraction layer split data does not add up to 100% with required accuracy.

Sample for console:

```text
Writing plain text (892 bytes) to generic output
At 2022-12-01 #92 for oil: Split data sum error by -41.61%
At 2022-12-01 #154 for gas: Split data sum error by -15.15%
At 2022-12-01 #188 for oil: Split data sum error by 24.91%
At 2022-12-02 #132 for oil: Split data sum error by 31.28%
At 2022-12-03 #132 for gas: Split data sum error by 43.59%
```

Sample for xlsx:

| dt         | well_id | fluid | error                           |
|------------|---------|-------|---------------------------------|
| 2022-12-01 | 1       | oil   | Split data sum error by -41.61% |

