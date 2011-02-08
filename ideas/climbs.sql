select
	route.id               as id,
	panel.name             as panel,
	panel.sequence         as panel_seq,
	colour.colour          as colour,
	grade.grade            as grade,
	grade.sequence         as grade_seq,
	climb_type,
	date_climbed,
	success.outcome        as success,
	nice                   as n,
	onsight                as o,
	difficulty.description as diff,
	notes

from route
	left join climb      on ((climb.route_id      = route.id) and (climber_id = 1))
	left join colour     on (route.colour_id      = colour.id)
	left join panel      on (route.panel_id       = panel.id)
	left join grade      on (route.grade_id       = grade.id)
	left join climb_type on (panel.climb_type_id  = climb_type.id)
	left join success    on (climb.success_id     = success.id)
	left join rating     on (rating.route_id      = route.id)
	left join difficulty on (rating.difficulty_id = difficulty.id)
	left join climb_note on (rating.climb_note_id = climb_note.id)

where
	date_end is null

order by
	panel.sequence,
	grade.sequence,
	colour,
	date_climbed

