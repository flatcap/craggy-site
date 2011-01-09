var button_list;
var button_delete;
var button_cancel;
var entry_panel;

var list_ticks;
var route_data;

var xmlhttp_list;
var xmlhttp_del;

//initialise_ticks();
//initialise_rows();
initialise_buttons();

function initialise_buttons()
{
	button_list   = document.getElementById ('button_list');
	button_delete = document.getElementById ('button_delete');
	button_cancel = document.getElementById ('button_cancel');

	button_list.onclick = click_list;
	button_delete.onclick = click_delete;
	button_cancel.onclick = click_cancel;

	entry_panel = document.getElementById ('entry');
	entry_panel.onkeypress = callback_keypress;
	entry_panel.focus();

	buttons_update();
}

function initialise_ticks()
{
	var content = document.getElementsByClassName ('content');

	var body = content[0].getElementsByTagName ('tbody');

	list_ticks = body[0].getElementsByTagName ('input');

	for (i = 0; i < list_ticks.length; i++) {
		list_ticks[i].checked = true;
		list_ticks[i].onclick = check_click;
	}

	var master = document.getElementById ('tick_master');
	master.checked = false;
	master.onclick = tick_master_click;
}

function initialise_rows()
{
	var content = document.getElementsByClassName ('content');

	var body = content[0].getElementsByTagName ('tbody');

	var trs = body[0].getElementsByTagName ('tr');
	for (i = 0; i < trs.length; i++) {
		trs[i].onclick = row_clicked;
	}

}


function click_list()
{
	var str = entry_panel.value;
	str = encodeURI(str);

	if (window.XMLHttpRequest) {
		xmlhttp_list = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp_list = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp_list.onreadystatechange = callback_list;
	xmlhttp_list.open ("GET", "route_work.php?action=list&data=" + encodeURI (entry_panel.value));
	xmlhttp_list.send();
}

function click_delete()
{
	var count = list_ticks.length;
	if (count === 0) {
		buttons_update();	// Shouldn't happen
		return;
	}

	var ids = new Array();
	for (i = 0; i < count; i++) {
		if (list_ticks[i].checked) {
			ids.push (list_ticks[i].id.substring(3));
		}
	}

	if (!confirm ("About to delete " + count + " routes.\nAre you sure?")) {
		return;
	}

	var str = ids.join(',');
	if (window.XMLHttpRequest) {
		xmlhttp_del = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp_del = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp_del.onreadystatechange = callback_delete;
	xmlhttp_del.open ("GET", "route_work.php?action=delete&data=" + str, true);
	xmlhttp_del.send();
}


function click_cancel()
{
}


function route_get_node (node, name)
{
	try {
		return node.getElementsByTagName(name)[0].firstChild.nodeValue;
	} catch (er) {
	}

	return "";
	//return "<td>" + txt + "</td>";
}

function callback_keypress (e)
{
	if (e.keyCode == 13) {
		click_list();
		return false;
	}

	return true;
}

function callback_list()
{
	if ((xmlhttp_list.readyState != 4) || (xmlhttp_list.status != 200))
		return;

	var txt = "<table cellspacing=0 border=1>" +
		"<thead>" +
		"<tr>" +
		"<th><input type='checkbox' id='tick_master'></th>" +
		//"<th>ID</th>" +
		"<th>Panel</th>" +
		"<th>Colour</th>" +
		"<th>Grade</th>" +
		"</tr>" +
		"</thead>" +
		"<tbody>";


	x = xmlhttp_list.responseXML.documentElement.getElementsByTagName("route");
	route_data = new Array();
	for (i = 0; i < x.length; i++) {
		var route = new Array();
		var id = route_get_node (x[i], "id");
		route['id']     = id;
		route['panel']  = route_get_node (x[i], "panel");
		route['colour'] = route_get_node (x[i], "colour");
		route['grade']  = route_get_node (x[i], "grade");
		route_data[id]  = route;
	}
	for (s in route_data) {
		id     = route_data[s]['id'];
		panel  = route_data[s]['panel'];
		colour = route_data[s]['colour'];
		grade  = route_data[s]['grade'];

		txt += "<tr>";
		txt += "<td><input type='checkbox' id='id_" + id + "'></td>";
		//txt += "<td>" + id + "</td>";
		txt += "<td>" + panel + "</td>";
		txt += "<td>" + colour + "</td>";
		txt += "<td>" + grade + "</td>";
		txt += "</tr>";
	}

	txt += "</tbody>" +
	       "</table>";

	var table = document.getElementById ('route_list');
	table.innerHTML = txt;

	//button_set_state (button_list, false);
	initialise_ticks();
	initialise_rows();
	buttons_update();
}

function callback_delete()
{
	if ((xmlhttp_del.readyState != 4) || (xmlhttp_del.status != 200))
		return;

	var response = xmlhttp_del.responseText;
	if (response.length === 0)
		return;

	alert (response);

	var table = document.getElementById ('route_list');
	table.innerHTML = "";
	buttons_update();
}


function callback_cancel()
{
	alert ('cancel');
}


function check_click(e)
{
	this.checked = !this.checked;
	buttons_update();
}

function tick_master_click()
{
	for (i = 0; i < list_ticks.length; i++) {
		list_ticks[i].checked = this.checked;
	}
	buttons_update();
}

function row_clicked()
{
	var ticks = this.getElementsByTagName ('input');

	ticks[0].checked = !ticks[0].checked;
	buttons_update();
}

function button_set_state (button, enabled)
{
	if (enabled) {
		button.disabled = false;
		button.className = "enabled";
	} else {
		button.disabled = true;
		button.className = "disabled";
	}
}

function buttons_update()
{
	var set = false;
	for (i = 0; i < list_ticks.length; i++) {
		if (list_ticks[i].checked) {
			set = true;
			break;
		}
	}

	button_set_state (button_list, true);
	button_set_state (button_delete, set);
	button_set_state (button_cancel, set);
}


