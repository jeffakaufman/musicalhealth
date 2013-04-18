/* FEO */

var _currentRequestIsSaving = false;

$(document).ready(function() {
	$("input, textarea, select, button").uniform();
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
	$('#setsongform').ajaxForm({
		dataType:'json', 
		beforeSubmit:_currentRequestFormBeforeCallback, 
		success:_currentRequestFormSuccessCallback,
		error:_currentRequestFormErrorCallback
	});
	$('#setsongformsave').click(function() {
		if (!_currentRequestIsSaving) {
			_currentRequestIsSaving = true;
			$("#overlay").height($(document).height());
			$("#overlay").fadeIn('fast', function() {});
			$('#setsongform').submit();
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
	
	$('.numeric').numeric();
	$('.alphanumeric').alphanumeric({allow:"'()&:!?,. "});
});

function _currentRequestFormBeforeCallback(formData, jqForm, options) {
	_currentRequestIsSaving = true;
	$("#setsongformerror").hide();
	return;
};;;

function _currentRequestFormSuccessCallback(data) {
	if (window.console) {
		console.log(data);
	}
	setTimeout(function() {
		$("#overlay").fadeOut('fast', function() {});
		$("html").scrollTop(0);
		$("body").scrollTop(0);
		$(document).scrollTop(0);
		$(window).scrollTop(0);
		if (data.errno != 0) {
			$("#setsongformerror").html(data.msg);
			$("#setsongformerror").show();
			switch(data.errno) {
				case 300:
					$("input[name=title]").focus();
				break;
				case 400:
					$("input[name=author]").focus();
				break;
			}
			_currentRequestIsSaving = false;
		} else {
			$("#setsongformerror").hide();
			$("#setsongformerror").html("");
			window.location.href='./?an=editing.view&r=allsongs';
		}
		return false;
	}, 2500);
};;;

function _currentRequestFormErrorCallback(xhr, ajaxOptions, thrownError) {
	if (window.console) {
		console.log(thrownError);
	}
	setTimeout(function() { 
		$("#overlay").fadeOut('fast', function() {});
		$("#setsongformerror").html("This request cannot be processed. Unexpected Error.");
		$("#setsongformerror").show();
		_currentRequestIsSaving = false;
		return false;
	}, 500);
};;;

/* EOF */