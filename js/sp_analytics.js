var formStarted = 0;
var navigating = false;

window.addEventListener("beforeunload", function() {
  onBeforeUnload();
});

function onBeforeUnload() {
  var url = this.location.href;
  if (formStarted!==0) {
    //var formId = jQuery('body').find("input[type='hidden'][name='form_id']").val();
    sendForm(url);
  }
  if(navigating) {
    return;
  }
  sendExit(url);
};

jQuery(document).ready(function() {

    jQuery('.frm_forms').on('focus', 'input', function(e) {
      jQuery(e.currentTarget).on('input', function() {
        formStarted++;
        });
    });
    
    jQuery('body').on('click keypress', 'a, button, .room, .sp_track, .triage_choice_box, .consult-text-button', function(e) {
        
        var target = jQuery(e.target);

        if(target.is("a")) { //check if its a navigation item || target.is("button") - checking buttons not necessary, buttons don't have location??
          if(checkInternal(e.currentTarget)) { //check if link is same domain
            navigating = true;
          }
        }

        if(target.is("button[type='submit']")) {
          var formName = jQuery('body').find('form').attr('id');
          formStarted = 0;
          sendStats(formName + "_submit", window.location.href);
        } else {
          sendStats(jQuery(e.currentTarget).text(), window.location.href);
        }
    });
});

function checkInternal(elementToTest) {
  return(elementToTest.host === window.location.host);
}

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

function sendForm(page) {
    jQuery.ajax({
      url: "/sp_tracking.php",
      data: {
        page: page,
        form: true
      }
    });
}