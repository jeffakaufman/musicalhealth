/* FEO */

$(document).ready(function() {
	$(".actionactivate").live("click", function() {
		$.post("?an=editing.setactivateproduct", { id:this.id, activate:1 });
		 $(this).removeClass("actionactivate");
		 $(this).addClass("actiondeactivate");
		 $(this).text('deactivate');
	});
	$(".actiondeactivate").live("click", function() {
		console.log(this);
		$.post("?an=editing.setactivateproduct", { id:this.id, activate:0 });
		 $(this).removeClass("actiondeactivate");
		 $(this).addClass("actionactivate");
		 $(this).text('activate');
	});
});

//editing.setactivateproduct

/* EOF */