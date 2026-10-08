<?php
/*
Plugin Name: Akka Headless WP – Forms
Plugin URI: https://github.com/aventyret/akka-wp/blob/main/plugins/akka-headless-wp-forms
Description: Forms plugin for Akka
Author: Mediakooperativet, Äventyret
Author URI: https://aventyret.com
Version: 3.0.0
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
define('AKKA_HEADLESS_WP_FORMS_VER', "3.0.0");

// ACF field type 'unique_id' used by form fields. Bundled since akka-headless-wp v3 dropped it.
add_action('acf/include_field_types', function () {
  if (acf_get_field_type('unique_id')) {
    return;
  }
  if (!class_exists('\\PhilipNewcomer\\ACF_Unique_ID_Field\\ACF_Field_Unique_ID')) {
    require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'vendor/philipnewcomer/acf-unique-id-field/src/ACF_Field_Unique_ID.php');
  }
  new \PhilipNewcomer\ACF_Unique_ID_Field\ACF_Field_Unique_ID();
}, 20);

require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-post-data.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-post-type.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-block.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-api.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'includes/ahw-forms-comment.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'public/ahw-forms-hooks.php');
require_once(AKKA_HEADLESS_WP_FORMS_DIR . 'public/ahw-forms-rest-endpoints.php');
