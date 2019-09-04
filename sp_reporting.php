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

$countsPerDay = [
    "pagesRetrieved"=>0,
    "totalSessions"=>0,
    "uniqueUsers"=>0,
    "searches"=>0
];
    
$countsTotal = [
	"rooms"=>[],
	"externalLinks"=>0,
    "nhsChoices"=>0,
    "selfRefer"=>0,
    "incompleteForms"=>0
];

$sessions = [];
$html = "";

//array filter for checking if text matches a certain type??

while ($row = mysqli_fetch_array($result,MYSQLI_NUM)) {
    list($when, $url, $type, $text, $domain, $session, $returning) = $row;
    
    if(!isset($sessions[$session])) {
        $sessions[$session] = 1;
    }
    
    if($returning === 0) {
        $countsPerDay["uniqueUsers"]++;
    }
    
    if($text === "I would lke to request a referral") {
        $countsTotal["selfRefer"]++;
    }
	
	//we're not recording page hits atm? links != hits
	
	//if its of link type and the url starts with a / or http or https
	if($type === "link" && preg_match("!^(/|#|https?://(www)?)", $url)) { //this excludes anchors and nulls
		
		if(preg_match("!^(/|#|https?://(www)?{$_REQUEST['HTTP_HOST']})!", $url)) {
			$parts = explode('/', $url);
			$page = $parts[count($parts)];
			//this will be an internal link
		} else {
			$countsTotal['externalLinks']++;
		}
	} else if($type === "room") { //deal with anchors and nulls or other types of link i.e. room.
		if (!isset($countsTotal['rooms'][$text])) {
			$countsTotal['rooms'][$text]++;
		}
	}
}

mysqli_free_result($result);

$countsPerDay["totalSessions"] = count($sessions);

$data = array($countsPerDay, $countsTotal);

echo json_encode($data);
?>