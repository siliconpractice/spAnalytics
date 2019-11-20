window.addEventListener("beforeunload", function(event) {
  var url = this.location.href;
  sendExit(url);
});

jQuery(document).ready(function() {

    var formStarted = false;
    
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
    });
});

function sendStats(link, parent) {
        jQuery.ajax({
            url: "/sp_tracking.php",
            data: {
                text: link, //either form name or link text
                parent: parent,
                link: true
            }
        });
}

function sendExit(page) {
    jQuery.ajax({
      url: "/sp_tracking.php",
      data: {
        page: page,
        exit: true
      }
    });
}