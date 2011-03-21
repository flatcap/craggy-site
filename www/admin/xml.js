function xml_get_node (node, name)
{
	try {
		return node.getElementsByTagName (name)[0].firstChild.nodeValue;
	} catch (e) {
	}

	return "";
}

function xml_get_attr (xml, attr_name)
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

function xml_get_errors (xml, separator)
{
	if (!xml)
		return "";
	if (!separator)
		separator = "<br>";

	var errstr = "";
	var x = xml.getElementsByTagName ("error");
	for (i = 0; i < x.length; i++) {
		var e = x[i];
		if (e && e.childNodes) {
			errstr += e.childNodes[0].nodeValue;
			if (i < x.length)
				errstr += separator;
		}
	}

	return errstr;
}


function ajax_get (url, params, callback)
{
	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	var data = new Array();
	for (var i in params) {
		data.push (i + "=" + params[i]);
	}

	var getstr = url + "?" + data.join ('&');

	x.onreadystatechange = callback;
	x.open ("GET", getstr, true);
	x.setRequestHeader ("Content-Type", "text/plain");	// application/xml; charset=ISO-8859-1
	x.send();

	return x;
}

function ajax_post (url, poststr, callback)
{
	var x;
	if (window.XMLHttpRequest) {
		x = new XMLHttpRequest();			// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		x = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	x.onreadystatechange = callback;
	x.open ("POST", url, true);
	x.setRequestHeader ("Content-Type", "text/plain");	// application/xml; charset=ISO-8859-1
	x.send (poststr);

	return x;
}

