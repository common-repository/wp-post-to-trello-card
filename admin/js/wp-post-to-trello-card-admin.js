jQuery( document ).ready(function() {
    jQuery("#bptc_board").change(function() {
    	// 
    	jQuery("#bptc_loader").show();
    	jQuery("#bptc_list").prop('disabled', true);
    	// 
    	var bptc_selectedVal = this.value;
     	// AJAX Starts
     	var get_worksheets_cre = {
     	    'action': 'bptc_ajax_response',
     	    'boardId': bptc_selectedVal,
     	};

     	jQuery.post(ajaxurl,get_worksheets_cre,function(response_worksheets) {
     	    var list = JSON.parse(response_worksheets);
     	    // console.log(list);
     	    // 
     	    jQuery("#bptc_loader").hide();
     	    jQuery("#bptc_list").prop('disabled', false);
     	    // 
     	    jQuery('#bptc_list').empty();
     	    jQuery('#bptc_list').append('<option value=""> Select a List	</option>');
     	    jQuery.each( list, function( key, value ) {
     	      	jQuery('#bptc_list').append('<option value="' + key + '">' + value + '</option>');
     	    });
     	});
     	// AJAX Ends
    });
});
