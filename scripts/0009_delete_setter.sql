# if i delete setter id 5...

# how many routes will be deleted?
select count(id) from craggy_route where setter_id = 5;

# how many climbs will be deleted?
select count(craggy_setter.id)
	from craggy_climb, craggy_route, craggy_setter
	where
		craggy_climb.route_id = craggy_route.id and
		craggy_route.setter_id = craggy_setter.id and
		craggy_setter.id = 5;

-- ----------------------------------------------------

# delete the climbs associated with the setter
delete craggy_climb
	from craggy_climb, craggy_route, craggy_setter
	where
		craggy_climb.route_id = craggy_route.id and
		craggy_route.setter_id = craggy_setter.id and
		craggy_setter.id = 5;

# delete the routes associated with the setter
delete
	from craggy_route
	where
		setter_id = 5;

# delete the setter
delete
	from craggy_setter
	where
		id = 5;

# list how many rows have been inserted, updated or deleted
select row_count() as count

-- ---------------------------------------------------

# how many setters, routes, climbs?
select count(id) from craggy_setter; select count(id) from craggy_route; select count(id) from craggy_climb;

