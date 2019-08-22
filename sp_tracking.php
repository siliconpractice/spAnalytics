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

$urlFilter['javascript:void(0)'] = true;

list($db, $prefix) = getLogin();

$returning = isset($_COOKIE['sp_longterm'])?1:0;

//file_put_contents("tracking.log", "Entered this routine: " . $prefix . "url: " . $_REQUEST['url'] . " text: " . $_REQUEST['text'] . " domain: " . $_SERVER['HTTP_HOST'] . " session id: " . $_COOKIE['sp_session'] . " returninguser: " . $returning, FILE_APPEND);

setcookie('sp_longterm', '1', time()+60*60*24*90); //<-- set perm cookie, one year

//if (!in_array($_REQUEST['url'], $urlFilter)) {
    
if (!isset($urlFilter[$_REQUEST['url']])) {
    
    file_put_contents("tracking.log", "Inside the if statement: " . $_REQUEST['url'], FILE_APPEND);
    
    $sql = $db->prepare("INSERT into {$prefix}sp_analytics(whenrecorded, url, linktext, domain, sessionid, returninguser) values(now(), ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssi", $_REQUEST['url'], $_REQUEST['text'], $_SERVER['HTTP_HOST'], $_COOKIE['sp_session'], $returning);
    $sql->execute();
    $sql->close();
    echo "Ok";
    die();
} else {
    file_put_contents("tracking.log", "Inside the else statement: " . $_REQUEST['url'], FILE_APPEND);
}
?>