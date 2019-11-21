<?php
error_reporting(-1);
ini_set('display_errors', 'On');

/* PLACE THIS FILE IN THE ROOT (www) DIRECTORY */

function getParam($file, $param)
{
    $found = preg_match("/$param',\s*'([^']*)/", $file, $matches);
    return $found?$matches[1]:'';
}

function getLogin()
{
    $r = file_get_contents("wp-config.php");
    if ($r !== false) {
        $dbname = getParam($r, 'DB_NAME');
        $user = getParam($r, 'DB_USER');
        $pwd = getParam($r, 'DB_PASSWORD');
        $host = getParam($r, 'DB_HOST');
        preg_match("/table_prefix\s*=\s*'([^']*)/", $r, $matches);
        $prefix = count($matches) > 1?$matches[1]:'';
        $db = new mysqli($host, $user, $pwd, $dbname);
        return [$db,$prefix];
    }
    return false;
}

function createReport()
{
    list($db, $prefix) = getLogin();
    $sql = "SELECT cat.category, cat.sub_category, cat.link, cal.day_num, cal.month_num, cal.year_num, p.list_size, p.practice_name, p.sp_shortcode, p.ccg, f.time_clicked, f.user_id FROM {$prefix}sp_fact_clicks f 
	INNER JOIN {$prefix}sp_dim_category cat ON cat.category_id = f.category_id
	INNER JOIN {$prefix}sp_dim_calendar cal ON cal.calendar_id = f.calendar_id
	INNER JOIN {$prefix}sp_dim_practice p ON p.practice_id = f.practice_id";
    $statement = $db->prepare($sql);
    $statement->execute();
    $result = $statement->get_result();
    // $rows = $result->fetch_all(MYSQLI_ASSOC);

    //var_dump($rows);
    $rownum = 0;

    while ($row = $result->fetch_assoc()) {
        echo "<br><br>" . $rownum;
        echo "<br>Link: " . $row['link'];
        echo "<br>Practice: " . $row['practice_name'];
        echo "<br>User Id: " . $row['user_id'];
        $rownum++;
    }

    // for ($row = 0; $row < $rows.length; $row++) {
    //     list($category, $subcategory, $link, $daynum, $monthnum, $yearnum, $listsize, $practice, $shortcode, $ccg, $timeclicked, $user) = $rows[$row];
        
    //     echo "Row " . $row . ": " . $link;
    // }
}

createReport();

//
//list($db, $prefix) = getLogin();
//
//$result = mysqli_query($db,"SELECT whenrecorded, url, type, linktext, domain, sessionid, returninguser, parent FROM {$prefix}sp_analytics");
//
//$countsPerDay = [];
//$left = [];
////$room = [];
////$roomCounts = [];
//
////array filter for checking if text matches a certain type??
//
//while ($row = mysqli_fetch_array($result,MYSQLI_NUM)) {
//    list($when, $url, $type, $text, $domain, $session, $returning, $parent) = $row;
//
//  $date = substr($when, 0, 10);
//
//
//  //check what room the link was clicked /from/
//  //make sure parent is not null, explode parent on /, get third part?
//  $parentExp = explode("/",$parent);
//  $parentRoom = $parentExp[4];
//
//
//  if(!isset($countsPerDay[$date])) {
//      $countsPerDay[$date]=[
//          "uniqueUsers"=>0,
//          "sessions"=>[],
//          "summary"=>[]
//      ];
//  }
//
//  //reassign 'type' for more specific information
//  if($type == 'link') {
//      //is url null?
//      if(!$url == 'none') { //this is either a button or a div hence it has no url
//
//      }
//      if(preg_match("#^(/conditions/)#", $url)) {
//          //a conditon
//          $type = 'Self-help';
//      }
//      if(preg_match("!^(https?://(www)?)!", $url) && !preg_match("!^(https?://(www)?{$_SERVER['HTTP_HOST']})!", $url)) {
//          //external url
//          $type = 'External';
//      }
//  }
//
//  //Build the table row headings
//  if(!isset($left[$type][$text])) {
//      $left[$type][$text]=1;
//  }
//  $countsPerDay[$date]['summary'][$type][$text]++;
//
//  //Totals
//    if(!isset($countsPerDay[$date]["sessions"][$session])) {
//        $countsPerDay[$date]["sessions"][$session] = 1;
//    }
//
//    if($returning === 0) {
//      $countsPerDay[$date]["uniqueUsers"]++;
//    }
//}
//
//mysqli_free_result($result);
//
//echo "<table><tr><td>";
//
//foreach($countsPerDay as $key=>$value) {
//  echo "<td><strong>" . $key . "</strong>";
//}
//
//echo "<tr><td><strong>Totals </strong><tr><td>Unique users";
//foreach($countsPerDay as $c) {
//  if($countsPerDay['uniqueUsers'] > 0) {
//      echo "<td>" . $c['uniqueUsers'];
//  } else {
//      echo "<td>0";
//  }
//}
//echo "<tr><td>Sessions";
//foreach($countsPerDay as $c) {
//  echo "<td>" . count($c['sessions']);
//}
//
//foreach($left as $lkey=>$lval) {
//  echo "<tr><td><strong>" . $lkey . "</strong>";
//  foreach($lval as $key => $val) {
//      echo "<tr><td>" . $key;
//      foreach($countsPerDay as $c) {
//          if (isset($c['summary'][$lkey][$key])) {
//              echo "<td>" . $c['summary'][$lkey][$key];
//          } else {
//              echo "<td>0";
//          }
//      }
//  }
//}
//
//echo "</table>";

//echo json_encode($countsPerDay);
