function CreateXMLDoc()
{
	if (document.implementation.createDocument &&
		document.implementation.createDocumentType) {
		var fruitDocType = document.implementation.createDocumentType ("fruit", "SYSTEM", "<!ENTITY tf 'tropical fruit'>");
		var xmlDoc = document.implementation.createDocument ("", "fruits", fruitDocType);

		var fruitNode = xmlDoc.createElement ("fruit");
		fruitNode.setAttribute ("name" , "avocado");
		xmlDoc.documentElement.appendChild (fruitNode);

		if (typeof (XMLSerializer) != "undefined") {
			var serializer = new XMLSerializer();
			alert(serializer.serializeToString(xmlDoc));
		}
	} else {
		alert ("Your browser does not support this example");
	}
}

