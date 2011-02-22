#!/bin/bash

# -f = force
[ "x$1" = "x-f" ] && rm -f craggy_russon.sql

#OPTS="--compact --no-data --database craggy"
OPTS="--skip-extended-insert --add-drop-database --skip-dump-date --single-transaction"

DATABASE="craggy"
TABLES=(climb climb_type climber colour data difficulty grade panel rating route setter success v_route)

if [ ! -f craggy_russon.sql ]; then
	mysqldump $OPTS -h127.0.0.1 -P3307 -ubackup -pphokio10 $DATABASE ${TABLES[*]} \
		| sed	-e '/^-- Server version/d' \
			-e 's/^-- Host: 127.0.0.1/-- Host: russon.org/' \
			-e 's/\(DEFINER=\)`root`/\1`craggy`/' \
		> craggy_russon.sql
fi

mysqldump $OPTS -h127.0.0.1 -P3306 -ucraggy $DATABASE ${TABLES[*]} \
	| sed	-e '/^-- Server version/d' \
		-e 's/^-- Host: 127.0.0.1/-- Host: flatcap.org/' \
		-e 's/\(DEFINER=\)`root`/\1`craggy`/' \
	> craggy_flatcap.sql

dwdiff -c -C 3 --color=red,yellow craggy_russon.sql craggy_flatcap.sql | less -E

