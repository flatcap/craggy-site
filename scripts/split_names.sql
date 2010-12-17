# split the craggy_setter 'name' into 'first_name' and 'surname'

# drop v_route (it will break)
drop view v_route;

-- ----------------------------------------
-- craggy_setter
-- ----------------------------------------

# add new columns
alter table craggy_setter add column first_name text;
alter table craggy_setter add column surname text;

# split names
update craggy_setter set first_name = substring_index (name, ' ', 1);
update craggy_setter set surname = substring_index (name, ' ', -1);
update craggy_setter set first_name = "Features", surname = "" where id = 1;

# drop old name column
alter table craggy_setter drop column name;

-- ----------------------------------------
-- craggy_climber
-- ----------------------------------------

# add new columns
alter table craggy_climber add column first_name text;
alter table craggy_climber add column surname text;

# split names
update craggy_climber set first_name = substring_index (name, ' ', 1);
update craggy_climber set surname = substring_index (name, ' ', -1);

# drop old name column
alter table craggy_climber drop column name;

-- ----------------------------------------

# rebuild v_route
create view v_route as
	select
		craggy_route.id as id,
		craggy_panel.name as panel,
		craggy_panel.sequence as panel_seq,
		craggy_colour.colour as colour,
		craggy_grade.grade as grade,
		craggy_grade.sequence as grade_seq,
		v_panel.climb_type as climb_type,
		trim(concat_ws(' ', craggy_setter.first_name, craggy_setter.surname)) as setter,
		craggy_route.date_set as date_set,
		craggy_route.notes as notes,
		craggy_panel.height as height
	from
		craggy_route
		left join craggy_colour on (craggy_route.colour_id = craggy_colour.id)
		left join craggy_panel on (craggy_route.panel_id = craggy_panel.id)
		left join craggy_grade on (craggy_route.grade_id = craggy_grade.id)
		left join craggy_setter on (craggy_route.setter_id = craggy_setter.id)
		left join v_panel on (craggy_panel.name = v_panel.name);

