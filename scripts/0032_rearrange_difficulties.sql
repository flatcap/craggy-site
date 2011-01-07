# change the range of difficulties

set foreign_key_checks = 0;

truncate craggy_difficulty;

insert into craggy_difficulty (sequence, description) values
	(10, "very, easy"),
	(20, "easy"),
	(30, "medium"),
	(40, "hard"),
	(50, "very, hard");

update craggy_rating set difficulty_id = 3 where difficulty_id = 4;
update craggy_rating set difficulty_id = 4 where difficulty_id = 6;

set foreign_key_checks = 1;

