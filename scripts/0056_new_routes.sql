# add new routes
update route set date_end = "2011-02-11" where id in
	(1,2,3,4,5,6,7,8,9,10,11,128,129,131,132,133,135,136,137,138,139,140,141,142,143,145,146,147,148,149,150,151,152,153,365);

insert into route (panel_id, colour_id, grade_id, setter_id, date_set) values
	( 1, 21, 6,  2, "2011-02-09"),
	( 1, 20, 11, 2, "2011-02-09"),
	( 2, 16, 4,  2, "2011-02-09"),
	( 2, 1,  8,  2, "2011-02-09"),
	( 2, 7,  9,  2, "2011-02-09"),
	( 3, 10, 6,  2, "2011-02-09"),
	( 3, 15, 6,  2, "2011-02-09"),
	( 3, 3,  10, 2, "2011-02-09"),
	( 4, 7,  4,  2, "2011-02-09"),
	( 4, 21, 6,  2, "2011-02-09"),
	( 4, 14, 9,  2, "2011-02-09"),
	(42, 7,  3,  2, "2011-02-11"),
	(42, 21, 9,  2, "2011-02-11"),
	(43, 15, 5,  2, "2011-02-11"),
	(43, 1,  6,  2, "2011-02-11"),
	(43, 3,  9,  2, "2011-02-11"),
	(44, 7,  3,  2, "2011-02-11"),
	(44, 16, 6,  2, "2011-02-11"),
	(44, 21, 10, 2, "2011-02-11"),
	(45, 3,  3,  2, "2011-02-11"),
	(45, 14, 5,  2, "2011-02-11"),
	(45, 8,  8,  2, "2011-02-11"),
	(46, 11, 5,  2, "2011-02-10"),
	(46, 1,  6,  2, "2011-02-10"),
	(46, 7,  8,  2, "2011-02-10"),
	(47, 15, 3,  2, "2011-02-10"),
	(47, 3,  6,  2, "2011-02-10"),
	(47, 21, 7,  2, "2011-02-10"),
	(48, 16, 5,  2, "2011-02-10"),
	(48, 7,  6,  2, "2011-02-10"),
	(48, 18, 7,  2, "2011-02-10"),
	(49, 3,  3,  2, "2011-02-10"),
	(49, 21, 5,  2, "2011-02-10"),
	(49, 14, 6,  2, "2011-02-10"),
	(28, 3,  2,  6, "2011-02-11");
