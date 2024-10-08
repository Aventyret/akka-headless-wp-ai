<?php
use \Akka_headless_wp_utils as Utils;
use \Akka_headless_wp_resolvers as Resolvers;

define("AKKA_OPEN_AI_SECRET", getenv("AKKA_OPEN_AI_SECRET"));

class Akka_headless_wp_ai {
  public static function get_meta_description($data) {
    $post_id = Utils::getRouteParam($data, 'post_id');
    $post = get_post($post_id);
    if (!$post) {
      return;
    }
    $length = 160;
    if (isset($_GET['length']) && $_GET['length']) {
      $length = $_GET['length'];
    }
    $messages = [
      [
        "role" => "system",
        "content" => "Du ska hjälpa mig att skriva en kort sökordsoptimerad sammanfattning av en text."
      ],
      [
        "role" => "user",
        "content" => "Skriv en sökordsoptimerad sammanfattning på maximalt " . $length . " tecken av den här texten: " . $post->post_content
      ]
    ];
    if (isset($_GET['prompt']) && $_GET['prompt']) {
      $messages[] = [
        "role" => "user",
        "content" => urldecode($_GET['prompt'])
      ];
    }
    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
      "headers" => [
        "Authorization" => "Bearer " . AKKA_OPEN_AI_SECRET,
        "Content-Type" => "application/json"
      ],
      "body" => json_encode([
        "model" => "gpt-3.5-turbo",
        "messages" => $messages
      ])
    ]);
    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (!isset($data["choices"]) || empty($data["choices"])) {
      return [
        "content" => null
      ];
    }
    $choice = $data["choices"][0];
    return [
      "content" => $choice["message"]["content"],
    ];
  }

  public static function post_types() {
    return apply_filters("ahw_ai_post_types", ["post", "page"]);
  }
}
