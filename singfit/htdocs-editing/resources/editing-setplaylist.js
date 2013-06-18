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
	$('input[name=freeforall]').change(function() {
		if ($(this).is(':checked') == true) {
			$('input[name=apple_product_price]').val('0.00');
			$('input[name=onlyforsubscriber]').attr('checked', false);
			$.uniform.update(
				$('input[name=onlyforsubscriber]').attr("checked", false)
			);
			$('input[name=freeforsubscriber]').attr('checked', false);
			$.uniform.update(
				$('input[name=freeforsubscriber]').attr("checked", false)
			);
		}
	});
	$('input[name=onlyforsubscriber]').change(function() {
		if ($(this).is(':checked') == true) {
			$('input[name=freeforall]').attr('checked', false);
			$.uniform.update(
				$('input[name=freeforall]').attr("checked", false)
			);
		}
	});
	$('input[name=freeforsubscriber]').change(function() {
		if ($(this).is(':checked') == true) {
			$('input[name=freeforall]').attr('checked', false);
			$.uniform.update(
				$('input[name=freeforall]').attr("checked", false)
			);
		}
	});
	$('input[name=apple_product_name]').keyup(function() {
		if ($(this).val().length < 1) {
			$("input[name=apple_product_id]").val('');
			return;
		}
		if ($(this).val().length < 3) {
			return;
		} else if (_currentFecthingIdentifier == true) {
			return;
		}
		_currentFecthingIdentifier = true;
		_keyupDelayed(function() {
			if ($("input[name=apple_product_name]").val().length < 3) {
				_currentFecthingIdentifier = false;
				return;
			}
			$.post("?an=editing.getproductidentifier", { name: $("input[name=apple_product_name]").val() }, function(data) {
				try {
					decode = $.parseJSON(data);
					$("input[name=apple_product_id]").val(decode.identifier);
				} catch (err) {
					_currentFecthingIdentifier = false;
					return;
				};
				_currentFecthingIdentifier = false;
			});
		}, 800);
	});
	$('.genredetach').click(function() {
		$(this).parent().hide();
		$("#attached_genres").arrayDel(parseInt(this.id));
		$(this).parent().remove();
	});
	$('#genre').change(function() {
		if ($("#attached_genres").arrayCount() >= 5) {
			$.uniform.update(
				$(this).val(0).attr('selected', true)
			);
			return;
		}
		var value = $(this).val();
		var name =  $('#' + this.id + '>option:selected').text();
		$('.genredetach').unbind('click');
		var vals = $("#attached_genres").arrayVal();
		if (-1 == $.inArray(parseInt(value), vals)) {
			$("#attached_genres").arrayAdd(parseInt(value));
			$('#genreattached').append('<div class="noselect"><a class="noselect genredetach" href="#" id="' + value + '">detach</a> <span class="noselect plain-green">' + name + '</span></div>');
		}
		$('.genredetach').click(function() {
			$(this).parent().hide();
			$("#attached_genres").arrayDel(parseInt(this.id));
			$(this).parent().remove();
		});
		$.uniform.update(
			$(this).val(0).attr('selected', true)
		);
	});
	$('.featuredetach').click(function() {
		$(this).parent().hide();
		$("#attached_features").arrayDel(parseInt(this.id));
		$(this).parent().remove();
	});
	$('#feature').change(function() {
		if ($("#attached_features").arrayCount() >= 5) {
			$.uniform.update(
				$(this).val(0).attr('selected', true)
			);
			return;
		}
		var value = $(this).val();
		var name =  $('#' + this.id + '>option:selected').text();
		$('.featuredetach').unbind('click');
		var vals = $("#attached_features").arrayVal();
		if (-1 == $.inArray(parseInt(value), vals)) {
			$("#attached_features").arrayAdd(parseInt(value));
			$('#featureattached').append('<div class="noselect"><a class="noselect featuredetach" href="#" id="' + value + '">detach</a> <span class="noselect plain-green">' + name + '</span></div>');
		}
		$('.featuredetach').click(function() {
			$(this).parent().hide();
			$("#attached_features").arrayDel(parseInt(this.id));
			$(this).parent().remove();
		});
		$.uniform.update(
			$(this).val(0).attr('selected', true)
		);
	});
	$('.findsongdetach').click(function() {
		$(this).parent().hide();
		$("#attached_songs").arrayDel(parseInt(this.id));
		$(this).parent().remove();
	});
	$('#findsongsearch').keyup(function() {
		if ($("#findsongsearch").val().trim().length < 4) {
			$("#findsongresult").html('');
			return;
		} else if (_currentSearchIsFinding == true) {
			return;
		}
		_currentSearchIsFinding = true;
		_keyupDelayed(function() {
			if ($("#findsongsearch").val().trim().length < 4) {
				$("#findsongresult").html('');
				_currentSearchIsFinding = false;
				return;
			}
			$.post("?an=editing.findsong", { search: $("#findsongsearch").val() }, function(data) {
				var items = [];
				$('.findsongattach').unbind('click');
				try {
					decode = $.parseJSON(data);
				} catch (err) { 
					_currentSearchIsFinding = false;
					return; 
				};
				var vals = $("#attached_songs").arrayVal();
				$.each(decode, function(key, val) {
					if (-1 == $.inArray(parseInt(val.id), vals)) {
						var action = '<a class="noselect findsongattach" href="#" name="' + val.title + '" id="' + val.id + '">attach</a>';
						items.push('<div class="noselect">' + action + ' <span class="noselect plain-brown">' + $.ellipsisText(val.title, 75) + '</span><div class="elementline"></div></div>');
					}
				});
				$("#findsongresult").html(items.join(''));
				$('.findsongattach').click(function() {
					$('.findsongdetach').unbind('click');
					$("#attached_songs").arrayAdd(parseInt(this.id));
					if ($("#attached_songs").arrayCount() > 1) {
						$("input[name=apple_product_name]").val('');
					} else {
						$("input[name=apple_product_name]").val(this.name);
					}
					$('input[name=apple_product_name]').trigger('keyup');
					$("#findsongattached").append('<div class="noselect"><a class="noselect findsongdetach" href="#" id="' + this.id + '">detach</a> <span class="noselect plain-green">' + $.ellipsisText(this.name, 75) + '</span><div class="elementspacer"></div></div>');
					$(this).parent().remove();
					$('.findsongdetach').click(function() {
						$(this).parent().hide();
						$("#attached_songs").arrayDel(parseInt(this.id));
						if ($("#attached_songs").arrayCount() < 1) {
							$("input[name=apple_product_name]").val('');
							$('input[name=apple_product_name]').trigger('keyup');
						}
						$('#findsongsearch').trigger('keyup');
						$(this).parent().remove();
					});
				});
				_currentSearchIsFinding = false;
			});
		}, 300);
	});
	$('#setplaylistform').ajaxForm({
		dataType:'json', 
		beforeSubmit:_currentRequestFormBeforeCallback, 
		success:_currentRequestFormSuccessCallback,
		error:_currentRequestFormErrorCallback
	});
	$('#setplaylistformsave').click(function() {
		if (_currentFecthingIdentifier) {
			return;
		}
		if (!_currentRequestIsSaving) {
			_currentRequestIsSaving = true;
			$("#overlay").height($(document).height());
			$("#overlay").fadeIn('slow', function() {});
			$('#setplaylistform').submit();
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
	$("#setplaylistformerror").hide();
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
			$("#setplaylistformerror").html(data.msg);
			$("#setplaylistformerror").show();
			switch(data.errno) {
				case 300:
					$("input[name=apple_product_name]").focus();
				break;
				case 400:
				case 500:
					$("input[name=apple_product_price]").focus();
				break;
			}
			_currentRequestIsSaving = false;
		} else {
			$("#setplaylistformerror").hide();
			$("#setplaylistformerror").html("");
			window.location.href='./?an=editing.view&r=allplaylists';
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
		$("#setplaylistformerror").html("This request cannot be processed. Unexpected Error.");
		$("#setplaylistformerror").show();
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