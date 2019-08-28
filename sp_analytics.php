<?php

/*
 * Plugin Name: SP Analytics
 * Description: Record analytics events
 * Version:     1.0.0
 * Author:      Silicon Practice Ltd
 * Author URI:  http://www.siliconpractice.co.uk/
 * License:     Copyright (c) Silicon Practice Ltd. You have no permissison to disclose, copy or distribute this code.
 */


add_action('wp_enqueue_scripts', 'add_sp_analytics');

function add_sp_analytics() {
   wp_enqueue_script('sp_analytics_js', plugins_url('js/sp_analytics.js',__FILE__),array('jquery'),false,true); 
}

add_action('init', 'track_sp_analytics');

function track_sp_analytics() {
    
    if (isset($_COOKIE['sp_session'])) { //this is the session id that will be stored in the database. Ensure unique?
        setcookie('sp_session', $_COOKIE['sp_session'], time()+60*15); //set new cookie for 15 minutes duration
    } else {
        setcookie('sp_session', uniqid("SESS", true), time()+60*15); //update current cookie time for new 15 min duration
    }
//setcookie('sp_longterm', '1', time()+60*60*24*90); //<-- set perm cookie, one year I think this should be set when a user first visits the site???
}

?>