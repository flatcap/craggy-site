// To do
//
// Find buttons
//     Add - enabled - always
//     Edit - enabled - on selection
//     Delete - enabled - on selection
//
// Find master checkbox
//     Set all others to match
//     On toggle - set others
//
// Find checkboxes
//     Set onClick hander for each
//     onClick check if *any* are selected and enable/disable buttons
//
// Hover highlight on <tr>
//
// tr.onclick find and toggle checkbox
//
// Delete:
//     highlight setters
//     click delete
//         client->server: delete query
//         are you sure - 2 setters, 402 routes?
//         client->server: delete commit
//         2 setters, 402 routes deleted
//         client->server: list setters
//         update grid
//
// function to decode xml into nested arrays - how?
// function to encode nested arrays into xml - how?

var button_add;
var button_edit;
var button_delete;
var button_list;
var button_save;
var button_cancel;

var list_ticks;
var setter_data;

//initialise_ticks();
//initialise_rows();
initialise_buttons();

function initialise_buttons()
{
	button_add = document.getElementById ('button_add');
	button_edit = document.getElementById ('button_edit');
	button_delete = document.getElementById ('button_delete');
	button_list = document.getElementById ('button_list');

	button_add.onclick = click_add;
	button_edit.onclick = click_edit;
	button_delete.onclick = click_delete;
	button_list.onclick = click_list;

	buttons_update();
}

function initialise_ticks()
{
	var content = document.getElementsByClassName ('content');

	var body = content[0].getElementsByTagName ('tbody');

	list_ticks = body[0].getElementsByTagName ('input');

	for (i = 0; i < list_ticks.length; i++) {
		list_ticks[i].checked = false;
		list_ticks[i].onclick = check_click;
	}

	var master = document.getElementById ('tick_master');
	master.checked = false;
	master.onclick = tick_master_click;
}

function initialise_rows()
{
	var content = document.getElementsByClassName ('content');

	var body = content[0].getElementsByTagName ('tbody');

	var trs = body[0].getElementsByTagName ('tr');
	for (i = 0; i < trs.length; i++) {
		trs[i].onclick = row_clicked;
	}

}


function click_add()
{
	alert ('add');
}

function click_edit()
{
	var work = document.getElementById ("work_area");
	var txt = "";

	txt += "<table border=0 cellspacing=0 cellpadding=0>";
	txt += "<thead>";
	txt += "<tr>";
	txt += "<th>ID</th>";
	txt += "<th>Initials</th>";
	txt += "<th>First Name</th>";
	txt += "<th>Surname</th>";
	txt += "</tr>";
	txt += "</thead>";
	txt += "<tbody>";

	for (var i = 0; i < list_ticks.length; i++) {
		if (!list_ticks[i].checked)
			continue;
		id = list_ticks[i].id.substring(3);
		txt += "<tr>";
		txt += "<td>" + id + "</td>";
		txt += "<td><input type='text' size='4' value='"  + setter_data[id]['initials']   + "'></td>";
		txt += "<td><input type='text' size='20' value='" + setter_data[id]['first_name'] + "'></td>";
		txt += "<td><input type='text' size='20' value='" + setter_data[id]['surname']    + "'></td>";
		txt += "</tr>";
	}
	txt += "</tbody>";
	txt += "</table>";

	txt += "<div class='buttons'>";
	txt += "<br>";
	txt += "<input type='submit' type='button' id='button_save' value='Save'>";
	txt += "&nbsp;";
	txt += "<input type='submit' type='button' id='button_cancel' value='Cancel'>";

	work.innerHTML = txt;

	button_save   = document.getElementById ('button_save');
	button_cancel = document.getElementById ('button_cancel');

	button_save.onclick   = callback_save;
	button_cancel.onclick = callback_cancel;

	// Disable edit, delete, list
}

function click_delete()
{
	var ids = new Array();
	for (i = 0; i < list_ticks.length; i++) {
		if (list_ticks[i].checked) {
			ids.push (list_ticks[i].id.substring(3));
		}
	}

	var str = ids.join(',');
	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	x.onreadystatechange = callback_delete_query;
	x.open ("GET", "setter_work.php?action=delete_query&data=" + str, true);
	x.send();
}

function click_list()
{
	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	x.onreadystatechange = callback_list;
	x.open ("GET", "setter_work.php?action=list");
	x.send();
}


function click_cancel()
{
}

function click_save()
{
}


function route_get_node (node, name)
{
	try {
		return node.getElementsByTagName(name)[0].firstChild.nodeValue;
	} catch (er) {
	}

	return "";
	//return "<td>" + txt + "</td>";
}

function callback_delete_query()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var response = this.responseText;
	if (response.length === 0)
		return;

	if (!confirm (response))
		return;

	var ids = new Array();
	for (i = 0; i < list_ticks.length; i++) {
		if (list_ticks[i].checked) {
			ids.push (list_ticks[i].id.substring(3));
		}
	}

	var str = ids.join(',');

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	x.onreadystatechange = callback_delete;
	x.open ("GET", "setter_work.php?action=delete&data=" + str, true);
	x.send();
}

function callback_delete()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var response = this.responseText;
	if (response.length === 0)
		return;

	alert (response);
	click_list();
}

function callback_list()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var txt = "<table cellspacing=0 border=1>" +
		"<thead>" +
		"<tr>" +
		"<th><input type='checkbox' id='tick_master'></th>" +
		"<th>ID</th>" +
		"<th>Initials</th>" +
		"<th>First Name</th>" +
		"<th>Surname</th>" +
		"<th>Count</th>" +
		"</tr>" +
		"</thead>" +
		"<tbody>";


	x = this.responseXML.documentElement.getElementsByTagName("setter");
	setter_data = new Array();
	for (i = 0; i < x.length; i++) {
		var setter = new Array();
		var id = route_get_node (x[i], "id");
		setter['id']         = id;
		setter['initials']   = route_get_node (x[i], "initials");
		setter['first_name'] = route_get_node (x[i], "first_name");
		setter['surname']    = route_get_node (x[i], "surname");
		setter['count']      = route_get_node (x[i], "count");
		setter_data[id] = setter;
	}
	for (s in setter_data) {
		id         = setter_data[s]['id'];
		initials   = setter_data[s]['initials'];
		first_name = setter_data[s]['first_name'];
		surname    = setter_data[s]['surname'];
		count      = setter_data[s]['count'];

		txt += "<tr>";
		txt += "<td><input type='checkbox' id='id_" + id + "'></td>";
		txt += "<td>" + id + "</td>";
		txt += "<td>" + initials + "</td>";
		txt += "<td>" + first_name + "</td>";
		txt += "<td>" + surname + "</td>";
		txt += "<td>" + count + "</td>";
		txt += "</tr>";
	}

	txt += "</tbody>" +
	       "</table>";

	var table = document.getElementById ('setter_table');
	table.innerHTML = txt;

	//button_set_state (button_list, false);
	initialise_ticks();
	initialise_rows();
	buttons_update();
}


function callback_save()
{
	alert ('save');
}

function callback_cancel()
{
	alert ('cancel');
}


function check_click(e)
{
	this.checked = !this.checked;
	buttons_update();
}

function tick_master_click()
{
	for (i = 0; i < list_ticks.length; i++) {
		list_ticks[i].checked = this.checked;
	}
	buttons_update();
}

function row_clicked()
{
	var ticks = this.getElementsByTagName ('input');

	ticks[0].checked = !ticks[0].checked;
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

	button_set_state (button_add, true);
	button_set_state (button_edit, set);
	button_set_state (button_delete, set);
	button_set_state (button_list, true);
}


