var button_add;
var button_save;
var button_delete;
var entry_routes;
var entry_date;
var entry_setter;
var table_route;

initialise();

function initialise()
{
	button_add    = document.getElementById ('button_add');
	button_save   = document.getElementById ('button_save');
	button_delete = document.getElementById ('button_delete');

	button_add.onclick    = click_add;
	button_save.onclick   = click_save;
	button_delete.onclick = click_delete;

	entry_routes = document.getElementById ('entry');
	entry_routes.onkeypress = callback_catch_enter;
	entry_routes.onkeyup    = callback_keyup;
	entry_routes.focus();

	entry_date   = document.getElementById ('date');
	complete_initialise ('date', 'date');

	entry_setter = document.getElementById ('setter');
	complete_initialise ('setter', 'setter');

	notify_initialise ('notify_area');

	buttons_update();
}


function htmlentities(str)
{
	return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
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


function get_row (row, columns)
{
	var obj = new Object();

	var children = row.children;
	if (!children)
		return null;

	if (children.length != columns.length)
		return null;

	for (var i = 0; i < children.length; i++) {
		if (columns[i] === null)
			continue;

		if (children[i].childElementCount === 0) {
			obj[columns[i]] = children[i].innerHTML;		// Just text
		} else {
			var f = children[i].firstChild;
			if (f.nodeName.toLowerCase() == 'input') {
				if (f.type.toLowerCase() == 'checkbox') {
					obj[columns[i]] = children[i].firstChild.checked;	// A tickbox
				} else if (f.type.toLowerCase() == 'text') {
					obj[columns[i]] = children[i].firstChild.value;		// A textbox
				}
			}
		}
	}

	return obj;
}

function obj_to_xml (name, obj)
{
	var xml = '<' + name + '>';

	for (o in obj) {
		if (obj[o].length === 0)
			continue;
		xml += '<' + o + '>' + encodeURIComponent (obj[o]) + '</' + o + '>';
	}

	xml += '</' + name + '>';

	return xml;
}


function click_add()
{
	if (!entry_routes)
		return;
	if (entry_routes.value.length === 0)
		return;
	notify_close();

	var params = new Object();
	params.action = 'add';
	params.date   = encodeURIComponent (entry_date.value);
	params.setter = encodeURIComponent (entry_setter.value);
	params.routes = encodeURIComponent (entry_routes.value);

	ajax_get ('add_route_work.php', params, callback_add);
}

function click_delete()
{
	notify_close();

	var rows = table_get_selected (table_route);
	if (!rows)
		return;

	for (var i = 0; i < rows.length; i++) {
		table_row_delete (table_route, rows[i]);
	}

	buttons_update();
}

function click_save()
{
	notify_close();

	var list = document.getElementById ('route_list');
	if (!list)
		return;

	var tb = document.getElementsByTagName ('tbody');
	if (!tb)
		return;
	var rows = tb[0].children;

	var xml = '<list type="climb">';
	var columns = new Array (null, 'panel', 'colour', 'grade', 'setter', 'date', 'notes');
	for (var i = 0; i < rows.length; i++) {
		var r = get_row (rows[i], columns);
		xml += obj_to_xml ('route', r);
	}
	xml += '</list>';

	var params = new Object();
	params.action    = 'save';
	params.route_xml = encodeURIComponent (xml);

	ajax_get ('add_route_work.php', params, callback_save);
}


function display_errors (xml)
{
	var errstr = xml_get_errors (xml.responseXML.documentElement);
	if (errstr.length > 0) {
		notify_message (errstr);
		return true;
	}

	return false;
}


function callback_add()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var list = document.getElementById ('route_list');
	if (!list)
		return;

	if (display_errors(this))
		return;

	if (list.children.length === 0) {
		var columns = [
			{ "name": "tick",   "type": "checkbox" },
			{ "name": "panel",  "type": "input",   "title": "Panel",  "size":  3  },
			{ "name": "colour", "type": "input",   "title": "Colour", "size": 12, "validator": "colour" },
			{ "name": "grade",  "type": "input",   "title": "Grade",  "size":  5, "validator": "grade"  },
			{ "name": "setter", "type": "input",   "title": "Setter", "size": 12, "validator": "setter" },
			{ "name": "date",   "type": "input",   "title": "Date",   "size": 12, "validator": "date"   },
			{ "name": "notes",  "type": "input",   "title": "Notes",  "size": 12  }
		];

		table_route = table_create ('route', columns);
		if (table_route)
			list.appendChild (table_route);
	} else {
		var tlist = list.getElementsByTagName ('table');
		if (!tlist)
			return;
		table_route = tlist[0];
	}

	if (!table_route)
		return;

	var i;
	var x = this.responseXML.documentElement.getElementsByTagName("route");
	for (i = 0; i < x.length; i++) {
		table_add_row (table_route, x[i]);
	}

	var master = document.getElementById ('tick_master');
	master.checked = true;
	master.onclick = tick_master_click;
	buttons_update();

	// empty route entry
	entry_routes.value = "";
	// set focus
	entry_routes.focus();
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

function callback_keyup (e)
{
	buttons_update();
	return true;
}


function check_click(e)
{
	this.checked = !this.checked;
	buttons_update();
}

function tick_master_click()
{
	table_select_all (table_route, this.checked);
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
	var rows     = (table_get_row_count (table_route) > 0);
	var text     = (entry_routes.value.length > 0);
	var selected = table_get_selected (table_route);
	if (selected) {
		selected = (selected.length > 0);
	}

	button_set_state (button_add,    text);
	button_set_state (button_save,   rows);
	button_set_state (button_delete, selected);
}

