# split out the route notes to craggy_route_note

alter table craggy_route add column note_id int(11) after notes;

insert into craggy_route_note (notes) values ("With bridging");
insert into craggy_route_note (notes) values ("No bridging");
insert into craggy_route_note (notes) values ("Following easiest line to centre of arch");
insert into craggy_route_note (notes) values ("Discs and features - Start on route 19");
insert into craggy_route_note (notes) values ("Discs and features");
insert into craggy_route_note (notes) values ("Discs and features - Start on route 20");
insert into craggy_route_note (notes) values ("6b without using large holes to right");
insert into craggy_route_note (notes) values ("Dark blue");
insert into craggy_route_note (notes) values ("Light blue");
insert into craggy_route_note (notes) values ("Chimney");
insert into craggy_route_note (notes) values ("Hands allowed on arete");

update craggy_route set note_id = 1 where notes like "With bridging";
update craggy_route set note_id = 2 where notes like "No bridging";
update craggy_route set note_id = 3 where notes like "Following easiest line to centre of arch";
update craggy_route set note_id = 4 where notes like "Discs and features - Start on route 19";
update craggy_route set note_id = 5 where notes like "Discs and features";
update craggy_route set note_id = 6 where notes like "Discs and features - Start on route 20";
update craggy_route set note_id = 7 where notes like "6b without using large holes to right";
update craggy_route set note_id = 8 where notes like "Dark blue";
update craggy_route set note_id = 9 where notes like "Light blue";
update craggy_route set note_id = 10 where notes like "Chimney";
update craggy_route set note_id = 11 where notes like "Hands allowed on arete";

alter table craggy_route drop column notes;
alter table craggy_route add constraint foreign key (note_id) references craggy_route_note (id) on delete set null;

# rebuild v_route
drop view v_route;
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
		craggy_route_note.notes as notes,
		craggy_panel.height as height
	from
		craggy_route
		left join craggy_colour on (craggy_route.colour_id = craggy_colour.id)
		left join craggy_panel on (craggy_route.panel_id = craggy_panel.id)
		left join craggy_grade on (craggy_route.grade_id = craggy_grade.id)
		left join craggy_setter on (craggy_route.setter_id = craggy_setter.id)
		left join craggy_route_note on (craggy_route.note_id = craggy_route_note.id)
		left join v_panel on (craggy_panel.name = v_panel.name)
	where
		(craggy_route.date_end is null);

