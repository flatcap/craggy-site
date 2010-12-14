// To do
// Find buttons
//     Add - enabled - always
//     Edit - enabled - on selection
//     Delete - enabled - on selection
// Find master checkbox
//     Set all others to match
//     On toggle - set others
// Find checkboxes
//     Set onClick hander for each
//     onClick check if *any* are selected and enable/disable buttons
// Hover highlight on <tr>
// tr.onclick find and toggle checkbox

var content = document.getElementsByClassName ('content');

var body = content[0].getElementsByTagName ('tbody');

var tables = body[0].getElementsByTagName ('input');

for (i = 0; i < tables.length; i++) {
	tables[i].checked = true;
}

var trs = body[0].getElementsByTagName ('tr');
for (i = 0; i < trs.length; i++) {
	trs[i].onclick = row_clicked;
}

var master = document.getElementById ('tick_master');
master.checked = true;
master.onclick = tick_master_click;

function tick_master_click()
{
	for (i = 0; i < tables.length; i++) {
		tables[i].checked = this.checked;
	}
}

function row_clicked()
{
	var ticks = this.getElementsByTagName ('input');

	ticks[0].checked = !ticks[0].checked;
}
