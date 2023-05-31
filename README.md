### About

Command to compute rig layer split rates from rig stat input files. Not optimised for high volume inputs.

### Setup

Setup when using docker compose assuming current working directory in package root:
1. `cp .env.example .env` and change according to your needs
2. Run `docker-compose run composer install`
3. Run samples with `docker-compose run php ./sampleUsage.sh` to verify your setup works

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

For allocation compute source files, layer split data represents rate percentage per layer and must add up to 100% with at least 1e-5 accuracy.

##### "Extraction rates\splits series" into "Split allocation series"

For allocation computation layer rates are computed using `layer_rate = well_rate * layer_split / 100`.

Sample xlsx output:

![computationXlsxExample.png](docs/computationXlsxExample.png)

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

