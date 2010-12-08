drop view if exists v_panel;
drop view if exists v_route;

rename table climb_type to craggy_climb_type;
rename table climber to craggy_climber;
rename table climbs to craggy_climb;
rename table colour to craggy_colour;
rename table data to craggy_data;
rename table difficulty to craggy_difficulty;
rename table grade to craggy_grade;
rename table panel to craggy_panel;
rename table route to craggy_route;
rename table setter to craggy_setter;
rename table success to craggy_success;

create view v_panel as
	select
		craggy_panel.number as number,
		craggy_climb_type.type as climb_type
			from craggy_panel
				left join craggy_climb_type on (craggy_panel.type = craggy_climb_type.id);

create view v_route as
	select
		craggy_route.id as id,
		craggy_panel.number as panel,
		craggy_colour.colour as colour,
		craggy_grade.grade as grade,
		craggy_grade.order as grade_num,
		v_panel.climb_type as climb_type,
		craggy_setter.name as setter,
		craggy_route.date_set as date_set,
		craggy_route.notes as notes,
		craggy_panel.height as height
			from craggy_route
				left join craggy_colour on (craggy_route.colour = craggy_colour.id)
				left join craggy_panel on (craggy_route.panel = craggy_panel.id)
				left join craggy_grade on (craggy_route.grade = craggy_grade.id)
				left join craggy_setter on (craggy_route.setter = craggy_setter.id)
				left join v_panel on (craggy_panel.number = v_panel.number);

