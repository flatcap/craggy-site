function xml_get_node (node, name)
{
	try {
		return node.getElementsByTagName(name)[0].firstChild.nodeValue;
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
	var x = xml.getElementsByTagName("error");
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

