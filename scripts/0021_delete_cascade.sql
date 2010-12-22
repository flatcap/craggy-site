# update the foreign keys to delete/null

alter table craggy_climb drop foreign key craggy_climb_ibfk_1;
alter table craggy_climb drop foreign key craggy_climb_ibfk_2;
alter table craggy_climb drop foreign key craggy_climb_ibfk_3;
alter table craggy_climb drop foreign key craggy_climb_ibfk_4;
alter table craggy_panel drop foreign key craggy_panel_ibfk_1;
alter table craggy_route drop foreign key craggy_route_ibfk_1;
alter table craggy_route drop foreign key craggy_route_ibfk_2;
alter table craggy_route drop foreign key craggy_route_ibfk_3;
alter table craggy_route drop foreign key craggy_route_ibfk_4;

alter table craggy_climb change success_id success_id int(11) null;
alter table craggy_route change setter_id setter_id int(11) null;

alter table craggy_panel add constraint foreign key (climb_type_id) references craggy_climb_type (id) on delete cascade;
alter table craggy_route add constraint foreign key (colour_id)     references craggy_colour     (id) on delete cascade;
alter table craggy_route add constraint foreign key (grade_id)      references craggy_grade      (id) on delete cascade;
alter table craggy_route add constraint foreign key (panel_id)      references craggy_panel      (id) on delete cascade;
alter table craggy_route add constraint foreign key (setter_id)     references craggy_setter     (id) on delete set null;
alter table craggy_climb add constraint foreign key (climber_id)    references craggy_climber    (id) on delete cascade;
alter table craggy_climb add constraint foreign key (difficulty_id) references craggy_difficulty (id) on delete set null;
alter table craggy_climb add constraint foreign key (route_id)      references craggy_route      (id) on delete cascade;
alter table craggy_climb add constraint foreign key (success_id)    references craggy_success    (id) on delete set null;

