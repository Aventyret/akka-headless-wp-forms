<?php
use \Akka_headless_wp_akka_post_types as PostTypes;
use \Akka_headless_wp_resolvers as Resolvers;

class Akka_headless_wp_forms_comment
{
  private static $post_type_slug = 'akka_form';

  public static function hooks()
  {
    add_action('admin_menu', function () {
      remove_menu_page('edit-comments.php');
    });
  }
}

Akka_headless_wp_forms_comment::hooks();
