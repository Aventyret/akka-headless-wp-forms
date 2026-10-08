<?php
use \Akka\AkkaBlocks;
use \Akka\Resolvers;
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

        $form_post = PostData::get_form_post($props['formId']);

        if (!$form_post || $form_post['post_type'] != 'akka_form') {
          return $props;
        }

        $form_post['post_content'] = get_the_content(null, false, $props['formId']);
        $props['form'] = apply_filters('ahw_post_data', $form_post);

        return $props;
      },
      'post_types' => apply_filters('ahw_forms_form_block_post_types', ['page']),
    ]);
  }
}

Akka_headless_wp_forms_block::init();
