jQuery(document).ready(function() {

    var formStarted = false;
    
    jQuery('body').on('click', 'a, button, .sp_track, .frm_forms, .triage_choice_box', function(e) {

        var data1;
        var data2;
        
        if(e.currentTarget.classList.contains("frm_forms")) { //if they've clicked anywhere in a form
            
            var formName = jQuery(this).find("form").attr('id'); //get name of form clicked in
            data1 = formName;
            data2 = "form";
            
            if(jQuery(e.target).is("input")) {
                formStarted = true; //form has been started
            } else if (jQuery(e.target).is("button[type='submit']")) {
                formStarted = false; //form has been submitted, reset started variable
            } else {
                //clicked outside of an input
            }
        } else if(e.currentTarget.classList.contains("triage_choice_box")) {
            data1 = jQuery(this).find("span").text();
            data2 = "choice";
        } else if(e.currentTarget.classList.contains(".sp_track")) {
            data1 = jQuery(this).text();
            data2 = "room";
        } else {
            data1 = jQuery(this).text(); //works for buttons, links.
            data2 = "link";
            //data1 = "sp_track"; //What works for sp_track??? (e.g. rooms);
        }
        
        jQuery.ajax({
            url: "/sp_tracking.php",
            data: {
                text: data1, //either form name or link text
                url: jQuery(this).attr("href"),//this is useful for filtering
                type: data2
            }
        });
        
        jQuery('#analytics-report-btn').on('click', function() {
            getReport();
            
        });
    });
    
    function getReport() {
        jQuery.ajax({
            url: "/sp_reporting.php",
            success: function(data, status) {
                console.log(data + "status: " + status);
                jQuery('#analytics-report-content').text(data);
            }
        });
    }
});