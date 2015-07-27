<?php

class LB_Save_And_Then_Action_Next extends LB_Save_And_Then_Action {

	function get_name() {
		return __('Save and Next', 'lb-save-and-then');
	}

	function get_id() {
		return 'labelblanc.next';
	}

	function get_description() {
		return __('Shows the <strong>next post</strong> edit form after save.', 'lb-save-and-then');
	}

	function get_button_label_pattern( $post ) {
		return __('%s and Next', 'lb-save-and-then');
	}

	function is_enabled( $post ) {
		return !! LB_Save_And_Then_Utils::get_adjacent_post( $post, 'next' );
	}

	function get_button_title( $post ) {
		if( ! $this->is_enabled( $post ) ) {
			return __('You are at the last post', 'lb-save-and-then');
		} else {
			$next_post = LB_Save_And_Then_Utils::get_adjacent_post( $post, 'next' );
			return __('Next post is "' . $next_post->post_title . '"', 'lb-save-and-then');
		}
	}

	function get_redirect_url( $current_url, $post ) {
		$next_post = LB_Save_And_Then_Utils::get_adjacent_post( $post, 'next' );

		// Should not happen, but just to be sure
		if( ! $next_post ) {
			return $current_url;
		}

		$url_parts = LB_Save_And_Then_Utils::parse_url( $current_url );
		$params = $url_parts['query'];

		// Query params to add
		$params['post'] = $next_post->ID;
		$params['action'] = 'edit';
		$params[ LB_Save_And_Then_Messages::HTTP_PARAM_UPDATED_POST_ID ] = $post->ID;

		// Standard query params that are kept:
		// - message

		return LB_Save_And_Then_Utils::admin_url( 'post.php', $params );
	}
}