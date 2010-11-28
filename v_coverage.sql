create view v_climbs as

select
	route.id as id,
	panel.number as panel,
	colour.colour as colour,
	grade.grade as grade,
	grade.order as grade_num,
	climber_id,
	date_climbed,
	v_panel.climb_type as climb_type,
	success,
	downclimb as d,
	nice as n,
	onsight as o,
	difficulty as diff,
	climbs.notes as notes

	from route

		left join climbs on (climbs.route_id = route.id)
		left join colour on (route.colour = colour.id)
		left join panel on (route.panel = panel.id)
		left join grade on (route.grade = grade.id)
		left join v_panel on (route.panel = v_panel.number);

climbs
	id
	climber_id
	route_id
	date_climbed
	success
	downclimb
	nice
	difficulty
	onsight
	notes


climb_type
	id
	type

climber
	id
	name

colour
	id
	colour
	abbr

data
	name
	value

grade
	id
	order
	grade

panel
	id
	number
	type
	height
	arch
	arete
	chimney
	chockstone
	featured
	flake
	overhang
	roof
	slab
	wall

route
	id
	panel
	colour
	grade
	notes
	setter
	date_set

setter
	id
	initials
	name

