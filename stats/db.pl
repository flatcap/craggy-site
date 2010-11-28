#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Data::Dumper;

use Time::HiRes qw/gettimeofday/;

my $database='craggy';
my $host='127.0.0.1';
my $port=3307;
my $username='craggy';
my $password='Vailae7j';

my $db = DBI->connect("DBI:mysql:database=$database;host=$host;port=$port", $username, $password) || die "Could not connect to database: $DBI::errstr";

#printf ("connected!\n");

my $sth;
my $result;
my @array;

#$sth = $db->prepare('SELECT id,colour,abbr FROM colour');
#$sth->execute();
#printf ("Colours:\n");
#while ($result = $sth->fetchrow_hashref()) {
#	printf ("\t%3d %-13s %s\n", $result->{id}, $result->{colour}, $result->{abbr});
#}
#printf ("\n");
#$sth->finish();

#$sth = $db->prepare('SELECT id,colour,abbr FROM colour');
#$sth->execute();
#printf ("Colours\n");
#while (@array = $sth->fetchrow_array()) {
#	printf ("\t%3d %-13s %s\n", $array[0], $array[1], $array[2]);
#}
#printf ("\n");
#$sth->finish();

#$db->do('CREATE TABLE exmpl_tbl (id INT, val VARCHAR(100))');
#$db->do('INSERT INTO exmpl_tbl VALUES(1, ?)', undef, 'Hello');
#$db->do('INSERT INTO exmpl_tbl VALUES(2, ?)', undef, 'World');
#my $c = $db->do('DELETE FROM exmpl_tbl WHERE id=1');
#print "Deleted $c rows\n";

#$sth = $db->prepare('SELECT id,colour,abbr FROM colour');
#my ($s1, $m1) = gettimeofday;
#$sth->execute();
#my ($s2, $m2) = gettimeofday;
#$sth->finish();
#my $time = (($s2-$s1) * 1000000 + ($m2-$m1)) / 1000000;
#printf ("%fs\n", $time);
#printf ("%f\n", 1/$time);

$db->disconnect();

#print Dumper @array;
