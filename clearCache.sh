#!/bin/bash

set -e

cd /container/home/www/zbk/cache/frbs

if [ "$(pwd)" != "/container/home/www/zbk/cache/frbs" ]; then
	echo "ERROR: not in the correct dir";
else
	echo "OK: correct dir $(pwd)"
fi

for i in articleList categoriesForParent categoryTree css productDetail productExport productList
do
	echo "* changing to $i"
	cd $i
	echo "* pwd=$(pwd)"
	echo "* removing all files"
	rm -f *
	echo "* removing done"
	cd ../
done


