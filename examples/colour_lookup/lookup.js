function colour_initialise (entry_id, focus)
{
	var colour_entry = document.getElementById (entry_id);

	colour_entry.onblur     = colour_validate;
	colour_entry.onkeypress = colour_onkeypress;

	if (focus)
		colour_entry.focus();
}

function colour_callback()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var response = this.responseText;
	if (response.length === 0)
		return;

	var split = response.indexOf (",");
	if (split < 0)
		return;

	var colour  = response.substring (0, split);
	var entryid = response.substring (split+1);

	var entry = document.getElementById (entryid);
	if (!entry)
		return;

	entry.value = colour;
}

function colour_validate()
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
	x.onreadystatechange = colour_callback;
	x.open ("GET", "lookup.php?q=" + encodeURI(str) + '&id=' + this.id, true);
	x.send();
}

function colour_onkeypress (e)
{
	// Validate on enter, space or comma
	if ((e.keyCode == 13) || (e.charCode == 32) || (e.charCode == 44)) {
		colour_validate();
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

