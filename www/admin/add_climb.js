var button_add;
var button_save;
var button_delete;
var entry_panel;
var entry_date;

var xmlhttp_add;
var xmlhttp_save;

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
	entry_setter = document.getElementById ('setter');

	notify_initialise ('notify_area');

	//buttons_update();
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
	xmlhttp_add.open ("GET", "add_climb_work.php?action=add&data=" + encodeURIComponent (entry_panel.value));
	xmlhttp_add.send();
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

	var table = document.getElementById ('climb_list');
	var body = table.getElementsByTagName('tbody');
	var trs = body[0].childNodes;

	ticks.reverse();
	for (var i = 0; i < count; i++) {
		var index = ticks[i];
		climb_data.splice (index, 1);
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

	var xml = render_xml (climb_data);
	xml = encodeURIComponent (xml);

	if (window.XMLHttpRequest) {
		xmlhttp_save = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp_save = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp_save.onreadystatechange = callback_save;
	xmlhttp_save.open ("GET", "add_climb_work.php?action=save&data=" + xml);
	xmlhttp_save.setRequestHeader ("Content-Type", "text/plain");
	xmlhttp_save.send();
}


function callback_add()
{
	if ((xmlhttp_add.readyState != 4) || (xmlhttp_add.status != 200))
		return;

	alert (xmlhttp_add.responseText);
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

	if (!climb_data)
		climb_data = new Array();
	var i;
	var id_base = climb_data.length;
	x = xmlhttp_add.responseXML.documentElement.getElementsByTagName("climb");
	for (i = 0; i < x.length; i++) {
		var climb = new Object();
		id = id_base + i;
		climb.id       = id;
		climb.panel    = climb_get_node (x[i], "panel");
		climb.colour   = climb_get_node (x[i], "colour");
		climb.grade    = climb_get_node (x[i], "grade");
		climb.date     = date;
		climb.setter   = setter;
		climb_data[id] = climb;
	}

	for (i = 0; i < climb_data.length; i++) {
		id     = climb_data[i].id;
		panel  = climb_data[i].panel;
		colour = climb_data[i].colour;
		grade  = climb_data[i].grade;
		date   = climb_data[i].date;
		setter = climb_data[i].setter;

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

	var table = document.getElementById ('climb_list');
	table.innerHTML = txt;

	initialise_ticks();
	initialise_rows();
	buttons_update();

	// empty climb entry
	// set focus
}

function callback_save()
{
	if ((xmlhttp_save.readyState != 4) || (xmlhttp_save.status != 200))
		return;

	x = xmlhttp_save.responseText;
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

