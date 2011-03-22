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

function notify_info (text)
{
	notify_message (text, '#0f0', '#000');
}

function notify_warning (text)
{
	notify_message (text, '#f84', '#000');
}

function notify_error (text)
{
	notify_message (text, '#f66', '#000');
}

function notify_message (text, background, colour)
{
	if (!notify_area || !background || !colour) {
		return;
	}

	if (text.length === 0) {
		notify_close();
		return;
	}

	notify_area.innerHTML             = text;
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


