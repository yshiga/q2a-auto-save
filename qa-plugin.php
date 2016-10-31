<?php

/*
	Plugin Name: Auto Save
	Plugin URI: Provide an automatic save.
	Plugin Description: Test Plugin
	Plugin Version: 1.0
	Plugin Date: 2016-10-28
	Plugin Author: 38qa.net
	Plugin Author URI: http://38qa.net/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.7
	Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// layer
qa_register_plugin_layer('qa-auto-save-layer.php','Auto Save Layer');
// page
qa_register_plugin_module('page', 'qa-auto-save-response.php', 'qa_auto_save_response_page', 'Auto Save Page');
