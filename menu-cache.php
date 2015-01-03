<?php
/*
Plugin Name: Menu Cache
Plugin URI: http://geek.ryanhellyer.net/products/menu-cache/
Description: Caches WordPress navigation menus
Author: Ryan Hellyer
Version: 1.0
Author URI: https://geek.hellyer.kiwi/

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
	const CACHE_TIME = 3600;

	/**
	 * Set variable.
	 */
	public $transient;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Create unique transient key for this page
		$this->transient = 'nav-' . md5( get_option( self::CACHE_KEY ) . $_SERVER['REQUEST_URI'] . var_export( $args, true ) );

		// Load hooks and filters
		add_action( 'wp_update_nav_menu', array( $this, 'refresh_cache' ) );
		add_filter( 'wp_nav_menu',        array( $this, 'set_cached_menu' ), 10, 2 );
		add_filter( 'pre_wp_nav_menu',    array( $this, 'get_cached_menu' ), 10, 2 );
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

		set_transient( $this->transient, $nav_menu, 60 * 30 );

		return $nav_menu;
	}

	/**
	 * Get the cached menu
	 * 
	 * @param  bool    $dep   Deprecated variable
	 * @param  array   $args  The menu arguments
	 * @return string  The cached menu
	 */
	public function get_cached_menu( $dep = null, $args ) {

		// Return the cached menu if possible
		if ( false === ( $menu = get_transient( $this->transient ) ) ) {
			return null;
		} else {
			return $menu;
		}

	}

}
new Menu_Cache();
