# add an end date to routes

# routes expire
alter table craggy_route add column date_end date default null;
alter table craggy_route add key date_end (date_end);

# climbs expire
alter table craggy_climb add column active boolean default true;
alter table craggy_climb add key active (active);

# rebuild v_route
drop view v_route;
create view v_route as
	select
		craggy_route.id as id,
		craggy_panel.name as panel,
		craggy_panel.sequence as panel_seq,
		craggy_colour.colour as colour,
		craggy_grade.grade as grade,
		craggy_grade.sequence as grade_seq,
		v_panel.climb_type as climb_type,
		trim(concat_ws(' ', craggy_setter.first_name, craggy_setter.surname)) as setter,
		craggy_route.date_set as date_set,
		craggy_route.notes as notes,
		craggy_panel.height as height
	from
		craggy_route
		left join craggy_colour on (craggy_route.colour_id = craggy_colour.id)
		left join craggy_panel on (craggy_route.panel_id = craggy_panel.id)
		left join craggy_grade on (craggy_route.grade_id = craggy_grade.id)
		left join craggy_setter on (craggy_route.setter_id = craggy_setter.id)
		left join v_panel on (craggy_panel.name = v_panel.name)
	where
		(craggy_route.date_end is null);

