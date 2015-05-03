<?php

class LB_Save_And_Then_Actions {

	/**
	 * Id of the 'use last' action
	 */
	const ACTION_LAST = '_last';

	static protected $actions = array();
	static protected $special_action_last;

	static function setup() {
		add_action( 'admin_init', array( get_called_class(), 'load_actions' ) );
	}

	static function load_actions() {
		self::$actions = apply_filters( 'lbsat_load_actions', self::$actions );
	}

	static function get_actions() {
		return self::$actions;
	}
}