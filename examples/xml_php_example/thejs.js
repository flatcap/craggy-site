$(document).ready(function() {

	$("#getData").click(function(){

	var data = "";

		/* change the $.get to "thephp.php" when that is setup */

		$.get("thexml.xml", function(theXML){

				$('person',theXML).each(function(i){

					var title = $(this).find("firstname").attr("title");
					var firstname = $(this).find("firstname").text();
					var surname = $(this).find("surname").text();

					data += title + " " + firstname + " " + surname + "<br>";

				});

				$("#container").html(data);
		});

	});

});
