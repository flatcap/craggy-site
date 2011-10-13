#!/bin/bash

PATH="/bin:/usr/bin"

umask 0002

# Change to the working directory
pushd ${0%/*} > /dev/null

php checklist.php > ../guildford.pdf

popd > /dev/null
exit 0

