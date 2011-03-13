#!/bin/bash

oocalc guildford_6a.ods &

TMP="/tmp/info.$RANDOM"
wget -O $TMP "http://craggy.russon.org/6a.php?format=csv"
COUNT=$(wc -l $TMP | cut -d\  -f 1)
COUNT=$((COUNT-1))

echo "$COUNT Routes"

cat $TMP | xclip
rm -f $TMP

