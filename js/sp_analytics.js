jQuery(document).ready(function() {
    jQuery('body').on('click', 'a, button, .sp_track', function() {
        jQuery.ajax({
            url: "/sp_tracking.php",
            data: {
                text: jQuery(this).text(),
                url: jQuery(this).attr("href")
            } 
        });
    });
});