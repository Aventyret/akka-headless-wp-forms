<?php
use \Akka\AkkaBlocks;
use \Akka\Resolvers;
use \Akka\Post;
use \Akka_headless_wp_forms_post_data as PostData;

class Akka_headless_wp_forms_block
{
  public static function init()
  {
    AkkaBlocks::register_block_type('akka/form', [
      'akka_component_name' => apply_filters('ahw_forms_form_component_name', 'AkkaForm'),
      'block_props_callback' => function ($post_id, $block_attributes) {
        $props = $block_attributes;

        if (!Resolvers::resolve_field($props, 'formId')) {
          return $props;
        }

        $props['form'] = Post::get_single($props['formId']);

        return $props;
      },
      'post_types' => apply_filters('ahw_forms_form_block_post_types', ['page']),
    ]);
  }
}

Akka_headless_wp_forms_block::init();
