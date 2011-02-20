function cookie_create (name, value, days)
{
	var expires;
	if (days) {
		var date = new Date();
		date.setDate (date.getDate() + days);
		expires = "; expires=" + date.toUTCString();
	} else {
		expires = "";
	}
	document.cookie = name + "=" + escape (value) + expires + "; path=/";
}

function cookie_read (name)
{
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf (name + "=");
		if (c_start != -1) {
			c_start += name.length + 1;
			c_end = document.cookie.indexOf (";", c_start);
			if (c_end == -1) {
				c_end = document.cookie.length;
			}
			return unescape (document.cookie.substring (c_start, c_end));
		}
	}
	return null;
}

function cookie_erase (name)
{
	cookie_create (name, "", -1);
}

function hello()
{
	username = cookie_read ('username');
	if ((username !== null) && (username !== "")) {
		alert ('Welcome again ' + username + '!');
	} else {
		username = prompt ('Please enter your name:', "");
		if ((username !== null) && (username !== "")) {
			cookie_create ('username', username, 7);
		}
	}
}
