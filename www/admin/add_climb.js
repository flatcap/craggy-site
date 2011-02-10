var button_add;
var button_save;
var button_delete;
var entry_panel;
var entry_date;

var xmlhttp_add;
var xmlhttp_save;

var climb_data;

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


function climb_get_node (node, name)
{
	try {
		return node.getElementsByTagName(name)[0].firstChild.nodeValue;
	} catch (er) {
	}

	return "";
}

function table_create (columns, ticklist)
{
	var t  = document.createElement ('table');
	var th = document.createElement ('thead');
	var tb = document.createElement ('tbody');

	t.border      = 1;
	t.cellspacing = 0;

	t.appendChild (th);
	t.appendChild (tb);

	var r = t.insertRow (0)

	if (ticklist) {
		var c = document.createElement ('th');
		var i = document.createElement ('input');

		i.id   = 'tick_master';
		i.type = 'checkbox';
		c.appendChild (i);
		r.appendChild (c);
	}

	var name;
	for (name in columns) {
		var c = document.createElement ('th');
		c.innerHTML = columns[name];
		r.appendChild (c);
	}

	return t;
}

function table_add_row (table, columns, data, tick)
{
	if (!table || !columns || !data)
		return;
	
	var r = table.insertRow (-1);
	var c = r.insertCell (-1)
	var i = document.createElement ('input');

	i.id   = 'tick_master';
	i.type = 'checkbox';
	c.appendChild (i);
	r.appendChild (c);

	for (name in columns) {
		c = r.insertCell (-1)
		c.innerHTML = climb_get_node (data, columns[name]);
	}
}


function callback_add()
{
	if ((xmlhttp_add.readyState != 4) || (xmlhttp_add.status != 200))
		return;

	var list = document.getElementById ('climb_list');
	if (!list)
		return;

	if (list.children.length === 0) {
		var columns = new Array ("ID", "Panel", "Colour", "Grade", "Setter", "Date", "Notes");
		var t = table_create (columns, true);
		list.appendChild (t);
	}

	var table = list.getElementsByTagName ('table');

	var date = entry_date.value;

	if (!climb_data)
		climb_data = new Array();

	var i;
	var id_base = climb_data.length;
	x = xmlhttp_add.responseXML.documentElement.getElementsByTagName("route");
	for (i = 0; i < x.length; i++) {
		var columns = new Array ("id", "panel", "colour", "grade", "setter", "date", "notes");
		table_add_row (table[0], columns, x[i], true);
	}

	//initialise_ticks();
	//initialise_rows();
	//buttons_update();

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

