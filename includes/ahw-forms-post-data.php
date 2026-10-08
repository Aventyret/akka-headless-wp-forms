<?php
/**
 * Builds form post data in the v1 shape (ACF fields at top level).
 *
 * Akka\Post::get_single() in akka-headless-wp v3 is not used: it strips ACF
 * fields, overwrites global $post and builds Yoast SEO meta, which fails for
 * the non-public akka_form post type.
 */

class Akka_headless_wp_forms_post_data
{
    public static function get_form_post($post_id)
    {
        $post = get_post($post_id);
        if (!$post || $post->post_status !== 'publish') {
            return null;
        }

        $form_post = [
            'post_id' => $post->ID,
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'post_status' => $post->post_status,
            'slug' => $post->post_name,
        ];

        $fields = function_exists('get_fields') ? get_fields($post->ID) : null;
        if (is_array($fields)) {
            $form_post = array_merge($fields, $form_post);
        }

        return $form_post;
    }
}
