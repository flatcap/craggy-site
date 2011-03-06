var button_add;
var button_save;
var button_delete;
var entry_panel;
var entry_date;
var entry_setter;

var list_ticks;
var route_data;

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

	entry_date   = document.getElementById ('date');
	input_initialise ('date', 'date');
	entry_date.focus(); //tmp

	entry_setter = document.getElementById ('setter');
	input_initialise ('setter', 'setter');

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


function htmlentities(str)
{
	return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function render_html (xml)
{
	return "";
}

function render_xml (list)
{
	var xml = "";

	xml += "<list type='route'>";
	for (var i = 0; i < list.length; i++) {
		xml += "\t<route>";
		for (var j in list[i]) {
			xml += "\t\t<" + j + ">";
			xml += list[i][j];
			xml += "</" + j + ">";
		}
		xml += "\t</route>";
	}
	xml += "</list>";

	return xml;
}


function click_add()
{
	notify_close();
	var str = entry_panel.value;
	str = encodeURI(str);

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	x.onreadystatechange = callback_add;
	x.open ("GET", "add_route_work.php?action=add&data=" + encodeURIComponent (entry_panel.value));
	x.send();
}

function click_delete()
{
	notify_close();

	var ticks = get_ticks();
	var count = ticks.length;
	if (count === 0) {
		buttons_update();	// Shouldn't happen
		return;
	}

	var table = document.getElementById ('route_list');
	var body = table.getElementsByTagName('tbody');
	var trs = body[0].childNodes;

	ticks.reverse();
	for (var i = 0; i < count; i++) {
		var index = ticks[i];
		route_data.splice (index, 1);
		body[0].removeChild (trs[index]);
	}
}

function click_save()
{
	notify_close();

	var ticks = get_ticks();
	var count = ticks.length;
	if (count === 0) {
		buttons_update();	// Shouldn't happen
		return;
	}

	var xml = render_xml (route_data);
	xml = encodeURIComponent (xml);

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	x.onreadystatechange = callback_save;
	x.open ("GET", "add_route_work.php?action=save&data=" + xml);
	x.setRequestHeader ("Content-Type", "text/plain");
	x.send();
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
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var txt = "<table cellspacing=0 border=1>" +
		"<thead>" +
		"<tr>" +
		"<th><input type='checkbox' id='tick_master'></th>" +
		"<th>ID</th>" +
		"<th>Panel</th>" +
		"<th>Colour</th>" +
		"<th>Grade</th>" +
		"<th>Setter</th>" +
		"<th>Date</th>" +
		"<th>Notes</th>" +
		"</tr>" +
		"</thead>" +
		"<tbody>";

	var setter = entry_setter.value;
	var date   = entry_date.value;

	if (!route_data)
		route_data = new Array();
	var i;
	var id_base = route_data.length;
	x = this.responseXML.documentElement.getElementsByTagName("route");
	for (i = 0; i < x.length; i++) {
		var route = new Object();
		id = id_base + i;
		route.id       = id;
		route.panel    = route_get_node (x[i], "panel");
		route.colour   = route_get_node (x[i], "colour");
		route.grade    = route_get_node (x[i], "grade");
		route.date     = date;
		route.setter   = setter;
		route_data[id] = route;
	}

	for (i = 0; i < route_data.length; i++) {
		id     = route_data[i].id;
		panel  = route_data[i].panel;
		colour = route_data[i].colour;
		grade  = route_data[i].grade;
		date   = route_data[i].date;
		setter = route_data[i].setter;

		txt += "<tr>";
		txt += "<td><input type='checkbox' id='id_" + id + "'></td>";
		txt += "<td>" + id + "</td>";
		txt += "<td>" + panel + "</td>";
		txt += "<td>" + colour + "</td>";
		txt += "<td>" + grade + "</td>";
		txt += "<td>" + setter + "</td>";
		txt += "<td>" + date + "</td>";
		txt += "<td>" + '' + "</td>";
		txt += "</tr>";
	}

	txt += "</tbody>" +
	       "</table>";

	var table = document.getElementById ('route_list');
	table.innerHTML = txt;

	initialise_ticks();
	initialise_rows();
	buttons_update();

	// empty route entry
	// set focus
}

function callback_save()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	x = this.responseText;
	notify_message (x);
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
	var ticks = get_ticks();
	var set = (ticks.length > 0);

	button_set_state (button_add,    true);
	button_set_state (button_save,   set);
	button_set_state (button_delete, set);
}

function get_ticks()
{
	var ids = new Array();

	if (list_ticks) {
		for (var i = 0; i < list_ticks.length; i++) {
			if (list_ticks[i].checked) {
				ids.push (list_ticks[i].id.substring(3));
			}
		}
	}

	return ids;
}
