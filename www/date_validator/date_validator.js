var default_date;

function date_initialise (input, date)
{
	default_date = date;
	input.value = date;
	date_onblur (input);
}

function date_onfocus (input)
{
	if (input.value == default_date) {
		input.value = "";
	}
}

function date_callback()
{
	if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200)) {
		var datef = document.getElementById ('date_formatted');

		var response = xmlhttp.responseText;
		var bra = response.indexOf ('(');
		var ket = response.indexOf (')', bra);

		if (reponse.length == 0) {
			response = "empty";
		}
		if ((bra == -1) || (ket == -1)) {
			datef.innerHTML = response;
		} else {
			datef.innerHTML = response.substring (bra+1, ket);
		}
	}
}

function date_onblur (input, field)
{
	var datef = document.getElementById (field);
	str = input.value;
	if (str.length == 0) { 
		input.value = default_date;
		str = default_date;
	}
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}
	xmlhttp.onreadystatechange = date_callback;
	xmlhttp.open ("GET", "date_validator.php?q = " + str, true);
	xmlhttp.send();
}

