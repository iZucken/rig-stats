#!/bin/bash

docker-compose run --rm php ./sampleUsage.sh

open .coverage/index.html
