# more new climbs

insert into rating (climber_id,route_id,difficulty_id,nice) values
	(1, 376, 2, 0),
	(1, 375, 2, 0),
	(1, 357, 4, 0),
	(1, 380, 2, 0),
	(1, 381, 2, 0),
	(1, 382, 2, 1),
	(1, 383, 3, 0),
	(1, 384, 3, 0),
	(1, 385, 4, 0),
	(1, 386, 2, 0),
	(1, 387, 3, 0),
	(1, 388, 5, 0),
	(1, 389, 3, 0),
	(1, 390, 3, 0),
	(1, 391, 3, 0),
	(1, 392, 2, 0),
	(1, 393, 4, 0),
	(1, 394, 3, 0),
	(1, 395, 3, 0),
	(1, 396, 4, 0),
	(1, 397, 3, 0),
	(1, 398, 2, 0),
	(1, 399, 3, 0),
	(1, 400, 4, 0);

insert into climb (climber_id,route_id,success_id,date_climbed) values
	(1, 376, 4, "2011-02-16"),
	(1, 375, 4, "2011-02-16"),
	(1, 357, 1, "2011-02-16"),
	(1, 360, 3, "2011-02-16"),
	(1, 380, 3, "2011-02-16"),
	(1, 381, 3, "2011-02-16"),
	(1, 382, 2, "2011-02-16"),
	(1, 383, 3, "2011-02-16"),
	(1, 384, 2, "2011-02-16"),
	(1, 385, 1, "2011-02-16"),
	(1, 386, 4, "2011-02-16"),
	(1, 387, 3, "2011-02-16"),
	(1, 388, 2, "2011-02-16"),
	(1, 389, 3, "2011-02-16"),
	(1, 390, 2, "2011-02-16"),
	(1, 391, 2, "2011-02-16"),
	(1, 392, 4, "2011-02-16"),
	(1, 393, 3, "2011-02-16"),
	(1, 394, 2, "2011-02-16"),
	(1, 395, 3, "2011-02-16"),
	(1, 396, 2, "2011-02-16"),
	(1, 397, 3, "2011-02-16"),
	(1, 398, 4, "2011-02-16"),
	(1, 399, 4, "2011-02-16"),
	(1, 400, 4, "2011-02-16");

# set rating
update rating set climb_note_id = 18 where route_id = 196;
update rating set climb_note_id =  1 where route_id = 344;
update rating set climb_note_id =  1 where route_id = 357;
update rating set climb_note_id = 45 where route_id = 382;
update rating set climb_note_id =  6 where route_id = 385;
update rating set climb_note_id =  1 where route_id = 389;
update rating set climb_note_id = 45 where route_id = 391;
update rating set climb_note_id =  1 where route_id = 393;
update rating set climb_note_id = 45 where route_id = 394;
update rating set climb_note_id =  1 where route_id = 400;

# create notes
insert into climb_note (notes) values
	("easy for a 6c"),
	("reachy, 1 fall, careless"),
	("many falls, 6b?"),
	("very reachy, 2 falls"),
	("1 fall, 6a?");

# add to rating
update rating set climb_note_id = 62 where route_id = 345;
update rating set climb_note_id = 63 where route_id = 384;
update rating set climb_note_id = 64 where route_id = 388;
update rating set climb_note_id = 65 where route_id = 390;
update rating set climb_note_id = 66 where route_id = 396;

