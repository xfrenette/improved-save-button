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

if( ! class_exists( 'LB_Save_And_Then_Utils' ) ) {

/**
 * Utilities functions used by the plugin.
 */

class LB_Save_And_Then_Utils {

	static protected $adjacent_post_cache = array();

	/**
	 * Returns the full URL to a file in this plugins folder.
	 * 
	 * This is a wrapper around Wordpress's plugins_url() function
	 * setup to automatically check in this plugin's folder. Takes a
	 * relative file path relative to the plugin's root folder.
	 * 
	 * @param  string $file The file path relative to the plugin's folder.
	 * @return string       The full URL to the file
	 */
	static function plugins_url( $file ) {
		return plugins_url( $file, dirname( __FILE__ ) );
	}

	/**
	 * Returns this plugin's basename. Returns the same thing
	 * as plugin_basename( __FILE__ ) if called from the plugin's
	 * main file.
	 *
	 * Yes, I know, kind of ugly. The reason is that __FILE__
	 * (so plugin_basename( __FILE__ ) ) doesn't work as wished
	 * with symbolic links on Windows.
	 *
	 * @return string
	 */
	static function plugin_main_file_basename() {
		$main_file_path = LB_Save_And_Then::get_main_file_path();
		$relative_folder = basename( dirname( $main_file_path ) );
		$relative_file = basename( $main_file_path );
		return  $relative_folder . '/' . $relative_file;
	}

	/**
	 * Takes a WP_Post instance and returns the next or previous
	 * (depending on $dir value) post the current user can edit,
	 * ordered by publication date.
	 *
	 * Wordpress already has an get_adjacent_post function, but it checks
	 * only posts with 'published' state. We needed to check any post that
	 * would be shown on an administration post list page (so with
	 * publication status of 'published', 'draft', 'future', ...)
	 * 
	 * @param  WP_Post $post  The post
	 * @param  string $dir    'next' or 'previous'. Specifies which post to return
	 * @return (WP_Post|null) The adjacent post or null if no post is found
	 */
	static function get_adjacent_post( $post, $dir = 'next' ) {
		global $wpdb;
		$cache_id = $post->ID . '-' . $dir;

		if( ! array_key_exists( $cache_id, self::$adjacent_post_cache ) ) {
			$op = $dir == 'next' ? '>' : '<';
			$order = $dir == 'next' ? 'ASC' : 'DESC';
			$exclude_states = get_post_stati( array( 'show_in_admin_all_list' => false ) );
			$additionnal_where = '';

			// If the current user cannot edit others posts, we add a WHERE clause
			// where only the user's post are returned
			$post_type_object = get_post_type_object( get_post_type( $post ) );

			if ( ! current_user_can( $post_type_object->cap->edit_others_posts ) ) {
				$additionnal_where .= ' AND post_author = \'' . get_current_user_id() . '\'';
			}
			
			$query = $wpdb->prepare("
					SELECT p.ID FROM $wpdb->posts AS p
					WHERE p.post_date $op %s AND p.post_type = %s
					AND (p.post_status NOT IN ('" . implode( "','", $exclude_states ) . "'))
					$additionnal_where
					ORDER BY p.post_date $order LIMIT 1
				",
				 $post->post_date, $post->post_type
			);
			$found_post_id = $wpdb->get_var( $query );

			if( $found_post_id ) {
				self::$adjacent_post_cache[ $cache_id ] = get_post( $found_post_id );
			} else {
				self::$adjacent_post_cache[ $cache_id ] = null;
			}
		}

		return self::$adjacent_post_cache[ $cache_id ];
	}

	/**
	 * Returns true if the $url is the listing page of $post_type.
	 * 
	 * @param  string  $url       The url to check
	 * @param  string  $post_type The post type. Defaults to 'post'
	 * @return boolean
	 */
	static function url_is_posts_list( $url, $post_type = 'post' ) {
		$url_parts = self::parse_url( $url );
		$url_params = $url_parts['query'];

		// If no post type is set in the URL, defaults to 'post'
		$url_post_type = isset( $url_params['post_type'] ) ? $url_params['post_type'] : 'post';

		// True if the url is edit.php and the post type is the same
		// @todo may need to check if the path is exactly the same
		//       (with domain and everything)
		return (
			strpos( $url_parts['path'], 'edit.php' ) !== false
			&&
			$url_post_type == $post_type
		);
	}

	/**
	 * from a url string, returns the address parts
	 * and the query params as an associative array
	 * @param  [type] $url [description]
	 * @return [type] [description]
	 */
	static function parse_url( $url ) {
		$url_parts = parse_url( $url );
		$query = array();
		wp_parse_str( $url_parts['query'], $query );
		$url_parts['query'] = $query;
		return $url_parts;
	}

	static function admin_url( $relative_url, $params = array() ) {
		$url = admin_url( $relative_url );
		return add_query_arg( $params, $url );
	}
} // end class

} // end if( class_exists() )