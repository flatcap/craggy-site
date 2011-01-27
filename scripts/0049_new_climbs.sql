# add new climbs

insert into climb (climber_id, route_id, success_id, date_climbed) values
	(1, 346, 4, 2011-01-19),
	(1, 347, 4, 2011-01-19),
	(1, 348, 1, 2011-01-19),
	(1, 349, 4, 2011-01-19),
	(1, 350, 3, 2011-01-19),
	(1, 351, 3, 2011-01-19),
	(1, 352, 4, 2011-01-19),
	(1, 353, 4, 2011-01-19),
	(1, 354, 3, 2011-01-19),
	(1, 335, 4, 2011-01-19),
	(1, 334, 4, 2011-01-19),
	(1, 336, 2, 2011-01-19),
	(1, 340, 4, 2011-01-19),
	(1, 341, 4, 2011-01-19),
	(1, 342, 4, 2011-01-19),
	(1, 317, 4, 2011-01-19),
	(1, 318, 2, 2011-01-19),
	(1, 319, 2, 2011-01-19),
	(1, 261, 4, 2011-01-19),
	(1, 262, 4, 2011-01-19),
	(1, 263, 3, 2011-01-19),
	(1, 320, 4, 2011-01-26),
	(1, 321, 4, 2011-01-26),
	(1, 322, 3, 2011-01-26),
	(1, 314, 4, 2011-01-26),
	(1, 315, 3, 2011-01-26),
	(1, 316, 3, 2011-01-26),
	(1, 337, 4, 2011-01-26),
	(1, 338, 4, 2011-01-26),
	(1, 339, 3, 2011-01-26),
	(1, 327, 3, 2011-01-26),
	(1, 325, 3, 2011-01-26),
	(1, 333, 3, 2011-01-26),
	(1, 46,  3, 2011-01-26),
	(1, 303, 4, 2011-01-26),
	(1, 304, 2, 2011-01-26);

insert into climb_note (notes) values
	("nearly managed downclimb"),
	("wobbly");

insert into rating (climber_id, route_id, difficulty_id, nice, onsight, climb_note_id) values
	(1, 346, 2, 0, 1, null),
	(1, 347, 3, 0, 1, null),
	(1, 348, 5, 0, 0, null),
	(1, 349, 3, 0, 1, null),
	(1, 350, 3, 0, 1, null),
	(1, 351, 4, 0, 1, 61),
	(1, 352, 2, 0, 1, null),
	(1, 353, 3, 0, 1, null),
	(1, 354, 3, 0, 1, null),
	(1, 335, 2, 0, 1, null),
	(1, 334, 3, 0, 1, null),
	(1, 336, 5, 0, 0, null),
	(1, 340, 2, 0, 1, null),
	(1, 341, 3, 0, 1, null),
	(1, 342, 4, 1, 1, null),
	(1, 317, 2, 0, 1, null),
	(1, 318, 4, 0, 0, null),
	(1, 319, 5, 0, 0, null),
	(1, 337, 2, 0, 1, null),
	(1, 338, 3, 0, 1, null),
	(1, 339, 4, 1, 1, null),
	(1, 327, 2, 1, 1, null),
	(1, 303, 2, 0, 1, null),
	(1, 304, 4, 0, 0, null);

# update the duplicate routes in the ratings table
update rating set difficulty_id=4, climb_note_id=NULL, nice=0, onsight=0 where id=30;
update rating set difficulty_id=2, climb_note_id=NULL, nice=0, onsight=1 where id=254;
update rating set difficulty_id=3, climb_note_id=NULL, nice=0, onsight=1 where id=255;
update rating set difficulty_id=3, climb_note_id=60,   nice=0, onsight=1 where id=256;
update rating set difficulty_id=2, climb_note_id=NULL, nice=0, onsight=1 where id=287;
update rating set difficulty_id=4, climb_note_id=50,   nice=0, onsight=1 where id=288;
update rating set difficulty_id=5, climb_note_id=51,   nice=0, onsight=0 where id=289;
update rating set difficulty_id=2, climb_note_id=NULL, nice=0, onsight=1 where id=290;
update rating set difficulty_id=3, climb_note_id=52,   nice=0, onsight=1 where id=291;
update rating set difficulty_id=4, climb_note_id=53,   nice=0, onsight=1 where id=292;
update rating set difficulty_id=2, climb_note_id=55,   nice=1, onsight=0 where id=308;
update rating set difficulty_id=3, climb_note_id=58,   nice=1, onsight=0 where id=313;

