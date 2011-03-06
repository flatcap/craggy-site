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
	alert ('save');
}

function click_reset()
{
	alert ('reset');
}

function click_cancel()
{
	alert ('cancel');
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
		c.innerHTML = climb_get_node (data, columns[name]);
	}
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
		tbcc[1].appendChild (inp);
		inp.value = val;
		inp.size = 10;
		inp.id = "panel" + i;
		input_initialise (inp.id, "panel");

		val = tbcc[2].innerHTML;
		tbcc[2].innerHTML = "";
		var inp = document.createElement ('input');
		inp.type = "text";
		tbcc[2].appendChild (inp);
		inp.value = val;
		inp.size = 10;
		inp.id = "colour" + i;
		input_initialise (inp.id, "colour");

		val = tbcc[3].innerHTML;
		tbcc[3].innerHTML = "";
		var inp = document.createElement ('input');
		inp.type = "text";
		tbcc[3].appendChild (inp);
		inp.value = val;
		inp.size = 10;
		inp.id = "grade" + i;
		input_initialise (inp.id, "grade");
	}

	//initialise_ticks();
	//initialise_rows();
	//buttons_update();

	entry_climb.value = "";
	// set focus
}

function callback_save()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var response = this.responseText;
	if (response.length === 0)
		return;

	alert (response);
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
}


