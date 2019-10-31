jQuery(document).ready(function() {

    var formStarted = false;
	
	jQuery(window).unload(function() {
//		#form was exited
	});
    
    jQuery('body').on('click', 'a, button, .room, .sp_track, .frm_forms, .triage_choice_box, .consult-text-button', function(e) {

        var data1; //identifier e.g. text or url
        var data2; //type e.g. room
		var data3; //parent
        
        if(e.currentTarget.classList.contains("frm_forms")) { //if they've clicked anywhere in a form
            
            var formName = jQuery(this).find("form").attr('id'); //get name of form clicked in
            data1 = formName;
            data2 = "form";
			data3 = "";
			console.log("Form clicked");
            
            if(jQuery(e.target).is("input")) {
                formStarted = true; //form has been started
            } else if (jQuery(e.target).is("button[type='submit']")) {
                formStarted = false; //form has been submitted, reset started variable
            } else {
                //clicked outside of an input
            }
		} else if(e.currentTarget.classList.contains("room")) {
            data1 = jQuery(this).text();
            data2 = "room";
//        } else if(e.currentTarget.classList.contains("triage_choice_box")) {
//            data1 = jQuery(this).find("span").text();
//            data2 = "triage choice";
//		} else if(e.currentTarget.classList.contains("consult-text-button")) {
//            data1 = jQuery(this).text();
//            data2 = "room choice";
//		} else if(jQuery(e.currentTarget).is('a') && !e.currentTarget.classList.contains("sp_track") && !e.currentTarget.classList.contains("triage_choice_box")) {
		} else {
            data1 = jQuery(this).text(); //works for divs, buttons, links.
            data2 = "link";
        }
		
		var url;
		
		if(jQuery(this).attr("href")) {
			url = jQuery(this).attr("href");
		} else {
			url = "none";
		}
		
		console.log(window.location.href);
        
//		$link, $nowday, $nowmonth, $nowyear, $practice, $userid
		
        jQuery.ajax({
            url: "/sp_tracking.php",
            data: {
                text: data1, //either form name or link text
                url: url,//this is useful for filtering
                type: data2,
				parent: window.location.href
            }
        });
        
        jQuery('#analytics-report-btn').on('click', function() {
            getReport();
        });
    });

});