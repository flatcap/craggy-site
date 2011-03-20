var button_add;
var button_save;
var button_delete;
var entry_climb;
var entry_date;
var table_climb;

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
	if (!entry_climb)
		return;
	if (entry_climb.value.length === 0)
		return;
	notify_close();

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	var str  = "add_climb_work.php?";
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

	var table = document.getElementById ('route_list');
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

	var list = document.getElementById ('route_list');
	if (!list)
		return;

	var tb = document.getElementsByTagName ('tbody');
	if (!tb)
		return;
	var rows = tb[0].children;

	var xml = '<list type="climb">';
	//var columns = new Array ("tick", "panel", "colour", "grade", "type", "date", "success", "diff", "nice", "notes");
	var columns = new Array (null, "panel", "colour", null, "date", "success", "difficulty", "nice", "notes");
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
		if (table_climb)
			list.appendChild (table_climb);
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

