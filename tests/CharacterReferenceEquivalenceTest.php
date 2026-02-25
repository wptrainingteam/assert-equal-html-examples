<?php
/**
 * Tests demonstrating assertEqualHTML's HTML character reference normalization.
 *
 * Any given character has multiple valid representations in HTML: a literal
 * character, a named reference (&not;), a decimal reference (&#172;), a
 * padded decimal (&#0172;), a hex reference (&#xAC;), a padded hex
 * (&#x0000AC;), or even a named reference without a semicolon (&not).
 * assertEqualHTML treats all of them as equivalent. assertSame does not.
 *
 * @package AssertEqualHTMLExamples
 */

class CharacterReferenceEquivalenceTest extends WP_UnitTestCase {

	/**
	 * Demonstrates why assertSame is fragile for HTML character references.
	 *
	 * The NOT SIGN (¬, U+00AC) can be encoded as &not; or &#172; or &#xAC;,
	 * among others. To assertSame, those strings look completely different even
	 * though every browser renders them identically.
	 *
	 * This test is marked skipped so the suite stays green.
	 * Comment out markTestSkipped() to see it fail.
	 */
	public function test_assertsame_fails_for_character_references(): void {
		$this->markTestSkipped(
			'Intentionally skipped. Remove markTestSkipped() to see how assertSame ' .
			'fails when the same character is encoded differently.'
		);

		$expected = '<meta name="&not;">';
		$actual   = '<meta name="&#172;">';

		// ❌ This will fail even though both encode the same character (¬).
		$this->assertSame( $expected, $actual );
	}

	/**
	 * assertEqualHTML normalizes all representations of a character to the same
	 * value before comparing, so every encoding of ¬ (U+00AC) is equivalent.
	 *
	 * Representations covered:
	 *  - Literal UTF-8 character
	 *  - Named reference:          &not;
	 *  - Decimal reference:        &#172;
	 *  - Padded decimal reference: &#0172;
	 *  - Hex reference:            &#xAC;
	 *  - Padded hex reference:     &#x0000AC;
	 *  - Named reference without semicolon: &not
	 */
	public function test_all_character_reference_forms_are_equivalent(): void {
		$expected = <<<HTML
		<meta
			not-literal="¬"
			not-named="¬"
			not-decimal="¬"
			not-decimal-padded="¬"
			not-hex="¬"
			not-hex-padded="¬"
			also-not="¬"
		>
		HTML;

		$actual = <<<HTML
		<meta
			not-literal="¬"
			not-named="&not;"
			not-decimal="&#172;"
			not-decimal-padded="&#0172;"
			not-hex="&#xAC;"
			not-hex-padded="&#x0000AC;"
			also-not="&not"
		>
		HTML;

		// ✅ Passes because assertEqualHTML decodes all character references
		// before comparing — literal, named, decimal, hex, with or without
		// a trailing semicolon.
		$this->assertEqualHTML( $expected, $actual );
	}
}
