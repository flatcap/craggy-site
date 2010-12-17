#!/bin/bash

BOYS="$HOME/git/tips/boys.txt"
GIRLS="$HOME/git/tips/girls.txt"

# clear tables
echo "truncate craggy_climb;"
echo "truncate craggy_climber;"
echo "truncate craggy_route;"
echo "truncate craggy_setter;"

# 10 random girl setters
sort --random-sort $GIRLS | sed 's/.*/insert into craggy_setter (initials,name) values ("\0","\0");/;10q'

# 100 random boy climbers
sort --random-sort $BOYS | sed 's/.*/insert into craggy_climber (name) values ("\0");/;100q'

# 200 routes
#  21 colours
#  86 panels
#  21 grades
#  10 setters
# 200 max days old
for ((i = 0; i < 200; i++)); do
	PANEL=$((RANDOM%86+1))
	COLOUR=$((RANDOM%21+1))
	GRADE=$((RANDOM%21+1))
	SETTER=$((RANDOM%10+1))
	DAYS_AGO=$((RANDOM%200))
	DATE=$(date -d "$DAYS_AGO days ago" "+%Y-%m-%d")

	echo "insert into craggy_route (panel_id, colour_id, grade_id, setter_id, date_set) values ($PANEL,$COLOUR,$GRADE,$SETTER,'$DATE');"
done

# 5000 climbs
# 100 climber_id
# 200 route_id
# 200 days ago date_climbed
# 4 success_id
for ((i = 0; i < 5000; i++)); do
	CLIMBER=$((RANDOM%100+1))
	ROUTE=$((RANDOM%200+1))
	SUCCESS=$((RANDOM%4+1))
	DAYS_AGO=$((RANDOM%200))
	DATE=$(date -d "$DAYS_AGO days ago" "+%Y-%m-%d")

	echo "insert into craggy_climb (climber_id, route_id, success_id, date_climbed) values ($CLIMBER,$ROUTE,$SUCCESS,'$DATE');"
done

