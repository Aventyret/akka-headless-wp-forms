<?php
add_action( 'rest_api_init', function () {
  register_rest_route( AKKA_API_BASE, '/form/(?P<form_id>[0-9]+)', array(
    'methods' => 'POST',
    'callback' => 'Akka_headless_wp_forms_api::submit_form',
    'permission_callback' => 'Akka_headless_wp_content::can_get_content',
  ) );
} );
