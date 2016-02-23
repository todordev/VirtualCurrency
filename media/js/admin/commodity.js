jQuery(document).ready(function() {
	
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'commodity.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };

    jQuery(".js-remove-images").on('click', function (event) {

        event.preventDefault();

        if (window.confirm(Joomla.JText._('COM_VIRTUALCURRENCY_QUESTION_REMOVE_IMAGES'))) {
            window.location = jQuery(this).attr('href');
        }
    });

});