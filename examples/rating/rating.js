var sMax;	// Is the maximum number of stars
var holder;	// Is the holding pattern for clicked state
var preSet;	// value once a selection has been made
var rated;

function initialise (div, names)
{
	var div = document.getElementById (div);
	sMax = names.length;

	for (var i = 0; i < sMax; i++) {
		var link = document.createElement('a');
		link.setAttribute ('title', names[i]);
		link.setAttribute ('id', '_' + i);
		link.onclick     = rateIt;
		link.onmouseover = rating;
		link.onmouseout  = off;
		div.appendChild (link); 
	}
}

// Rollover for image Stars
function rating ()
{
	if (rated)
		return;

	s = this.id.replace ("_", ''); // Get the selected star
	a = 0;
	for (i = 0; i < sMax; i++) {
		if (i <= s) {
			id = "_" + i;
			on = "on" + s;
			document.getElementById (id).className = on;
			document.getElementById ("rateStatus").innerHTML = this.title;
			holder = a + 1;
			a++;
		} else {
			document.getElementById ("_" + i).className = "";
		}
	}
}

// For when you roll out of the the whole thing
function off ()
{
	if (rated)
		return;

	if (preSet) {
		rating (preSet);
		document.getElementById ("rateStatus").innerHTML = document.getElementById ("ratingSaved").innerHTML;
	} else {
		for (i = 0; i < sMax; i++) {
			document.getElementById ("_" + i).className = "";
			document.getElementById ("rateStatus").innerHTML = this.parentNode.title;
		}
	}
}

// When you actually rate something
function rateIt ()
{
	if (rated)
		return;

	document.getElementById ("rateStatus").innerHTML = document.getElementById ("ratingSaved").innerHTML + " :: " + this.title;
	preSet = this;
	rated = 1;
	sendRate (this);
	rating (this);
}

// Send the rating information somewhere using Ajax or something like that.
function sendRate (sel)
{
	alert ("Your rating was: " + sel.title);
}

