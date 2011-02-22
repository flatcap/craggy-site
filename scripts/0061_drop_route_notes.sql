# merge route_note table
alter table route add column notes varchar(255) after note_id;

update route set notes = "With bridging"                            where note_id = 1;
update route set notes = "No bridging"                              where note_id = 2;
update route set notes = "Following easiest line to centre of arch" where note_id = 3;
update route set notes = "Discs and features - Start on route 19"   where note_id = 4;
update route set notes = "Discs and features"                       where note_id = 5;
update route set notes = "Discs and features - Start on route 20"   where note_id = 6;
update route set notes = "6b without using large holes to right"    where note_id = 7;
update route set notes = "Dark blue"                                where note_id = 8;
update route set notes = "Light blue"                               where note_id = 9;
update route set notes = "Chimney"                                  where note_id = 10;
update route set notes = "Hands allowed on arete"                   where note_id = 11;

drop trigger t_route_note_i;
drop trigger t_route_note_u;
drop trigger t_route_note_d;
alter table route drop foreign key route_ibfk_5;
drop table route_note;

alter table route drop column note_id;

drop view v_route;
create view v_route as
	select
		route.id              as id,
		panel.name            as panel,
		panel.sequence        as panel_seq,
		colour.colour         as colour,
		grade.grade           as grade,
		grade.sequence        as grade_seq,
		climb_type.climb_type as climb_type,
		trim(concat_ws(' ',setter.first_name, setter.surname)) as setter,
		route.date_set        as date_set,
		route.notes           as notes,
		panel.height          as height
	from route
		left join colour     on (route.colour_id     = colour.id)
		left join panel      on (route.panel_id      = panel.id)
		left join grade      on (route.grade_id      = grade.id)
		left join setter     on (route.setter_id     = setter.id)
		left join climb_type on (panel.climb_type_id = climb_type.id)
	where
		isnull (route.date_end);

