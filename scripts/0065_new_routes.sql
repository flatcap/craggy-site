# new routes
update route set date_end = "2011-02-24" where panel_id in (5,6) and date_end is null;

insert into route (panel_id, colour_id, grade_id, setter_id, date_set) values
	(5, 11, 5,  2, "2011-02-24"),
	(5, 10, 6,  2, "2011-02-24"),
	(5, 3,  8,  2, "2011-02-24"),
	(5, 1,  11, 2, "2011-02-24"),
	(6, 16, 4,  2, "2011-02-24"),
	(6, 7,  10, 2, "2011-02-24"),
	(6, 8,  9,  2, "2011-02-24"),
	(6, 21, 6,  2, "2011-02-24");
