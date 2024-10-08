<?php
add_action( 'admin_enqueue_scripts', function() {
  wp_enqueue_script( 'akka-ai', AKKA_HEADLESS_WP_AI_URI . 'public/akka-ai.js', NULL, AKKA_HEADLESS_WP_AI_VER, true );
  wp_enqueue_style( 'akka-ai', AKKA_HEADLESS_WP_AI_URI . 'public/akka-ai.css', NULL, AKKA_HEADLESS_WP_AI_VER, 'all' );

});

add_action('init', function() {
  $metafields = [ 'akka_ai_description', 'akka_ai_length', 'akka_ai_prompt', 'akka_ai_use_description' ];
  foreach( $metafields as $metafield ){
      // Pass an empty string to register the meta key across all existing post types.
      register_post_meta( '', $metafield, array(
          'show_in_rest' => true,
          'type' => 'string',
          'single' => true,
          'sanitize_callback' => 'sanitize_text_field',
          'auth_callback' => function() {
              return current_user_can( 'edit_posts' );
          }
      ));
  }
});
