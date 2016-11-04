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
	protected $cache_time;

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
		add_action( 'plugins_loaded',                                     array( $this, 'setup' ) );
	}

	/**
	 * Set default plugin values.
	 */
	public function setup() {
		/**
		 * Filters the expiration time of navigation menu cache.
		 *
		 * @param int $expiration Navigation menu cache expiration time. Default one hour.
		 */
		$this->cache_time = apply_filters( 'menu_cache_expiration', HOUR_IN_SECONDS );
	}

	/**
	 * Get the transient.
	 * 
	 * @param    array  $args  The menu arguments
	 * @return   array  The transient key
	 * @access   protected
	 */
	protected function get_transient( $args ) {
		// Check if unique hash for the menu was set
		if ( ! isset( $args->menu_cache_hash ) ) {
			/**
			 * Filters the hash of a navigation menu.
			 *
			 * @param string   $hash MD5 hash of parsable string representation of wp_nav_menu() arguments.
			 * @param stdClass $args An object containing wp_nav_menu() arguments.
			 */
			$hash = apply_filters( 'menu_cache_hash', md5( var_export( $args, true ) ), $args );

			$args->menu_cache_hash = (string) $hash;
		}

		/**
		 * Filters the URI of a current page.
		 *
		 * @param string $uri The URI which was given in order to access current page.
		 * @param stdClass $args An object containing wp_nav_menu() arguments.
		 */
		$request_uri = apply_filters( 'menu_cache_request_uri', $_SERVER['REQUEST_URI'], $args );

		$transient = 'nav-' . md5( $this->get_cache_version() . $request_uri . $args->menu_cache_hash );
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
		do_action( 'menu-cache-purged' );
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
	 * Get current version of cache.
	 *
	 * @return string $version Cache version
	 */
	public function get_cache_version() {
		$version = get_option( self::CACHE_KEY );

		// If no version was set, set new one
		if ( ! $version ) {
			$this->refresh_cache();

			// Get new version
			$version = get_option( self::CACHE_KEY );
		}

		return $version;
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
$GLOBALS['menu_cache'] = new Menu_Cache();
