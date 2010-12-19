#!/bin/bash

if [ ! -f craggy_russon.sql ]; then
	mysqldump $OPTS -h127.0.0.1 -P3307 -ubackup -pphokio10 craggy | sed -e '/^-- Server version/d' \
									    -e 's/^-- Host: 127.0.0.1/-- Host: russon.org/' \
								> craggy_russon.sql
fi

(
	echo "drop database craggy;"
	echo "create database craggy;"
	echo "use craggy;"
	cat craggy_russon.sql
) | mysql -s

