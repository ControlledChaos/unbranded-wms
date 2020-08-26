<?php
/**
 * Administration Bootstrap
 *
 * @package App_Package
 * @subpackage Administration
 */

/**
 * In administration screens
 *
 * @since Previous 2.3.2
 */
if ( ! defined( 'APP_ADMIN' ) ) {
	define( 'APP_ADMIN', true );
}

if ( ! defined( 'APP_NETWORK_ADMIN' ) ) {
	define( 'APP_NETWORK_ADMIN', false );
}

if ( ! defined( 'WP_USER_ADMIN' ) ) {
	define( 'WP_USER_ADMIN', false );
}

if ( ! APP_NETWORK_ADMIN && ! WP_USER_ADMIN ) {
	define( 'WP_BLOG_ADMIN', true );
}

if ( isset( $_GET['import'] ) && ! defined( 'WP_LOAD_IMPORTERS' ) ) {
	define( 'WP_LOAD_IMPORTERS', true );
}

nocache_headers();

if ( get_option( 'db_upgraded' ) ) {

	flush_rewrite_rules();
	update_option( 'db_upgraded',  false );

	/**
	 * Fires on the next page load after a successful DB upgrade.
	 *
	 * @since Previous 2.8.0
	 */
	do_action( 'after_db_upgrade' );

} elseif ( get_option( 'db_version' ) != $wp_db_version && empty( $_POST ) ) {

	if ( ! is_network() ) {

		wp_redirect( admin_url( 'upgrade.php?_wp_http_referer=' . urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );

		exit;

	/**
	 * Filters whether to attempt to perform the network DB upgrade routine.
	 *
	 * In single site, the user would be redirected to APP_ADMIN_DIR/upgrade.php.
	 * In network, the DB upgrade routine is automatically fired, but only
	 * when this filter returns true.
	 *
	 * If the network is 50 sites or less, it will run every time. Otherwise,
	 * it will throttle itself to reduce load.
	 *
	 * @since Previous 3.0.0
	 * @param bool $do_mu_upgrade Whether to perform the network upgrade routine. Default true.
	 */
	} elseif ( apply_filters( 'do_mu_upgrade', true ) ) {

		$c = get_blog_count();

		/*
		 * If there are 50 or fewer sites, run every time. Otherwise, throttle to reduce load:
		 * attempt to do no more than threshold value, with some +/- allowed.
		 */
		if ( $c <= 50 || ( $c > 50 && mt_rand( 0, (int)( $c / 50 ) ) == 1 ) ) {

			require_once( APP_INC_PATH . '/http.php' );
			$response = wp_remote_get( admin_url( 'upgrade.php?step=1' ), [ 'timeout' => 120, 'httpversion' => '1.1' ] );

			// This action is documented in APP_ADMIN_DIR/network/upgrade.php.
			do_action( 'after_mu_upgrade', $response );
			unset( $response );
		}

		unset( $c );
	}
}

// Load administration files.
require_once( APP_INC_PATH . '/backend/load-admin.php' );

auth_redirect();

// Schedule trash collection.
if ( ! wp_next_scheduled( 'wp_scheduled_delete' ) && ! wp_installing() ) {
	wp_schedule_event( time(), 'daily', 'wp_scheduled_delete' );
}

// Schedule Transient cleanup.
if ( ! wp_next_scheduled( 'delete_expired_transients' ) && ! wp_installing() ) {
	wp_schedule_event( time(), 'daily', 'delete_expired_transients' );
}

set_screen_options();

$date_format = __( 'F j, Y' );
$time_format = __( 'g:i a' );

wp_enqueue_script( 'common' );

/**
 * $pagenow is set in vars.php
 * $wp_importers is sometimes set in APP_INC_PATH . '/backend/import.php
 * The remaining variables are imported as globals elsewhere, declared as globals here.
 *
 * @global string $pagenow
 * @global array  $wp_importers
 * @global string $hook_suffix
 * @global string $plugin_page
 * @global string $typenow
 * @global string $taxnow
 */
global $pagenow, $wp_importers, $hook_suffix, $plugin_page, $typenow, $taxnow;

$page_hook = null;

$editing = false;

if ( isset( $_GET['page'] ) ) {
	$plugin_page = wp_unslash( $_GET['page'] );
	$plugin_page = plugin_basename( $plugin_page );
}

if ( isset( $_REQUEST['post_type'] ) && post_type_exists( $_REQUEST['post_type'] ) ) {
	$typenow = $_REQUEST['post_type'];
} else {
	$typenow = '';
}

if ( isset( $_REQUEST['taxonomy'] ) && taxonomy_exists( $_REQUEST['taxonomy'] ) ) {
	$taxnow = $_REQUEST['taxonomy'];
} else {
	$taxnow = '';
}

if ( APP_NETWORK_ADMIN ) {
	require( APP_ADMIN_PATH . '/network/menu.php' );
} elseif ( WP_USER_ADMIN ) {
	require( APP_ADMIN_PATH . '/user/menu.php' );
} else {
	require( APP_ADMIN_PATH . '/menu.php' );
}

if ( current_user_can( 'manage_options' ) ) {
	wp_raise_memory_limit( 'admin' );
}

/**
 * Fires as an admin screen or script is being initialized.
 *
 * Note, this does not just run on user-facing admin screens.
 * It runs on admin-ajax.php and admin-post.php as well.
 *
 * This is roughly analogous to the more general {@see 'init'} hook, which fires earlier.
 *
 * @since Previous 2.5.0
 */
do_action( 'admin_init' );

if ( isset( $plugin_page ) ) {

	if ( ! empty( $typenow ) ) {
		$the_parent = $pagenow . '?post_type=' . $typenow;
	} else {
		$the_parent = $pagenow;
	}

	if ( ! $page_hook = get_plugin_page_hook( $plugin_page, $the_parent ) ) {

		$page_hook = get_plugin_page_hook( $plugin_page, $plugin_page );

		// Back-compat for plugins using add_management_page().
		if ( empty( $page_hook ) && 'edit.php' == $pagenow && '' != get_plugin_page_hook( $plugin_page, 'tools.php' ) ) {

			// There could be plugin specific params on the URL, so we need the whole query string.
			if ( ! empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
				$query_string = $_SERVER[ 'QUERY_STRING' ];
			} else {
				$query_string = 'page=' . $plugin_page;
			}

			wp_redirect( admin_url( 'tools.php?' . $query_string ) );

			exit;
		}
	}

	unset( $the_parent );
}

$hook_suffix = '';

if ( isset( $page_hook ) ) {
	$hook_suffix = $page_hook;
} elseif ( isset( $plugin_page ) ) {
	$hook_suffix = $plugin_page;
} elseif ( isset( $pagenow ) ) {
	$hook_suffix = $pagenow;
}

set_current_screen();

// Handle plugin admin pages.
if ( isset( $plugin_page ) ) {

	if ( $page_hook ) {
		/**
		 * Fires before a particular screen is loaded.
		 *
		 * The load-* hook fires in a number of contexts. This hook is for plugin screens
		 * where a callback is provided when the screen is registered.
		 *
		 * The dynamic portion of the hook name, `$page_hook`, refers to a mixture of plugin
		 * page information including:
		 * 1. The page type. If the plugin page is registered as a submenu page, such as for
		 *    Settings, the page type would be 'settings'. Otherwise the type is 'toplevel'.
		 * 2. A separator of '_page_'.
		 * 3. The plugin basename minus the file extension.
		 *
		 * Together, the three parts form the `$page_hook`. Citing the example above,
		 * the hook name used would be 'load-settings_page_pluginbasename'.
		 *
		 * @see get_plugin_page_hook()
		 *
		 * @since Previous 2.1.0
		 */
		do_action( "load-{$page_hook}" );

		if ( ! isset( $_GET['noheader'] ) ) {
			require_once( APP_ADMIN_PATH . '/admin-header.php' );
		}

		/**
		 * Used to call the registered callback for a plugin screen.
		 *
		 * @ignore
		 * @since Previous 1.5.0
		 */
		do_action( $page_hook );

	} else {

		if ( validate_file( $plugin_page ) ) {
			wp_die( __( 'Invalid plugin page.' ) );
		}

		if ( ! ( file_exists( APP_PLUGINS_PATH . "/$plugin_page" ) && is_file( APP_PLUGINS_PATH . "/$plugin_page" ) ) && ! ( file_exists( APP_EXTENSIONS_PATH . "/$plugin_page" ) && is_file( APP_EXTENSIONS_PATH . "/$plugin_page" ) ) ) {
			wp_die( sprintf( __( 'Cannot load %s.' ), htmlentities( $plugin_page ) ) );
		}

		/**
		 * Fires before a particular screen is loaded.
		 *
		 * The load-* hook fires in a number of contexts. This hook is for plugin screens
		 * where the file to load is directly included, rather than the use of a function.
		 *
		 * The dynamic portion of the hook name, `$plugin_page`, refers to the plugin basename.
		 *
		 * @see plugin_basename()
		 *
		 * @since Previous 1.5.0
		 */
		do_action( "load-{$plugin_page}" );

		if ( ! isset( $_GET['noheader'] ) ) {
			require_once( APP_ADMIN_PATH . '/admin-header.php' );
		}

		if ( file_exists( APP_EXTENSIONS_PATH . "/$plugin_page" ) ) {
			include( APP_EXTENSIONS_PATH . "/$plugin_page" );
		} else {
			include( APP_PLUGINS_PATH . "/$plugin_page" );
		}
	}

	include( APP_ADMIN_PATH . '/admin-footer.php' );

	exit();

} elseif ( isset( $_GET['import'] ) ) {

	$importer = $_GET['import'];

	if ( ! current_user_can( 'import' ) ) {
		wp_die( __( 'Sorry, you are not allowed to import content.' ) );
	}

	if ( validate_file( $importer ) ) {
		wp_redirect( admin_url( 'import.php?invalid=' . $importer ) );

		exit;
	}

	if ( ! isset( $wp_importers[$importer] ) || ! is_callable( $wp_importers[$importer][2] ) ) {
		wp_redirect( admin_url( 'import.php?invalid=' . $importer ) );

		exit;
	}

	/**
	 * Fires before an importer screen is loaded.
	 *
	 * The dynamic portion of the hook name, `$importer`, refers to the importer slug.
	 *
	 * @since Previous 3.5.0
	 */
	do_action( "load-importer-{$importer}" );

	$parent_file  = 'tools.php';
	$submenu_file = 'import.php';
	$title        = __( 'Import' );

	if ( ! isset( $_GET['noheader'] ) ) {
		require_once( APP_ADMIN_PATH . '/admin-header.php' );
	}

	require_once( APP_INC_PATH . '/backend/upgrade.php' );

	define( 'WP_IMPORTING', true );

	/**
	 * Whether to filter imported data through kses on import.
	 *
	 * Network uses this hook to filter all data through kses by default,
	 * as a super administrator may be assisting an untrusted user.
	 *
	 * @since Previous 3.1.0
	 *
	 * @param bool $force Whether to force data to be filtered through kses. Default false.
	 */
	if ( apply_filters( 'force_filtered_html_on_import', false ) ) {

		// Always filter imported data with kses on network.
		kses_init_filters();
	}

	call_user_func( $wp_importers[$importer][2] );

	include( APP_ADMIN_PATH . '/admin-footer.php' );

	// Make sure rules are flushed.
	flush_rewrite_rules( false );

	exit();

} else {
	/**
	 * Fires before a particular screen is loaded.
	 *
	 * The load-* hook fires in a number of contexts. This hook is for core screens.
	 *
	 * The dynamic portion of the hook name, `$pagenow`, is a global variable
	 * referring to the filename of the current page, such as 'admin.php',
	 * 'post-new.php' etc. A complete hook for the latter would be
	 * 'load-post-new.php'.
	 *
	 * @since Previous 2.1.0
	 */
	do_action( "load-{$pagenow}" );

	/*
	 * The following hooks are fired to ensure backward compatibility.
	 * In all other cases, 'load-' . $pagenow should be used instead.
	 */
	if ( $typenow == 'page' ) {

		if ( $pagenow == 'post-new.php' ) {
			do_action( 'load-page-new.php' );
		} elseif ( $pagenow == 'post.php' ) {
			do_action( 'load-page.php' );
		}

	}  elseif ( $pagenow == 'edit-tags.php' ) {

		if ( $taxnow == 'category' ) {
			do_action( 'load-categories.php' );
		} elseif ( $taxnow == 'link_category' ) {
			do_action( 'load-edit-link-categories.php' );
		}

	} elseif( 'term.php' === $pagenow ) {
		do_action( 'load-edit-tags.php' );
	}
}

if ( ! empty( $_REQUEST['action'] ) ) {
	/**
	 * Fires when an 'action' request variable is sent.
	 *
	 * The dynamic portion of the hook name, `$_REQUEST['action']`,
	 * refers to the action derived from the `GET` or `POST` request.
	 *
	 * @since Previous 2.6.0
	 */
	do_action( 'admin_action_' . $_REQUEST['action'] );
}