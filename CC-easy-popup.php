<?php
/**
 * Plugin Name: CC Easy Popup
 * Description: Popup anything you like at your hompage, Sub-pages, Posts at anytime. Popup your content, Flash news, Facebook Like page, and more you need. 
 * Plugin URI: http://www.wordpress.org/extend/plugins/cc-easy-popup
 * Version: 1.0.0
 * Author: cygnusplugins
 * Author URI: http://codecygnus.com
 * Text Domain: cc-easy-popup
 * License: GPL2 or Later
 */

if ( !defined('CPAS_POPUP_PATH') ) { define('CPAS_POPUP_PATH', plugin_dir_path( __FILE__ )); }
if ( !defined('CPAS_POPUP_URL') ) { define('CPAS_POPUP_URL', plugin_dir_url( __FILE__ )); }
if ( !defined('CPAS_POPUP_POST_URL') ) { define('CPAS_POPUP_POST_URL', admin_url('admin-post.php')); }
if ( !defined('CPAS_POPUP_AJAX_URL') ) { define('CPAS_POPUP_AJAX_URL', admin_url('admin-ajax.php')); }
if ( !defined('CPAS_PLUGIN_PREFIX') ) { define('CPAS_PLUGIN_PREFIX', 'CC_'); }
if ( !defined('CPAS_PLUGIN_NAME') ) { define('CPAS_PLUGIN_NAME', 'CC Popup'); } 
if ( !defined('CPAS_PLUGIN_HOME_URL') ) { define('CPAS_PLUGIN_HOME_URL', home_url()); } 

// User
include (CPAS_POPUP_PATH . 'CC-popup-frontend.php');

// Admin

if(is_admin()) {
	include (CPAS_POPUP_PATH . 'admin/CC-popup-admin-settings.php');
	require_once( CPAS_POPUP_PATH.'scripts.php' );
}