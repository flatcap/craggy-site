var button_add;
var button_save;
var button_delete;
var entry_climb;
var entry_date;
var table_climb;

initialise();

function initialise()
{
	button_add    = document.getElementById ('button_add');
	button_save   = document.getElementById ('button_save');
	button_delete = document.getElementById ('button_delete');

	button_add.onclick    = click_add;
	button_save.onclick   = click_save;
	button_delete.onclick = click_delete;

	entry_climb = document.getElementById ('entry');
	entry_climb.onkeypress = callback_catch_enter;
	entry_climb.focus();

	entry_date    = document.getElementById ('date');
	entry_climber = document.getElementById ('climber');

	notify_initialise ('notify_area');

	buttons_update();
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
		xml += '<' + o + '>' + escape (obj[o]) + '</' + o + '>';
	}

	xml += '</' + name + '>';

	return xml;
}


function click_add()
{
	if (!entry_climb)
		return;
	if (entry_climb.value.length === 0)
		return;
	notify_close();

	var params = new Object();
	params.action  = 'add';
	params.climbs  = encodeURIComponent (entry_climb.value);
	params.date    = encodeURIComponent (entry_date.value);
	params.climber = encodeURIComponent (entry_climber.value);

	ajax_get ('add_climb_work.php', params, callback_add);
}

function click_delete()
{
	notify_close();

	var rows = table_get_selected (table_climb);
	if (!rows)
		return;

	for (var i = 0; i < rows.length; i++) {
		table_row_delete (table_climb, rows[i]);
	}

	entry_climb.focus();	// not ideal
	buttons_update();
}

function click_save()
{
	notify_close();

	var xml = table_to_xml (table_climb, 'all');
	if (xml === "")
		return;

	var params = new Object();
	params.action    = 'save';
	params.climb_xml = encodeURIComponent (xml);
	params.climber   = encodeURIComponent (entry_climber.value);

	ajax_get ('add_climb_work.php', params, callback_save);
}


function display_errors (xml)
{
	var x = xml.responseXML.documentElement.getElementsByTagName("error");
	var errstr = "";
	for (i = 0; i < x.length; i++) {
		var e = x[i];
		if (e && e.childNodes) {
			errstr += e.childNodes[0].nodeValue + "<br>";
		}
	}

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
			{ "name": "tick",       "type": "checkbox" },
			{ "name": "panel",      "type": "text",    "title": "Panel",      "size":  3 },
			{ "name": "colour",     "type": "text",    "title": "Colour",     "size":  3 },
			{ "name": "grade",      "type": "text",    "title": "Grade",      "size":  5},
			{ "name": "date",       "type": "input",   "title": "Date",       "size":  8, "validator": "date"       },
			{ "name": "success",    "type": "input",   "title": "Success",    "size":  8, "validator": "success"    },
			{ "name": "difficulty", "type": "input",   "title": "Difficulty", "size":  8, "validator": "difficulty" },
			{ "name": "nice",       "type": "input",   "title": "Nice",       "size":  3, "validator": "nice"       },
			{ "name": "notes",      "type": "input",   "title": "Notes",      "size": 10 }
		];

		table_climb = table_create ('climb', columns);
		if (table_climb) {
			list.appendChild (table_climb);
			var master = document.getElementById ('tick_master');
			master.checked = true;
			master.onclick = tick_master_click;
		}
	} else {
		var tlist = list.getElementsByTagName ('table');
		if (!tlist)
			return;
		table_climb = tlist[0];
	}

	if (!table_climb)
		return;

	var date = entry_date.value;

	var i;
	var x = this.responseXML.documentElement.getElementsByTagName("route");
	for (i = 0; i < x.length; i++) {
		table_add_row (table_climb, x[i]);
	}

	entry_climb.value = "";
	entry_climb.focus();
	table_set_clicks (table_climb, check_click); // temporary kludge
	buttons_update();
}

function callback_save()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	if (display_errors(this))
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


function tick_master_click()
{
	table_select_all (table_climb, this.checked);
	buttons_update();
}

function check_click(e)
{
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
	var rows = (table_get_row_count (table_climb) > 0);
	var sel  = (table_get_selected (table_climb).length > 0);
	var text = (entry_climb.value.length > 0);

	button_set_state (button_add,    text);		// some text in entry
	button_set_state (button_delete, sel);		// some ticked rows
	button_set_state (button_save,   rows);		// some rows in table
}

