<?php
namespace AkkaForms;

class Comment
{
  private static $post_type_slug = 'akka_form';

  public static function hooks()
  {
    add_action('admin_menu', function () {
      remove_menu_page('edit-comments.php');
    });
  }
}

Comment::hooks();
