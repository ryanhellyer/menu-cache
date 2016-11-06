<?php

/**
 * Code used when the plugin is deleted.
 */

// Exit if not on uninstall
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! WP_UNINSTALL_PLUGIN ) {
	exit;
}

// Include and initialize main plugin file
include( WP_PLUGIN_DIR . '/' . WP_UNINSTALL_PLUGIN );

// Get cache key name
$key = $GLOBALS['menu_cache']::CACHE_KEY;

// Delete option
delete_option( $key );
