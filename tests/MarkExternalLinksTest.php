<?php
/**
 * Tests for my_plugin_mark_external_links().
 *
 * @package AssertEqualHTMLExamples
 */

class MarkExternalLinksTest extends WP_UnitTestCase {

	/**
	 * External links get data-external="true" and rel="noopener noreferrer".
	 */
	public function test_external_links_get_marked(): void {
		$input = '<p>Visit <a href="https://example.com" class="external-link">example.com</a></p>';

		$expected = '<p>Visit <a href="https://example.com" class="external-link" data-external="true" rel="noopener noreferrer">example.com</a></p>';

		$this->assertEqualHTML( $expected, my_plugin_mark_external_links( $input ) );
	}

	/**
	 * Internal links (same domain as home_url()) are left untouched.
	 */
	public function test_internal_links_are_unchanged(): void {
		$input    = '<p>Read <a href="' . home_url( '/about' ) . '">about us</a></p>';
		$expected = $input;

		$this->assertEqualHTML( $expected, my_plugin_mark_external_links( $input ) );
	}

	/**
	 * An existing rel attribute is preserved and extended with the new values.
	 */
	public function test_existing_rel_attribute_is_extended(): void {
		$input    = '<p><a href="https://example.com" rel="sponsored">Sponsor</a></p>';
		$expected = '<p><a href="https://example.com" rel="sponsored noopener noreferrer" data-external="true">Sponsor</a></p>';

		$this->assertEqualHTML( $expected, my_plugin_mark_external_links( $input ) );
	}

	/**
	 * Mixed content: external links are marked, internal ones are not.
	 */
	public function test_mixed_links(): void {
		$internal_url = home_url( '/contact' );

		$input = '
			<nav>
				<a href="' . $internal_url . '">Contact</a>
				<a href="https://wordpress.org">WordPress.org</a>
			</nav>
		';

		$expected = '
			<nav>
				<a href="' . $internal_url . '">Contact</a>
				<a href="https://wordpress.org" data-external="true" rel="noopener noreferrer">WordPress.org</a>
			</nav>
		';

		$this->assertEqualHTML( $expected, my_plugin_mark_external_links( $input ) );
	}
}
