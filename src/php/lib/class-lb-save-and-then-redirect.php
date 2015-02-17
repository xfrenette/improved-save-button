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

if( ! class_exists( 'LB_Save_And_Then_Redirect' ) ) {

/**
 * Manages the redirection after a post save
 */

class LB_Save_And_Then_Redirect {

	/**
	 * Main entry point. Setups all the Wordpress hooks.
	 */
	static function setup() {
		add_filter( 'redirect_post_location', array( get_called_class(), 'redirect_post_location' ), 10, 2 );
	}
	
	/**
	 * Changes the redirect URL after a post save/creation if applicable.
	 *
	 * If the redirect parameter set by this plugin is set, we determine
	 * the URL where to redirect (ex: to a new post, the next post, the
	 * posts list, ...). Called by the filter 'redirect_post_location'.
	 * 
	 * @param  string $location Current new location defined by Wordpress
	 * @param  string $post_id  Id of the saved/created post
	 * @return string           The new (or unchanged) URL where to redirect
	 */
	static function redirect_post_location( $location, $post_id ) {
		/**
		 * Set in Wordpress' post.php
		 * @var string
		 */
		global $action;

		// Only enabled on save or publish actions
		if( ! isset( $_POST['save'] ) && ! isset( $_POST['publish'] ) ) {
			return $location;
		}

		/**
		 * @see  Wordpress' post.php
		 */
		if( ! isset( $action ) || $action != 'editpost' ) {
			return $location;
		}

		if( ! isset( $_POST[ LB_Save_And_Then::HTTP_PARAM_ACTION ] ) ) {
			return $location;
		}

		$sat_action = trim( $_POST[ LB_Save_And_Then::HTTP_PARAM_ACTION ] );

		$current_post = get_post( $post_id );
		$post_type = get_post_type( $post_id );

		$old_url = parse_url( $location );
		wp_parse_str( $old_url['query'], $url_get_params );

		/****
		 * Parameters we want to remove from the URL.
		 * We will add them again if we want to show the next/previous post.
		 ****/
		unset( $url_get_params['post'] );
		unset( $url_get_params['action'] );

		/****
		 * Parameters we want to add to the URL.
		 ****/
		$url_get_params[ LB_Save_And_Then::HTTP_PARAM_UPDATED_POST_ID ] = $post_id;

		/**
		 * We build the $new_url (without all the parameters) based on the
		 * wanted $sat_action.
		 */
		switch( $sat_action ) {

			case 'next':
				$adjacent_post_dir = 'next';

				// Fall to next case

			case 'previous':
				if( ! isset( $adjacent_post_dir ) ) {
					$adjacent_post_dir = 'prev';
				}

				$adjacent_post = LB_Save_And_Then_Utils::get_adjacent_post( $current_post, $adjacent_post_dir == 'prev' ? 'previous':'next' );

				if( $adjacent_post ) {
					$new_url = get_edit_post_link( $adjacent_post->ID, 'url' );
					break;
				} else {
					// There is no post before or after,
					// so we fall through the 'new' case.
				}

			case 'new':
				$admin_url = 'post-new.php';
				if( $post_type && 'post' != $post_type ) {
					$admin_url .= '?post_type=' . $post_type;
				}
				$new_url = admin_url( $admin_url );
				break;

			case 'list':
				$admin_url = 'edit.php';
				if( $post_type && 'post' != $post_type ) {
					$admin_url .= '?post_type=' . $post_type;
				}
				$new_url = admin_url( $admin_url );

				/**
				 * If the user was already in the correct listing page, we want
				 * to preserve the same parameters, like orderby, paged, ... so
				 * we only adjust this referer url and use it as $new_url
				 */
				if( isset( $_REQUEST[ LB_Save_And_Then::HTTP_PARAM_REFERER ] ) ) {
					$referer_url = $_REQUEST[ LB_Save_And_Then::HTTP_PARAM_REFERER ];

					// If the referer URL is the post type listing page
					if( self::url_is_posts_list( $referer_url, $post_type ) ) {
						// We remove unwanted parameters
						$params_to_remove = array(
							'locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed', 'ids'
						);
						$new_url = remove_query_arg( $params_to_remove, $referer_url );
						
						// We reset the parameters we want to add
						$url_get_params = array( 'updated' => '1' );
					}
				}

				break;
		}

		if( $new_url ) {
			$new_url = add_query_arg( $url_get_params, $new_url );
			$location = $new_url;
		}

		return $location;
	}

	/**
	 * Returns true if the $url is the listing page of $post_type.
	 * 
	 * @param  string  $url       The url to check
	 * @param  string  $post_type The post type. Defaults to 'post'
	 * @return boolean
	 */
	protected static function url_is_posts_list( $url, $post_type = 'post' ) {
		$url_parts = parse_url( $url );
		$url_params = array();
		if( array_key_exists( 'query', $url_parts ) ) {
			parse_str( $url_parts['query'], $url_params );
		}

		// If no post type is set in the URL, defaults to 'post'
		$url_post_type = isset( $url_params['post_type'] ) ? $url_params['post_type'] : 'post';

		// True if the url is edit.php and the post type is the same
		return (
			strpos( $url_parts['path'], 'edit.php' ) !== false
			&&
			$url_post_type == $post_type
		);
	}
} // end class

} // end if( class_exists() )