#!/bin/bash

# -q = quick
[ "x$1" = "x-q" ] || rm -f craggy_russon_compact.sql

OPTS="--extended-insert --add-drop-database --database craggy --skip-dump-date --single-transaction"

if [ ! -f craggy_russon_compact.sql ]; then
	mysqldump $OPTS -h127.0.0.1 -P3307 -ubackup -pphokio10 |	\
		sed -e '/^-- Server version/d'				\
		    -e 's/^-- Host: 127.0.0.1/-- Host: russon.org/'	\
		> craggy_russon_compact.sql
fi

mysql < craggy_russon_compact.sql

