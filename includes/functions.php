<?php
/**
 * Plugin functions — the code snippets from the article.
 *
 * These functions are deliberately kept filter-free so they can be called
 * directly in unit tests without side-effects.
 */

declare( strict_types=1 );

/**
 * Adds loading="lazy" to every <img> tag in a content string.
 *
 * @param string $content HTML content.
 * @return string Modified HTML content.
 */
function my_plugin_lazy_load_images( string $content ): string {
	$processor = new WP_HTML_Tag_Processor( $content );
	while ( $processor->next_tag( 'img' ) ) {
		$processor->set_attribute( 'loading', 'lazy' );
	}
	return $processor->get_updated_html();
}

/**
 * Marks external links with data-external="true" and rel="noopener noreferrer".
 *
 * Internal links (same host as home_url()) are left untouched.
 *
 * @param string $content HTML content.
 * @return string Modified HTML content.
 */
function my_plugin_mark_external_links( string $content ): string {
	$processor = new WP_HTML_Tag_Processor( $content );

	while ( $processor->next_tag( 'a' ) ) {
		$href = $processor->get_attribute( 'href' );

		if ( $href && str_starts_with( $href, 'http' ) && ! str_contains( $href, home_url() ) ) {
			$processor->set_attribute( 'data-external', 'true' );
			$rel = $processor->get_attribute( 'rel' );
			$processor->set_attribute( 'rel', trim( ( $rel ?? '' ) . ' noopener noreferrer' ) );
		}
	}

	return $processor->get_updated_html();
}

/**
 * Renders the "my-plugin/card" dynamic block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Inner block content.
 * @return string Block HTML output.
 */
function my_plugin_render_card_block( array $attributes, string $content ): string {
	$tag = new WP_HTML_Tag_Processor(
		'<div class="wp-block-my-plugin-card"></div>'
	);
	$tag->next_tag();

	if ( ! empty( $attributes['backgroundColor'] ) ) {
		$tag->set_attribute(
			'style',
			'background-color: ' . esc_attr( $attributes['backgroundColor'] ) . ';'
		);
	}

	if ( ! empty( $attributes['className'] ) ) {
		foreach ( explode( ' ', $attributes['className'] ) as $class ) {
			$tag->add_class( $class );
		}
	}

	return str_replace(
		'</div>',
		$content . '</div>',
		$tag->get_updated_html()
	);
}

/**
 * Injects Interactivity API directives into core/list block HTML.
 *
 * Hooked to render_block in the main plugin file.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data including blockName and attrs.
 * @return string Modified block HTML.
 */
function my_plugin_add_list_interactivity( string $block_content, array $block ): string {
	if ( 'core/list' !== $block['blockName'] ) {
		return $block_content;
	}

	$p = new WP_HTML_Tag_Processor( $block_content );

	if ( $p->next_tag( 'ul' ) ) {
		$p->set_attribute( 'data-wp-interactive', 'my-plugin/list' );
		$p->set_attribute( 'data-wp-context', wp_json_encode( array( 'expanded' => false ) ) );
	}

	while ( $p->next_tag( 'li' ) ) {
		$p->set_attribute( 'data-wp-on--click', 'actions.toggle' );
	}

	return $p->get_updated_html();
}
