# invalidate all old routes on panels 5,6
update route set date_end = '2011-01-20' where id in (12,13,14,15,16,17,18,19,20);

# add new routes
insert into route (panel_id, colour_id, grade_id, setter_id, date_set) values
	(5, 11, 3, 9, '2011-01-20'),
	(5, 17, 7, 9, '2011-01-20'),
	(5, 7, 8, 9, '2011-01-20'),
	(5, 3, 9, 9, '2011-01-20'),
	(5, 1, 10, 9, '2011-01-20'),
	(6, 21, 4, 9, '2011-01-20'),
	(6, 8, 5, 9, '2011-01-20'),
	(6, 12, 8, 9, '2011-01-20'),
	(6, 16, 8, 9, '2011-01-20');
