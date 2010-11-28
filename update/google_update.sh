#!/bin/bash

PATH="/bin:/usr/bin"

# --------------------------------------------------------
# Start in the right place

WORK_DIR="$HOME/update"
cd "$WORK_DIR"
if [ $? = 1 ]; then
	echo "Work dir doesn't exist: $WORK_DIR"
	exit 1
fi

# --------------------------------------------------------
# Clean up before we start

rm -f *.svg
rm -f *.png
rm -f *.dat

# --------------------------------------------------------
# Update our database from a google doc.

WORK_DIR="$HOME/www/admin"

pushd "$WORK_DIR" > /dev/null
if [ $? = 1 ]; then
	echo "Work dir doesn't exist: $WORK_DIR"
	exit 1
fi

php google.php > /dev/null

[ $? = 1 ] && echo "Google update failed"
popd > /dev/null

# --------------------------------------------------------
# Generate and install the graphics

convert()
{
	[ -n "$1" ] || exit 1

	local BASE=$(basename "$1" .svg)
	rsvg-convert --background-color white "$1" > "$BASE.png"
}


WORK_DIR="$HOME/www/img"

for i in age colour grade; do
	php $i.php > $i.dat
	gnuplot $i.plot
	convert $i.svg
	install -m 644 $i.png $WORK_DIR
done

# --------------------------------------------------------
# Clean up again

rm -f *.svg
rm -f *.png
rm -f *.dat

exit 0

