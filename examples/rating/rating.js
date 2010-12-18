var star_count;
var holder;	// Is the holding pattern for clicked state
var preSet;	// value once a selection has been made
var rated;

function rating_initialise (div, names)
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
}

function rating_mouseover()
{
	if (rated)
		return;

	s = this.id.replace ("_", ''); // Get the selected star
	a = 0;
	for (i = 0; i < star_count; i++) {
		if (i <= s) {
			document.getElementById ("_" + i).className = "on" + s;
			document.getElementById ("rateStatus").innerHTML = this.title;
			holder = a + 1;
			a++;
		} else {
			document.getElementById ("_" + i).className = "";
		}
	}
}

function rating_mouseout()
{
	if (rated)
		return;

	if (preSet) {
		rating_mouseover (preSet);
		document.getElementById ("rateStatus").innerHTML = document.getElementById ("ratingSaved").innerHTML;
	} else {
		for (i = 0; i < star_count; i++) {
			document.getElementById ("_" + i).className = "";
			document.getElementById ("rateStatus").innerHTML = this.parentNode.title;
		}
	}
}

function rating_click()
{
	if (rated) {
		preSet = null;
		rated = 0;
		document.getElementById ("rateStatus").innerHTML = this.title;
		return;
	}

	document.getElementById ("rateStatus").innerHTML = this.title + " - Saved";
	preSet = this;
	rated = 1;
	//alert ("Your rating was: " + sel.title);
}

