<?php
/**
 * Tests for my_plugin_lazy_load_images().
 *
 * Demonstrates the difference between a brittle assertSame and the resilient
 * assertEqualHTML when testing functions that modify HTML attributes.
 *
 * @package AssertEqualHTMLExamples
 */

class LazyLoadImagesTest extends WP_UnitTestCase {

	/**
	 * Demonstrates why assertSame is fragile for HTML assertions.
	 *
	 * WP_HTML_Tag_Processor inserts new attributes before the existing ones,
	 * so the output is:
	 *
	 *     <img loading="lazy" src="photo.jpg" alt="A photo" class="size-full">
	 *
	 * If the expected string has loading= anywhere else, assertSame fails —
	 * even though the HTML is semantically identical.
	 *
	 * This test is marked skipped so the suite stays green. 
	 * Comment out markTestSkipped() to see it fail.
	 */
	public function test_assertsame_is_fragile(): void {
		$this->markTestSkipped(
			'Intentionally skipped. Remove markTestSkipped() to see how assertSame ' .
			'fails when attribute order differs from the expected string.'
		);

		$input = '<p><img src="photo.jpg" alt="A photo" class="size-full"></p>';

		// This expected string has loading= LAST — but WP_HTML_Tag_Processor
		// puts new attributes first, so the actual output has it FIRST.
		$expected_wrong_order = '<p><img src="photo.jpg" alt="A photo" class="size-full" loading="lazy"></p>';

		// ❌ This will fail because attribute order does not match.
		$this->assertSame( $expected_wrong_order, my_plugin_lazy_load_images( $input ) );
	}

	/**
	 * assertEqualHTML ignores attribute order — the test passes regardless of
	 * where loading="lazy" ends up in the serialized output.
	 */
	public function test_adds_loading_attribute(): void {
		$input    = '<p><img src="photo.jpg" alt="A photo" class="size-full"></p>';
		$expected = '<p><img src="photo.jpg" alt="A photo" class="size-full" loading="lazy"></p>';

		// ✅ Passes no matter where loading= appears in the attribute list.
		$this->assertEqualHTML( $expected, my_plugin_lazy_load_images( $input ) );
	}

	/**
	 * Multiple images in the same content all get the attribute.
	 */
	public function test_adds_loading_to_multiple_images(): void {
		$input = '
			<figure>
				<img src="hero.jpg" alt="Hero">
			</figure>
			<p>
				<img src="thumb.jpg" alt="Thumbnail" class="size-thumbnail">
			</p>
		';

		$expected = '
			<figure>
				<img src="hero.jpg" alt="Hero" loading="lazy">
			</figure>
			<p>
				<img loading="lazy" src="thumb.jpg" alt="Thumbnail" class="size-thumbnail">
			</p>
		';

		$this->assertEqualHTML( $expected, my_plugin_lazy_load_images( $input ) );
	}

	/**
	 * Content with no images passes through unchanged.
	 */
	public function test_content_without_images_is_unchanged(): void {
		$input = '<p>Just a paragraph with <a href="https://example.com">a link</a>.</p>';

		$this->assertEqualHTML( $input, my_plugin_lazy_load_images( $input ) );
	}
}
