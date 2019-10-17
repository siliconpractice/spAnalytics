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
	
    function getReport() {
        jQuery.ajax({
            url: "/sp_reporting.php",
			dataType: "json",
            success: function(data, status) {
				console.log(data);
				var tbl = jQuery('#analytics-report-content')[0];
				
				var datecounter = -1;
				jQuery.each(data, function(akey, avalue) { //for each date...
					var fieldcounter = -1;
					datecounter++;
					jQuery.each(avalue, function(bkey, bvalue) { //for each set of counts within a date (this is a COLUMN)
						fieldcounter++;
						addRow(tbl, bkey, bvalue, datecounter, fieldcounter);
					});
				});
				addThead(tbl, data);
            }
        });
    }
	
	function addRow(table, key, value, i, j) {
		var row;
		if (i == 0) { //if i = 0 then we are on the first date so make a new row for every item
			row = table.insertRow();
				var first = row.insertCell(0);
				var firstVal = document.createTextNode(key);
				first.appendChild(firstVal);
		} else { //if i is more than 0 then its not a new row
			row = table.rows[j];

			var cell = row.insertCell(-1);
			var text;

			if (typeof value === 'object' && value !== null) {
				
				text = document.createTextNode("Object.");
			} else {
				text = document.createTextNode(value);
			}
			cell.appendChild(text);
		}
	}
	
	function addThead(table, data) {
		
		var thead = table.createTHead();
		var row = thead.insertRow(0);
		var thCorner = document.createElement("th");
		var textCorner = document.createTextNode("Date: ");
		thCorner.appendChild(textCorner);
		row.appendChild(thCorner);
		
		jQuery.each(data, function(key, value) {
			var th = document.createElement("th");
			var text = document.createTextNode(key);
			th.appendChild(text);
			row.appendChild(th);
		});
	}
});