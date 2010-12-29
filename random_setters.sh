#!/bin/bash

BOYS="$HOME/git/tips/boys.txt"
GIRLS="$HOME/git/tips/girls.txt"
SURNAMES="$HOME/git/tips/surnames.txt"

# clear tables
echo
echo "truncate craggy_climb;"
echo "truncate craggy_climber;"
echo "truncate craggy_route;"
echo "truncate craggy_setter;"
echo

# 10 random girl setters
echo "insert into craggy_setter (initials, first_name, surname) values "
paste	<(sort --random-sort $GIRLS)											\
	<(sort --random-sort $SURNAMES) | head -n 10 |								\
	awk '{ printf "\t(\"%s%s\", \"%s\", \"%s\"),\n", tolower(substr($1,1,1)), tolower(substr($2,1,1)), $1, $2 }' |	\
	sed '$s/,$/;\n/'

# 100 random boy climbers
echo "insert into craggy_climber (first_name, surname) values"
paste	<(sort --random-sort $BOYS)											\
	<(sort --random-sort $SURNAMES) | head -n 100 |								\
	awk '{ printf "\t(\"%s\", \"%s\"),\n", $1, $2 }' |								\
	sed '$s/,$/;\n/'

# 200 routes
#  21 colours
#  86 panels
#  20 grades
#  10 setters
# 200 max days old
echo "insert into craggy_route (panel_id, colour_id, grade_id, setter_id, date_set) values"
for ((i = 0; i < 200; i++)); do
	PANEL=$((RANDOM%86+1))
	COLOUR=$((RANDOM%21+1))
	GRADE=$((RANDOM%20+1))
	SETTER=$((RANDOM%10+1))

	YEAR=2010
	MONTH=$((RANDOM%11+1))
	DAY=$((RANDOM%28+1))
	DATE="$YEAR-$MONTH-$DAY"

	#DAYS_AGO=$((RANDOM%200))
	#DATE=$(date -d "$DAYS_AGO days ago" "+%Y-%m-%d")

	echo -e "\t($PANEL, $COLOUR, $GRADE, $SETTER, '$DATE'),"
done | sed '$s/,$/;\n/'

# 5000 climbs
# 100 climber_id
# 200 route_id
# 200 days ago date_climbed
# 4 success_id
echo "insert into craggy_climb (climber_id, route_id, success_id, date_climbed) values"
for ((i = 0; i < 5000; i++)); do
	CLIMBER=$((RANDOM%100+1))
	ROUTE=$((RANDOM%200+1))
	SUCCESS=$((RANDOM%4+1))

	YEAR=2010
	MONTH=$((RANDOM%11+1))
	DAY=$((RANDOM%28+1))
	DATE="$YEAR-$MONTH-$DAY"

	#DAYS_AGO=$((RANDOM%200))
	#DATE=$(date -d "$DAYS_AGO days ago" "+%Y-%m-%d")

	echo -e "\t($CLIMBER, $ROUTE, $SUCCESS, '$DATE'),"
done | sed '$s/,$/;\n/'

