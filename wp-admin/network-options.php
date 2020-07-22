<?php
/**
 * Network settings administration panel.
 *
 * @package App_Package
 * @subpackage Network
 * @since 3.0.0
 */

require_once( dirname( __FILE__ ) . '/admin.php' );

wp_redirect( network_admin_url('settings.php') );