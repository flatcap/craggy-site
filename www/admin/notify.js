var notify_area;
function notify_initialise (divname)
{
	if (!divname) {
		divname = 'notify_area';
	}

	notify_area = document.getElementById (divname);
	if (!notify_area) {
		return;
	}

	notify_area.onclick = notify_close;
	notify_area.style.display = 'none';
}

function notify_message (message, background, colour)
{
	if (!notify_area) {
		return;
	}

	if (message.length === 0) {
		notify_close();
		return;
	}

	if (!background) {
		background = 'orange';
	}

	if (!colour) {
		colour = 'black';
	}

	notify_area.innerHTML             = message;
	notify_area.style.backgroundColor = background;
	notify_area.style.color           = colour;
	notify_area.style.display         = 'block';
}

function notify_close()
{
	if (!notify_area) {
		return;
	}

	notify_area.style.display = 'none';
	notify_area.innerHTML     = '';
}


