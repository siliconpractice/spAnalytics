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

list($db, $prefix) = getLogin();

$result = mysqli_query($db,"SELECT whenrecorded, url, type, linktext, domain, sessionid, returninguser FROM {$prefix}sp_analytics");

$countsPerDay = [];
$left = [];

//array filter for checking if text matches a certain type??

while ($row = mysqli_fetch_array($result,MYSQLI_NUM)) {
    list($when, $url, $type, $text, $domain, $session, $returning) = $row;
    
	$date = substr($when, 0, 10);
	   
	if(!isset($countsPerDay[$date])) {
		$countsPerDay[$date]=[
			"uniqueUsers"=>0,
			"sessions"=>[],
			"summary"=>[]
		];
	}
	
	//reassign 'type' for more specific information
	if($type== 'link') {
		if(preg_match("#^(/conditions/)#", $url)) {
			//a conditon
			$type = 'Self-help';
		} else if (preg_match("!^(/|#|https?://(www)?)", $url)) { //its a link, not an anchor or null
			echo "its a link";
			if (!preg_match("!^(/|#|https?://(www)?{$_SERVER['HTTP_HOST']})!", $url)) { //its a link but not to the current domain
				//external url
				echo "its an external link";
				$type = 'external';
			}
		}
	}
		
	if(!isset($left[$type][$text])) {
		$left[$type][$text]=1;
	}		
	$countsPerDay[$date]['summary'][$type][$text]++;
	
	/* Totals */
    if(!isset($countsPerDay[$date]["sessions"][$session])) {
        $countsPerDay[$date]["sessions"][$session] = 1;
    }
	
    if($returning === 0) {
		$countsPerDay[$date]["uniqueUsers"]++;
    }

//			$parts = explode('/', $url);
//			$page = $parts[count($parts)];

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

foreach($left as $lkey=>$lval) {
	echo "<tr><td><strong>" . $lkey . "</strong>";
	foreach($lval as $key => $val) {
		echo "<tr><td>" . $key;
		foreach($countsPerDay as $c) {
			if (isset($c['summary'][$lkey][$key])) {
				echo "<td>" . $c['summary'][$lkey][$key];	
			} else {
				echo "<td>0";
			}
		}
	}
}

echo "</table>";

//echo json_encode($countsPerDay);
?>