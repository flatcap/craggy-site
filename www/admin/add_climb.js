var button_add;
var button_save;
var button_delete;
var entry_climb;
var entry_date;

initialise_buttons();

function initialise_buttons()
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

	//buttons_update();
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
	notify_close();
	var str = entry_climb.value;
	if (str.length === 0)
		return;
	str = encodeURI(str);

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	str  = "add_climb_work.php?";
	str += "action=add";
	str += "&climbs=" + encodeURIComponent (entry_climb.value);
	str += "&date=" + encodeURIComponent (entry_date.value);
	str += "&climber=" + encodeURIComponent (entry_climber.value);
	x.onreadystatechange = callback_add;
	x.open ("GET", str);
	x.send();
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

	var climb_data;
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

	var list = document.getElementById ('climb_list');
	if (!list)
		return;

	var tb = document.getElementsByTagName ('tbody');
	if (!tb)
		return;
	var rows = tb[0].children;

	var xml = '<list type="climb">';
	//var columns = new Array ("tick", "panel", "colour", "grade", "type", "date", "success", "diff", "nice", "notes");
	var columns = new Array (null, "panel", "colour", null, null, "date", "success", "difficulty", "nice", "notes");
	for (var i = 0; i < rows.length; i++) {
		var r = get_row (rows[i], columns);
		xml += obj_to_xml ('climb', r);
	}
	xml += '</list>';

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	var str  = "add_climb_work.php?";
	str += "action=save";
	str += "&climb_xml=" + encodeURIComponent (xml);
	str += "&climber=" + encodeURIComponent (entry_climber.value);
	x.open ("GET", str);
	x.onreadystatechange = callback_save;
	x.setRequestHeader ("Content-Type", "text/plain");
	x.send();

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
	var c = r.insertCell (-1);
	var i = document.createElement ('input');

	i.id   = 'tick_master';
	i.type = 'checkbox';
	c.appendChild (i);
	r.appendChild (c);

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


function callback_add()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	//var x = this.responseText; notify_message (x); return;

	var list = document.getElementById ('climb_list');
	if (!list)
		return;

	if (display_errors(this))
		return;

	var columns3 = [
		{ "name": "tick", "type": "checkbox" },
		{ "name": "id", "type": "hidden" },
		{ "name": "panel", "type": "text", "size": 3 },
		{ "name": "colour", "type": "text", "size": 3, "validator": "lookup_colour.php" }
	];

	var columns;
	if (list.children.length === 0) {
		//columns = new Array ("ID", "Panel", "Colour", "Grade", "Type", "Date", "Success", "Diff", "Nice", "Notes", "Errors");
		columns = new Array ("", "", "", "", "Date", "Success", "Diff", "Nice", "Notes");
		var t = table_create (columns, true);
		list.appendChild (t);
	}

	var table = list.getElementsByTagName ('table');

	var date = entry_date.value;

	var i;
	var x = this.responseXML.documentElement.getElementsByTagName("route");
	for (i = 0; i < x.length; i++) {
		columns = new Array ("panel", "colour", "grade", "climb_type", "date", "success", "difficulty", "nice", "notes");
		table_add_row (table[0], columns, x[i], true);
	}

	/* messing about with auto-complete */
	var tb = list.getElementsByTagName ('tbody');
	var tbc = tb[0].children;

	for (i = 0; i < tbc.length; i++) {	// number of <tr>
		tbcc = tbc[i].children;
		var c = tbcc.length;
		var val;

		val = tbcc[5].innerHTML;
		tbcc[5].innerHTML = "";
		var inp  = document.createElement ('input');
		inp.type = "text";
		tbcc[5].appendChild (inp);
		inp.value = val;
		inp.size = 10;
		inp.id = "date" + i;
		//inp.height = 22;
		input_initialise (inp.id, "lookup_date.php");

		val = tbcc[6].innerHTML;
		tbcc[6].innerHTML = "";
		var inp  = document.createElement ('input');
		inp.type = "text";
		tbcc[6].appendChild (inp);
		inp.value = val;
		inp.size = 10;
		inp.id = "success" + i;
		//inp.height = 22;
		input_initialise (inp.id, "lookup_success.php");

		val = tbcc[9].innerHTML;
		tbcc[9].innerHTML = "";
		inp  = document.createElement ('input');
		inp.type = "text";
		tbcc[9].appendChild (inp);
		inp.value = val;
		inp.size = 20;
		inp.id = "nice" + i;
		//inp.height = 22;

		val = tbcc[8].innerHTML;
		tbcc[8].innerHTML = "";
		inp  = document.createElement ('input');
		inp.type = "text";
		tbcc[8].appendChild (inp);
		inp.value = val;
		inp.size = 6;
		inp.id = "nice" + i;
		//inp.height = 22;
		input_initialise (inp.id, "lookup_nice.php");

		val = tbcc[7].innerHTML;
		tbcc[7].innerHTML = "";
		inp  = document.createElement ('input');
		inp.type = "text";
		tbcc[7].appendChild (inp);
		inp.value = val;
		inp.size = 10;
		inp.id = "difficulty" + i;
		//inp.height = 22;
		input_initialise (inp.id, "lookup_difficulty.php");

		val = tbcc[2].innerHTML;
		tbcc[2].innerHTML = "";
		inp  = document.createElement ('input');
		inp.type = "text";
		tbcc[2].appendChild (inp);
		inp.value = val;
		inp.size = 10;
		inp.id = "colour" + i;
		//inp.height = 22;
		input_initialise (inp.id, "lookup_colour.php");

		//inp.onkeypress = inp_keypress;
		//inp.onblur     = inp_blur;
		var z = 1;
	}
	/**/

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


function inp_keypress (e)
{
	if (e.keyCode == 13) {
		//alert ("enter pressed");
		return false;
	}

	return true;
}

function inp_blur()
{
	//alert ("blur");
}

