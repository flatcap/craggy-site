var uniq_id = 0;

function table_create (data_type, columns)
{
	if (!data_type || !columns)
		return null;

	var t  = document.createElement ('table');
	var th = document.createElement ('thead');
	var tb = document.createElement ('tbody');

	t.border      = 1;
	t.cellspacing = 0;
	t.data_type   = data_type;

	t.appendChild (th);
	t.appendChild (tb);

	var row = t.insertRow (0);
	if (!row)
		return null;

	for (var i in columns) {
		var col   = columns[i];
		var name  = col['name'];
		var type  = col['type'];
		if (!name || !type)
			continue;

		var size  = col['size'];
		var valid = col['validator'];
		var title = col['title'];
		if (!title)
			title = name;

		var cell = document.createElement ('th');
		cell.col_name      = name;
		cell.col_type      = type;
		cell.col_size      = size;
		cell.col_validator = valid;

		switch (type) {
		case 'checkbox':
			var tick = document.createElement ('input');
			tick.id      = name + '_master';
			tick.type    = 'checkbox';
			tick.checked = true;
			cell.appendChild (tick);
			break;
		case 'input':
		case 'text':
		default:
			cell.innerHTML = title;
			break;
		}

		row.appendChild (cell);
	}

	return t;
}

function table_add_row (table, data)
{
	if (!table || !data)
		return false;

	var head = table_get_header (table);
	if (!head)
		return false;

	var cols = head.children;
	if (!cols || (cols.length === 0))
		return false;

	var body = table_get_body (table);
	if (!body)
		return false;

	var row = document.createElement ('tr');
	var id  = xml_get_node (data, 'id');
	if (id === "") {
		uniq_id++;
		id = "U" + uniq_id;
	}
	row.id = "row_" + id;

	var col_name;
	var col_type;
	var col_size;
	var col_validator;
	for (var i = 0; i < cols.length; i++) {
		col_name      = cols[i].col_name;
		col_type      = cols[i].col_type;
		col_size      = cols[i].col_size;
		col_validator = cols[i].col_validator;

		var value = xml_get_node (data, col_name);

		var cell = document.createElement ('td');	// don't allocate for id

		switch (col_type) {
		case 'checkbox':
			var tick = document.createElement ('input');
			tick.id      = col_name + "_" + id;
			tick.type    = 'checkbox';
			tick.checked = true;		// or some function of 'value'
			cell.appendChild (tick);
			break;
		case 'input':
			var input = document.createElement ('input');
			input.id       = col_name + "_" + id;
			input.type     = 'text';
			input.value    = value;
			input.size     = col_size;
			input.original = value;
			complete_initialise2 (input, col_validator);
			cell.appendChild (input);
			break;
		case 'text':
		default:
			cell.innerHTML = value;
			break;
		}

		if (col_type != "id")
			row.appendChild (cell);
	}

	body.appendChild (row);

	return true;
}

function table_get_header (table)
{
	if (!table)
		return null;

	var hlist = table.getElementsByTagName ('thead');
	if (!hlist)
		return null;

	var rlist = hlist[0].getElementsByTagName ('tr');
	if (!rlist)
		return null;

	return rlist[0];
}

function table_get_body (table)
{
	if (!table)
		return null;

	var blist = table.getElementsByTagName ('tbody');
	if (!blist)
		return null;

	return blist[0];
}

function table_get_row_count (table)
{
	if (!table)
		return 0;

	var blist = table.getElementsByTagName ('tbody');
	if (!blist)
		return 0;

	var rlist = blist[0].getElementsByTagName ('tr');
	if (!rlist)
		return 0;

	return rlist.length;
}


function table_reset_cell (cell)
{
	if (!cell)
		return false;

	if (cell.children === 0)
		return true;		// nothing we can do

	for (var i = 0; i < cell.children.length; i++) {
		var item = cell.children[i];
		if (item.nodeName.toLowerCase() != 'input') {
			continue;
		}

		item.value = item.original;
		item.className = "";
	}

	return true;
}

function table_reset_row (table, row_num)
{
	if (!table)
		return false;

	var blist = table.getElementsByTagName ('tbody');
	if (!blist)
		return false;

	var rlist = blist[0].getElementsByTagName ('tr');
	if (!rlist)
		return false;

	if (row_num >= rlist.length)
		return false;

	var row = rlist[row_num];

	// row has children (th)
	// th has 0 children -> text
	// th has n children -> input
	// input has value, original

	for (var i = 0; i < row.children.length; i++) {
		table_reset_cell (row.children[i]);
	}

	return true;
}

function table_destroy (table)
{
	if (!table)
		return;

	var parent = table.parentNode;
	if (!parent)
		return;
	var children = parent.children;
	for (var i = 0; i < children.length; i++) {
		if (children[i] == table) {
			parent.removeChild (children[i]);
			break;
		}
	}
}


function table_select_all (table, state)
{
	var body = table_get_body (table);
	if (!body)
		return;

	var rlist = body.getElementsByTagName ('tr');
	if (!rlist)
		return;

	for (var i = 0; i < rlist.length; i++) {
		var item = rlist[i].firstChild;
		item = item.firstChild;
		if (item.nodeName.toLowerCase() != 'input')
			continue;
		if (item.type.toLowerCase() != 'checkbox')
			continue;
		item.checked = state;
	}
}

