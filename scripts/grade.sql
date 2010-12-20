# excise an unused grade and update the routes to match

set foreign_key_checks = 0;

update craggy_route set grade_id = grade_id - 1 where grade_id > 7;
delete from craggy_grade where id = 7;
update craggy_grade set id = id - 1 where id > 7;

set foreign_key_checks = 1;

