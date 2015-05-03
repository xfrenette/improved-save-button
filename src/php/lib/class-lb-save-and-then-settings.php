<?php

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

if( ! class_exists( 'LB_Save_And_Then_Settings' ) ) {

/**
 * Manages the settings page and settings utilities.
 */

class LB_Save_And_Then_Settings {

	/**
	 * Constants used in defining settings names, settings page and
	 * settings menu.
	 */
	const OPTION_GROUP = 'lb-save-and-then';
	const MAIN_SETTING_NAME = 'lb-save-and-then-options';
	const MENU_SLUG = 'save-and-then';

	/**
	 * Main entry point. Setups all the Wordpress hooks.
	 */
	static function setup() {
		add_action( 'admin_init', array( get_called_class(), 'setup_settings' ) );
		add_action( 'admin_init', array( get_called_class(), 'setup_settings_fields' ) );
		add_action( 'admin_enqueue_scripts', array( get_called_class(), 'add_admin_scripts' ) );
		add_action( 'admin_menu', array( get_called_class(), 'create_administration_menu' ) );
		$plugin = LB_Save_And_Then_Utils::plugin_main_file_basename();
		add_filter("plugin_action_links_$plugin", array( get_called_class(), 'plugin_settings_link' ) );
	}

	/**
	 * Adds JavaScript files required on the settings page. Only add them
	 * on the plugin's settings page.
	 * 
	 * @param string  $page_id  Id of the page currently shown
	 */
	static function add_admin_scripts( $page_id ) {
		if( $page_id != 'settings_page_' . self::MENU_SLUG ) {
			return;
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		wp_enqueue_script(
			'lb-save-and-then-settings-page',
			LB_Save_And_Then_Utils::plugins_url( "js/settings-page{$min}.js" ),
			array('jquery'),
			'1.0',
			true
		);
	}

	/**
	 * Register in Wordpress the settings where we will save the options.
	 */
	static function setup_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::MAIN_SETTING_NAME,
			array( get_called_class(), 'validate_setting' )
		);
	}

	/**
	 * Defines the settings sections and fields of the settings page.
	 */
	static function setup_settings_fields() {
		$setting_section_name = 'lb-save-and-then-settings-section';

		add_settings_section(
			$setting_section_name,
			null, // No section title
			null, // We don't want to show any particular content
			self::MENU_SLUG
		);

		add_settings_field(
			'lb-save-and-then-set-as-default',
			__('Display button as default', 'lb-save-and-then'),
			array( get_called_class(), 'create_setting_field' ),
			self::MENU_SLUG,
			$setting_section_name,
			array( 'option_name' => 'set-as-default' )
		);

		add_settings_field(
			'lb-save-and-then-actions',
			__('Actions to show', 'lb-save-and-then'),
			array( get_called_class(), 'create_setting_field' ),
			self::MENU_SLUG,
			$setting_section_name,
			array( 'option_name' => 'actions' )
		);

		add_settings_field(
			'lb-save-and-then-default-action',
			__('Default action', 'lb-save-and-then'),
			array( get_called_class(), 'create_setting_field' ),
			self::MENU_SLUG,
			$setting_section_name,
			array( 'option_name' => 'default-action' )
		);
	}

	/**
	 * Adds a menu item in the settings menu to the plugin's
	 * settings page.
	 */
	static function create_administration_menu() {
		add_options_page(
			sprintf( __('%s Settings', 'lb-save-and-then'), LB_Save_And_Then::get_localized_name() ),
			__('@@plugin.name', 'lb-save-and-then'),
			'manage_options',
			self::MENU_SLUG,
			array( get_called_class(), 'create_options_page' )
		);
	}

	/**
	 * Outputs HTML of the settings page
	 */
	static function create_options_page() {
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		<div class="wrap">
		<h2><?php printf( __('<em>%s</em> Settings', 'lb-save-and-then'), LB_Save_And_Then::get_localized_name() ); ?></h2>
		<form method="post" action="options.php" data-lb-sat-settings="form">
			<?php settings_fields( self::OPTION_GROUP ); ?>
			<?php do_settings_sections( self::MENU_SLUG ); ?>
			<input type="submit" value="<?php esc_attr_e('Save Changes'); ?>"class="button button-primary" />
		</form>
		</div>
		<?php
	}

	/**
	 * Based on the field, outputs its HTML. This method can generate the HTML
	 * of each field used in the settings page.
	 * 
	 * @param  array  $args  Arguments passed as last parameter in add_settings_field
	 */
	static function create_setting_field( $args ) {
		// The values of all the settings
		$options = self::get_options();
		$actions = LB_Save_And_Then_Actions::get_actions();
		$option_field_name = self::MAIN_SETTING_NAME . '[' . $args['option_name'] . ']';
		// The setting value for this field
		$option_value = $options[ $args['option_name'] ];

		$html = '';

		switch ( $args['option_name'] ) {

			case 'set-as-default':
				$html .= '<fieldset><label><input type="checkbox" name="' . $option_field_name. '" value="1"' . checked( 1, $option_value, false ) . '/>';
				$html .= '<span>' . __('Display the new save button as the default one', 'lb-save-and-then') . '</span></label></fieldset>';
				break;

			case 'actions':
				$html .= '<fieldset>';

				foreach ( $actions as $action_index => $action ) {
					$action_id = $action->get_id();

					$html .= '<label><input type="checkbox" name="' . $option_field_name . '['. $action_id .']" value="1" data-lb-sat-settings="action" data-lb-sat-settings-value="'. $action_id .'" ' . checked( 1, $option_value[ $action_id ], false ) . '/>';
					$html .= '<span>' . $action->get_name() . '</span>';

					if( $action->get_description() ) {
						$html .= ' <span class="description"> — ' . $action->get_description() . '</span>';
					}

					$html .= '</label>';

					if( $action_index != count( $actions ) - 1 ) {
						$html .= '<br />';
					}
				}

				$html .= '</fieldset>';
				break;

			case 'default-action':
				$html .= '<fieldset>';

				$action_index = -1;

				do {

					// Special case : we show the "use last" action as first element
					if ( -1 == $action_index ) {

						$action_id = LB_Save_And_Then_Actions::ACTION_LAST;
						$action_name = '<em>' . __('Last used', 'lb-save-and-then') . '</em>';
						$action_description = __('The last action that was used', 'lb-save-and-then');

					} else {

						$html .= '<br />';

						$action = $actions[ $action_index ];
						$action_id = $action->get_id();
						$action_name = $action->get_name();
						$action_description = '';
					}

					$html .= '<label><input type="radio" name="' . $option_field_name . '" value="'. $action_id .'" data-lb-sat-settings="default"' . checked( $action_id, $option_value, false ) . '/>';
					
					$html .= '<span>' . $action_name . '</span>';

					if( $action_description ) {
						$html .= ' <span class="description"> — ' . $action_description . '</span>';
					}

					$html .= '</label>';

					$action_index++;

				} while( $action_index < count( $actions ) );

				$html .= '</fieldset>';
				break;
		}

		echo $html;
	}

	/**
	 * Creates the "Settings" link in the plugins page.
	 */
	static function plugin_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . self::MENU_SLUG . '">' . __('Settings') . '</a>'; 
		array_unshift( $links, $settings_link ); 
		return $links; 
	}

	/**
	 * Analyses the arguments received from the request, builds
	 * a new 'clean' settings array (with default if required)
	 * and returns it.
	 * 
	 * @param  array  $input  Parameters received in the request
	 * @return array          Cleaned settings array
	 */
	static function validate_setting( $input ) {
		$defaults = self::get_default_values();
		$actions = LB_Save_And_Then_Actions::get_actions();

		// Defaults, if none set
		$sanitized_input = array(
			'set-as-default' => false, // Default
			'default-action' => $defaults['default-action'],
			'actions' => array()
		);

		if( ! $input )
			$input = array();

		// set-as-default
		if( isset( $input['set-as-default'] ) && '1' == $input['set-as-default'] ) {
			$sanitized_input['set-as-default'] = true;
		}

		// actions
		if( ! isset( $input['actions'] ) )
			$input['actions'] = array();

		foreach ( $actions as $action ) {
			$action_id = $action->get_id();

			if( isset( $input['actions'][ $action_id ] ) && '1' == $input['actions'][ $action_id ] ) {
				$sanitized_input['actions'][ $action_id ] = true;
			} else {
				$sanitized_input['actions'][ $action_id ] = false;
			}
		}

		// default action
		if(
			$input['default-action'] == LB_Save_And_Then_Actions::ACTION_LAST
			|| array_key_exists( $input['default-action'], $sanitized_input['actions'] )
				&& true == $sanitized_input['actions'][ $input['default-action'] ]
		) {
			$sanitized_input['default-action'] = $input['default-action'];
		} else {
			// We should not get here normally (but possible with a modified request),
			// so, just in case, we use the '_last' type.
			$sanitized_input['default-action'] = LB_Save_And_Then_Actions::ACTION_LAST;
		}

		return $sanitized_input;
	}

	/**
	 * Generates an options array with default values.
	 * 
	 * @return array Associative array of options
	 */
	static function get_default_values() {
		$defaults = array(
			'set-as-default' => true,
			'actions' => array(),
			'default-action' => '' // Set below
		);

		// We select all the available actions
		$actions = LB_Save_And_Then_Actions::get_actions();

		foreach ( $actions as $action ) {
			$defaults['actions'][ $action->get_id() ] = true;
		}

		// The default action is the '_last' one.
		$defaults['default-action'] = LB_Save_And_Then_Actions::ACTION_LAST;

		return $defaults;
	}

	/**
	 * Returns an array of all the option values saved in the database,
	 * where non-defined options are set with the defaults.
	 * 
	 * @return array Associative array of options
	 */
	static function get_options() {
		$options = get_option( self::MAIN_SETTING_NAME );

		if( ! $options )
			$options = array();

		return array_replace_recursive( self::get_default_values(), $options );
	}

	/**
	 * Returns an associative array of all the actions enabled in the
	 * settings page. The keys are the action id and the values are the
	 * action data array as returned by LB_Save_And_Then::get_actions().
	 * 
	 * @return array The enabled types
	 */
	static function get_enabled_actions() {
		$options = self::get_options();
		$all_actions = LB_Save_And_Then::get_actions();

		$active_actions = array();

		if( isset( $options['actions'] ) ) {
			foreach ( $options['actions'] as $action_key => $action_value ) {
				if( $action_value ) {
					$active_actions[ $action_key ] = $all_actions[ $action_key ];
				}
			}
		}

		return $active_actions;
	}
} // end class

} // end if( class_exists() )