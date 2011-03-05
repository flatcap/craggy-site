function input_initialise (entry_id, lookup)
{
	var entry = document.getElementById (entry_id);

	entry.onblur     = input_validate;
	entry.onkeypress = input_onkeypress;
	entry.lookup     = lookup;
}

function input_callback()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	if (this.responseText.length === 0)
		return;

	var entry = document.getElementById (this.lookup);
	if (!entry)
		return;

	entry.value = this.responseText;
}

function input_validate()
{
	var str = this.value;
	if (str.length === 0) {
		return;
	}

	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	str = this.lookup + "?q=" + encodeURI(str);
	x.lookup = this.id;
	x.onreadystatechange = input_callback;
	x.open ("GET", str, true);
	x.send();
}

function input_onkeypress (e)
{
	// Validate on enter, space or comma
	if ((e.keyCode == 13) || (e.charCode == 32) || (e.charCode == 44)) {
		input_validate();
		return false;
	}

	var item;
	if (e.keyCode == 38) {
		// move up
		item = this;
		while ((item = item.previousSibling)) {
			if (item.localName == 'input') {
				item.focus();
				item.select();
				break;
			}
		}
		return false;
	} else if (e.keyCode == 40) {
		// move down
		item = this;
		while ((item = item.nextSibling)) {
			if (item.localName == 'input') {
				item.focus();
				item.select();
				break;
			}
		}
		return false;
	}

	return true;
}

