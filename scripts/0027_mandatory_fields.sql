# mark a few columns as mandatory

alter table craggy_panel change column climb_type_id climb_type_id int(11) not null;
alter table craggy_route_note change column notes notes text not null;
alter table craggy_climb_note change column notes notes text not null;
alter table craggy_rating change column climber_id climber_id int(11) not null;
alter table craggy_rating change column route_id route_id  int(11) not null;
alter table craggy_climber change column first_name first_name text not null;
alter table craggy_climber change column surname surname text not null;
