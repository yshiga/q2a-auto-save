<?php

/*
	Plugin Name: Auto Save
	Plugin URI: https://github.com/yshiga/q2a-auto-save
	Plugin Description: Provide an automatic save.
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

define( 'AS_DIR', dirname( __FILE__ ) );
define('AS_KEY_QUESTION', 'q2a_as_question');
define('AS_KEY_ANSWER', 'q2a_as_answer');
define('AS_KEY_COMMENT', 'q2a_as_comment');
define('AS_KEY_BLOG', 'q2a_as_blog');

// language
qa_register_plugin_phrases('qa-auto-save-lang-*.php', 'qa_as_lang');
// layer
qa_register_plugin_layer('qa-auto-save-layer.php','Auto Save Layer');
// page
qa_register_plugin_module('page', 'qa-auto-save-response.php', 'qa_auto_save_response_page', 'Auto Save Page');
// event
qa_register_plugin_module('event', 'qa-auto-save-event.php', 'q2a_auto_save_event', 'Auto Save Event');
