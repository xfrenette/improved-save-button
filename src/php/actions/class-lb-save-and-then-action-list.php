<?php

class LB_Save_And_Then_Action_List extends LB_Save_And_Then_Action {

	const COOKIE_LAST_EDIT_URL = 'lbsat_last_edit_url';

	function __construct() {
		add_action('current_screen', array( $this, 'check_post_list_page' ) );
	}

	/**
	 * If we are on an edit page, we save the url in a cookie.
	 * When we do a save and list, we will return to this page.
	 * @todo comment
	 * @param  [type] $wp_screen [description]
	 * @return [type] [description]
	 */
	function check_post_list_page( $wp_screen ) {
		if( $wp_screen->base == 'edit' ) {
			$url = admin_url('edit.php');
			error_log($_SERVER['QUERY_STRING']);

			if( $_SERVER['QUERY_STRING'] ) {
				$url .= '?' . $_SERVER['QUERY_STRING'];
			}
			setcookie( self::COOKIE_LAST_EDIT_URL, $url );
		}
	}
	
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
		$params = array(
			'updated' => '1'
		);

		if( $post_type && 'post' != $post_type ) {
			$params['post_type'] = $post_type;
		}

		// Default return url : the edit screen of the post type
		$redirect_url = LB_Save_And_Then_Utils::admin_url( 'edit.php', $params );

		// If an edit url was set in the cookie, we retrieve it
		// and we use it only if it is an edit page of the same
		// post type
		if( isset( $_COOKIE[ self::COOKIE_LAST_EDIT_URL ] ) ) {
			$cookie_url = trim( $_COOKIE[ self::COOKIE_LAST_EDIT_URL ] );

			if( LB_Save_And_Then_Utils::url_is_posts_list( $cookie_url, $post_type ) ) {
				// We remove some unwanted params
				$params_to_remove = array(
					'locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed', 'ids'
				);
				$redirect_url = remove_query_arg( $params_to_remove, $cookie_url );

				// We set the new parameters
				$redirect_url = add_query_arg( $params, $redirect_url );
			}
		}

		return $redirect_url;
	}
	
}