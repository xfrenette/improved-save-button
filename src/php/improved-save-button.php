<?php
/*
Plugin Name: @@plugin.name
Description: Adds a "Save" button to the post edit screen that saves the post and immediately redirect to one of the common page: the post listing, the new post form or the previous or next post edit page.
Author: Label Blanc
Version: 1.0.1
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
	'class-lb-save-and-then-redirect.php',
	'class-lb-save-and-then-messages.php',
);

foreach ( $lib_files_to_include as $file_name ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib' . DIRECTORY_SEPARATOR . $file_name );
}

if( !class_exists( 'LB_Save_And_Then' ) ) {

/**
 * Main class. Mainly calls the setup function of other classes and
 * define the list of 'actions'.
 */
class LB_Save_And_Then {

	/**
	 * Id of the 'use last' action
	 */
	const ACTION_LAST = '_last';

	/**
	 * Main entry point of the plugin. Calls the setup function
	 * of the other classes.
	 */
	static function setup() {
		LB_Save_And_Then_Settings::setup();
		LB_Save_And_Then_Post_Edit::setup();
		LB_Save_And_Then_Redirect::setup();
		LB_Save_And_Then_Messages::setup();

		add_action( 'admin_init', array( get_called_class(), 'load_languages' ) );
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
	 * Returns all the possible actions.
	 *
	 * Array structure :
	 * array(
	 *   <action id> => array(
	 *     'name' => <Name, displayed in the settings page>,
	 *     'button_label_pattern' => <Pattern to generate the button name
	 *                                when the action is selected. %s is replaced
	 *                                with the publish button label (ex: 'Update')>,
	 *     'description' => <Displayed in the settings page>
	 *   )
	 * )
	 * @return array All the available actions
	 */
	static function get_actions() {
		return array(
			'new'      => array(
				'name' => __('Save and New', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and New', 'lb-save-and-then'),
				'description' => __('Shows the <strong>new post</strong> form after save.', 'lb-save-and-then'),
			),
			'list'     => array(
				'name' => __('Save and List', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and List', 'lb-save-and-then'),
				'description' => __('Shows the <strong>posts list</strong> after save.', 'lb-save-and-then'),
			),
			'next'     => array(
				'name' => __('Save and Next', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and Next', 'lb-save-and-then'),
				'description' => __('Shows the <strong>next post</strong> edit form after save.', 'lb-save-and-then'),
			),
			'previous' => array(
				'name' => __('Save and Previous', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and Previous', 'lb-save-and-then'),
				'description' => __('Shows the <strong>previous post</strong> edit form after save.', 'lb-save-and-then'),
			),
		);
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