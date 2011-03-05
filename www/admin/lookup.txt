lookup.php
	switch ($lookup)
		colour
			include "validate_colour.php";
			return validate_colour ($input, $options);
		date
		difficulty
		grade
		nice
		panel
		setter
		success

combine
	lookup_colour.php and libs/colour.php
	etc

options:
	match case
	first match
	best match
	all matches
	get all possible values

validation:
	input: text string - "input=red&option1=true&option2=false"
	output: xml:
		<validation type='colour'>
			<colour>Red</colour>
			<input>rd</input>
			<option name='option1' value='true' />
			<option name='option2' value='false' />
			<match type='first'>Red</match>			or type='best'
			<match>Red/White</match>
			<error>More than one match</error>
		</validation>
libs/
	validate_colour
	validate_date
		option:	range_start
		option:	range_end
		option:	format_yyyymmdd
		option:	format_human
	validate_difficulty
	validate_grade
	validate_nice
	validate_panel
	validate_setter
	validate_success
