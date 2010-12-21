# turn success into an enumeration
# also include downclimb as a success level

drop table if exists success;
create table success (
	id int(11) not null auto_increment,
	sequence int(11) not null,
	outcome tinytext not null,
	description tinytext,
	primary key (id)
) engine=innodb auto_increment=1 default charset=utf8;

insert into success (sequence, outcome, description) values
	(0, "failed", "Didn't reach the top"),
	(10, "success", "Made it to the top, but rested/fell off"),
	(20, "clean", "Made it to the top without rests/falls"),
	(30, "downclimb", "Climbed up and down without rests/falls");

update climbs set success = "downclimb" where downclimb = 1;
alter table climbs drop column downclimb;

alter table climbs add column success2 int(11) after success;

update climbs set success2 = 1 where success = "failed";
update climbs set success2 = 2 where success = "success";
update climbs set success2 = 3 where success = "clean";
update climbs set success2 = 4 where success = "downclimb";

alter table climbs drop column success;
alter table climbs change column success2 success int(11);

alter table climbs
	add key (success),
	add constraint climbs_ibfk_3 foreign key (success) references success (id);

