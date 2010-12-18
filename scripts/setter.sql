# Tidy up the setter table

set foreign_key_checks = 0;

update craggy_route set setter_id=3 where setter_id=5;  # rr
update craggy_route set setter_id=4 where setter_id=13; # nk
update craggy_route set setter_id=5 where setter_id=14; # br
update craggy_route set setter_id=6 where setter_id=15; # gh
update craggy_route set setter_id=7 where setter_id=16; # jc
update craggy_route set setter_id=8 where setter_id=17; # ds

delete from craggy_setter where (id = 3) or (id = 4) or (id = 6) or (id = 7) or (id = 8) or (id = 9);

update craggy_setter set id=3 where id=5;  # rr
update craggy_setter set id=4 where id=13; # nk
update craggy_setter set id=5 where id=14; # br
update craggy_setter set id=6 where id=15; # gh
update craggy_setter set id=7 where id=16; # jc
update craggy_setter set id=8 where id=17; # ds

alter table craggy_setter auto_increment = 9;

insert into craggy_setter (initials,first_name,surname) values ('mh', 'Mike', 'Hadcock');

delete from craggy_setter where id > 9;

set foreign_key_checks = 1;

