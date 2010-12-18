var star_count;
var span_status;
var rated;

// Array
//     [ description = "Quite Easy", id = "42" ]
//     ...

function rating_initialise (div, names, description, status)
{
	var div = document.getElementById (div);
	star_count = names.length;

	for (var i = 0; i < star_count; i++) {
		var link = document.createElement('a');
		link.setAttribute ('title', names[i]);
		link.setAttribute ('id', '_' + i);
		link.onclick     = rating_click;
		link.onmouseover = rating_mouseover;
		link.onmouseout  = rating_mouseout;
		div.appendChild (link); 
	}

	span_status = document.getElementById (status);
}

function rating_mouseover()
{
	if (!rated)
		rating_refresh (this);
}

function rating_mouseout()
{
	if (rated)
		return;

	for (i = 0; i < star_count; i++) {
		document.getElementById ("_" + i).className = "";
		rating_status (this.parentNode.title);
	}
}

function rating_status (text)
{
	if (span_status)
		span_status.innerHTML = text;
}

function rating_refresh (item)
{
	rating_status (item.title);
	s = item.id.replace ("_", '');
	for (i = 0; i < star_count; i++) {
		if (i <= s) {
			document.getElementById ("_" + i).className = "on" + s;
		} else {
			document.getElementById ("_" + i).className = "";
		}
	}
}

function rating_click()
{
	if (rated) {
		rated = 0;
		rating_status (this.title);
		rating_refresh (this);
	} else {
		rating_status (this.title + " - Saved");
		rated = 1;
		//alert ("Your rating was: " + this.title);
	}
}

