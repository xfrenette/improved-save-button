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

	function get_redirect_url( $current_url, $post ) {
		$post_type = get_post_type( $post );
		$url_parts = LB_Save_And_Then_Utils::parse_url( $current_url );
		$params = $url_parts['query'];

		// We delete unwanted query params
		unset( $params['post'] );
		unset( $params['action'] );
		unset( $params['message'] );

		// Query params to add
		if( $post_type && 'post' != $post_type ) {
			$params['post_type'] = $post_type;
		}

		$params['updated'] = 1;

		// Standard query params that are kept:
		// - (none)

		return LB_Save_And_Then_Utils::admin_url( 'edit.php', $params );
	}
	
}