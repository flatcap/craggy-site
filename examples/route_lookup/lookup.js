var route_entry;
var route_matches;

function route_initialise (entry_id, matches_id)
{
	route_entry   = document.getElementById(entry_id);
	route_matches = document.getElementById(matches_id);

	route_entry.onkeyup = route_onkeyup;
	route_entry.focus();
}

function route_callback()
{
	if ((xmlhttp.readyState != 4) || (xmlhttp.status != 200))
		return;

	var response = xmlhttp.responseText;

	split = response.indexOf (" - ");
	if (split >= 0) {
		start = response.substring (0, split);
		end   = response.substring (split+3);
		route_entry.value = start;
		route_matches.innerHTML = end;
	} else {
		route_matches.innerHTML = response;
	}
}

function route_onkeyup()
{
	str = route_entry.value;
	if (str.length === 0) {
		route_matches.innerHTML = "";
		return;
	}

	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = route_callback;
	xmlhttp.open ("GET", "lookup.php?q=" + encodeURI(str), true);
	xmlhttp.send();
}

