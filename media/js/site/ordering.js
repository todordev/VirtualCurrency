jQuery(document).ready(function() {

	var terms = jQuery("#vc-terms");
	
	jQuery(terms).on("click", function(event) {
		if(jQuery(this).is(':checked')) {
			jQuery(".vc-terms").val(1);
		} else {
			jQuery(".vc-terms").val(0);
		}
	});
});