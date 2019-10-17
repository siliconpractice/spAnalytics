<?php

/* PLACE THIS FILE IN THE ROOT (www) DIRECTORY */

function getParam($file,$param) {
	$found = preg_match("/$param',\s*'([^']*)/",$file,$matches);
	return $found?$matches[1]:'';
}

function getLogin(){
  $r = file_get_contents("wp-config.php");
  if ($r !== false) {
		$dbname = getParam($r,'DB_NAME');
		$user = getParam($r,'DB_USER');
		$pwd = getParam($r,'DB_PASSWORD');
		$host = getParam($r,'DB_HOST');
    preg_match("/table_prefix\s*=\s*'([^']*)/",$r,$matches);
    $prefix = count($matches) > 1?$matches[1]:'';
    $db = new mysqli($host,$user,$pwd,$dbname);
    return [$db,$prefix];   
  }
  return false;
}

function setLinkType($type, $url) {
	//reassign 'type' for more specific information
	if($type == 'link') {
		//is url null?
		if(!$url == 'none') { //this is either a button or a div hence it has no url
			
		}
		if(preg_match("#^(/conditions/)#", $url)) {
			//a conditon
			$type = 'Self-help';
		}
		if(preg_match("!^(https?://(www)?)!", $url) && !preg_match("!^(https?://(www)?{$_SERVER['HTTP_HOST']})!", $url)) {
			//external url
			$type = 'External';
		}
	}
	return $type;
}

list($db, $prefix) = getLogin();

$result = mysqli_query($db,"SELECT whenrecorded, url, type, linktext, domain, sessionid, returninguser, parent FROM {$prefix}sp_analytics");

$countsPerDay = [];
$left = [];
$room = [];
$roomCounts = [];

//array filter for checking if text matches a certain type??

while ($row = mysqli_fetch_array($result,MYSQLI_NUM)) {
    list($when, $url, $type, $text, $domain, $session, $returning, $parent) = $row;
    
	$date = substr($when, 0, 10);
	
	$parentExp = explode("/",$parent);
	$parentBranch;
	$parentRoom;
	
	//check what room the link was clicked /from/
	//make sure parent is not null, explode parent on /, get third part?
	
	//set data structure for counts for each date (excluding rooom children)
	if(!isset($countsPerDay[$date])) {
		$countsPerDay[$date]=[
			"uniqueUsers"=>0,
			"sessions"=>[],
			"summary"=>[]
		];
	}
	
	if(!isset($roomCounts[$date])) {
		$roomCounts[$date]=[];
	}

	//set data structure for counts for room children
	if(isset($parentExp[3]) && $parentExp[3] == "digitalpractice") { //type is room child
		$parentRoom = $parentExp[4];
		$parentRoom = str_replace("-", " ", $parentRoom);
		
		if(!isset($room[$parentRoom][$text])) {
			$room[$parentRoom][$text]=1;
		}
		$roomCounts[$date][$parentRoom][$text]++;
		
	//set data structure for every other type of count	
	} else {
		
		$type = setLinkType($type, $url);
		
		//Build the table row headings
		if(!isset($left[$type][$text])) {
			$left[$type][$text]=1;
		}		
		$countsPerDay[$date]['summary'][$type][$text]++;		
	}
	
	//Totals
    if(!isset($countsPerDay[$date]["sessions"][$session])) {
        $countsPerDay[$date]["sessions"][$session] = 1;
    }
	
    if($returning === 0) {
		$countsPerDay[$date]["uniqueUsers"]++;
    }
}

mysqli_free_result($result);

echo "<table><tr><td>";

foreach($countsPerDay as $key=>$value) {
	echo "<td><strong>" . $key . "</strong>";
}

echo "<tr><td><strong>Totals </strong><tr><td>Unique users";
foreach($countsPerDay as $c) {
	if($countsPerDay['uniqueUsers'] > 0) {
		echo "<td>" . $c['uniqueUsers'];
	} else {
		echo "<td>0";
	}
}
echo "<tr><td>Sessions";
foreach($countsPerDay as $c) {
	echo "<td>" . count($c['sessions']);
}
//echo "<tr><td><strong>Rooms</strong>";
//foreach($room as $rkey=>$rval) {
//	echo "<tr><td><strong>" . $rkey . "</strong>";
//	foreach($rval as $key=>$val) {
//		echo "<tr><td>" . $key;
//		foreach($roomCounts as $c) { //each date
//			echo "<td>";
//			if (isset($c[$rkey][$key])) {
//				echo $c[$rkey][$key];
//			} else {
//				echo "0";
//			}
//		}
//	}
//}

foreach($left as $lkey=>$lval) { //for each item in the left menu..
	echo "<tr><td><strong>" . $lkey . "</strong>"; //echo the name of the item on a new row, e.g. forms, external
	foreach($lval as $key=>$val) { //for each value..
		echo "<tr><td>" . $key; //echo the value on a new row
		foreach($countsPerDay as $ckey=>$cval) { //each date
			if (isset($ckey['summary'][$lkey][$key])) {
				echo "<td>" . $ckey['summary'][$lkey][$key];	
			} else {
				echo "<td>0";
			}
			//see if left hand value exists in the roomcounts array parent, case insensitive - probably won't need room, just roomcounts
			if(isset($roomCounts[$ckey][$lkey][$key])) {
				echo "<tr><td>" . $roomCounts[$ckey][$lkey][$key];
				
				foreach($roomCounts as $r) {
					if (isset($r[$lkey][$key])) {
						echo "<td>" . $r[$lkey][$key];	
					} else {
						echo "<td>0";
					}
				}
			}
			
//			if (strcasecmp($c['summary'][$lkey][$key], $) == 0) { //if case insensitive match is exact match
//				
//			}
		}
	}
}

echo "</table>";

//echo json_encode($countsPerDay);
?>