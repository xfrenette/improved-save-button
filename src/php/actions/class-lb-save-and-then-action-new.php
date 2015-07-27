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

	function get_button_label_pattern() {
		return __('%s and New', 'lb-save-and-then');
	}

	function get_redirect_url( $current_url, $post ) {
		$post_type = get_post_type( $post );
		$url_parts = LB_Save_And_Then_Utils::parse_url( $current_url );
		$params = $url_parts['query'];

		// We delete unwanted query params
		unset( $params['post'] );
		unset( $params['action'] );

		// Query params to add
		if( $post_type && 'post' != $post_type ) {
			$params['post_type'] = $post_type;
		}

		// Standard query params that are kept:
		// - message

		return LB_Save_And_Then_Utils::admin_url( 'post-new.php', $params );
	}
}