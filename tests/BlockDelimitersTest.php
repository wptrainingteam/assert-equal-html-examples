<?php
/**
 * Tests demonstrating assertEqualHTML with serialized block markup.
 *
 * WordPress block markup combines HTML with block delimiter comments
 * (<!-- wp:namespace/name {...} -->). assertEqualHTML understands both:
 * it normalizes JSON attribute order inside delimiter comments, and HTML
 * attribute order in the markup itself.
 *
 * @package AssertEqualHTMLExamples
 */

class BlockDelimitersTest extends WP_UnitTestCase {

	/**
	 * Block comment attribute order does not affect the comparison.
	 *
	 * assertEqualHTML normalizes the JSON inside block delimiter comments,
	 * so {"align":"center","fontSize":"large"} and
	 * {"fontSize":"large","align":"center"} are treated as equal.
	 * The HTML class order is normalized too.
	 */
	public function test_block_comment_attribute_order_is_normalized(): void {
		$expected = '<!-- wp:paragraph {"align":"center","fontSize":"large"} -->'
			. '<p class="has-text-align-center has-large-font-size">Hello</p>'
			. '<!-- /wp:paragraph -->';

		// Same block, same HTML — only JSON key order and class order differ.
		$actual = '<!-- wp:paragraph {"fontSize":"large","align":"center"} -->'
			. '<p class="has-large-font-size has-text-align-center">Hello</p>'
			. '<!-- /wp:paragraph -->';

		$this->assertEqualHTML( $expected, $actual );
	}

	/**
	 * Intentionally failing test — run once to capture a screenshot of the
	 * BLOCK["namespace/name"] tree format, then remove this method.
	 *
	 * The failure output shows nested blocks as BLOCK["core/group"] and
	 * BLOCK["core/paragraph"] entries, with their JSON attributes and HTML
	 * structure rendered in separate, indented sections. Compare this to
	 * what assertSame would show for the same mismatch — raw serialized
	 * strings with no structure.
	 */
	public function test_nested_block_mismatch(): void {
		$this->markTestSkipped(
			'Intentionally skipped. Remove markTestSkipped() to see how assertSame ' .
			'fails when attribute order differs from the expected string.'
		);

		$expected = <<<'HTML'
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Hello world</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML;

		$actual = <<<'HTML'
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"align":"left"} -->
<p class="has-text-align-left">Hello world</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML;

		// This assertion intentionally fails to show the tree diff output.
		// Run: npm run test:php -- --filter test_nested_block_mismatch_for_screenshot
		$this->assertEqualHTML( $expected, $actual );
	}
}
