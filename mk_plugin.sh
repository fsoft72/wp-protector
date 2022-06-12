#!/bin/bash

if [ $# -ne 1 ]
then
	echo "Please, specify destination dir"
	exit 1
fi

dest=$1

cd ..
zip $dest/wp-protector.zip wp-protector/wp-protector.php wp-protector/README.md wp-protector/CHANGELOG.md
