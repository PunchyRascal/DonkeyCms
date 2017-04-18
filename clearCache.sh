#!/bin/bash

set -e

cd ./cache/donkeyCms

for i in articleList categoriesForParent categoryTree css productDetail productExport productList
do
	if [ -d "$i" ]
	then
		echo "* changing to $i"
		cd $i
		echo "* pwd=$(pwd)"
		echo "* removing all files"
		rm -f *
		echo "* removing done"
		cd ../
	fi
done


