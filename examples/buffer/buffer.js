var buffer_info;
var buffer_content;

function buffer_new_button (name, parent, callback)
{
	button = document.createElement("input");
	button.type  = "button";
	button.value = name;
	button.onclick = callback;
	parent.appendChild(button);

	return button;
}

function buffer_initialise (button_id, info_id, content_id)
{
	buffer_info    = document.getElementById (info_id);
	buffer_content = document.getElementById (content_id);

	var button_div = document.getElementById (button_id);

	buffer_new_button ("read",    button_div, buffer_click_read);
	buffer_new_button ("clear",   button_div, buffer_click_clear);
	buffer_new_button ("keep",    button_div, buffer_click_keep);
	buffer_new_button ("unknown", button_div, buffer_click_unknown);
}


function buffer_server (action)
{
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = buffer_callback;
	xmlhttp.open ("GET", "buffer.php?action=" + action, true);
	xmlhttp.send();
}

function buffer_click_read()
{
	buffer_server ("read");
}

function buffer_click_clear()
{
	buffer_server ("clear");
}

function buffer_click_keep()
{
	buffer_server ("keep");
}

function buffer_click_unknown()
{
	buffer_server ("unknown");
}


function buffer_get_node (node, name)
{
	try {
		txt = node.getElementsByTagName(name)[0].firstChild.nodeValue;
	} catch (er) {
		txt = "&nbsp;";
	}

	return "<td>" + txt + "</td>";
}

function buffer_get_attr (node, name)
{
	var attr = node.attributes;
	if (!attr)
		return null;

	var key = attr.getNamedItem (name);
	if (!key)
		return null;

	return key.nodeValue;
}


function buffer_callback()
{
	if ((xmlhttp.readyState != 4) || (xmlhttp.status != 200))
		return;

	// top-level node is <data>
	var data = xmlhttp.responseXML.documentElement;

	var rt = buffer_get_attr (data, 'retrieve_time');
	var ce = buffer_get_attr (data, 'cache_expiry');
	var no = buffer_get_attr (data, 'notes');

	var x = data.getElementsByTagName("route");
	var rows = x.length;

	var info = "";
	if (rt) info += "<li>Retrieve time: "    + rt   + " second</li>";
	if (ce) info += "<li>Cache expires in: " + ce   + " seconds</li>";
	if (no) info += "<li>Notes: "            + no   + "</li>";
	if (rows > 0) info += "<li>Rows: "       + rows + "</li>";
	if (info.length > 0) {
		info = "<ul>" + info + "</ul>";
	}

	buffer_info.innerHTML = info;

	var content = "";
	if (rows > 0) {
		content = "<table cellspacing=0 border=1><tr>" +
			  "<th>ID</th>" +
			  "<th>Panel</th>" +
			  "<th>Colour</th>" +
			  "<th>Grade</th>" +
			  "</tr>";

		for (i = 0; i < rows; i++) {

			content += "<tr>";
			content += buffer_get_node (x[i], "id");
			content += buffer_get_node (x[i], "panel");
			content += buffer_get_node (x[i], "colour");
			content += buffer_get_node (x[i], "grade");
			content += "</tr>";
		}
		content += "</table>";
	}

	buffer_content.innerHTML = content;
}

