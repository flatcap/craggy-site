/* Function common to all dialogs */
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

function callback_keypress (e)
{
	var keynum;
	if (window.event) {		// IE
		keynum = e.keyCode;
	} else if (e.which) {		// Netscape/Firefox/Opera
		keynum = e.which;
	}
	if (keynum == 13) {
		if (this.onenter) {
			this.onenter();
			return false;
		}
	}

	return true;
}

function callback_keyup (e)
{
	buttons_update();
}

function click_tick (e)
{
	buttons_update();
}

function click_tick_master()
{
	var table = table_get_table (this);
	table_select_all (table, this.checked);
	buttons_update();
}

function display_errors (xml)
{
	notify_close();

	var errstr = xml_get_errors (xml.responseXML.documentElement);
	if (errstr.length > 0) {
		notify_error (errstr);
		return true;
	}

	return false;
}


