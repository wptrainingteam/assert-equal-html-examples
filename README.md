# assertEqualHTML examples

Companion plugin for the article **"A Better Way to Assert HTML in WordPress with assertEqualHTML()"** on the WordPress Developer Blog.

Contains the four plugin functions from the article and their PHPUnit tests, all using `assertEqualHTML()` from `WP_UnitTestCase`.

## Requirements

- PHP 8.1+
- Node.js 20+ and npm
- Docker (for `wp-env`)
- Composer

## Quick start

```bash
# 1. Install dependencies (composer vendor/ is mounted inside the Docker container)
npm install
composer install

# 2. Start the WordPress test environment (requires Docker)
npm run env:start

# 3. Run all tests
npm run test:php
```

On first run `wp-env` pulls the WordPress Docker image (~300 MB). Subsequent runs start instantly.

## What's being tested

| Test file | Function | Article section |
|---|---|---|
| `LazyLoadImagesTest.php` | `my_plugin_lazy_load_images()` | Basic usage |
| `MarkExternalLinksTest.php` | `my_plugin_mark_external_links()` | HTML API transformations |
| `RenderCardBlockTest.php` | `my_plugin_render_card_block()` | Block render callbacks |
| `ListInteractivityTest.php` | `my_plugin_add_list_interactivity()` | Interactivity API directives |

## The fragile test demo

`class-test-lazy-load-images.php` includes `test_assertsame_is_fragile()`, which is skipped by default. Remove the `markTestSkipped()` call to see how `assertSame` fails when attribute order doesn't match — then note how `test_adds_loading_attribute()` passes with the same inputs.

## Project structure

```
assert-equal-html-examples/
├── assert-equal-html-examples.php   # Plugin entry point (hooks)
├── includes/
│   └── functions.php                # Plugin functions (no hooks — testable)
├── tests/
│   ├── bootstrap.php                # PHPUnit bootstrap
│   ├── LazyLoadImagesTest.php
│   ├── MarkExternalLinksTest.php
│   ├── RenderCardBlockTest.php
│   └── ListInteractivityTest.php
├── phpunit.xml.dist
├── composer.json
├── package.json
└── .wp-env.json
```

## Running a single test class

```bash
npm run test:php:filter LazyLoadImagesTest
npm run test:php:filter MarkExternalLinksTest
npm run test:php:filter RenderCardBlockTest
npm run test:php:filter ListInteractivityTest
```

## Notes

- `assertEqualHTML()` requires WordPress 6.9 or later.
- The `tests/bootstrap.php` expects `WP_TESTS_DIR` to point to the WordPress test library. `wp-env` sets this automatically inside the Docker container.
- All plugin functions live in `includes/functions.php` (no `add_filter` calls) so tests can call them directly without side-effects.
