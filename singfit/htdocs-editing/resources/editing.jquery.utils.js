/* FOE */

$.stringPos = function(str, search, index) {
	var theindex = (index || 0);
	var found = (str + '').indexOf(search, theindex);
	return parseInt(found);
}

$.ellipsisText = function(text, max, append) {
	var themax = (max || 80);
	var theappend = (append || '...');
	if (text.length <= themax) {
		return text;
	}
	var out = text.substring(0, themax);
	if ($.stringPos(text, ' ', 0) < 0) {
		return out + theappend;
	}
	return out.replace('/\w+$/g', '') + theappend;
}

$.arrayValid = function(value) {
	value = value || 'undefined';
	return (value instanceof Array) ? true : false;
}

$.arrayToString = function(arr) {
	return '[' + arr.join(",") + ']';
}

$.stringToArray = function(str) {
	var arr = [];
	if (str.length > 0 && str != "[]") {
		if ((0 > $.stringPos(str, '[' , 0)) && (0 > $.stringPos(str, ']' , str.length -1))) {
			str = '[' + str + ']';
		}
		arr = eval(str);
	}
	if (!$.arrayValid(arr)) {
		arr = [];
	}
	return arr; 
}

$.fn.arrayCount = function() {
	var arr = $(this[0]).arrayVal();
	return arr.length;
}

$.fn.arrayVal = function(newval) {
	var arr = [];
	if ($.arrayValid(newval)) {
		$(this[0]).val($.arrayToString(newval));
		return newval;
	}
	return $.stringToArray($(this[0]).val());
}

$.fn.arrayAdd = function(value) {
	var arr = $(this[0]).arrayVal();
	arr.push(value);
	$(this[0]).arrayVal(arr);
}

$.fn.arrayDelAtIndex = function(index) {
	if (-1 != index) {
		var arr = $(this[0]).arrayVal();
		arr.splice(index, 1 );
		$(this[0]).arrayVal(arr);
	}
}

$.fn.arrayDel = function(value) {
	var arr = $(this[0]).arrayVal();
	var index = $.inArray(value, arr);
	$(this[0]).arrayDelAtIndex(index);
}

/* EOF */