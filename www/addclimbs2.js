var route_entry;
var route_matches;
var route_results;

function route_initialise (entry_id, matches_id, results_id)
{
	route_entry   = document.getElementById(entry_id);
	route_matches = document.getElementById(matches_id);
	route_results = document.getElementById(results_id);

	route_entry.onkeyup = route_onkeyup;
}

function route_callback()
{
	if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
		var response = xmlhttp.responseText;
		if (response.length == 0)
			return;

		route_matches.innerHTML = response;
	}
}

function route_onkeyup()
{
	str = route_entry.value;
	if (str.length == 0)
		return;

	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = route_callback;
	xmlhttp.open ("GET", "getclimbs.php?q=" + str, true);
	xmlhttp.send();
}

