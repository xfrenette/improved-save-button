<?php
/*
Plugin Name: Save and create new or show list, etc.
Description: Adds more save option
Author: Label Blanc
Version: 1.0
Author URI: http://www.labelblanc.ca
*/

/**
 * Copyright 2014 Label Blanc (http://www.labelblanc.ca/)
 *
 * This file is part of the "Save then create new, show list, or more..."
 * Wordpress plugin.
 *
 * The "Save then create new, show list, or more..." Wordpress plugin
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
	 * Parameter defining the action to do after saving.
	 * Used in the redirection.
	 */
	const HTTP_PARAM_ACTION = 'lb-sat-action';

	/**
	 * Parameter defining the id of the post that was being modified
	 * before the redirect.
	 * Used in the success message display.
	 */
	const HTTP_PARAM_UPDATED_POST_ID = 'lb-sat-updated-post-id';

	/**
	 * Parameter defining the page where we were before getting to the
	 * current post edit screen.
	 * Used in the redirection.
	 */
	const HTTP_PARAM_REFERER = 'lb-sat-referer';

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
	}

	static function get_localized_name() {
		return __( 'Save and create new or show list, etc.', 'lb-save-and-then' );
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
				'name' => __('Save and new', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and new', 'lb-save-and-then'),
				'description' => __('Shows the <strong>new post</strong> form after save.', 'lb-save-and-then'),
			),
			'list'     => array(
				'name' => __('Save and list', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and list', 'lb-save-and-then'),
				'description' => __('Shows the <strong>posts list</strong> after save.', 'lb-save-and-then'),
			),
			'next'     => array(
				'name' => __('Save and next', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and next', 'lb-save-and-then'),
				'description' => __('Shows the <strong>next post</strong> edit form after save.', 'lb-save-and-then'),
			),
			'previous' => array(
				'name' => __('Save and previous', 'lb-save-and-then'),
				'button_label_pattern' =>__('%s and previous', 'lb-save-and-then'),
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