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

file_put_contents("tracking.log", "Entered this routine: " . $prefix . "url: " . $_REQUEST['url'] . " text: " . $_REQUEST['text'] . " domain: " . $_SERVER['HTTP_HOST'], FILE_APPEND);

$sql = $db->prepare("INSERT into {$prefix}sp_analytics(whenrecorded, url, linktext, domain) values(now(), ?, ?, ?)");
$sql->bind_param("sss", $_REQUEST['url'], $_REQUEST['text'], $_SERVER['HTTP_HOST']);
$sql->execute();
$sql->close();
echo "Ok";
die();
?>