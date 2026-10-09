<?php
use \AkkaForms\Api;
use \Akka\Router;

add_action( 'rest_api_init', function () {
  register_rest_route( AKKA_API_BASE, '/form/(?P<form_id>[0-9]+)', array(
    'methods' => 'POST',
    'callback' => [ Api::class, 'submit_form' ],
    'permission_callback' => [ Router::class, 'can_get_content' ],
  ) );
} );
