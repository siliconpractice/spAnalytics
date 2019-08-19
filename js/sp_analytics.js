jQuery(document).ready(function() {
    jQuery('body').on('click', 'a', function() {
        jQuery.ajax({
            url: "/sp_tracking.php",
            data: {
                text: jQuery(this).text(),
                url: jQuery(this).attr("href")
            } 
        });
    });
});