#!/bin/bash

if [ $# -ne 1 ]
then
	echo "Please, specify destination dir"
	exit 1
fi

dest=$1

cd ..
zip $dest/os3-protector.zip \
	wp-protector/os3-protector.php \
	wp-protector/README.md \
	wp-protector/CHANGELOG.md \
	wp-protector/readme.txt
