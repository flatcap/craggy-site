#!/bin/bash

(cd ..; php google.php)

if [ $? = 1 ]; then
	echo "Sync failed"
	exit 1
fi

CHART=$(cd ..; php chart.php | grep -o "http://[^']\+")

wget -o /dev/null -O graph_grades.png "$CHART"

if [ $? = 1 ]; then
	echo Chart failed
	exit 1
fi

echo "Got google chart"

