<?php
/*
Plugin Name: WP ASearch
Plugin URI: http://phalcosoft.com/
Description: This plugin enables you to search fast using ajax technology with stylish outputs.
Version: 1.0.1
Author: Phalcosoft
Author URI: http://phalcosoft.com/
*/

error_reporting(E_ALL ^ E_NOTICE);
include_once( dirname(__FILE__) . '/classes/config.php' );

$irb_as_globals = new IrbAsGlobals();
$_GLOBALS['irb_as_globals'] = $irb_as_globals;
$root 	= $irb_as_globals;

// Declaring Global classes
include_once( $root->coreDir . '/forms.php' );
include_once( $root->classesDir . '/handler.php' );
include_once( $root->classesDir . '/controller.php' );
include_once( $root->classesDir . '/installer.php' );
include_once( $root->classesDir . '/db.php' );

try{
	global $wpdb;
	$_GLOBALS['irbdb'] = $irbdb;
	$_GLOBALS['irbmysqli'] = $wpdb;
	$root->handler();
	
} catch (Exception $e) {
	$root->handler()->setMessage('error', $e->getMessage());
	$headerMsg = $root->handler()->getMessage();
}
