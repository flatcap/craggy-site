# massive db overhaul
# standardise lots of names
# make sure that compulsory fields are not null

drop view v_panel;
drop view v_route;

update craggy_climb set difficulty = null where difficulty=1;
delete from craggy_difficulty where id=1;

alter table craggy_route drop foreign key craggy_route_ibfk_1;
alter table craggy_route drop foreign key craggy_route_ibfk_2;
alter table craggy_route drop foreign key craggy_route_ibfk_3;
alter table craggy_route drop foreign key craggy_route_ibfk_4;
alter table craggy_panel drop foreign key craggy_panel_ibfk_1;
alter table craggy_climb drop foreign key craggy_climb_ibfk_1;
alter table craggy_climb drop foreign key craggy_climb_ibfk_2;
alter table craggy_climb drop foreign key craggy_climb_ibfk_3;
alter table craggy_climb drop foreign key craggy_climb_ibfk_4;

alter table craggy_climb change column success success_id int(11);
alter table craggy_climb change column difficulty difficulty_id int(11);
alter table craggy_route change column panel panel_id int(11);
alter table craggy_route change column colour colour_id int(11);
alter table craggy_route change column grade grade_id int(11);
alter table craggy_route change column setter setter_id int(11);
alter table craggy_panel change column type climb_type_id int(11);
alter table craggy_panel change column number name tinytext;
alter table craggy_grade change column `order` sequence int(11);
alter table craggy_climb_type change column type climb_type tinytext;

alter table craggy_climb_type modify column climb_type tinytext not null;
alter table craggy_climb modify column success_id int(11) not null;
alter table craggy_difficulty modify column description tinytext not null;
alter table craggy_grade modify column sequence int(11) not null;
alter table craggy_panel modify column name tinytext not null;
alter table craggy_panel modify column climb_type_id int(11) not null;
alter table craggy_route modify column panel_id int(11) not null;
alter table craggy_route modify column colour_id int(11) not null;
alter table craggy_route modify column grade_id int(11) not null;
alter table craggy_route modify column setter_id int(11) not null;
alter table craggy_route modify column notes text null;
alter table craggy_setter modify column initials tinytext not null;
alter table craggy_success modify column description tinytext not null;

create view v_panel as
	select
		craggy_panel.name as name,
		craggy_climb_type.climb_type as climb_type
			from craggy_panel
				left join craggy_climb_type on (craggy_panel.climb_type_id = craggy_climb_type.id);

create view v_route as
	select
		craggy_route.id as id,
		craggy_panel.name as panel,
		craggy_colour.colour as colour,
		craggy_grade.grade as grade,
		craggy_grade.sequence as grade_num,
		v_panel.climb_type as climb_type,
		craggy_setter.name as setter,
		craggy_route.date_set as date_set,
		craggy_route.notes as notes,
		craggy_panel.height as height
			from craggy_route
				left join craggy_colour on (craggy_route.colour_id = craggy_colour.id)
				left join craggy_panel on (craggy_route.panel_id = craggy_panel.id)
				left join craggy_grade on (craggy_route.grade_id = craggy_grade.id)
				left join craggy_setter on (craggy_route.setter_id = craggy_setter.id)
				left join v_panel on (craggy_panel.name = v_panel.name);

alter table craggy_route add constraint foreign key (grade_id) references craggy_grade (id);
alter table craggy_route add constraint foreign key (panel_id) references craggy_panel (id);
alter table craggy_route add constraint foreign key (setter_id) references craggy_setter (id);
alter table craggy_route add constraint foreign key (colour_id) references craggy_colour (id);
alter table craggy_panel add constraint foreign key (climb_type_id) references craggy_climb_type (id);
alter table craggy_climb add constraint foreign key (route_id) references craggy_route (id);
alter table craggy_climb add constraint foreign key (climber_id) references craggy_climber (id);
alter table craggy_climb add constraint foreign key (success_id) references craggy_success (id);
alter table craggy_climb add constraint foreign key (difficulty_id) references craggy_difficulty (id);

