#!/bin/bash

docker-compose run --rm php ./example-php.sh

open .coverage/index.html
