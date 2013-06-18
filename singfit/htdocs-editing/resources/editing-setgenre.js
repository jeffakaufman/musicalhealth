/* FEO */

var _currentRequestIsSaving = false;
var _currentSearchIsFinding = false;
var _currentFecthingIdentifier = false;

$(document).ready(function() {
	$("input, textarea, select, button").uniform();
	$("input:checkbox").live("click", function() {
		$.uniform.update(
			$(this).attr("checked", this.checked)
		);
	});
	$("input[type=text]").attr("autocomplete","off");
	$("body").append('<div id="overlay" class="noselect"><img class="loader" src="resources/vendor/ajax-loader.gif" alt="" /></div>');
	$("#overlay").hide();
	$("#overlay").css({
		'opacity': 0.6,
		'position':'absolute',
		'top':0,
		'left':0,
		'background-color':'black',
		'width':'100%',
		'z-index':100
	});
	$('#setgenreform').ajaxForm({
		dataType:'json', 
		beforeSubmit:_currentRequestFormBeforeCallback, 
		success:_currentRequestFormSuccessCallback,
		error:_currentRequestFormErrorCallback
	});
	$('#setgenreformsave').click(function() {
		if (_currentFecthingIdentifier) {
			return;
		}
		if (!_currentRequestIsSaving) {
			_currentRequestIsSaving = true;
			$("#overlay").height($(document).height());
			$("#overlay").fadeIn('slow', function() {});
			$('#setgenreform').submit();
			$("html").scrollTop(0);
			$("body").scrollTop(0);
			$(document).scrollTop(0);
			$(window).scrollTop(0);
		}
		return;
	});
	$(window).resize(function() {
		$("#overlay").height($(document).height());
	});
	$('.real').numeric({allow:"."});
	$('.alphanumeric').alphanumeric({allow:"'()&:!?,. "});
});

function _stripNonASCII(str) {
	return str.replace(/[^a-zA-Z0-9]/g, '');
};;;

function _currentRequestFormBeforeCallback(formData, jqForm, options) {
	_currentRequestIsSaving = true;
	$("#setgenreformerror").hide();
	return;
};;;

function _currentRequestFormSuccessCallback(data) {
	if (window.console) {
		console.log(data);
	}
	setTimeout(function() {
		$("#overlay").fadeOut('slow', function() {});
		$("html").scrollTop(0);
		$("body").scrollTop(0);
		$(document).scrollTop(0);
		$(window).scrollTop(0);
		if (data.errno != 0) {
			$("#setgenreformerror").html(data.msg);
			$("#setgenreformerror").show();
			switch(data.errno) {
				case 300:
					$("input[name=name]").focus();
				break;
			}
			_currentRequestIsSaving = false;
		} else {
			$("#setgenreformerror").hide();
			$("#setgenreformerror").html("");
			window.location.href='./?an=editing.view&r=allgenres';
		}
		return false;
	}, 2500);
};;;

function _currentRequestFormErrorCallback(xhr, ajaxOptions, thrownError) {
	if (window.console) {
		console.log(thrownError);
	}
	setTimeout(function() { 
		$("#overlay").hide();
		$("#setgenreformerror").html("This request cannot be processed. Unexpected Error.");
		$("#setgenreformerror").show();
		_currentRequestIsSaving = false;
		return false;
	}, 500);
};;;

var _keyupDelayed = (function() {
	var timer = 0;
	return function(callback, ms){
		clearTimeout(timer);
		timer = setTimeout(callback, ms);
	};
})();;;

/* EOF */