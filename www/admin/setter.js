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

var list_ticks;

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
	alert ('edit');
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
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = callback_delete_query;
	xmlhttp.open ("GET", "work.php?action=delete_query&data=" + str, true);
	xmlhttp.send();
}

function click_list()
{
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = callback_list;
	xmlhttp.open ("GET", "work.php?action=list");
	xmlhttp.send();
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
	if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
		var response = xmlhttp.responseText;
		if (response.length == 0)
			return;

		if (!confirm (response))
			return;
		return;
		var bra = response.indexOf ('(');
		var ket = response.indexOf (')', bra);

		if ((bra == -1) || (ket == -1)) {
			date_match.innerHTML = response;
		} else {
			date_match.innerHTML = response.substring (bra+1, ket);
		}
	}
}

function callback_list()
{
	if ((xmlhttp.readyState != 4) || (xmlhttp.status != 200))
		return;

	var txt = "<table cellspacing=0 border=1>" +
		"<thead>" +
		"<tr>" +
		"<th><input type='checkbox' id='tick_master'></th>" +
		"<th>ID</th>" +
		"<th>Setter</th>" +
		"<th>Count</th>" +
		"</tr>" +
		"</thead>" +
		"<tbody>";


	x = xmlhttp.responseXML.documentElement.getElementsByTagName("setter");
	for (i = 0; i < x.length; i++) {

		id    = route_get_node (x[i], "id");
		name  = route_get_node (x[i], "name");
		count = route_get_node (x[i], "count");

		txt += "<tr>";
		txt += "<td><input type='checkbox' id='id_" + id + "'></td>";
		txt += "<td>" + id + "</td>";
		txt += "<td>" + name + "</td>";
		txt += "<td>" + count + "</td>";
		txt += "</tr>";
	}

	txt += "</tbody>" +
	       "</table>";

	var table = document.getElementById ('setter_table');
	table.innerHTML = txt;

	button_set_state (button_list, false);
	initialise_ticks();
	initialise_rows();
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
}


