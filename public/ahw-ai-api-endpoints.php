<?php
add_action( 'rest_api_init', function () {
  register_rest_route( AKKA_API_BASE, '/ai/meda_description/(?P<post_id>[0-9]+)', array(
    'methods' => 'GET',
    'callback' => 'Akka_headless_wp_ai::get_meta_description',
    'permission_callback' => function () {
      return current_user_can("edit_posts");
    },
  ) );
} );
