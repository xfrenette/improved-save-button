<?php

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

if( ! class_exists( 'LB_Save_And_Then_Post_Edit' ) ) {

/**
 * Management of the "edit post" and "new post" admin pages.
 */

class LB_Save_And_Then_Post_Edit {

	/**
	 * Main entry point. Setups all the Wordpress hooks.
	 */
	static function setup() {
		add_action( 'admin_enqueue_scripts', array( get_called_class(), 'add_admin_scripts' ) );
		add_action( 'post_submitbox_start', array( get_called_class(), 'post_submitbox_start' ) );
	}

	/**
	 * Adds JavaScript and CSS files on the "edit post" or "new post"
	 * page.
	 * 
	 * @param string  $page_id  Page id where we are.
	 */
	static function add_admin_scripts( $page_id ) {

		if( $page_id != 'post.php' && $page_id != 'post-new.php' ) {
			return;
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		wp_enqueue_script(
			'lb-save-and-then-post-edit',
			LB_Save_And_Then_Utils::plugins_url( "js/post-edit{$min}.js" ),
			array('jquery', 'utils'),
			'1.0',
			true
		);

		wp_enqueue_style(
			'lb-save-and-then-post-edit',
			LB_Save_And_Then_Utils::plugins_url( 'css/post-edit.css' ),
			array(),
			'1.0'
		);

		wp_style_add_data( 'lb-save-and-then-post-edit', 'rtl', 'replace' );
	}


	/**
	 * Adds JavaScript and some HTML to the 'post submit box' in the
	 * edit page.
	 *
	 * Mainly outputs the JavaScript object containing all the enabled
	 * actions and some settings set in Wordpress. Also create
	 * a hidden input containing the referer (used when doing the
	 * redirection).
	 */
	static function post_submitbox_start() {

		$options = LB_Save_And_Then_Settings::get_options();
		$enabled_actions = LB_Save_And_Then_Settings::get_enabled_actions();

		// If the user didn't select any action, we quit here
		if( ! count( $enabled_actions ) )
			return;

		/**
		 * The JavaScript object that will be serialized in
		 * window.LabelBlanc.SaveAndThen.
		 * 
		 * @var array
		 */
		$js_object = array(
			'setAsDefault' => $options['set-as-default'],
			'actions' => array(),
			'defaultActionId' => $options['default-action'],
		);

		// We add to $js_object all the actions and some data
		// about them.
		foreach ( $enabled_actions as $action_key => $action_data ) {
			$action_info = array(
				'id' => $action_key,
				'buttonLabelPattern' => $action_data['button_label_pattern'],
				'enabled' => true // may be set to false below
			);

			// If action is 'next', we check if we have a next post (same
			// logic with 'previous'). If we don't have one, we disable
			// the action and set a special title.
			if( 'next' == $action_key || 'previous' == $action_key ) {
				$adjacent_post = LB_Save_And_Then_Utils::get_adjacent_post( get_post(), $action_key );
				
				if( ! $adjacent_post ) {
					$action_info['enabled'] = false;

					if( 'next' == $action_key ) {
						$action_info['title'] = __('You are at the last post', 'lb-save-and-then');
					}

					if( 'previous' == $action_key ) {
						$action_info['title'] = __('You are at the first post', 'lb-save-and-then');
					}
				}
			}

			$js_object['actions'][] = $action_info;
		}

		// Output of the JavaScript object
		echo '<script type="text/javascript">';
		echo 'window.LabelBlanc = window.LabelBlanc || {};';
		echo 'window.LabelBlanc.SaveAndThen = window.LabelBlanc.SaveAndThen || {};';
		echo 'window.LabelBlanc.SaveAndThen.ACTION_LAST = "' . LB_Save_And_Then::ACTION_LAST . '";';
		echo 'window.LabelBlanc.SaveAndThen.HTTP_PARAM_ACTION = "' . LB_Save_And_Then::HTTP_PARAM_ACTION . '";';
		echo 'window.LabelBlanc.SaveAndThen.config = ' . json_encode( $js_object );
		echo '</script>';

		// Output of the referer in a hidden field
		echo '<input type="hidden" name="' . LB_Save_And_Then::HTTP_PARAM_REFERER . '" value="' . wp_get_referer() . '" />';
	}
} // end class

} // end if( class_exists() )