<?php 

function trackUsers() {
    
    $cid = uniqid("cid", true); //26 characters long with more entropy and prefix
 
 if (!isset($_COOKIE[$cid])) { //checks if user cookie already exists on their client. If is set, user has visited before
    setcookie('sp_cid', $cid, time()+31556926); //sets a cookie called sp_cid with the unique user id that lasts one year
    //if setting new cookie, must be new session, therefore add to session count? save uid in database?
  }
}

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
    $db = mysqli_connect($host,$user,$pwd,$dbname);
    return [$db,$prefix];   
  }
  return false;
}

// Example of use - lists formid and their names on the current site

//list($db, $prefix) = getLogin();
//$result = mysqli_query($db,"SELECT formid,name from {$prefix}sp_formcontrol where formid > 0"); // gets the formid and names
//while ($row = mysqli_fetch_array($result,MYSQLI_NUM)) {
//	echo 'Formid ' . $row[0] . ' is ' . $row[1] . '<br>'; // $row[0] is formid and $row[1] is name 
//} 
//mysqli_free_result($result);

//list($db, $prefix) = getLogin();
//
//$mysqli = mysqli_query($db, "INSERT into tbl_sp_statslog VALUES(``,``,``,`$userid`)";
    
?>