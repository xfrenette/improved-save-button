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

if( ! class_exists( 'LB_Save_And_Then_Messages' ) ) {

/**
 * Manages message display in the administation header after a redirect.
 */

class LB_Save_And_Then_Messages {

	/**
	 * URL parameter defining the id of the post that was being modified
	 * before the redirect.
	 */
	const HTTP_PARAM_UPDATED_POST_ID = 'lb-sat-updated-post-id';

	/**
	 * Main entry point. Setups all the Wordpress hooks.
	 */
	static function setup() {
		add_filter( 'post_updated_messages', array( get_called_class(), 'post_updated_messages' ), 99 );
		add_filter( 'removable_query_args', array( get_called_class(), 'removable_query_args' ), 99 );
	}

	/**
	 * Adds the URL param containing the last modified post id to
	 * the list of URL params that can be removed after being used once.
	 * 
	 * @param  array $removable_query_args An array of parameters to remove from the URL.
	 * @return array                       The array with the added param.
	 */
	static function removable_query_args( $removable_query_args ) {
		$removable_query_args[] = self::HTTP_PARAM_UPDATED_POST_ID;

		return $removable_query_args;
	}

	/**
	 * If the plugin did a redirect, we update the success message to
	 * show a link to the post we were.
	 *
	 * Called by the post_updated_messages filter. Wordpress' default messages
	 * are updated. The message is only shown when the
	 * redirected page (the page shown after saving) is the 'edit' or
	 * 'new' post page. It doesn't show on a 'list posts' page.
	 *
	 * @see          Wordpress' edit-form-advanced.php:63
	 * @param  array $messages Associative array of messages per post type
	 * @return array The modified messages array
	 */
	static function post_updated_messages( $messages ) {

		// Only modify the messages if this plugin did a redirect
		if( ! isset( $_REQUEST[ self::HTTP_PARAM_UPDATED_POST_ID ] ) ) {
			return $messages;
		}

		$new_messages = self::get_post_updated_messages( trim( $_REQUEST[ self::HTTP_PARAM_UPDATED_POST_ID ] ) );

		if( ! $new_messages ) {
			return $messages;
		}

		foreach ( $new_messages as $post_type => $post_type_messages ) {
			
			if( ! isset( $messages[ $post_type ] ) ) {
				// If the $new_messages set messages for a post type not
				// yet defined in the current $messages array, we initialise
				// it with the 'post' post type messages.
				$messages[ $post_type ] = $messages['post'];
			}

			$codes_to_message_keys = array(
				1  => 'updated',
				4  => 'updated-nolink',
				6  => 'published',
				8  => 'submitted',
				9  => 'scheduled',
				10 => 'draft-updated'
			);

			// We update the default success messages
			foreach ( $codes_to_message_keys as $code => $message_key ) {
				if( isset( $post_type_messages[ $message_key ] ) ) {
					$messages[ $post_type ][ $code ]  = $post_type_messages[ $message_key ];
				}
			}
		}

		return $messages;
	}


	/**
	 * Returns modified version of successful update messages shown
	 * on the post edit page.
	 *
	 * After a successful redirect, the id of the post that was being
	 * modified is passed in the URL. We thus update all the messages
	 * that contain a link to the modified post.
	 *
	 * The returned array is in the following format (where <post_type> is
	 * a string of a post type, like 'page') :
	 * array(
	 *   <post_type> => array(
	 *     'updated' => <message>,
	 *     'updated-nolink' => <message>,
	 *     'published' => <message>,
	 *     'scheduled' => <message>,
	 *     'draft-updated' => <message>,
	 *   ),
	 *
	 *   <other post_type> => array( ... ),
	 *
	 *   ...
	 * )
	 *
	 * @see            Wordpress' edit-form-advanced.php:63 for usage
	 * @param  string  $post_ID Id of the post that was modified
	 * @return array   Messages per post type
	 */
	static protected function get_post_updated_messages( $post_ID ) {
		$post = get_post( $post_ID );

		if( ! $post )
			return null;

		$post_ID = $post->ID;
		$post_url = get_permalink( $post_ID );
		$post_preview_url = add_query_arg( 'preview', 'true', $post_url );

		$messages = array();

		$messages['post'] = array(
			'updated' => sprintf( __('Post updated. <a href="%s">View post</a>'), esc_url( $post_url ) ),
			'updated-nolink' => __('Post updated.'),
			'published' => sprintf( __('Post published. <a href="%s">View post</a>'), esc_url( $post_url ) ),
			'submitted' => sprintf( __('Post submitted. <a target="_blank" href="%s">Preview post</a>'), esc_url( $post_preview_url ) ),
			'scheduled' => sprintf(
				__('Post scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview post</a>'),
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
				esc_url( $post_preview_url ) ),
			'draft-updated' => sprintf( __('Post draft updated. <a target="_blank" href="%s">Preview post</a>'), esc_url( $post_preview_url ) ),
		);

		$messages['page'] = array(
			'updated' => sprintf( __('Page updated. <a href="%s">View page</a>'), esc_url( $post_url ) ),
			'updated-nolink' => __('Page updated.'),
			'published' => sprintf( __('Page published. <a href="%s">View page</a>'), esc_url( $post_url ) ),
			'submitted' => sprintf( __('Page submitted. <a target="_blank" href="%s">Preview page</a>'), esc_url( $post_preview_url ) ),
			'scheduled' => sprintf(
				__('Page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview page</a>'),
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
				esc_url( $post_preview_url ) ),
			'draft-updated' => sprintf( __('Page draft updated. <a target="_blank" href="%s">Preview page</a>'), esc_url( $post_preview_url ) ),
		);

		return $messages;
	}
} // end class

} // end if( class_exists() )