<?php
/*
Plugin Name: Menu Cache
Plugin URI: https://geek.hellyer.kiwi/plugins/menu-cache/
Description: Caches WordPress navigation menus
Author: Ryan Hellyer
Version: 1.0.1
Author URI: https://geek.hellyer.kiwi/
Text Domain: menu-cache

Copyright (c) 2015 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


/**
 * Cache WordPress menus.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Menu_Cache {

	/**
	 * Set the time key constant.
	 */
	const CACHE_KEY = 'cache-wordpress-menu-time';

	/**
	 * Set the cache time.
	 */
	protected $cache_time = HOUR_IN_SECONDS;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Load hooks and filters
		add_filter( 'wp_nav_menu',                                        array( $this, 'set_cached_menu' ), 10, 2 );
		add_filter( 'pre_wp_nav_menu',                                    array( $this, 'get_cached_menu' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'purge_admin_link' ) );
		add_action( 'wp_update_nav_menu',                                 array( $this, 'refresh_cache' ) );
		add_action( 'admin_init',                                         array( $this, 'purge_cache' ) );
		add_action( 'plugins_loaded',                                     array( $this, 'localization' ) );
	}

	/**
	 * Get the transient.
	 * 
	 * @param    array  $args  The menu arguments
	 * @return   array  The transient key
	 * @access   protected
	 */
	protected function get_transient( $args ) {
		$transient = 'nav-' . md5( get_option( self::CACHE_KEY ) . $_SERVER['REQUEST_URI'] . var_export( $args, true ) );
		return $transient;
	}

	/*
	 * Setup localization for translations.
	 */
	public function localization() {

		// Localization
		load_plugin_textdomain(
			'menu-cache', // Unique identifier
			false, // Deprecated abs path
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Languages folder
		);

	}

	/**
	 * Purge cache.
	 */
	public function purge_cache() {

		// Bail out if request not sent
		if ( ! isset( $_GET['purge_menu_cache'] ) ) {
			return;
		}

		// Bail out if nonce security check fails
		if ( ! check_admin_referer( 'purge_menu_cache' ) ) {
			return;
		}

		// Refresh the cache
		$this->refresh_cache();

		// Leave a message mentioning that cache has been refreshed
		add_action( 'admin_notices', array( $this, 'purge_success_notice' ) );

		// Allow other plugins to know that we purged
		do_action( 'minit-cache-purged' );
	}

	/**
	 * Leave notice alerting user that purge process was successful.
	 */
	public function purge_success_notice() {

		printf(
			'<div class="updated"><p>%s</p></div>',
			__( 'Success: Menu cache purged.', 'menu-cache' )
		);

	}

	/**
	 * Adding a cache purge link.
	 * 
	 * @param  array   $links   The links to display on the plugins page
	 * @return array   The links to display on the plugins page
	 */
	public function purge_admin_link( $links ) {

		$links[] = sprintf(
			'<a href="%s">%s</a>',
			wp_nonce_url( add_query_arg( 'purge_menu_cache', true ), 'purge_menu_cache' ),
			__( 'Purge cache', 'menu-cache' )
		);

		return $links;
	}

	/**
	 * Refresh the cache.
	 * Works by setting a time-stamp which is used for each menus transient hash.
	 */
	public function refresh_cache() {
		update_option( self::CACHE_KEY, time() );
	}

	/**
	 * Set the menu cache.
	 * 
	 * @param  string   $nav_menu   The nav menu content
	 * @param  array    $args       The menu arguments
	 * @return string   The cached menu
	 */
	public function set_cached_menu( $nav_menu, $args ) {

		$transient = $this->get_transient( $args );
		set_transient( $transient, $nav_menu, $this->cache_time );

		return $nav_menu;
	}

	/**
	 * Get the cached menu.
	 * 
	 * @param  bool    $dep   Deprecated variable
	 * @param  array   $args  The menu arguments
	 * @return string  The cached menu
	 */
	public function get_cached_menu( $dep = null, $args ) {

		$transient = $this->get_transient( $args );

		// Return the cached menu if possible
		if ( false === ( $menu = get_transient( $transient ) ) ) {
			return null;
		} else {
			return $menu;
		}

	}

}
new Menu_Cache();
