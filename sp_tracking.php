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

function checkLink($link, $subcat, $cat)
{
    $category_id = "none";
    list($db, $prefix) = getLogin();
    $sql = "SELECT cat.category_id FROM {$prefix}sp_dim_category cat WHERE cat.link = ? AND cat.sub_category = ? AND cat.category = ? LIMIT 1";
    $statement = $db->prepare($sql);
    $statement->bind_param('sss', $link, $subcat, $cat);
    $statement->execute();
    $result = $statement->get_result();
    while ($row = $result->fetch_assoc()) {
        $category_id = $row['category_id'];
    };
    $statement->close();
    return $category_id;
}

function checkForm($form_name)
{
    $form_id = "none";
    list($db, $prefix) = getLogin();
    $sql = "SELECT form_id FROM {$prefix}sp_dim_forms WHERE form_name = ? LIMIT 1";
    $statement = $db->prepare($sql);
    $statement->bind_param('s', $form_name);
    $statement->execute();
    $result = $statement->get_result();
    while ($row = $result->fetch_assoc()) {
        $form_id = $row['form_id'];
    };
    $statement->close();
    return $form_id;
}

function getParams()
{
    $parent;
    $now = new DateTime();
    $params = [
        'link' => 'none',
        'parent' => 'none',
        'subcat' => 'none',
        'cat' => 'none',
        'page' => 'none',
        'form_name' => 'none',
        'userid' => 'none'
    ];

    if (!empty($_REQUEST['parent'])) {
        $params['parent'] = $_REQUEST['parent'];
        $parent = explode("/", $_REQUEST['parent']);

        if (!empty($parent[3])) {
            $params['cat'] = $parent[3];
        }

        if (!empty($parent[4])) {
            $params['subcat'] = $parent[4];
        }
    }

    if (!empty($_REQUEST['text'])) {
        $params['link'] = $_REQUEST['text'];
    }

    if (isset($_COOKIE['sp_session'])) {
        $params['userid'] = $_COOKIE['sp_session'];
    }

    if (!empty($_REQUEST['page'])) {
        $params['page'] = $_REQUEST['page'];
        $form_url = explode("/", $params['page']);
        $params['form_name'] = str_replace('-', ' ', $form_url[4]);
    }

    if (!empty($_REQUEST['formid'])) {
        $params['formid'] = $_REQUEST['formid'];
    }

    return $params;
}

function insertLink()
{
    list($db, $prefix) = getLogin();
    $params = getParams();
    $link = $params['link'];
    $parent = $params['parent'];
    $subcat = $params['subcat'];
    $cat = $params['cat'];
    $userid = $params['userid'];

    $category_id = checkLink($link, $subcat, $cat);

    if (($category_id == 'none')) {
        $sql = "INSERT into {$prefix}sp_dim_category (category, sub_category, link) VALUES (?, ?, ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("sss", $cat, $subcat, $link);
        if ($statement->execute()) {
            echo "Success";
            $category_id = $db->insert_id;
        } else {
            echo "Error: " . $db->error;
        }
        $statement->close();
    }
    
    if (($category_id !== 'none') && ($userid !== "none")) {
        $sql = "INSERT into {$prefix}sp_fact_clicks (category_id, time_clicked, user_id) VALUES (?, NOW(), ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("is", $category_id, $userid);
        if ($statement->execute()) {
            echo "Success";
        } else {
            echo "Error: " . $db->error;
        }
        $statement->close();
    } else {
        echo "Error";
    }
}

function insertExit()
{
    list($db, $prefix) = getLogin();
    $params = getParams();
    $page = $params['page'];
    $userid = $params['userid'];

    if (($page !== 'none') && ($userid !== "none")) {
        $sql = "INSERT into {$prefix}sp_fact_exits (page, time_exited, user_id) VALUES (?, NOW(), ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("ss", $page, $userid);
        $statement->execute();
        $statement->close();
    }
}

function insertAbandoned()
{
    list($db, $prefix) = getLogin();
    $params = getParams();
    $page = $params['page'];
    $userid = $params['userid'];
    $form_name = $params['form_name'];

    $form_id = checkForm($form_name);

    if (($form_id == 'none')) {
        $sql = "INSERT into {$prefix}sp_dim_forms (form_name) VALUES (?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("s", $form_name);
        if ($statement->execute()) {
            echo "Success";
            $form_id = $db->insert_id;
        } else {
            echo "Error: " . $db->error;
        }
        $statement->close();
    }

    if (($form_id !== 'none') && ($userid !== "none")) {
        $sql = "INSERT into {$prefix}sp_fact_abandoned (form_id, time_abandoned, user_id) VALUES (?, NOW(), ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("is", $form_id, $userid);
        $statement->execute();
        $statement->close();
    }
}

//Only for testing purposes
function writeToLog(string $content)
{
    file_put_contents("tracking.log", "\n" . $content . "\n \n --- \n \n", FILE_APPEND);
}

if (isset($_REQUEST['link'])) {
    insertLink();
}

if (isset($_REQUEST['exit'])) {
    insertExit();
}

if (isset($_REQUEST['form'])) {
    insertAbandoned();
}

if (list($db, $prefix) = getLogin()) {
    $db->close();
}
