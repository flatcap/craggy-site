var button_add;
var button_save;
var button_delete;
var button_cancel;
var entry_panel;

var list_ticks;
var route_data;

var xmlhttp_add;
var xmlhttp_del;

//initialise_ticks();
//initialise_rows();
initialise_buttons();

function initialise_buttons()
{
	button_add    = document.getElementById ('button_add');
	button_save   = document.getElementById ('button_save');
	button_delete = document.getElementById ('button_delete');

	button_add.onclick    = click_add;
	button_save.onclick   = click_save;
	button_delete.onclick = click_delete;

	entry_panel = document.getElementById ('entry');
	entry_panel.onkeypress = callback_catch_enter;
	entry_panel.focus();

	notify_initialise ('notify_area');

	//buttons_update();
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


function click_add()
{
	notify_close();
	var str = entry_panel.value;
	str = encodeURI(str);

	if (window.XMLHttpRequest) {
		xmlhttp_add = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp_add = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp_add.onreadystatechange = callback_add;
	xmlhttp_add.open ("GET", "add_work.php?action=add&data=" + encodeURIComponent (entry_panel.value));
	xmlhttp_add.send();
}

function click_delete()
{
	notify_close();
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
	xmlhttp_del.open ("GET", "add_work.php?action=delete&data=" + str, true);
	xmlhttp_del.send();
}

function click_save()
{
	notify_close();
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

function callback_add()
{
	if ((xmlhttp_add.readyState != 4) || (xmlhttp_add.status != 200))
		return;

	x = xmlhttp_add.responseText;
	notify_message (x);
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


	x = xmlhttp_add.responseXML.documentElement.getElementsByTagName("route");
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

function callback_catch_enter (e)
{
	if (e.keyCode == 13) {
		click_add();
		return false;
	}

	return true;
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


