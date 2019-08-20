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

function track_sp_analytics() {
    
    $cid = 
    
    if (!isset($_COOKIE($cid)) {
        setcookie('name', 'data', time()+60*15); //<-- temp cookie, 15 minutes
    })
    if (!isset($_COOKIE()) {
      setcookie('name', 'data', time()+60*60*24*365); //<-- set perm cookie, one year 
    })
}

?>