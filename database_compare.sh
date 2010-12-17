#!/bin/bash

#OPTS="--no-data"
OPTS=""

mysqldump $OPTS -h127.0.0.1 -P3307 -ubackup -pphokio10 craggy | sed -e '/^-- Server version/d' \
								    -e 's/^-- Host: 127.0.0.1/-- Host: russon.org/' \
								    -e 's/\(DEFINER=\)`root`/\1`craggy`/' \
							> craggy_russon.sql

mysqldump $OPTS -h127.0.0.1 -P3306 -ucraggy            craggy | sed -e '/^-- Server version/d' \
								    -e 's/^-- Host: 127.0.0.1/-- Host: flatcap.org/' \
							> craggy_flatcap.sql


dwdiff -c -C 5 --color=red,yellow craggy_russon.sql craggy_flatcap.sql | less -E

