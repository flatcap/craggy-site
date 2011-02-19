# add new climbs
insert into climb (climber_id,route_id,success_id,date_climbed) values
	(1, 375, 4, "2011-02-18"),
	(1, 376, 4, "2011-02-18"),
	(1, 377, 2, "2011-02-18"),
	(1, 372, 4, "2011-02-18"),
	(1, 373, 4, "2011-02-18"),
	(1, 374, 1, "2011-02-18"),
	(1, 380, 4, "2011-02-18"),
	(1, 381, 4, "2011-02-18"),
	(1, 382, 3, "2011-02-18"),
	(1, 384, 4, "2011-02-18"),
	(1, 383, 4, "2011-02-18"),
	(1, 385, 1, "2011-02-18"),
	(1, 387, 4, "2011-02-18"),
	(1, 386, 4, "2011-02-18"),
	(1, 388, 2, "2011-02-18"),
	(1, 401, 4, "2011-02-18"),
	(1, 412, 4, "2011-02-18"),
	(1, 413, 3, "2011-02-18"),
	(1, 408, 4, "2011-02-18"),
	(1, 409, 4, "2011-02-18"),
	(1, 406, 3, "2011-02-18"),
	(1, 405, 3, "2011-02-18");

# add ratings
insert into rating (climber_id,route_id,difficulty_id) values
	(1, 372, 3),
	(1, 373, 3),
	(1, 374, 4),
	(1, 377, 3),
	(1, 401, 2),
	(1, 405, 3),
	(1, 406, 4),
	(1, 408, 2),
	(1, 409, 3),
	(1, 412, 4),
	(1, 413, 3);

# one nice route
update rating set nice = 1 where route_id = 380;

# create notes
insert into climb_note (notes) values
	("leapy"),				# 69
	("many falls"),				# 70
	("no idea how to get started"),		# 71
	("now I know"),				# 72
	("reachy, strange route");		# 73

# add to rating
update rating set climb_note_id = 70 where route_id = 377;
update rating set climb_note_id = 69 where route_id = 372;
update rating set climb_note_id = 71 where route_id = 374;
update rating set climb_note_id = 45 where route_id = 382;
update rating set climb_note_id =  1 where route_id = 384;
update rating set climb_note_id = 72 where route_id = 385;
update rating set climb_note_id = 73 where route_id = 412;

# updated note
update climb_note set notes = "2 falls, 6b?" where id = 64;

# 43 blue remove climb note
update rating set climb_note_id = null where id = 361;

# remove 28 blue (br)
delete from route where id = 411;

# tidy notes
delete from climb_note where id in (22,28,41,42,43,63);

