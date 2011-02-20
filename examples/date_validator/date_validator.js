var date_entry;
var date_match;
var date_default;

function date_initialise (date_id, match_id, date_def)
{
	date_entry   = document.getElementById (date_id);
	date_match   = document.getElementById (match_id);
	date_default = date_def;

	// Set event handlers
	date_entry.onfocus = date_onfocus;
	date_entry.onblur  = date_onblur;

	date_entry.value = date_def;
	date_onblur();
}

function date_onfocus()
{
	if (date_entry.value == date_default) {
		date_entry.value = "";
	}
}

function date_callback()
{
	if ((xmlhttp.readyState != 4) || (xmlhttp.status != 200))
		return;

	var response = xmlhttp.responseText;
	if (response.length === 0)
		return;

	var bra = response.indexOf ('(');
	var ket = response.indexOf (')', bra);

	if ((bra == -1) || (ket == -1)) {
		date_match.innerHTML = response;
	} else {
		date_match.innerHTML = response.substring (bra+1, ket);
	}
}

function date_onblur()
{
	str = date_entry.value;
	if (str.length === 0) {
		date_entry.value = date_default;
		str = date_default;
	}
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = date_callback;
	xmlhttp.open ("GET", "date_validator.php?q=" + str, true);
	xmlhttp.send();
}

function date_get_match()
{
	return date_match.value;
}

