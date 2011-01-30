# route updates

# change setter to ben
update route set setter_id = 5 where id in (355,359,360);

# add new routes
insert into route (panel_id, colour_id, grade_id, setter_id, date_set) values
	(55, 20, 4, 5, '2011-01-28'),
	(56,  3, 3, 5, '2011-01-28');

# 56 is now a top-rope
update panel set climb_type_id = 2 where id = 56;

