#!/bin/bash

OPTS="--extended-insert --add-drop-database --database craggy --skip-dump-date --single-transaction"

mysqldump $OPTS -h127.0.0.1 -P3307 -ubackup -pphokio10 |	\
	sed -e '/^-- Server version/d'				\
	    -e 's/^-- Host: 127.0.0.1/-- Host: russon.org/'	\
	> craggy_russon.sql

mysql < craggy_russon.sql

