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

var button_add;
var button_edit;
var button_delete;
var list_ticks;

initialise_ticks();
initialise_rows();
initialise_buttons();

function initialise_buttons()
{
	button_add = document.getElementById ('button_add');
	button_edit = document.getElementById ('button_edit');
	button_delete = document.getElementById ('button_delete');

	button_add.onclick = click_add;
	button_edit.onclick = click_edit;
	button_delete.onclick = click_delete;

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
	xmlhttp.onreadystatechange = delete_callback;
	xmlhttp.open ("GET", "work.php?delete=" + str, true);
	xmlhttp.send();
}


function delete_callback()
{
	if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
		var response = xmlhttp.responseText;
		if (response.length == 0)
			return;

		alert (response);
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


