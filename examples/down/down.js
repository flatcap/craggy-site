
var entry1  = document.getElementById ('text1');
var entry2  = document.getElementById ('text2');
var entry3  = document.getElementById ('text3');

var results = document.getElementById ('results');

entry2.focus();

entry1.onkeypress = callback_keypress;
entry2.onkeypress = callback_keypress;
entry3.onkeypress = callback_keypress;

function callback_keypress (e)
{
	var item;
	if (e.keyCode == 38) {
		// move up
		item = this;
		while ((item = item.previousSibling)) {
			if (item.localName == 'input') {
				item.focus();
				break;
			}
		}
		return false;
	} else if (e.keyCode == 40) {
		// move down
		item = this;
		while ((item = item.nextSibling)) {
			if (item.localName == 'input') {
				item.focus();
				break;
			}
		}
		return false;
	} else {
		results.innerHTML =
			'keyCode  = ' + e.keyCode  + '<br />' +
			'charCode = ' + e.charCode + '<br />' +
			'shiftKey = ' + e.shiftKey + '<br />' +		// Shift key
			'ctrlKey  = ' + e.ctrlKey  + '<br />' +		// CTRL key
			'altKey   = ' + e.altKey   + '<br />' +		// ALT key
			'metaKey  = ' + e.metaKey;			// Windows key
		return true;
	}
}

