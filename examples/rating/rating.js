var all_id     = new Array();
var all_text   = new Array();
var all_status = new Array();
var all_locked = new Array();
var all_css    = new Array();

function rating_initialise (div_name, status, desc, ids, texts, css)
{
	var div = document.getElementById (div_name);
	star_count = ids.length;

	var index = all_id.length;
	div.className = "rating";
	div.id = index;
	div.title = desc;

	all_id.push     (ids);
	all_text.push   (texts);
	all_status.push (document.getElementById (status));
	all_locked.push (false);
	all_css.push    (css);

	for (var i = 0; i < ids.length; i++) {
		var link = document.createElement ('a');
		link.id          = i;
		link.onclick     = rating_click;
		link.onmouseover = rating_mouseover;
		link.onmouseout  = rating_mouseout;
		div.appendChild (link);
	}

	rating_status (index, desc);
}


function get_star (index, item)
{
	var div = get_div (item);
	var ch = div.childNodes;

	for (var i = 0; i < ch.length; i++) {
		if (ch[i] == item) {
			return all_css[index][i];
		}
	}

	return all_css[index][0];
}

function get_index (item)
{
	return get_div (item).id;
}

function get_div (item)
{
	return item.parentNode;
}


function rating_mouseover()
{
	var index = get_index (this);
	if (!all_locked[index]) {
		rating_refresh (index, this);
	}
}

function rating_mouseout()
{
	var index = get_index (this);
	if (all_locked[index]) {
		return;
	}

	var div = get_div (this);
	var star_count = all_id[index].length;

	rating_status (index, this.parentNode.title);
	var children = div.childNodes;
	var c = get_star (index, this);
	for (var i = 0; i < children.length; i++) {
		children[i].className = "";
	}
}

function rating_status (index, text)
{
	if (all_status[index]) {
		all_status[index].innerHTML = text;
	}
}

function rating_refresh (index, item)
{
	var div = get_div (item);
	var star_count = all_id[index].length;

	var children = div.childNodes;
	var c = get_star (index, item);
	for (var i = 0; i < children.length; i++) {
		children[i].className = c;
		if (children[i] == item) {
			rating_status (index, all_text[index][i]);
			c = "";
		}
	}
}

function rating_click()
{
	var index = get_index (this);
	if (all_locked[index]) {
		all_locked[index] = false;
		rating_status (index, all_text[index][this.id]);
		rating_refresh (index, this);
	} else {
		rating_status (index, all_text[index][this.id] + " - Saved");
		all_locked[index] = true;
		//alert ("Your rating was: " + this.title);
	}
}

