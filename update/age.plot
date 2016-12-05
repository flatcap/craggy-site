#!/usr/bin/gnuplot

#set terminal svg size 800,400 dashed
set terminal svg size 800,400
set output "age.svg"
set title "Craggy Ages" font ",16"
set xlabel "Age (Months)"
set ylabel "Number of Climbs"
set xtics nomirror
set ytics nomirror
set border 3
set yrange [0:100]
#set clip one

set style data lines

set style line 1 lt 1 lw 3 lc rgb "#6688ff"
set style line 2 lt 1 lw 2 lc rgb "#ff4444"
set style line 3 lt 1 lw 1 lc rgb "#dddddd"

set grid ytics xtics ls 3

plot	"age.dat" using 2:xtic(1) ls 1 notitle

