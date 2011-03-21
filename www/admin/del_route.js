var button_clear;
var button_delete;
var button_list;
var entry_date;
var entry_panel;
var table_route;

initialise();

function initialise()
{
	button_clear  = document.getElementById ('button_clear');
	button_delete = document.getElementById ('button_delete');
	button_list   = document.getElementById ('button_list');

	button_clear.onclick  = click_clear;
	button_delete.onclick = click_delete;
	button_list.onclick   = click_list;

	entry_date = document.getElementById ('date');
	complete_initialise ('date', 'date');

	entry_panel = document.getElementById ('panel');
	entry_panel.onenter    = click_list;		// Our own callback
	entry_panel.onkeypress = callback_keypress;
	entry_panel.onkeyup    = callback_keyup;
	entry_panel.focus();

	notify_initialise ('notify_area');
	buttons_update();
}


function click_clear()
{
	if (!table_route)
		return;

	var rows = table_get_selected (table_route);
	if (!rows)
		return;

	for (var i = 0; i < rows.length; i++) {
		table_row_delete (table_route, rows[i]);
	}

	entry_panel.focus();
	buttons_update();
}

function click_delete()
{
	var ids = table_get_selected (table_route);
	if (!ids || (ids.length === 0)) {
		buttons_update();	// Shouldn't happen
		return;
	}

	if (!confirm ("About to delete " + ids.length + " routes.\nAre you sure?")) {
		return;
	}

	var params = new Object();
	params.action = 'delete';
	params.date   = entry_date.value;	// XXX encode it?
	params.data   = ids.join (',');

	ajax_get ('del_route_work.php', params, callback_delete);
}

function click_list()
{
	var params = new Object();
	params.action = 'list';
	params.data   = encodeURI (entry_panel.value);

	ajax_get ('del_route_work.php', params, callback_list);
}


function callback_delete()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var response = this.responseText;
	if (response.length === 0)
		return;

	alert (response);

	var table = document.getElementById ('route_list');
	table.innerHTML = "";
	buttons_update();
}

function callback_list()
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
			{ "name": "panel",  "type": "text",    "title": "Panel",  "size":  3 },
			{ "name": "colour", "type": "text",    "title": "Colour", "size": 12 },
			{ "name": "grade",  "type": "text",    "title": "Grade",  "size":  5 }
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

	entry_panel.value = "";

	buttons_update();
}


function buttons_update()
{
	var sel  = (table_get_selected (table_route).length > 0);
	var text = (entry_panel.value.length > 0);

	button_set_state (button_clear,  sel);		// some ticked rows
	button_set_state (button_delete, sel);		// some ticked rows
	button_set_state (button_list,   text);		// some text in entry
}


