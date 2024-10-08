<?php
add_action( 'admin_enqueue_scripts', function() {
  wp_enqueue_script( 'akka-ai', AKKA_HEADLESS_WP_AI_URI . 'public/akka-ai.js', NULL, AKKA_HEADLESS_WP_AI_VER, true );
  wp_localize_script('akka-ai', 'akkaAi', ["postTypes" => Akka_headless_wp_ai::post_types()]);
  wp_enqueue_style( 'akka-ai', AKKA_HEADLESS_WP_AI_URI . 'public/akka-ai.css', NULL, AKKA_HEADLESS_WP_AI_VER, 'all' );

});

add_action('init', function() {
  $metafields = [ 'akka_ai_description', 'akka_ai_length', 'akka_ai_prompt', 'akka_ai_use_description' ];
  foreach(Akka_headless_wp_ai::post_types() as $post_type) {
    foreach( $metafields as $metafield ){
        register_post_meta( $post_type, $metafield, array(
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() {
                return current_user_can( 'edit_posts' );
            }
        ));
    }
  }
});

add_filter( 'wpseo_metadesc', function($description) {
  if (!in_array($post->post_type, Akka_headless_wp_ai::post_types())) {
    return $description;
  }
  if(get_post_meta($post_id, "akka_ai_use_description", true)) {
    return get_post_meta($post_id, "akka_ai_description", true);
  }
  return $description;
});

add_filter('ahw_seo_description', function($description, $post) {
  if (!in_array($post->post_type, Akka_headless_wp_ai::post_types())) {
    return $description;
  }
  if(get_post_meta($post_id, "akka_ai_use_description", true)) {
    return get_post_meta($post_id, "akka_ai_description", true);
  }
  return $description;
}, 10, 2);
