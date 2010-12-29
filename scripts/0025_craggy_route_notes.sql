# create a craggy_route_notes table and split up craggy_climb

drop table if exists craggy_route_notes;
create table craggy_route_notes (
	id int(11) not null auto_increment,
	notes text,
	primary key (id)
) engine innodb;

