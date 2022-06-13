#!/bin/bash

if [ $# -ne 1 ]
then
	echo "Please, specify destination dir"
	exit 1
fi

dest=$1

cd ..
zip $dest/os3-protector.zip \
	os3-protector/os3-protector.php \
	os3-protector/README.md \
	os3-protector/CHANGELOG.md \
	os3-protector/readme.txt
