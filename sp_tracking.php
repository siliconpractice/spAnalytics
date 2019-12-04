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
    $params['userid'] = $_COOKIE['sp_session'];
    $params['page'] = $_REQUEST['page'];
    return $params;
}

function getInternal($page)
{
    $explode = explode("/", $page);
    $url = $explode[2];
    writeToLog("In the getInternal function, Page is " . $page . "and url is " . $url);
    list($db, $prefix) = getLogin();
    $sql = "SELECT option_value FROM {$prefix}options WHERE option_name = 'spm_multisite'";
    $result = $db->query($sql);
    $rows = $result->fetch_array();
    $result->close();

    $offest = 0;

    while ($offest !== false) {
        $a = stripos($rows[0], '"internalcode"') + strlen('"internalcode"');
        $b = stripos($rows[0], '"', $a) +1;
        $c = stripos($rows[0], '"', $b);

        $d = stripos($rows[0], '"domain"') + strlen('"domain"');
        $e = stripos($rows[0], '"', $d) +1;
        $f = stripos($rows[0], '"', $e);

        $domain = substr($rows[0], $e, $f - $e);
        $internal = substr($rows[0], $b, $c - $b);

        writeToLog("Domain:" . $domain . "\n Internal code: " . $internal);
        if ($domain == $url) {
            writeToLog('Domain equals url');
            return $internal;
        }
        
        $offset = $f+1;
    }
}

function insertLink()
{
    list($db, $prefix) = getLogin();
    $params = getParams();
    $page = $params['page'];
    $userid = $params['userid'];
    $link = $_REQUEST['text'];
    $parent = explode("/", $page);
    $internal = getInternal($page);

    if (!empty($parent[3])) {
        $cat = $parent[3];
    } else {
        $cat = 'none';
    }

    if (!empty($parent[4])) {
        $subcat = $parent[4];
    } else {
        $subcat = 'none';
    }

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
        $sql = "INSERT into {$prefix}sp_fact_clicks (category_id, time_clicked, user_id, internal_code) VALUES (?, NOW(), ?, ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("iss", $category_id, $userid, $internal);
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
    $internal = getInternal($page);

    if (($page !== 'none') && ($userid !== "none")) {
        $sql = "INSERT into {$prefix}sp_fact_exits (page, time_exited, user_id, internal_code) VALUES (?, NOW(), ?, ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("sss", $page, $userid, $internal);
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
    $form_url = explode("/", $page);
    $form_name = str_replace('-', ' ', $form_url[4]);
    $internal = getInternal($page);
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
        $sql = "INSERT into {$prefix}sp_fact_abandoned (form_id, time_abandoned, user_id, internal_code) VALUES (?, NOW(), ?, ?)";
        $statement = $db->prepare($sql);
        $statement->bind_param("iss", $form_id, $userid, $internal);
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
