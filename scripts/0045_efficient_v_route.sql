# more efficient select for v_route;

drop view v_route;
create view v_route as
	select
		route.id AS id,
		panel.name AS panel,
		panel.sequence AS panel_seq,
		colour.colour AS colour,
		grade.grade AS grade,
		grade.sequence AS grade_seq,
		climb_type AS climb_type,
		trim(concat_ws(' ',setter.first_name,setter.surname)) AS setter,
		route.date_set AS date_set,
		route_note.notes AS notes,
		panel.height AS height
	from
		route
		left join colour on (route.colour_id = colour.id)
		left join panel on (route.panel_id = panel.id)
		left join grade on (route.grade_id = grade.id)
		left join setter on (route.setter_id = setter.id)
		left join route_note on (route.note_id = route_note.id)
		left join climb_type on (panel.climb_type_id = climb_type.id)
	where
		isnull (route.date_end)
