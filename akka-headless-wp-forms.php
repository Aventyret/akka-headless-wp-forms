<?php
/*
Plugin Name: Akka Headless WP – Forms
Plugin URI: https://github.com/aventyret/akka-wp/blob/main/plugins/akka-headless-wp-forms
Description: Forms plugin for Akka
Author: Mediakooperativet, Äventyret
Author URI: https://aventyret.com
Version: 1.1.0
*/

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)){
    die('Invalid URL');
}

if (defined('AKKA_HEADLESS_WP_FORMS'))
{
    die('Invalid plugin access');
}

define('AKKA_HEADLESS_WP_FORMS',  __FILE__ );
define('AKKA_HEADLESS_WP_FORMS_DIR', plugin_dir_path( __FILE__ ));
define('AKKA_HEADLESS_WP_FORMS_URI', plugin_dir_url( __FILE__ ));
define('AKKA_HEADLESS_WP_FORMS_VER', "1.1.0");

require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-post-type.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-block.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-api.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-comment.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'public/ahw-forms-hooks.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'public/ahw-forms-rest-endpoints.php');
