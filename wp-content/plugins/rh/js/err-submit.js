function getSelectedText()
{
    var txt = null;
	if (window.getSelection)
    {
        txt = window.getSelection();
             }
    else if (document.getSelection)
    {
        txt = document.getSelection();
            }
    else if (document.selection)
    {
        txt = document.selection.createRange().text;
            }

	if (txt != null && txt.toString) { txt = txt.toString(); }

    return txt;
}

function aaaurlencode(text)
{
  var trans = [];
  for (var i=0x410; i<=0x44F; i++) trans[i] = i-0x350;
  trans[0x401] = 0xA8;
  trans[0x451] = 0xB8;
  var ret = [];
  for (var i=0; i<text.length; i++)
  {
    var n = text.charCodeAt(i);
    if(typeof trans[n] != 'undefined') n = trans[n];
    if(n <= 0xFF) ret.push(n);
  }
  return escape(String.fromCharCode.apply(null,ret));
}






var Utf8 = {

	// public method for url encoding
	encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// public method for url decoding
	decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}

function showContent(txt) {
	jQuery('#translation_notice_form #original_text').html(txt.replace(/(<.*?>)/ig,""));
    jQuery.fn.colorbox({
		width: "600px",
		height: "555px", 
		iframe: true, 
		href: "http://api.mywordpress.ru/submissions/new?url="+encodeURIComponent(window.location)+"&text="+txt
	}); 
}

shortcut.add("Ctrl+Alt+R",function() {
	showContent(getSelectedText());
});
