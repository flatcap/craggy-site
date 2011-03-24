function complete_initialise (entry, lookup)
{
	if (!entry)
		return;

	entry.onblur     = complete_onblur;
	entry.onkeypress = complete_onkeypress;
	entry.lookup     = lookup;
	entry.error_id   = entry.id + "_error";
}

function complete_initialise_by_id (entry_id, lookup)
{
	var entry = document.getElementById (entry_id);
	complete_initialise (entry, lookup);
}


function complete_callback()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	// if no response, do nothing
	var xml = this.responseXML.documentElement;
	if (!xml)
		return;

	var entry = document.getElementById (this.lookup);
	if (!entry)
		return;

	// if wrong type (root.attr != entry.lookup)
	//	internal error
	var type = xml_get_attr (xml, "type");
	if (type != entry.lookup)
		return;

	var entry_err = document.getElementById (entry.error_id);

	// if errors exist
	//	get errors
	//	turn input red (class='error')
	//	find error_box for this input
	//	display errors
	var errstr = xml_get_errors (xml);
	if (errstr.length > 0) {
		entry.className = "error";
		if (entry_err)
			entry_err.innerHTML = errstr;
		return;
	}

	// if no errors
	//	get result
	//	display result
	//	turn input white (! class='error')
	//	find error_box for this input
	//	clear error_box
	var result = xml_get_node (xml, type);
	entry.value = result;
	if ((entry.original === null) || (entry.value == entry.original))
		entry.className = "";
	else
		entry.className = "diff";
	if (entry_err)
		entry_err.innerHTML = "";

	return;
}

function complete_onblur()
{
	complete_validate (this);
}

function complete_validate (input)
{
	if (!input)
		return;

	if (!input.lookup)
		return;

	var val = input.value;
	if (val.length === 0) {
		var orig = input.original;
		if (orig) {
			input.value     = orig;
			input.className = "";
		}
		return;
	}

	var xmlstr;

	xmlstr = "<?xml version='1.0' encoding='UTF-8'?>\n";
	xmlstr += "<validation type='" + input.lookup + "'>\n";
	xmlstr += "<input>" + val + "</input>\n";
	xmlstr += "</validation>";

	var x = ajax_post ('lookup.php', xmlstr, complete_callback);
	x.lookup = input.id;
}

function complete_onkeypress (e)
{
	var lookup = this.lookup;

	// Validate on enter or comma
	if ((e.keyCode == 13) || (e.charCode == 44)) {
		if (lookup) {
			complete_validate (this);
			return false;
		}
	}

	var item = this;
	var parent = item.parentNode;
	var grandparent = parent.parentNode;
	var sibling;
	if (e.keyCode == 38) {			// move up
		sibling = grandparent.previousSibling;
	} else if (e.keyCode == 40) {		// move down
		sibling = grandparent.nextSibling;
	}

	if ((e.keyCode == 38) || (e.keyCode == 40)) {
		if (sibling) {
			var j;
			var my_name = '###';
			if (this.id) {
				j = this.id.indexOf ('_');
				if (j >= 0)
					my_name = this.id.substr (0, j-1);
			}
			var c = sibling.children;
			for (var i = 0; i < c.length; i++) {
				var fc = c[i].firstChild;
				var fc_name;
				if (fc.id) {
					j = fc.id.indexOf ('_');
					if (j >= 0)
						fc_name = fc.id.substr (0, j-1);
				}
				if (fc_name == my_name) {
					fc.focus();
					fc.select();
					break;
				}
			}
		} else {
			complete_validate (this);
		}
	}

	return true;
}

