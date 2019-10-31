<?php
error_reporting(-1);
ini_set('display_errors', 'On');
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

function checkLink($link, $subcat, $cat) {
	list($db, $prefix) = getLogin();
	$sql = "SELECT cat.category_id FROM {$prefix}sp_dim_category cat WHERE cat.link = ? && cat.sub_category = ? && cat.category = ? LIMIT 1";
	$statement = $db->prepare($sql);
	$statement->bind_param('sss',$link,$subat,$cat);
	$statement->execute();
	$statement->bind_result($category_id);
	$statement->fetch();
	return $category_id;
}

function checkPractice($internal) {
	list($db, $prefix) = getLogin();
	$sql = "SELECT p.practice_id FROM {$prefix}sp_dim_practice p WHERE p.sp_shortcode = ? LIMIT 1";
	$statement = $db->prepare($sql);
	$statement->bind_param('s',$internal);
	$statement->execute();
	$statement->bind_result($practice_id);
	$statement->fetch();
	return $practice_id;
}

function getInternal() {
	list($db, $prefix) = getLogin();
	$sql = "SELECT option_value FROM {$prefix}options WHERE option_name = 'spm_multisite'";
	$result = $db->query($sql);
	$rows = $result->fetch_array();
	$result->close();

	$a = stripos($rows[0], '"internalcode"') + strlen('"internalcode"');
	$b = stripos($rows[0], '"', $a) +1;
	$c = stripos($rows[0], '"', $b);
	$length = $c - $b;
	$internal = substr($rows[0], $b, $length);

	return $internal;
}

function insert() {
	list($db, $prefix) = getLogin();
	
	$link;
	$parent;
	$subcat;
	$cat;
	$internal = getInternal();

	if (!empty($_REQUEST['parent'])) {
		$parent = explode("/",$_REQUEST['parent']);

		if (!empty($parent[3])) {
			$cat = $parent[3];
		} else {
			$cat = NULL;
		}

		if (!empty($parent[4])) {
			$subcat = $parent[4];
		} else {
			$subcat = NULL;
		}	
	}

	if (!empty($_REQUEST['text'])) {
		$link = $_REQUEST['text'];
	} else {
		$link = "No link clicked";
	}

	$now = new DateTime();
    $now_day = $now->format("j");
	$now_month = $now->format("n");
	$now_year = $now->format("Y");
	$userid = $_COOKIE['sp_session'];

	$category_id = checkLink($link,$subcat,$cat);
	$practice_id = checkPractice($internal);

	if(empty($category_id) && !empty($practice_id)) {
		$sql = "INSERT into {$prefix}sp_dim_category (category, sub_category, link) VALUES (?, ?, ?)";
		$statement = $db->prepare($sql);
		$statement->bind_param("sss", $category, $subcat, $link);
		if ($statement->execute()) {
			echo "Success";
			$category_id = $db->insert_id;
		}
		else {
			echo "Error: " . $db->error();
		}
		$statement->close();
	}
	
	file_put_contents("tracking.log", "\n 12: " . $category_id . "\n 13: " . $practice_id . "\n \n --- \n \n", FILE_APPEND);
	
	if (!empty($category_id) && !empty($practice_id)) {
		$sql = "INSERT into {$prefix}sp_fact_clicks (category_id, calendar_id, practice_id, time_clicked, user_id) VALUES (?, (SELECT cal.calendar_id FROM {$prefix}sp_dim_calendar cal WHERE cal.day_num = ? AND cal.month_num = ? && cal.year_num = ?), ?, NOW(), ?)";
		$statement = $db->prepare($sql);
		$statement->bind_param("iiiiis", $category_id, $now_day, $now_month, $now_year, $practice_id, $userid);
		if ($statement->execute()) {
			echo "Success";
		}
		else {
			echo "Error: " . $db->error();
		}
		$statement->close();
	} else if(empty($practice_id)) {
		echo "Practice not found. Please add practice to stats database.";
	} else {
		echo "Error";
	}
	$db->close();

}

insert();

?>