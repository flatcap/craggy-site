var button_list;
var button_save;
var button_reset;
var button_cancel;
var table_route;
var entry_panel;

initialise();

function initialise()
{
	button_list   = document.getElementById ('button_list');
	button_save   = document.getElementById ('button_save');
	button_reset  = document.getElementById ('button_reset');
	button_cancel = document.getElementById ('button_cancel');

	button_list.onclick   = click_list;
	button_save.onclick   = click_save;
	button_reset.onclick  = click_reset;
	button_cancel.onclick = click_cancel;

	entry_panel = document.getElementById ('entry');
	entry_panel.onkeypress = callback_keypress;
	entry_panel.onkeyup    = callback_keyup;
	entry_panel.focus();

	notify_initialise ('notify_area');

	buttons_update();
}


function click_list()
{
	var params = new Object();
	params.action = 'list';
	params.data   = entry_panel.value;

	ajax_get ('edit_route_work.php', params, callback_list);
}

function click_save()
{
	notify_close();

	var xml = table_to_xml (table_route, 'different');
	if (xml === "")
		return;

	var params = new Object();
	params.action    = 'save';
	params.route_xml = encodeURIComponent (xml);

	ajax_get ('edit_route_work.php', params, callback_save);
}

function click_reset()
{
	var table = document.getElementById ('route_list');
	var count = table_get_row_count (table);
	alert ('reset ' + count);

	for (var i = 0; i < count; i++) {
		table_reset_row (table, i);
	}
}

function click_cancel()
{
	alert ('cancel');
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


function callback_keypress (e)
{
	if (e.keyCode == 13) {
		click_list();
		return false;
	}

	return true;
}

function callback_keyup (e)
{
	buttons_update();
	return true;
}

function callback_list()
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
			{ "name": "panel",  "type": "text",    "title": "Panel",  "size":  3  },
			{ "name": "colour", "type": "input",   "title": "Colour", "size": 12, "validator": "colour" },
			{ "name": "grade",  "type": "input",   "title": "Grade",  "size":  5, "validator": "grade"  }
		];

		table_route = table_create ('route', columns);
		if (table_route) {
			list.appendChild (table_route);
			var master = document.getElementById ('tick_master');
			master.checked = true;
			master.onclick = tick_master_click;
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
	var x = this.responseXML.documentElement.getElementsByTagName("route");
	for (i = 0; i < x.length; i++) {
		table_add_row (table_route, x[i]);
	}

	table_set_clicks (table_route, check_click); // temporary kludge
	entry_panel.value = "";
	entry_panel.focus();
	buttons_update();
}

function callback_save()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var response = this.responseText;
	if (response.length === 0)
		return;

	notify_message (response);
}


function tick_master_click()
{
	table_select_all (table_route, this.checked);
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
	var rows = (table_get_row_count (table_route) > 0);
	var sel  = (table_get_selected (table_route).length > 0);
	var text = (entry_panel.value.length > 0);

	button_set_state (button_list,   text);		// some text in entry
	button_set_state (button_reset,  sel);		// some ticked rows
	button_set_state (button_save,   rows);		// some rows in table
	button_set_state (button_cancel, rows);		// some rows in table
}


