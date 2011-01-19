# create triggers to watch for table updates

# clear existing triggers
drop trigger if exists t_climb_d;
drop trigger if exists t_climb_i;
drop trigger if exists t_climb_u;
drop trigger if exists t_climb_note_d;
drop trigger if exists t_climb_note_i;
drop trigger if exists t_climb_note_u;
drop trigger if exists t_climb_type_d;
drop trigger if exists t_climb_type_i;
drop trigger if exists t_climb_type_u;
drop trigger if exists t_climber_d;
drop trigger if exists t_climber_i;
drop trigger if exists t_climber_u;
drop trigger if exists t_colour_d;
drop trigger if exists t_colour_i;
drop trigger if exists t_colour_u;
drop trigger if exists t_difficulty_d;
drop trigger if exists t_difficulty_i;
drop trigger if exists t_difficulty_u;
drop trigger if exists t_grade_d;
drop trigger if exists t_grade_i;
drop trigger if exists t_grade_u;
drop trigger if exists t_panel_d;
drop trigger if exists t_panel_i;
drop trigger if exists t_panel_u;
drop trigger if exists t_rating_d;
drop trigger if exists t_rating_i;
drop trigger if exists t_rating_u;
drop trigger if exists t_route_d;
drop trigger if exists t_route_i;
drop trigger if exists t_route_u;
drop trigger if exists t_route_note_d;
drop trigger if exists t_route_note_i;
drop trigger if exists t_route_note_u;
drop trigger if exists t_setter_d;
drop trigger if exists t_setter_i;
drop trigger if exists t_setter_u;
drop trigger if exists t_success_d;
drop trigger if exists t_success_i;
drop trigger if exists t_success_u;

-- ----------------------------------------------------------------------

# wipe the slate
delete from data where name like 'table_%';
insert into data (name) values
	('table_climb'),
	('table_climb_note'),
	('table_climb_type'),
	('table_climber'),
	('table_colour'),
	('table_difficulty'),
	('table_grade'),
	('table_panel'),
	('table_rating'),
	('table_route'),
	('table_route_note'),
	('table_setter'),
	('table_success');
update data set value=now() where name like 'table_%';

-- ----------------------------------------------------------------------

# tables relating to the climber
create trigger t_climb_i      after insert on climb      for each row update data set value=now() where name = 'table_climb';
create trigger t_climb_note_i after insert on climb_note for each row update data set value=now() where name = 'table_climb_note';
create trigger t_climb_type_i after insert on climb_type for each row update data set value=now() where name = 'table_climb_type';
create trigger t_climber_i    after insert on climber    for each row update data set value=now() where name = 'table_climber';
create trigger t_difficulty_i after insert on difficulty for each row update data set value=now() where name = 'table_difficulty';
create trigger t_rating_i     after insert on rating     for each row update data set value=now() where name = 'table_rating';
create trigger t_success_i    after insert on success    for each row update data set value=now() where name = 'table_success';

create trigger t_climb_u      after update on climb      for each row update data set value=now() where name = 'table_climb';
create trigger t_climb_note_u after update on climb_note for each row update data set value=now() where name = 'table_climb_note';
create trigger t_climb_type_u after update on climb_type for each row update data set value=now() where name = 'table_climb_type';
create trigger t_climber_u    after update on climber    for each row update data set value=now() where name = 'table_climber';
create trigger t_difficulty_u after update on difficulty for each row update data set value=now() where name = 'table_difficulty';
create trigger t_rating_u     after update on rating     for each row update data set value=now() where name = 'table_rating';
create trigger t_success_u    after update on success    for each row update data set value=now() where name = 'table_success';

create trigger t_climb_d      after delete on climb      for each row update data set value=now() where name = 'table_climb';
create trigger t_climb_note_d after delete on climb_note for each row update data set value=now() where name = 'table_climb_note';
create trigger t_climb_type_d after delete on climb_type for each row update data set value=now() where name = 'table_climb_type';
create trigger t_climber_d    after delete on climber    for each row update data set value=now() where name = 'table_climber';
create trigger t_difficulty_d after delete on difficulty for each row update data set value=now() where name = 'table_difficulty';
create trigger t_rating_d     after delete on rating     for each row update data set value=now() where name = 'table_rating';
create trigger t_success_d    after delete on success    for each row update data set value=now() where name = 'table_success';

-- ----------------------------------------------------------------------

# tables relating to the routes
create trigger t_colour_i     after insert on colour     for each row update data set value=now() where name = 'table_colour'     or name = 'table_v_route';
create trigger t_grade_i      after insert on grade      for each row update data set value=now() where name = 'table_grade'      or name = 'table_v_route';
create trigger t_panel_i      after insert on panel      for each row update data set value=now() where name = 'table_panel'      or name = 'table_v_route';
create trigger t_route_i      after insert on route      for each row update data set value=now() where name = 'table_route'      or name = 'table_v_route';
create trigger t_route_note_i after insert on route_note for each row update data set value=now() where name = 'table_route_note' or name = 'table_v_route';
create trigger t_setter_i     after insert on setter     for each row update data set value=now() where name = 'table_setter'     or name = 'table_v_route';

create trigger t_colour_u     after update on colour     for each row update data set value=now() where name = 'table_colour'     or name = 'table_v_route';
create trigger t_grade_u      after update on grade      for each row update data set value=now() where name = 'table_grade'      or name = 'table_v_route';
create trigger t_panel_u      after update on panel      for each row update data set value=now() where name = 'table_panel'      or name = 'table_v_route';
create trigger t_route_u      after update on route      for each row update data set value=now() where name = 'table_route'      or name = 'table_v_route';
create trigger t_route_note_u after update on route_note for each row update data set value=now() where name = 'table_route_note' or name = 'table_v_route';
create trigger t_setter_u     after update on setter     for each row update data set value=now() where name = 'table_setter'     or name = 'table_v_route';

create trigger t_colour_d     after delete on colour     for each row update data set value=now() where name = 'table_colour'     or name = 'table_v_route';
create trigger t_grade_d      after delete on grade      for each row update data set value=now() where name = 'table_grade'      or name = 'table_v_route';
create trigger t_panel_d      after delete on panel      for each row update data set value=now() where name = 'table_panel'      or name = 'table_v_route';
create trigger t_route_d      after delete on route      for each row update data set value=now() where name = 'table_route'      or name = 'table_v_route';
create trigger t_route_note_d after delete on route_note for each row update data set value=now() where name = 'table_route_note' or name = 'table_v_route';
create trigger t_setter_d     after delete on setter     for each row update data set value=now() where name = 'table_setter'     or name = 'table_v_route';

