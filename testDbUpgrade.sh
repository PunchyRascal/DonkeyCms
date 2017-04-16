#!/bin/bash

set -e

php db_upgrade/run.php PRE || exit $?

php db_upgrade/run.php POST || exit $?
