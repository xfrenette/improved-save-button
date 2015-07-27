<?php

class LB_Save_And_Then_Action_View extends LB_Save_And_Then_Action {
	
	function get_name() {
		return __('Save and View', 'lb-save-and-then');
	}

	function get_id() {
		return 'labelblanc.view';
	}

	function get_description() {
		return __('Shows the <strong>post itself</strong> after save.', 'lb-save-and-then');
	}

	function get_button_label_pattern( $post ) {
		return __('%s and View', 'lb-save-and-then');
	}

	function get_redirect_url( $current_url, $post ) {
		return get_permalink( $post->id );
	}
}