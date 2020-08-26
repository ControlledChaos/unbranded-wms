<?php
/**
 * Update/Install Plugin/Theme network administration panel.
 *
 * @package App_Package
 * @subpackage Network
 * @since 3.1.0
 */

if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'update-selected', 'activate-plugin', 'update-selected-themes' ) ) )
	define( 'IFRAME_REQUEST', true );

// Load the website management system.
require_once( dirname( __FILE__ ) . '/admin.php' );

require( APP_ADMIN_PATH . '/update.php' );