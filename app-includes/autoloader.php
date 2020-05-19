<?php
/**
 * Class autoloader
 *
 * @package App_Package
 * @subpackage Administration
 */

namespace AppNamespace;

/*
 * A map of classes to files to load
 *
 * The index is the class name, including namespace,
 * the value is the path to the corresponding file.
 */
const MAP = [

	// Classes used on the back end and/or front end.
	'AppNamespace\Includes\Walker'          => __DIR__ . '/classes/includes/class-walker.php',
	'AppNamespace\Includes\Walker_Nav_Menu' => __DIR__ . '/classes/includes/class-walker-nav-menu.php',
	'AppNamespace\Includes\Site_Icon'       => __DIR__ . '/classes/includes/class-site-icon.php',
	'AppNamespace\Includes\Error_Messages'  => __DIR__ . '/classes/includes/class-error-messages.php',

	// Classes used in the back end only.
	'AppNamespace\Backend\Dashboard'            => __DIR__ . '/classes/backend/class-dashboard.php',
	'AppNamespace\Backend\Screen'               => __DIR__ . '/classes/backend/class-screen.php',
	'AppNamespace\Backend\List_Table'           => __DIR__ . '/classes/backend/class-list-table.php',
	'AppNamespace\Backend\List_Table_Compat'    => __DIR__ . '/classes/backend/class-list-table-compat.php',
	'AppNamespace\Backend\Posts_List_Table'     => __DIR__ . '/classes/backend/class-posts-list-table.php',
	'AppNamespace\Backend\Terms_List_Table'     => __DIR__ . '/classes/backend/class-terms-list-table.php',
	'AppNamespace\Backend\Media_List_Table'     => __DIR__ . '/classes/backend/class-media-list-table.php',
	'AppNamespace\Backend\Comments_List_Table'  => __DIR__ . '/classes/backend/class-comments-list-table.php',
	'AppNamespace\Backend\Users_List_Table'     => __DIR__ . '/classes/backend/class-users-list-table.php',
	'AppNamespace\Backend\Plugins_List_Table'   => __DIR__ . '/classes/backend/class-plugins-list-table.php',
	'AppNamespace\Backend\Walker_Nav_Menu_Edit' => __DIR__ . '/classes/backend/class-walker-nav-menu-edit.php',
];

/**
 * Register classes to be loaded
 *
 * @link https://www.php.net/manual/en/function.spl-autoload-register.php
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
spl_autoload_register(
	function ( string $classname ) {
		if ( isset( MAP[ $classname ] ) ) {
			require MAP[ $classname ];
		}
	}
);

/**
 * Alias namespaced classes
 *
 * Make plugins and themes that call classes which were previously
 * not namespaced aware of the new locations.
 *
 * @since  1.0.0
 *
 * @link https://www.php.net/manual/en/function.class-alias.php
 */
\class_alias( Includes\Walker :: class, \Walker :: class );
\class_alias( Backend\Walker_Nav_Menu_Edit :: class, \Walker_Nav_Menu_Edit :: class );