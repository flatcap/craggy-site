# remove craggy_ prefix

# rename all the real tables
rename table craggy_climb      to climb;
rename table craggy_climb_note to climb_note;
rename table craggy_climb_type to climb_type;
rename table craggy_climber    to climber;
rename table craggy_colour     to colour;
rename table craggy_data       to data;
rename table craggy_difficulty to difficulty;
rename table craggy_grade      to grade;
rename table craggy_panel      to panel;
rename table craggy_rating     to rating;
rename table craggy_route      to route;
rename table craggy_route_note to route_note;
rename table craggy_setter     to setter;
rename table craggy_success    to success;

# rebuild v_panel
drop view v_panel;
create view v_panel as
	select
		panel.name as name,
		climb_type.climb_type as climb_type
	from
		panel
		left join climb_type on (panel.climb_type_id = climb_type.id);

# rebuild v_route
drop view v_route;
create view v_route as
	select
		route.id as id,
		panel.name as panel,
		panel.sequence as panel_seq,
		colour.colour as colour,
		grade.grade as grade,
		grade.sequence as grade_seq,
		v_panel.climb_type as climb_type,
		trim(concat_ws(' ', setter.first_name, setter.surname)) as setter,
		route.date_set as date_set,
		route_note.notes as notes,
		panel.height as height
	from
		route
		left join colour on (route.colour_id = colour.id)
		left join panel on (route.panel_id = panel.id)
		left join grade on (route.grade_id = grade.id)
		left join setter on (route.setter_id = setter.id)
		left join route_note on (route.note_id = route_note.id)
		left join v_panel on (panel.name = v_panel.name)
	where
		(route.date_end is null);

