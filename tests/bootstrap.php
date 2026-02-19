<?php
/**
 * PHPUnit bootstrap — loads the WordPress test environment.
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Error: WordPress test library not found at {$_tests_dir}." . PHP_EOL;
	echo "Run: composer run install-wp-tests (or see README.md)" . PHP_EOL;
	exit( 1 );
}

// Make tests_add_filter() available.
require "{$_tests_dir}/includes/functions.php";

/**
 * Load plugin functions (without the filter registrations).
 */
function _register_article_functions(): void {
	require dirname( __DIR__ ) . '/includes/functions.php';
}
tests_add_filter( 'muplugins_loaded', '_register_article_functions' );

// Boot WordPress.
require "{$_tests_dir}/includes/bootstrap.php";
