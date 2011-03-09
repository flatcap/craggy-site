var button_list;
var button_save;
var button_reset;
var button_cancel;

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
		return null
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

	var list = document.getElementById ('route_list');
	if (!list)
		return;

	var tb = document.getElementsByTagName ('tbody');
	if (!tb)
		return;
	var rows = tb[0].children;

	var xml;
	xml  = "<?xml version='1.0'?>\n";
	xml += "<?xml-stylesheet type='text/xsl' href='route.xsl'?>\n";
	xml += "<list type='route'>";
	//var columns = new Array ("tick", "panel", "colour", "grade", "type", "date", "success", "diff", "nice", "notes");
	var columns = new Array ("id", "panel", "colour", "grade");
	for (var i = 0; i < rows.length; i++) {
		var r = get_row (rows[i], columns, true);
		if (r) {
			// only send the rows that have changed
			xml += obj_to_xml ('route', r);
		}
	}
	xml += '</list>';

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
	alert ('reset');
}

function click_cancel()
{
	alert ('cancel');
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

	var r = t.insertRow (0);

	var c;
	if (ticklist) {
		c = document.createElement ('th');
		var i = document.createElement ('input');

		i.id   = 'tick_master';
		i.type = 'checkbox';
		c.appendChild (i);
		r.appendChild (c);
	}

	var name;
	for (name in columns) {
		c = document.createElement ('th');
		c.innerHTML = columns[name];
		r.appendChild (c);
	}

	return t;
}

function table_add_row (table, columns, data, tick)
{
	if (!table || !columns || !data)
		return;

	var tb = table.getElementsByTagName ('tbody');
	var r = document.createElement ('tr');
	tb[0].appendChild (r);

	var c;
	if (tick) {
		c = r.insertCell (-1);
		var i = document.createElement ('input');

		i.id   = 'tick_master';
		i.type = 'checkbox';
		c.appendChild (i);
		r.appendChild (c);
	}

	for (name in columns) {
		c = r.insertCell (-1);
		c.innerHTML = xml_get_node (data, columns[name]);
	}
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

	var columns3 = [
		{ "name": "tick", "type": "checkbox" },
		{ "name": "id", "type": "hidden" },
		{ "name": "panel", "type": "text", "size": 3 },
		{ "name": "colour", "type": "text", "size": 3, "validator": "colour" },
		{ "name": "grade", "type": "text", "size": 3 }
	];

	var columns;
	if (list.children.length === 0) {
		//columns = new Array ("ID", "Panel", "Colour", "Grade", "Type", "Date", "Success", "Diff", "Nice", "Notes", "Errors");
		columns = new Array ("ID", "Panel", "Colour", "Grade");
		var t = table_create (columns, false);
		list.appendChild (t);
	}

	var table = list.getElementsByTagName ('table');

	var i;
	var x = this.responseXML.documentElement.getElementsByTagName("route");
	for (i = 0; i < x.length; i++) {
		columns = new Array ("id", "panel", "colour", "grade");
		table_add_row (table[0], columns, x[i], false);
	}

	/* messing about with auto-complete */
	var tb = list.getElementsByTagName ('tbody');
	var tbc = tb[0].children;

	for (i = 0; i < tbc.length; i++) {	// number of <tr>
		tbcc = tbc[i].children;
		var c = tbcc.length;
		var val;

		val = tbcc[1].innerHTML;
		tbcc[1].innerHTML = "";
		inp = document.createElement ('input');
		inp.type = "text";
		inp.value = val;
		inp.size = 10;
		inp.id = "panel" + i;
		inp.original = val;
		tbcc[1].appendChild (inp);
		input_initialise (inp.id, "panel");

		val = tbcc[2].innerHTML;
		tbcc[2].innerHTML = "";
		var inp = document.createElement ('input');
		inp.type = "text";
		inp.value = val;
		inp.size = 10;
		inp.id = "colour" + i;
		inp.original = val;
		tbcc[2].appendChild (inp);
		input_initialise (inp.id, "colour");

		val = tbcc[3].innerHTML;
		tbcc[3].innerHTML = "";
		var inp = document.createElement ('input');
		inp.type = "text";
		inp.value = val;
		inp.size = 10;
		inp.id = "grade" + i;
		inp.original = val;
		tbcc[3].appendChild (inp);
		input_initialise (inp.id, "grade");
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

	var table = document.getElementById ('route_list');
	table.innerHTML = "";
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


