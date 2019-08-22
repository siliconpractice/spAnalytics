jQuery(document).ready(function() {
    jQuery('body').on('click', 'a, button, .sp_track, .frm_forms', function(e) {
        
        var clickcounter;
        
        if(e.currentTarget.classList.contains("frm_forms")) {
            alert("you clicked on a form");
            clickcounter++; 
        }
        
        jQuery.ajax({
            url: "/sp_tracking.php",
            data: {
                text: jQuery(this).text(),
                url: jQuery(this).attr("href")
            } 
        });
    });
});