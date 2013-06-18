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
	
	$(".delete").bind("click", function(){
    	var r=confirm("Are you sure you want to delete this playlist?");
        if (r==true)
        {
    	   parent = $(this).parents('tr');
    	   parent.hide();
    	   parent.next('tr').hide();
    	   $.post("?an=editing.deleteplaylist", { id:this.id, q:992 });    	   
        }
	});	
});

//editing.setactivateproduct

/* EOF */