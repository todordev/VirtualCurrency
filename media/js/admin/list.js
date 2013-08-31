jQuery(document).ready(function() {
	
	var listOrder   = document.getElementById("filter_order").value;
	
	Joomla.orderTable = function() {
		var table 	  	= document.getElementById("sortTable");
		var direction 	= document.getElementById("directionTable");
		var order 		= table.options[table.selectedIndex].value;
		var listOrder   = document.getElementById("filter_order").value;
		
		if (order != listOrder) {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
	
});