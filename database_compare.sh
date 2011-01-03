#!/bin/bash

# -f = force
[ "x$1" = "x-f" ] && rm -f craggy_russon.sql

#OPTS="--compact --no-data --database craggy"
OPTS="--skip-extended-insert --add-drop-database --skip-dump-date --single-transaction"

DATABASE="craggy"
TABLES=(craggy_climb craggy_climb_note craggy_climb_type \
	craggy_climber craggy_colour craggy_data \
	craggy_difficulty craggy_grade craggy_panel \
	craggy_rating craggy_route craggy_route_note \
	craggy_setter craggy_success v_panel v_route)

if [ ! -f craggy_russon.sql ]; then
	mysqldump $OPTS -h127.0.0.1 -P3307 -ubackup -pphokio10 $DATABASE ${TABLES[*]} \
		| sed	-e '/^-- Server version/d' \
			-e 's/^-- Host: 127.0.0.1/-- Host: russon.org/' \
			-e '/DEFINER=/d' \
		> craggy_russon.sql
fi

mysqldump $OPTS -h127.0.0.1 -P3306 -ucraggy $DATABASE ${TABLES[*]} \
	| sed	-e '/^-- Server version/d' \
		-e 's/^-- Host: 127.0.0.1/-- Host: flatcap.org/' \
		-e '/DEFINER=/d' \
	> craggy_flatcap.sql

dwdiff -c -C 3 --color=red,yellow craggy_russon.sql craggy_flatcap.sql | less -E

