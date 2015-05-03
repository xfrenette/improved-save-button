<?php

class LB_Save_And_Then_Action_New extends LB_Save_And_Then_Action {
	
	function get_name() {
		return __('Save and New', 'lb-save-and-then');
	}

	function get_id() {
		return 'labelblanc.new';
	}

	function get_description() {
		return __('Shows the <strong>new post</strong> form after save.', 'lb-save-and-then');
	}
}