select
	name as panel,
	colour,
	grade,
	difficulty.description as diff
from route
	left join panel on (panel_id = panel.id)
	left join rating on (route_id = route.id)
	left join difficulty on (difficulty_id = difficulty.id)
	left join colour on (colour_id = colour.id)
	left join grade on (grade_id = grade.id)
where
	date_end is null and
	grade_id in (7,8) and
	climb_type_id in (2,3)
order by
	panel.sequence,
	grade.sequence,
	colour;

# where climber_id = 1
# where climber_id = null

# Either collect the ratings of a single user
# Or average the ratings of all users

