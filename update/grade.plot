#!/usr/bin/gnuplot

#set terminal svg size 800,400 dashed
set terminal svg size 800,400
set output "grade.svg"
set title "Craggy Grades" font ",16"
set xlabel "Grade"
set ylabel "Count"
set xtics nomirror
set ytics nomirror
set mytics 2
set border 3

set style data lines
set yrange [0:55]

set style line 1 lt 1 lw 3 lc rgb "#6688ff"
set style line 2 lt 1 lw 2 lc rgb "#ff4444"
set style line 3 lt 1 lw 1 lc rgb "#dddddd"

set grid ytics mytics xtics ls 3

plot	"grade.dat" using 2:xtic(1) ls 1 title "Climbs", \
	"grade.dat" using 4:xtic(1) ls 2 title "Leads"

#set style data histogram
#set boxwidth 0.75
#set style fill solid
#set style histogram rowstacked
#set xrange [0:20]
#
#plot	"grade.dat" using 3:xtic(1) ls 1 title "Climbs", \
#	"grade.dat" using 4:xtic(1) ls 2 title "Leads"
