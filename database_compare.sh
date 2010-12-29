#!/bin/bash

#OPTS="--compact --no-data"
OPTS="--skip-extended-insert --add-drop-database --database craggy --skip-dump-date --single-transaction"


if [ ! -f craggy_russon.sql ]; then
	mysqldump $OPTS -h127.0.0.1 -P3307 -ubackup -pphokio10 | sed -e '/^-- Server version/d' \
								     -e 's/^-- Host: 127.0.0.1/-- Host: russon.org/' \
								     -e 's/\(DEFINER=`\)root/\1craggy/' \
								> craggy_russon.sql
fi

mysqldump $OPTS -h127.0.0.1 -P3306 -ucraggy | sed -e '/^-- Server version/d' \
						  -e 's/^-- Host: 127.0.0.1/-- Host: flatcap.org/' \
						  -e 's/\(DEFINER=`\)root/\1craggy/' \
							> craggy_flatcap.sql


dwdiff -c -C 3 --color=red,yellow craggy_russon.sql craggy_flatcap.sql | less -E

