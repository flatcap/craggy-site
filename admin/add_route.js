var button_add;
var button_clear;
var button_save;
var entry_date;
var entry_routes;
var entry_setter;
var table_route;

initialise();

function initialise()
{
	button_add   = document.getElementById ('button_add');
	button_clear = document.getElementById ('button_clear');
	button_save  = document.getElementById ('button_save');

	button_add.onclick   = click_add;
	button_clear.onclick = click_clear;
	button_save.onclick  = click_save;

	entry_routes = document.getElementById ('entry');
	entry_routes.onenter    = click_add;		// Our own callback
	entry_routes.onkeypress = callback_keypress;
	entry_routes.onkeyup    = callback_keyup;
	entry_routes.focus();

	entry_date = document.getElementById ('date');
	complete_initialise (entry_date, 'date');

	entry_setter = document.getElementById ('setter');
	complete_initialise (entry_setter, 'setter');

	notify_initialise ('notify_area');
	buttons_update();
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

function click_clear()
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
	var xml = table_to_xml (table_route, 'all');
	if (xml === "")
		return;

	var params = new Object();
	params.action    = 'save';
	params.route_xml = encodeURIComponent (xml);

	ajax_get ('add_route_work.php', params, callback_save);
}


function callback_add()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var list = document.getElementById ('route_list');
	if (!list)
		return;

	if (display_errors (this))
		return;

	if (list.children.length === 0) {
		var columns = [
			{ "name": "tick",   "type": "checkbox" },
			{ "name": "panel",  "type": "input",   "title": "Panel",  "size":  3  },
			{ "name": "colour", "type": "input",   "title": "Colour", "size": 12, "validator": "colour" },
			{ "name": "grade",  "type": "input",   "title": "Grade",  "size":  5, "validator": "grade"  },
			{ "name": "setter", "type": "input",   "title": "Setter", "size": 12, "validator": "setter" },
			{ "name": "date",   "type": "input",   "title": "Date",   "size": 12, "validator": "date"   },
			{ "name": "notes",  "type": "input",   "title": "Notes",  "size": 30  }
		];

		table_route = table_create ('route', columns);
		if (table_route) {
			list.appendChild (table_route);
			var master = document.getElementById ('tick_master');
			master.checked = true;
			master.onclick = click_tick_master;
		}
	} else {
		var tlist = list.getElementsByTagName ('table');
		if (!tlist)
			return;
		table_route = tlist[0];
	}

	if (!table_route)
		return;

	var i;
	var x = this.responseXML.documentElement.getElementsByTagName ("route");
	for (i = 0; i < x.length; i++) {
		table_add_row (table_route, x[i]);
	}

	table_set_clicks (table_route, click_tick); // temporary kludge

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

	if (display_errors (this))
		return;

	x = this.responseText;
	notify_warning (x);
}


function buttons_update()
{
	var rows = (table_get_row_count (table_route) > 0);
	var sel  = (table_get_selected (table_route).length > 0);
	var text = (entry_routes.value.length > 0);

	button_set_state (button_add,   text);
	button_set_state (button_clear, sel);
	button_set_state (button_save,  rows);
}

