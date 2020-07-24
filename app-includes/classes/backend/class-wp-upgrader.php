<?php
/**
 * Upgrade API: WP_Upgrader class
 *
 * Requires skin classes and WP_Upgrader subclasses for backward compatibility.
 *
 * @package App_Package
 * @subpackage Upgrader
 * @since 2.8.0
 */

/** WP_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-wp-upgrader-skin.php';

/** Plugin_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-plugin-upgrader-skin.php';

/** Theme_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-theme-upgrader-skin.php';

/** Bulk_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-bulk-upgrader-skin.php';

/** Bulk_Plugin_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-bulk-plugin-upgrader-skin.php';

/** Bulk_Theme_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-bulk-theme-upgrader-skin.php';

/** Plugin_Installer_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-plugin-installer-skin.php';

/** Theme_Installer_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-theme-installer-skin.php';

/** Language_Pack_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-language-pack-upgrader-skin.php';

/** Automatic_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-automatic-upgrader-skin.php';

/** WP_Ajax_Upgrader_Skin class */
require_once ABSPATH . APPINC . '/classes/backend/class-wp-ajax-upgrader-skin.php';

/** Plugin_Upgrader class */
require_once ABSPATH . APPINC . '/classes/backend/class-plugin-upgrader.php';

/** Theme_Upgrader class */
require_once ABSPATH . APPINC . '/classes/backend/class-theme-upgrader.php';

/** Language_Pack_Upgrader class */
require_once ABSPATH . APPINC . '/classes/backend/class-language-pack-upgrader.php';

/** Core_Upgrader class */
require_once ABSPATH . APPINC . '/classes/backend/class-core-upgrader.php';

/** File_Upload_Upgrader class */
require_once ABSPATH . APPINC . '/classes/backend/class-file-upload-upgrader.php';

/** WP_Automatic_Updater class */
require_once ABSPATH . APPINC . '/classes/backend/class-wp-automatic-updater.php';