select distinct date_set from route order by date_set desc limit 15;
select panel,colour,grade from v_routes where date_set >= "2010-08-06";
select panel,colour,grade,date_set from v_routes where date_set >= "2010-08-06" order by date_set desc, panel, grade_num;
