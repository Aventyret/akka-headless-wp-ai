<?php
add_action( 'admin_enqueue_scripts', function() {
  wp_enqueue_script( 'akka-ai', AKKA_HEADLESS_WP_AI_URI . 'public/akka-ai.js', NULL, AKKA_HEADLESS_WP_AI_VER, true );
  wp_localize_script('akka-ai', 'akkaAi', ["postTypes" => Akka_headless_wp_ai::post_types()]);
  wp_enqueue_style( 'akka-ai', AKKA_HEADLESS_WP_AI_URI . 'public/akka-ai.css', NULL, AKKA_HEADLESS_WP_AI_VER, 'all' );

});

add_action('init', function() {
  foreach(Akka_headless_wp_ai::post_types() as $post_type) {
    register_post_meta( $post_type, 'akka_ai', array(
        "show_in_rest" => [
            "schema" => [
                "type" => "object",
                "properties" => [
                    "description" => [
                        "type" => "string",
                    ],
                    "length" => [
                        "type" => "string",
                    ],
                    "prompt" => [
                        "type" => "string",
                    ],
                    "use_description" => [
                        "type" => "boolean",
                    ],
                ],
            ],
        ],
        'single' => true,
        'auth_callback' => function() {
            return current_user_can( 'edit_posts' );
        }
    ));
  }
});

add_filter( 'wpseo_metadesc', function($description) {
  if (!in_array(get_post_type(), Akka_headless_wp_ai::post_types())) {
    return $description;
  }
  if($akka_ai = get_post_meta(get_the_id(), "akka_ai", true)) {
    if ($akka_ai["use_description"]) {
      return $akka_ai["description"];
    }
  }
  return $description;
});

add_filter( 'the_seo_framework_custom_field_description', function($description) {
  if (!in_array(get_post_type(), Akka_headless_wp_ai::post_types())) {
    return $description;
  }
  if($akka_ai = get_post_meta(get_the_id(), "akka_ai", true)) {
    if ($akka_ai["use_description"]) {
      return $akka_ai["description"];
    }
  }
  return $description;
});

add_filter('ahw_seo_description', function($description, $post) {
  if (!in_array($post->post_type, Akka_headless_wp_ai::post_types())) {
    return $description;
  }
  if($akka_ai = get_post_meta($post->ID, "akka_ai", true)) {
    if ($akka_ai["use_description"]) {
      return $akka_ai["description"];
    }
  }
  return $description;
}, 10, 2);
