function loadxmldoc()
{
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();				// IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject ("Microsoft.XMLHTTP");	// IE6, IE5
	}

	document.getElementById ("spanout").innerHTML = "sending information...";

	var strname = encodeURI (document.getElementById ("txtname").value);
	var strmess = encodeURI (document.getElementById ("txtmess").value);

	xmlbody = "<?xml version='1.0' encoding='UTF-8'?>";
	xmlbody += "<entry>";
	xmlbody += "<name>" + strname + "</name>";
	xmlbody += "<message>" + strmess + "</message>";
	xmlbody += "</entry>";

	xmlhttp.open ("POST", "work.php", true);
	xmlhttp.onreadystatechange = buildxmlresults;
	xmlhttp.setRequestHeader ("Content-Type", "text/plain");
	xmlhttp.send (xmlbody);
}

function buildxmlresults()
{
	if ((this.readyState != 4) || (this.status != 200))
		return;

	var strtext = this.responseText;

	document.getElementById ("spanout").innerHTML = strtext;
}
