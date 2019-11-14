jQuery(document).ready(function() {

    var formStarted = false;
	
	jQuery(window).unload(function() {
//		#form was exited
	});
    
    jQuery('body').on('click', 'a, button, .room, .sp_track, .frm_forms, .triage_choice_box, .consult-text-button', function(e) {
        
		//click on a form
        if(e.currentTarget.classList.contains("frm_forms")) {
            
			//get name of form clicked in
            var formName = jQuery(this).find("form").attr('id');
            
            if(jQuery(e.target).is("input")) {
                //form has been started
				formStarted = true;
            } else if (jQuery(e.target).is("button[type='submit']")) {
                //form has been submitted, reset started variable
				formStarted = false;
				
				//send stats for form
				sendStats(formName + "_submit", window.location.href);
            }
		} else {
			sendStats(jQuery(this).text(), window.location.href);
		}
		
		//e.currentTarget.classList.contains("room") -> its a room
		//e.currentTarget.classList.contains("triage_choice_box") -> it's a triage choice
		//e.currentTarget.classList.contains("consult-text-button") -> it's a consult text e.g. Pharmacy
		//e.currentTarget.classList.contains("sp_track") -> it's a different thing

		//strip spaces from start and end.
		//ignore form clicks for purposes of click collection
    });

});

function sendStats(link, parent) {
        jQuery.ajax({
            url: "/sp_tracking.php",
            data: {
                text: link, //either form name or link text
				parent: parent
            }
        });
}