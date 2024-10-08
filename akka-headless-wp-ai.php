<?php
/*
Plugin Name: Akka Headless WP – AI
Plugin URI: https://github.com/aventyret/akka-wp/blob/main/plugins/akka-headless-wp-ai
Description: AI plugin for Akka
Author: Mediakooperativet, Äventyret
Author URI: https://aventyret.com
Version: 0.0.1
*/

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)){
    die('Invalid URL');
}

if (defined('AKKA_HEADLESS_WP_AI'))
{
    die('Invalid plugin access');
}

define('AKKA_HEADLESS_WP_AI',  __FILE__ );
define('AKKA_HEADLESS_WP_AI_DIR', plugin_dir_path( __FILE__ ));
define('AKKA_HEADLESS_WP_AI_URI', plugin_dir_url( __FILE__ ));
define('AKKA_HEADLESS_WP_AI_VER', "0.0.1");

require_once(AKKA_HEADLESS_WP_AI_DIR . 'includes/ahw-ai.php');
require_once(AKKA_HEADLESS_WP_AI_DIR . 'public/ahw-ai-api-endpoints.php');
require_once(AKKA_HEADLESS_WP_AI_DIR . 'public/ahw-ai-hooks.php');
