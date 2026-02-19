<?php
/**
 * Tests for my_plugin_add_list_interactivity().
 *
 * Demonstrates assertEqualHTML with Interactivity API directive injection —
 * the most common case where attribute order can vary between WP versions.
 *
 * @package AssertEqualHTMLExamples
 */

class ListInteractivityTest extends WP_UnitTestCase {

	/**
	 * The render_block filter injects data-wp-interactive, data-wp-context on
	 * the <ul>, and data-wp-on--click on every <li>.
	 *
	 * The expected HTML uses human-readable attribute order — assertEqualHTML
	 * ensures the test doesn't break when WP_HTML_Tag_Processor changes where
	 * it inserts the new attributes.
	 */
	public function test_list_block_gets_interactivity_directives(): void {
		$input = '
			<ul class="wp-block-list">
				<li>First item</li>
				<li>Second item</li>
			</ul>
		';

		$block = array( 'blockName' => 'core/list' );

		$output = my_plugin_add_list_interactivity( $input, $block );

		$expected = '
			<ul
				class="wp-block-list"
				data-wp-interactive="my-plugin/list"
				data-wp-context=\'{"expanded":false}\'
			>
				<li data-wp-on--click="actions.toggle">First item</li>
				<li data-wp-on--click="actions.toggle">Second item</li>
			</ul>
		';

		// ✅ assertEqualHTML normalizes HTML entity encoding, so
		// {"expanded":false} and {&quot;expanded&quot;:false} compare as equal.
		$this->assertEqualHTML( $expected, $output );
	}

}
