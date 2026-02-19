<?php
/**
 * Tests for my_plugin_render_card_block().
 *
 * Demonstrates assertEqualHTML with block render callbacks that produce
 * complex markup with multiple attributes and nested content.
 *
 * @package AssertEqualHTMLExamples
 */

class RenderCardBlockTest extends WP_UnitTestCase {

	/**
	 * The card block renders with a background-color style and extra classes.
	 *
	 * Note: assertEqualHTML normalizes class order — so it doesn't matter
	 * whether add_class() puts "is-style-outlined" before or after
	 * "wp-block-my-plugin-card".
	 */
	public function test_card_block_renders_with_background_color(): void {
		$attributes = array(
			'backgroundColor' => '#f5f5f5',
			'className'       => 'is-style-outlined my-custom-class',
		);

		$inner_content = '<p class="wp-block-paragraph">Hello</p>';

		$output = my_plugin_render_card_block( $attributes, $inner_content );

		// Attribute order and class order don't need to match exactly.
		$expected = '
			<div
				class="is-style-outlined my-custom-class wp-block-my-plugin-card"
				style="background-color: #f5f5f5;"
			>
				<p class="wp-block-paragraph">Hello</p>
			</div>
		';

		$this->assertEqualHTML( $expected, $output );
	}

	/**
	 * Without optional attributes, the card renders with the base class only.
	 */
	public function test_card_block_renders_without_optional_attributes(): void {
		$output = my_plugin_render_card_block( array(), '<p>Content</p>' );

		$expected = '<div class="wp-block-my-plugin-card"><p>Content</p></div>';

		$this->assertEqualHTML( $expected, $output );
	}

	/**
	 * Style whitespace differences don't cause false failures.
	 *
	 * "background-color: #f5f5f5;" and "background-color:#f5f5f5" are
	 * semantically equivalent and assertEqualHTML recognizes them as equal.
	 */
	public function test_style_whitespace_is_normalized(): void {
		$attributes = array( 'backgroundColor' => '#f5f5f5' );

		$output = my_plugin_render_card_block( $attributes, '' );

		// Either form of the style value should match.
		$expected_with_spaces    = '<div class="wp-block-my-plugin-card" style="background-color: #f5f5f5;"></div>';
		$expected_without_spaces = '<div class="wp-block-my-plugin-card" style="background-color:#f5f5f5"></div>';

		$this->assertEqualHTML( $expected_with_spaces, $output );
		$this->assertEqualHTML( $expected_without_spaces, $output );
	}
}
