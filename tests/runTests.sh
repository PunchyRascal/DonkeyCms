#!/bin/bash

set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $DIR/../ && ./vendor/bin/tester --colors 1 -j 4 -c c:/wamp/php/php.ini $DIR

echo "
 ____  _  _  ____    ____  ____  ____  ___
(  _ \/ )( \(  _ \  / ___)(  _ \(  __)/ __)
 ) __/) __ ( ) __/  \___ \ ) __/ ) _)( (__
(__)  \_)(_/(__)    (____/(__)  (____)\___)
"

cd $DIR/../ && ./vendor/bin/phpspec run

printf "
  ___  __  ____  ____  ___  ____  ____  ____  __  __   __ _
 / __)/  \(    \(  __)/ __)(  __)(  _ \(_  _)(  )/  \ (  ( \\
( (__(  O )) D ( ) _)( (__  ) _)  ) __/  )(   )((  O )/    /
 \___)\__/(____/(____)\___)(____)(__)   (__) (__)\__/ \_)__)

"

cd $DIR/../ && ./vendor/bin/codecept run --colors
