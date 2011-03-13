#!/bin/bash

# Open template
oowriter guildford.ott &

# Gather statistics

INFO="/tmp/info.$RANDOM"
ssh craggy@colo "(cd ~/www; php index.php)" > $INFO

AUTO=$(grep -o "Auto-belay: [[:digit:]]\+" $INFO)
HEIGHT=$(grep -o "Total Route Height: [[:digit:]]\+m" $INFO)
LAST=$(grep -o "Last route setting done on: [[:digit:]]\+ [[:alpha:]]\+ [[:digit:]]\+" $INFO)
LEAD=$(grep -o "Lead: [[:digit:]]\+" $INFO)
PANEL=$(grep -o "[[:digit:]]\+ Panels" $INFO)
ROUTE=$(grep -o "[[:digit:]]\+ Graded Routes" $INFO)
TOPR=$(grep -o "Top Rope: [[:digit:]]\+" $INFO)
rm -f $INFO

AUTO=${AUTO##* }
HEIGHT=${HEIGHT##* }
LAST=${LAST##*: }
LEAD=${LEAD##* }
PANEL=${PANEL% *}
ROUTE=${ROUTE%% *}
TOPR=${TOPR##* }

(
echo "    $PANEL Panels ($ROUTE Routes)"
echo "    Auto-belay: $AUTO"
echo "    Top Rope: $TOPR"
echo "    Lead: $LEAD"
echo "    Total Height: $HEIGHT"
echo "    Last Route Set: $LAST"
) | xclip

sleep 1

# Get route list
ssh craggy@colo "(cd ~/www; php checklist.php -f tabs)" | sed -e 's/+/Z/g' -e 's/\t/\t\t/' | xclip

