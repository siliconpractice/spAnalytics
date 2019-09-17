jQuery(document).ready(function() {

    var formStarted = false;
	
	jQuery(window).unload(function() {
//		#form was exited
	});
    
    jQuery('body').on('click', 'a, button, .sp_track, .frm_forms, .triage_choice_box', function(e) {

        var data1;
        var data2;
        
        if(e.currentTarget.classList.contains("frm_forms")) { //if they've clicked anywhere in a form
            
            var formName = jQuery(this).find("form").attr('id'); //get name of form clicked in
            data1 = formName;
            data2 = "form";
			console.log("Form clicked");
            
            if(jQuery(e.target).is("input")) {
                formStarted = true; //form has been started
            } else if (jQuery(e.target).is("button[type='submit']")) {
                formStarted = false; //form has been submitted, reset started variable
            } else {
                //clicked outside of an input
            }
        } else if(e.currentTarget.classList.contains("triage_choice_box")) {
			console.log("Choice clicked");
            data1 = jQuery(this).find("span").text();
            data2 = "choice";
        } else if(e.currentTarget.classList.contains("sp_track")) {
			console.log("Room clicked");
            data1 = jQuery(this).text();
            data2 = "room";
        } else if( jQuery(e.currentTarget).is('a') && !e.currentTarget.classList.contains("sp_track")) {
			console.log("Link clicked");
            data1 = jQuery(this).text(); //works for buttons, links.
            data2 = "link";
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
	
	//construct each COLUMN then print out??
	//User thead and tbody to better position data
    
    function getReport() {
        jQuery.ajax({
            url: "/sp_reporting.php",
			dataType: "json",
            success: function(data, status) {
				console.log(data);
				var corner = document.createElement("th");
				var cornerText = document.createTextNode("Date:");
				
				
				var tableText = '<tr><td>Date: ';
				var tbl = jQuery('#analytics-report-content2')[0]; //get the table with jQuery (at position 0 to grab DOM table not jQuery object)
				var thead = tbl.createTHead(); //create table head section in table
				var hrow = thead.insertRow(-1);
				
				hrow.appendChild(corner);
				
				jQuery.each(data, function(akey, avalue) { //for each date...
					var th = document.createElement("th"); //create a table head cell for each date
					var htext = document.createTextNode(akey); //create a text node containing the date
					th.appendChild(htext); //add the text node to the cell
					hrow.appendChild(th); //add the head section to the row
					
					//may need a COUNTER here
					var row2;
					
					jQuery.each(avalue, function(bkey, bvalue) { //for each set of counts within a date (this is a COLUMN)
						//create a new row. This needs to happen for every individual count in the set
						row2 = tbl.insertRow();
						var cellLabel = row2.insertCell();
						var textLabel = document.createTextNode(bkey);
						var cellValue = row2.insertCell();
						var textValue = document.createTextNode(bvalue);
						cellLabel.appendChild(textLabel);
						cellValue.appendChild(textValue);
						
//						if(typeof bvalue === 'object' && bvalue !== null) {
//						   	jQuery.each(bvalue, function(ckey, cvalue) { //rooms
//								addRow(tbl, bvalue);
//							});
//						} else {
//							//tableText+='<td>' + bkey + '<td>' + bvalue;
//						}
						
						for (key in avalue) {
//							
//							var leftCol = tbl.insertCell(0);
//							var textCol = document.createTextNode(bkey);
//							leftCol.appendChild(textCol);

//							counter++;
//							
//							if 
//							
//							var cell = row.insertCell(-1);
//							var text = document.createTextNode(bkey);
//							cell.appendChild(text);
						}
						//Object.keys(data).indexOf(akey)
						
						//tableText+='<tr>';
//						if(typeof bvalue === 'object' && bvalue !== null) {
//						   	jQuery.each(bvalue, function(ckey, cvalue) { //rooms
//								//tableText+='<td>' + ckey + '<td>' + cvalue;
//							});
//						} else {
//							//tableText+='<td>' + bkey + '<td>' + bvalue;
//						}
					});
				});
//				jQuery('#analytics-report-content').append(tableText);
//				
//				var tbl = jQuery('#analytics-report-content2')[0];
//				var theadData = Object.keys(data);
//				var tbodyData = data;
//				addThead(tbl, theadData);
//				
//				jQuery.each(data, function(akey, avalue) {
//						addColumnHeadings(tbl, avalue);
//						console.log(avalue);
//					});
            }
        });
    }
	
	function addThead(table, data) {
		
		var thead = table.createTHead();
		var row = thead.insertRow(-1);
		
		jQuery.each(data, function(key, value) {
			var th = document.createElement("th");
			var text = document.createTextNode(value);
			th.appendChild(text);
			row.appendChild(th);
		});
	}
	
	function addColumnHeadings(table, data) {
		var row = table.insertRow();
		jQuery.each(data, function(key, value) {
			//if(!) { //check if this row heading already exists??
			var cell = row.insertCell(Object.keys(data).indexOf(key));
			var text = document.createTextNode(value);
			cell.appendChild(text);
			//}
			
		});
	}
	
//	function appendColumn() {
//    var tbl = document.getElementById('my-table'), // table reference
//        i;
//    // open loop for each row and append cell
//    for (i = 0; i < tbl.rows.length; i++) {
//        createCell(tbl.rows[i].insertCell(tbl.rows[i].cells.length), i, 'col');
//    }
//}
//	
	function addCell(table, data) {
		var cell = row.insertCell();
		var text = document.createTextNode(data);
		cell.appendChild(text);
	}
	
	function addRow(table, data) {
		jQuery.each(data, function(key, value) {
			var row = table.insertRow();
			var cell = row.insertCell();
			var text = document.createTextNode(value);
			cell.appendChild(text);
		});
	}
});

/*
var tbl = jQuery('#analytics-report-content');
var theadData = Object.keys(data)
function addThead(table, data) {
var thead = table.createTHead();
var row = thead.insertRow();
jQuery.each(data, key) {
	var th = document.createElement("th");
	var text = document.createTextNode(key);
	th.appendChild(text);
	row.appendChild(th);
}
addThead(tbl, theadData);
}
*/