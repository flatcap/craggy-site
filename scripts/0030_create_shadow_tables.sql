# create shadow tables

create view climb      as select * from craggy_climb;
create view climb_note as select * from craggy_climb_note;
create view climb_type as select * from craggy_climb_type;
create view climber    as select * from craggy_climber;
create view colour     as select * from craggy_colour;
create view data       as select * from craggy_data;
create view difficulty as select * from craggy_difficulty;
create view grade      as select * from craggy_grade;
create view panel      as select * from craggy_panel;
create view rating     as select * from craggy_rating;
create view route      as select * from craggy_route;
create view route_note as select * from craggy_route_note;
create view setter     as select * from craggy_setter;
create view success    as select * from craggy_success;
