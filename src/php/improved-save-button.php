<?php
/*
Plugin Name: @@plugin.name
Description: Adds a "Save" button to the post edit screen that saves the post and immediately redirect to one of the common page: the post listing, the new post form or the previous or next post edit page.
Author: Label Blanc
Version: 1.0.2
Author URI: http://www.labelblanc.ca
Domain Path: /languages/
Text Domain: lb-save-and-then
*/

/**
 * Copyright @@copyright.year Label Blanc (http://www.labelblanc.ca/)
 *
 * This file is part of the "@@plugin.name"
 * Wordpress plugin.
 *
 * The "@@plugin.name" Wordpress plugin
 * is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$lib_files_to_include = array(
	'class-lb-save-and-then-utils.php',
	'class-lb-save-and-then-settings.php',
	'class-lb-save-and-then-post-edit.php',
	'class-lb-save-and-then-post-save.php',
	'class-lb-save-and-then-messages.php',
	'class-lb-save-and-then-actions.php',
	'class-lb-save-and-then-action.php',
);

foreach ( $lib_files_to_include as $file_name ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR . $file_name );
}

$actions_files_to_include = array(
	'class-lb-save-and-then-action-new.php',
	'class-lb-save-and-then-action-list.php',
	'class-lb-save-and-then-action-view.php',
	'class-lb-save-and-then-action-next.php',
	'class-lb-save-and-then-action-previous.php',
);

foreach ( $actions_files_to_include as $file_name ) {
	require_once( plugin_dir_path( __FILE__ ) . 'actions' . DIRECTORY_SEPARATOR . $file_name );
}

if( !class_exists( 'LB_Save_And_Then' ) ) {

/**
 * Main class. Mainly calls the setup function of other classes and
 * define the list of 'actions'.
 */
class LB_Save_And_Then {

	/**
	 * Main entry point of the plugin. Calls the setup function
	 * of the other classes.
	 */
	static function setup() {
		LB_Save_And_Then_Settings::setup();
		LB_Save_And_Then_Post_Edit::setup();
		LB_Save_And_Then_Post_Save::setup();
		LB_Save_And_Then_Messages::setup();
		LB_Save_And_Then_Actions::setup();

		add_action( 'admin_init', array( get_called_class(), 'load_languages' ) );
		add_action( 'lbsat_load_actions', array( get_called_class(), 'load_default_actions' ) );
	}

	/**
	 * Returns the localized name of the plugin
	 * @return string
	 */
	static function get_localized_name() {
		$plugin_data = get_plugin_data( __FILE__, false, true );
		return $plugin_data['Name'];
	}

	/**
	 * @todo DOC
	 */
	static function load_default_actions( $actions ) {
		$default_actions_classes = array(
			'LB_Save_And_Then_Action_New',
			'LB_Save_And_Then_Action_Next',
			'LB_Save_And_Then_Action_Previous',
			'LB_Save_And_Then_Action_List',
			'LB_Save_And_Then_Action_View',
		);

		foreach ( $default_actions_classes as $class_name ) {
			$actions[] = new $class_name();
		}

		return $actions;
	}

	/**
	 * Loads the language file for the admin. Must be called in the
	 * 'admin_init' hook, since it uses get_plugin_data() and this
	 * function is loaded once all admin files are included.
	 */
	static function load_languages() {
		$plugin_data = get_plugin_data( __FILE__, false, true );
		$path = dirname( LB_Save_And_Then_Utils::plugin_main_file_basename() );
		$path .= $plugin_data['DomainPath'];
		load_plugin_textdomain( $plugin_data['TextDomain'], false, $path );
	}

	/**
	 * Returns the full path of the plugin's main file (this file).
	 * Used in the utils
	 *
	 * @return string
	 */
	static function get_main_file_path() {
		return __FILE__;
	}

} // end class

} // end if( class_exists() )

LB_Save_And_Then::setup();