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

validation:
	input: text string - "input=red&option1=true&option2=false"
	output: xml:
		<validation type='colour'>
			<colour>Red</colour>
			<input>rd</input>
			<option name='option1' value='true' />
			<option name='option2' value='false' />
			<error>No Red route here</error>
		</validation>
libs/
	validate_colour
	validate_date
	validate_difficulty
	validate_grade
	validate_nice
	validate_panel
	validate_setter
	validate_success
