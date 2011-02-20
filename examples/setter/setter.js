var setter_entry;
var setter_matches;

function setter_initialise (entry_id, matches_id)
{
	setter_entry   = document.getElementById(entry_id);
	setter_matches = document.getElementById(matches_id);

	setter_entry.onkeyup = setter_onkeyup;
	setter_entry.onblur  = setter_onkeyup;
	setter_entry.focus();
}

function setter_get_node (node, name)
{
	try {
		txt = node.getElementsByTagName(name)[0].firstChild.nodeValue;
	} catch (er) {
		txt = "&nbsp;";
	}

	return "<td>" + txt + "</td>";
}

function setter_callback()
{
	if ((xmlhttp.readyState != 4) || (xmlhttp.status != 200))
		return;

	txt =	"<table cellspacing=0 border=1>" +
		"<thead>" +
		"<tr>" +
		"<th>ID</th>" +
		"<th>Initials</th>" +
		"<th>First Name</th>" +
		"<th>Surname</th>" +
		"</tr>" +
		"</thead>";


	x = xmlhttp.responseXML.documentElement.getElementsByTagName("setter");
	txt += "<tbody>";
	for (i = 0; i < x.length; i++) {

		txt += "<tr>";
		txt += setter_get_node (x[i], "id");
		txt += setter_get_node (x[i], "initials");
		txt += setter_get_node (x[i], "first_name");
		txt += setter_get_node (x[i], "surname");
		txt += "</tr>";
	}
	txt += "</tbody>";
	txt += "</table>";

	setter_matches.innerHTML = txt;
}

function setter_onkeyup()
{
	str = setter_entry.value;
	if (str.length === 0) {
		setter_matches.innerHTML = "";
		return;
	}

	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = setter_callback;
	xmlhttp.open ("GET", "setter.php?q=" + str, true);
	xmlhttp.send();
}

