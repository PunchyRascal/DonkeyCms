#!/bin/bash

set -e

if [ -f ./RELEASE-CHECK.lock ]
then
	echo "Previous release has not been finished"
	exit 1;
fi

touch ./RELEASE-CHECK.lock

cd ~/www/zbk

echo "git: fetching..."

git fetch || exit $?
git checkout origin/master -- db_upgrade/pre.php || exit $?
git checkout origin/master -- db_upgrade/post.php || exit $?

echo "git: checked out new PRE + POST db upgrade scripts"

php db_upgrade/run.php PRE || exit $?

echo "git: merging..."

git merge || exit $?

../composer install --no-dev || exit $?

php db_upgrade/run.php POST || exit $?

./clearCache.sh

git tag -f prod

git push -f origin prod

read -p "Press enter to continue"
