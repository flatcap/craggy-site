#!/usr/bin/gnuplot

#set terminal svg size 800,400 dashed
set terminal svg size 800,400
set output "colour.svg"
set title "Craggy Colours" font ",16"
unset xlabel
unset ylabel
set xtics nomirror rotate by -45
set xtics out offset -1.5,0
set ytics nomirror
set border 3

set style data histogram
set boxwidth 0.75
set style fill solid
set style histogram rowstacked

set style line 1 lt 1 lw 3 lc rgb "#6688ff"
set style line 2 lt 1 lw 2 lc rgb "#ff4444"
set style line 3 lt 1 lw 1 lc rgb "#dddddd"

set grid ytics xtics ls 3

plot	"colour.dat" using 2:xtic(1) ls 2 notitle

