<?php

class LB_Save_And_Then_Actions {

	/**
	 * Id of the 'use last' action
	 */
	const ACTION_LAST = '_last';

	static protected $actions = array();
	static protected $special_action_last;

	static function setup() {
		// Priority set to 9 to be sure it executes before the setting pages
		// creates the actions list
		add_action( 'admin_init', array( get_called_class(), 'load_actions' ), 9 );
	}

	static function load_actions() {
		self::$actions = apply_filters( 'lbsat_load_actions', self::$actions );
	}

	static function get_actions() {
		return self::$actions;
	}

	static function action_exists( $action_id ) {
		foreach ( self::$actions as $action ) {
			if ( $action->get_id() == $action_id ) {
				return true;
			}
		}

		return false;
	}
}