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

$result = mysqli_query($db,"SELECT whenrecorded, linktext, domain, sessionid, returninguser FROM {$prefix}sp_analytics");

$countsPerDay = [
    "PagesRetrieved"=>0,
    "TotalSessions"=>0,
    "UniqueUsers"=>0,
    "Searches"=>0
];
    
$countsTotal = [
    "NHSChoices"=>0,
    "SelfRefer"=>0,
    "IncompleteForms"=>0,
    "Wellbeing"=>0
];

$sessions = [];
$html = "";

//array filter for checking if text matches a certain type??

while ($row = mysqli_fetch_array($result,MYSQLI_NUM)) { // $row[0] is formid and $row[1] is name 
    list($when, $text, $domain, $session, $returning) = $row;
    
    file_put_contents("reporting.log", "Row is when: " . $when . " text: " . $text . " domain: " . $domain . " session: " . $session . " returning user: " . $returning, FILE_APPEND);
    
    if(!isset($sessions[$session])) {
        $sessions[$session] = 1;
    }
    
    if($returning === 0) {
        $countsPerDay["UniqueUsers"]++;
    }
    
    if($text === "I would lke to request a referral") {
        $countsTotal["SelfRefer"]++;
    }    
}

$countsPerDay["TotalSessions"] = count($sessions);

$html .= "\n Number of pages retrieved: " . $countsPerDay["PagesRetrieved"];
$html .= "\n Total sessions: " . $countsPerDay["TotalSessions"];
$html .= "\n Total unique users" . $countsPerDay["UniqueUsers"];
$html .= "\n Searches" . $countsPerDay["Searches"];
$html .= "\n Self referrals" . $countsTotal["SelfRefer"];
    
echo $html;

mysqli_free_result($result);
?>