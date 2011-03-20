var button_list;
var button_save;
var button_reset;
var button_cancel;
var table_route;

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
	entry_panel.focus();

	notify_initialise ('notify_area');

	buttons_update();
}


function get_row (row, columns, diff)
{
	var obj = new Object();

	var changed = false;

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
				var orig = f.original;
				if (f.type.toLowerCase() == 'checkbox') {
					obj[columns[i]] = f.checked;		// A tickbox
					if (orig != f.checked)
						changed = true;
				} else if (f.type.toLowerCase() == 'text') {
					obj[columns[i]] = f.value;		// A textbox
					if (orig != f.value)
						changed = true;
				}
			}
		}
	}

	if (diff && !changed)
		return null;
	else
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


function click_list()
{
	var str = entry_panel.value;

	//str = encodeURI(str);

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	x.onreadystatechange = callback_list;
	x.open ("GET", "edit_route_work.php?action=list&data=" + str);
	x.send();
}

function click_save()
{
	notify_close();

	var xml = table_to_xml (table_route, 'different');
	if (xml == "")
		return;

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	var str  = "edit_route_work.php?";
	str += "action=save";
	str += "&route_xml=" + encodeURIComponent (xml);
	x.open ("GET", str);
	x.onreadystatechange = callback_save;
	x.setRequestHeader ("Content-Type", "text/plain");
	x.send();
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

	//initialise_ticks();
	//initialise_rows();
	//buttons_update();

	//entry_climb.value = "";
	// set focus
}

function callback_save()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var response = this.responseText;
	if (response.length === 0)
		return;

	notify_message (response);
	return;

	/*
	var table = document.getElementById ('route_list');
	table.innerHTML = "";
	buttons_update();
	*/
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
	/*
	var set = false;
	for (i = 0; i < list_ticks.length; i++) {
		if (list_ticks[i].checked) {
			set = true;
			break;
		}
	}

	button_set_state (button_list, true);
	button_set_state (button_save, set);
	button_set_state (button_reset, set);
	button_set_state (button_cancel, set);
	*/
}


