<?php
/**
 * Plugin Name: assertEqualHTML examples
 * Description: Companion plugin for the "Stop hardcoding HTML strings in your WordPress PHPUnit tests" article.
 * Version:     1.0.0
 * Requires at least: 6.9
 * Requires PHP: 8.1
 */

declare( strict_types=1 );

require_once __DIR__ . '/includes/functions.php';

add_filter( 'the_content', 'my_plugin_lazy_load_images' );
add_filter( 'the_content', 'my_plugin_mark_external_links' );
add_filter( 'render_block', 'my_plugin_add_list_interactivity', 10, 2 );
