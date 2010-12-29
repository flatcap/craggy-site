# create a craggy_rating table and split up craggy_climb

drop table if exists craggy_rating;
create table craggy_rating (
	id int(11) not null auto_increment,
	climber_id int(11) not null,
	route_id int(11) not null,
	difficulty_id int(11) default null,
	nice int(11) default null,
	onsight int(11) default null,
	notes text,
	primary key (id),
	key climber_id (climber_id),
	key route_id (route_id),
	key difficulty_id (difficulty_id),
	constraint foreign key (climber_id) references craggy_climber(id) on delete cascade,
	constraint foreign key (route_id) references craggy_route(id) on delete cascade,
	constraint foreign key (difficulty_id) references craggy_difficulty(id) on delete set null
) engine innodb;

