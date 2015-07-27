<?php

abstract class LB_Save_And_Then_Action {

	/**
	 * Used in the settings page. For the button label, see ...
	 */
	abstract function get_name();

	/**
	 * 
	 */
	abstract function get_id();

	/**
	 * Used in the settings page
	 */
	abstract function get_description();

	/**
	 * returns true if the action can be executed in
	 * the current context, else false
	 */
	function is_enabled() {
		return true;
	}

	abstract function get_button_label_pattern();

	function get_button_title() {
		return '';
	}
}