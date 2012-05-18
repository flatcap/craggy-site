#!/bin/bash

PATH="/bin:/usr/bin"

umask 0027

# Change to the working directory
pushd ${0%/*} > /dev/null

cleanup()
{
	rm -f {age,colour,grade}.{dat,png,svg}
}

convert()
{
	[ -n "$1" ] || exit 1

	local BASE=$(basename "$1" .svg)
	rsvg-convert --background-color white "$1" > "$BASE.png"
}


cleanup

# Generate and install the graphics
for i in age colour grade; do
	php $i.php > $i.dat
	gnuplot $i.plot
	convert $i.svg
	mv $i.png ../style
done

cleanup

popd > /dev/null
exit 0

