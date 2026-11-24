# Akka Headless WP AI

This plugin enables AI features when running Akka headless WordPress.

## Requirements

- `aventyret/akka-headless-wp` **>= 2.0** (tested against 3.7.0).
  Core 2.0 renamed its hooks from the `ahw_`/`awh_` prefix to `akka_`, and
  moved the REST namespace from `headless/v1` to `akka/v2`. This plugin
  follows core 2.x and later only.
- `AKKA_OPEN_AI_SECRET` in the environment.

## Installation

Add this repository to your `composer.json`:

```
  "repositories": [
    ...
    {
      "type": "vcs",
      "url": "https://github.com/Aventyret/akka-headless-wp-ai.git"
    }
  ]
```

Install the plugin with `composer require aventyret/akka-headless-wp-ai`, then
activate it in your WP admin.

## Hooks

### `akka_ai_post_types`

Filters the post types that get the AI meta panel and the AI meta description.

```php
add_filter('akka_ai_post_types', function ($post_types) {
    return array_merge($post_types, ['landing_page']);
});
```

The former name `ahw_ai_post_types` is still applied, before
`akka_ai_post_types`. New code should use `akka_ai_post_types`.

## REST API

`GET <AKKA_API_BASE>/ai/meda_description/<post_id>`, with the optional query
parameters `length` and `prompt`. The route is registered under core's
`AKKA_API_BASE` constant, so it follows core's namespace.
