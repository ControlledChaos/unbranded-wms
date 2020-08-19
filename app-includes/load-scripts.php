<?php
/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
error_reporting(0);

// Absolute path to the system directory.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

define( 'APP_INC', 'app-includes' );

$load = $_GET['load'];
if ( is_array( $load ) ) {
	$load = implode( '', $load );
}

$load = preg_replace( '/[^a-z0-9,_-]+/i', '', $load );
$load = array_unique( explode( ',', $load ) );

if ( empty( $load ) ) {
	exit;
}

require( ABSPATH . APP_INC . '/backend/noop.php' );
require( ABSPATH . APP_INC . '/script-loader.php' );
require( ABSPATH . APP_INC . '/version.php' );

// 1 year.
$expires_offset = 31536000;
$out = '';

$wp_scripts = new WP_Scripts();
wp_default_scripts( $wp_scripts );

if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $app_version ) {

	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, [ 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ] ) ) {
		$protocol = 'HTTP/1.0';
	}

	header( "$protocol 304 Not Modified" );

	exit();
}

foreach ( $load as $handle ) {

	if ( ! array_key_exists( $handle, $wp_scripts->registered ) ) {
		continue;
	}

	$path = ABSPATH . $wp_scripts->registered[$handle]->src;
	$out .= get_file( $path ) . "\n";
}

header( "Etag: $app_version" );
header( 'Content-Type: application/javascript; charset=UTF-8' );
header( 'Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT' );
header( "Cache-Control: public, max-age=$expires_offset" );

echo $out;

exit;