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

	buttons_update();
}

function initialise_ticks()
{
	var content = document.getElementsByClassName ('content');

	var body = content[0].getElementsByTagName ('tbody');

	list_ticks = body[0].getElementsByTagName ('input');

	for (i = 0; i < list_ticks.length; i++) {
		list_ticks[i].checked = true;
		list_ticks[i].onclick = check_click;
	}

	var master = document.getElementById ('tick_master');
	master.checked = true;
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
