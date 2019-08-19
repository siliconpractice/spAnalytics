jQuery("#analyticsGo").click(function() {
    var data = {
        "data": "one"
    };
    
  jQuery.ajax
  ({
    type: "POST",
    url: "<?php echo get_template_directory_uri(); ?>/analytics.php",
    data: data,
      dataType: 'json',
    success: function(r)
    {
        console.log("Ajax success");
    }, 
    error: function( jqXhr, textStatus, errorThrown ){
        console.log( errorThrown );
    }
  });
 return false;
});