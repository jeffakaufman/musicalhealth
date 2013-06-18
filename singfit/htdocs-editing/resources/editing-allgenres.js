/* FEO */

$(document).ready(function() {
	$(".delete").bind("click", function(){
    	var r=confirm("Are you sure you want to delete this genre?");
        if (r==true)
        {
    	   parent = $(this).parents('tr');
    	   parent.hide();
    	   parent.next('tr').hide();
    	   $.post("?an=editing.deletegenre", { id:this.id, q:991 });    	   
        }
	});
});

//editing.setactivateproduct

/* EOF */