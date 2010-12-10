# change difficulty from text to an enumeration

drop table if exists difficulty;
create table difficulty (
	id int(11) not null auto_increment,
	sequence int(11) not null,
	description tinytext,
	primary key (id)
) engine=innodb auto_increment=1 default charset=utf8;

insert into difficulty (sequence, description) values
	(0, ""),
	(10, "easy"),
	(20, "quite easy"),
	(30, "medium"),
	(40, "quite hard"),
	(50, "hard");

alter table climbs add column difficulty2 int(11) after difficulty;

update climbs set difficulty2 = 1 where difficulty is null;
update climbs set difficulty2 = 1 where difficulty = "";
update climbs set difficulty2 = 2 where difficulty = "easy";
update climbs set difficulty2 = 4 where difficulty = "medium";
update climbs set difficulty2 = 6 where difficulty = "hard";

alter table climbs drop column difficulty;
alter table climbs change column difficulty2 difficulty int(11);

alter table climbs
	add key (difficulty),
	add constraint climbs_ibfk_4 foreign key (difficulty) references difficulty (id);

