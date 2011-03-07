function input_initialise (entry_id, lookup)
{
	var entry = document.getElementById (entry_id);

	entry.onblur     = input_onblur;
	entry.onkeypress = input_onkeypress;
	entry.lookup     = lookup;
	entry.error_id   = entry_id + "_error";
}


function route_get_node (node, name)
{
	try {
		return node.getElementsByTagName(name)[0].firstChild.nodeValue;
	} catch (er) {
	}

	return "";
}

function route_get_attr (xml, attr_name)
{
	if (!xml || !attr_name)
		return "";
	var attrs = xml.attributes;
	if (!attrs)
		return "";

	for (var i = 0; i < attrs.length; i++) {
		if (attrs[i].nodeName == attr_name)
			return attrs[i].nodeValue;
	}

	return "";
}

function route_get_errors (xml)
{
	var x = xml.getElementsByTagName("error");
	var errstr = "";
	for (i = 0; i < x.length; i++) {
		var e = x[i];
		if (e && e.childNodes) {
			errstr += e.childNodes[0].nodeValue + " ";
		}
	}

	return errstr;
}


function input_callback()
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
	var type = route_get_attr (xml, "type");
	if (type != entry.lookup)
		return;

	var entry_err = document.getElementById (entry.error_id);

	// if errors exist
	// 	get errors
	// 	turn input red (class='error')
	// 	find error_box for this input
	// 	display errors
	var errstr = route_get_errors (xml);
	if (errstr.length > 0) {
		entry.className = "error";
		if (entry_err)
			entry_err.innerHTML = errstr;
		return;
	}

	// if no errors
	// 	get result
	// 	display result
	// 	turn input white (! class='error')
	// 	find error_box for this input
	// 	clear error_box
	var result = route_get_node (xml, type);
	entry.value = result;
	if (entry.value == entry.original)
		entry.className = "";
	else
		entry.className = "diff";
	if (entry_err)
		entry_err.innerHTML = "";

	return;
}

function input_onblur()
{
	input_validate (this);
}

function input_validate (input)
{
	if (!input)
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

	xmlstr = "<?xml version='1.0' encoding='UTF-8'?>";
	xmlstr += "<validation type='" + input.lookup + "'>";
	xmlstr += "<input>" + val + "</input>";
	xmlstr += "</validation>";

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	x.lookup = input.id;
	x.onreadystatechange = input_callback;
	x.open ("POST", "lookup.php", true);
	x.setRequestHeader ("Content-Type", "text/plain");
	x.send (xmlstr);
}

function input_onkeypress (e)
{
	// Validate on enter, space or comma
	if ((e.keyCode == 13) || (e.charCode == 32) || (e.charCode == 44)) {
		input_validate (this);
		return false;
	}

	var item = this;
	var lookup = this.lookup;
	var parent = this.parentNode;
	var grandparent = parent.parentNode;
	var sibling;
	if (e.keyCode == 38) {			// move up
		sibling = grandparent.previousSibling;
	} else if (e.keyCode == 40) {		// move down
		sibling = grandparent.nextSibling;
	}

	if (sibling) {
		var c = sibling.children;
		for (var i = 0; i < c.length; i++) {
			var fc = c[i].firstChild;
			if (fc.lookup == lookup) {
				fc.focus();
				break;
			}
		}
	}

	return true;
}

