#!/bin/bash

WORK_DIR="/home/flatcap/work/craggy/www"
FILES="*.php *.pdf *.css img/*.png"
DIRS="img"

[ -d $WORK_DIR ] || exit 1

pushd $WORK_DIR >& /dev/null

chmod 755 $DIRS

chmod 644 $FILES
chcon -t public_content_t $FILES $DIRS

chmod 750 .priv

popd >& /dev/null

