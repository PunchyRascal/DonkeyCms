#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

tester -j 1 --coverage-src $DIR/../lib --coverage $DIR/codeCoverage.html --colors 1 -c c:/wamp/php/php.ini
