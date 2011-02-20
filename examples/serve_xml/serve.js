var route_entry;
var route_matches;

function route_initialise (entry_id, matches_id)
{
	route_entry   = document.getElementById(entry_id);
	route_matches = document.getElementById(matches_id);

	route_entry.onkeyup = route_onkeyup;
	route_entry.focus();
}

function route_get_node (node, name)
{
	try {
		txt = node.getElementsByTagName(name)[0].firstChild.nodeValue;
	} catch (er) {
		txt = "&nbsp;";
	}

	return "<td>" + txt + "</td>";
}

function route_callback()
{
	if ((xmlhttp.readyState != 4) || (xmlhttp.status != 200))
		return;

	txt =	"<table cellspacing=0 border=1><tr>" +
		"<th>ID</th>" +
		"<th>Route ID</th>" +
		"<th>Date Climbed</th>" +
		"<th>Success</th>" +
		"</tr>";

	x = xmlhttp.responseXML.documentElement.getElementsByTagName("climb");
	for (i = 0; i < x.length; i++) {

		txt += "<tr>";
		txt += route_get_node (x[i], "id");
		txt += route_get_node (x[i], "route_id");
		txt += route_get_node (x[i], "date_climbed");
		txt += route_get_node (x[i], "success_id");
		txt += "</tr>";
	}
	txt += "</table>";

	route_matches.innerHTML = txt;
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
	xmlhttp.open ("GET", "serve.php?q=climb", true);
	xmlhttp.send();
}

