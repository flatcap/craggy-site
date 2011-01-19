function loadXMLDoc (dname)
{
	if (window.XMLHttpRequest) {
		xhttp = new XMLHttpRequest();
	} else {
		xhttp = new ActiveXObject ("Microsoft.XMLHTTP");
	}
	xhttp.open ("GET", dname, false);
	xhttp.send ("");
	return xhttp.responseXML;
}

function displayResult()
{
	xml = loadXMLDoc ("cdcatalog.xml");
	xsl = loadXMLDoc ("cdcatalog.xsl");
	if (window.ActiveXObject) {
		// code for IE
		ex = xml.transformNode (xsl);
		document.getElementById ("example").innerHTML = ex;
	} else if (document.implementation && document.implementation.createDocument) {
		// code for Mozilla, Firefox, Opera, etc.
		xsltProcessor = new XSLTProcessor();
		xsltProcessor.importStylesheet (xsl);
		resultDocument = xsltProcessor.transformToFragment (xml, document);
		document.getElementById ("example").appendChild (resultDocument);
	}
}

