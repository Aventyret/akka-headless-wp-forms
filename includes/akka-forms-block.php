<?php
namespace AkkaForms;

use \Akka\AkkaBlocks;
use \Akka\Resolvers;
use \Akka\Post;

class Block
{
  public static function init()
  {
    AkkaBlocks::register_block_type('akka/form', [
      'akka_component_name' => apply_filters('akka_forms_form_component_name', 'AkkaForm'),
      'block_props_callback' => function ($post_id, $block_attributes) {
        $props = $block_attributes;

        if (!Resolvers::resolve_field($props, 'formId')) {
          return $props;
        }

        $props['form'] = Post::get_single($props['formId']);

        return $props;
      },
      'post_types' => apply_filters('akka_forms_form_block_post_types', ['page']),
    ]);
  }
}

Block::init();
