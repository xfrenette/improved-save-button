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
}