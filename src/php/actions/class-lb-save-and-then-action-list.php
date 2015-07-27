<?php

class LB_Save_And_Then_Action_List extends LB_Save_And_Then_Action {
	
	function get_name() {
		return __('Save and List', 'lb-save-and-then');
	}

	function get_id() {
		return 'labelblanc.list';
	}

	function get_description() {
		return __('Shows the <strong>posts list</strong> after save.', 'lb-save-and-then');
	}

	function get_button_label_pattern() {
		return __('%s and List', 'lb-save-and-then');
	}
	
}