# tidy database columns

alter table craggy_route change column notes notes text after setter_id;
alter table craggy_climb change column success_id success_id int(11) after route_id;
alter table craggy_panel change column climb_type_id climb_type_id int(11) after id;
alter table craggy_panel change column sequence sequence int(11) not null;

