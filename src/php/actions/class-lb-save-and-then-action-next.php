<?php

/**
 * Copyright @@copyright.year Label Blanc (http://www.labelblanc.ca/)
 *
 * This file is part of the "@@plugin.name"
 * Wordpress plugin.
 *
 * The "@@plugin.name" Wordpress plugin
 * is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 'Save and next' action: after saving the post, redirects to the
 * edit screen of the next post (same post type). If no next post exists,
 * (note that the action will be disabled, but just in case) does nothing
 * (the default Wordpress redirect).
 */
class LB_Save_And_Then_Action_Next extends LB_Save_And_Then_Action {

	/**
	 * @see LB_Save_And_Then_Action
	 */
	function get_name() {
		return __('Save and Next', 'lb-save-and-then');
	}

	/**
	 * @see LB_Save_And_Then_Action
	 */
	function get_id() {
		return 'labelblanc.next';
	}

	/**
	 * @see LB_Save_And_Then_Action
	 */
	function get_description() {
		return __('Shows the <strong>next post</strong> edit form after save.', 'lb-save-and-then');
	}

	/**
	 * @see LB_Save_And_Then_Action
	 */
	function get_button_label_pattern( $post ) {
		return __('%s and Next', 'lb-save-and-then');
	}

	/**
	 * Returns true only if there is a next post. Else
	 * returns false.
	 * 
	 * @see LB_Save_And_Then_Action
	 * @param  WP_Post $post
	 * @return boolean
	 */
	function is_enabled( $post ) {
		return !! LB_Save_And_Then_Utils::get_adjacent_post( $post, 'next' );
	}

	/**
	 * Returns the HTML title attribute for this action that says
	 * the name of the next post (if there is one), else a message
	 * indicating why the action is disabled.
	 *
	 * @see LB_Save_And_Then_Action
	 * @param  WP_Post $post
	 * @return string
	 */
	function get_button_title( $post ) {
		if( ! $this->is_enabled( $post ) ) {
			return __('You are at the last post', 'lb-save-and-then');
		} else {
			$next_post = LB_Save_And_Then_Utils::get_adjacent_post( $post, 'next' );
			return sprintf( __('Next post is "%s"', 'lb-save-and-then'), $next_post->post_title );
		}
	}

	/**
	 * Returns the URL of the next post's Edit screen. If there
	 * is not a next post, returns null.
	 *
	 * @see LB_Save_And_Then_Action
	 * @param  string $current_url
	 * @param  WP_Post $post
	 * @return string|null
	 */
	function get_redirect_url( $current_url, $post ) {
		$next_post = LB_Save_And_Then_Utils::get_adjacent_post( $post, 'next' );

		// Should not happen, but just to be sure
		if( ! $next_post ) {
			return null;
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