function table_row_delete (table, row_id)
{
	if (!table)
		return;
	if (row_id.substr (0, 4) != 'row_')
		row_id = "row_" + row_id;
	var row = document.getElementById (row_id);
	if (!row)
		return;

	var body = table_get_body (table);
	if (!body)
		return;

	if (row.parentNode != body)
		return;

	body.removeChild (row);
}


function table_get_selected (table)
{
	var body = table_get_body (table);
	if (!body)
		return null;

	var rlist = body.getElementsByTagName ('tr');
	if (!rlist)
		return null;

	var row_ids = new Array();
	for (var i = 0; i < rlist.length; i++) {
		if (!table_row_selected (rlist[i]))
			continue;
		var id = table_get_row_id (rlist[i]);
		row_ids.push (id.substr (4));	// strip "row_"
	}

	return row_ids;
}

function table_row_selected (row)
{
	if (!row)
		return false;

	var item = row.firstChild;
	item = item.firstChild;
	if (item.nodeName.toLowerCase() != 'input')
		return false;
	if (item.type.toLowerCase() != 'checkbox')
		return false;

	return item.checked;
}

function table_get_row_id (item)
{
	if (!item)
		return null;

	do {
		if (item.nodeName.toLowerCase() == 'tr')
			return item.id;
	} while ((item = item.parentNode))

	return null;
}

function table_get_table (item)
{
	if (!item)
		return null;

	do {
		if (item.nodeName.toLowerCase() == 'table')
			return item;
	} while ((item = item.parentNode))

	return null;
}


function table_cell_get_value (cell)
{
	if (!cell)
		return null;

	if (cell.childElementCount === 0) {
		return cell.innerHTML;					// Just text
	} else {
		var f = cell.firstChild;
		if (f.nodeName.toLowerCase() == 'input') {
			if (f.type.toLowerCase() == 'checkbox') {
				return f.checked;			// A tickbox
			} else if (f.type.toLowerCase() == 'text') {
				return  f.value;			// A textbox
			}
		}
	}

	return null;
}

function table_cell_get_orig (cell)
{
	if (!cell)
		return null;

	if (cell.childElementCount === 0)
		return null;

	var f = cell.firstChild;
	if (f.nodeName.toLowerCase() == 'input') {
		return f.original;
	}

	return null;
}

function table_get_id (row)
{
	if (!row)
		return null;

	var id = row.id;

	if (id.substring (0, 4) == 'row_')
		id = id.substr (4);

	return id;
}

function table_get_columns (table)
{
	if (!table)
		return null;

	var head = table_get_header (table);
	if (!head)
		return null;

	var children = head.children;
	var cols = new Array();
	for (var i = 0; i < children.length; i++) {
		var name = children[i].col_name;
		if (name) {
			cols.push (name);
		}
	}

	return cols;
}

function table_row_to_xml (row, cols, type, diff)
{
	if (!row || !cols)
		return "";

	var changed = false;

	var children = row.children;
	if (children.length != cols.length)
		return "";

	var xml = "";
	for (var i = 0; i < children.length; i++) {
		var name  = cols[i];
		if (!name)
			continue;
		var value = table_cell_get_value (children[i]);
		if (diff) {
			var orig = table_cell_get_orig (children[i]);
			if (orig && (value != orig)) {
				changed = true;
			}
		}

		xml += "<" + name + ">" + encodeURIComponent (value) + "</" + name + ">";
	}

	if (xml === "")
		return "";

	var id = table_get_id (row);
	if (id) {
		xml += "<id>" + id + "</id>";
	}

	if (diff && !changed)
		return "";

	xml = "<" + type + ">" + xml + "</" + type + ">";
	return xml;
}

function table_to_xml (table, selection)
{
	// selection can be 'all', 'different', or 'ticked'
	if (!table)
		return null;

	var cols = table_get_columns (table);
	cols[0] = null;				// Ignore the tick column

	var data_type = table.data_type;

	var body = table_get_body (table);
	var rlist = body.getElementsByTagName ('tr');
	if (!rlist)
		return null;

	var xml = "";
	for (var i = 0; i < rlist.length; i++) {
		if ((selection == 'ticked') && !table_row_selected (rlist[i]))
			continue;
		var diff = (selection == 'different');

		xml += table_row_to_xml (rlist[i], cols, data_type, diff);
	}

	if (xml === "") {
		return xml;
	}

	xml =	"<?xml version='1.0'?>\n" +
		"<list type='" + data_type + "'>" +
		xml +
		'</list>';

	return xml;
}

function table_set_clicks (table, callback)
{
	var ticks = table.getElementsByTagName ('input');
	if (!ticks)
		return;

	for (var i = 0; i < ticks.length; i++) {
		item = ticks[i];
		if (item.id != 'tick_master')
			item.onclick = callback;
	}
}


/*
function table_next_sibling()
{
}

function table_prev_sibling()
{
}

function table_find_type()
{
}

function table_get_row_by_id()
{
}
*
*/
