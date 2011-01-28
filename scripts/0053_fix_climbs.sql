# fix a few discrepancies in the climb/rating tables

alter table rating drop column onsight;

update rating set difficulty_id = 4 where id = 266;

# update some climb notes
update rating set climb_note_id = null where id in (272,308,313);
delete from climb_note where id in (55,58);

# generate some old climbs to fix the onsight calculation
insert into climb (climber_id, route_id, success_id, date_climbed) values
	(1,  93, 2, "2010-09-05"),
	(1,  96, 2, "2010-09-05"),
	(1, 140, 2, "2010-07-25"),
	(1, 146, 2, "2010-07-24"),
	(1, 149, 2, "2010-07-24"),
	(1, 150, 2, "2010-07-24"),
	(1,  40, 2, "2009-01-28"),
	(1,  46, 2, "2009-01-28"),
	(1, 134, 2, "2009-01-28");

update rating set difficulty_id = 4 where id = 302;
update rating set difficulty_id = 4 where id = 266;

update rating set climb_note_id = null where id = 219;
update climb_note set notes = "did it without the large holes" where id = 25;
update climb_note set notes = "6b?" where id = 49;
update climb_note set notes = "6a+?" where id = 51;

