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
 * 'Save and view' action: after saving the post, redirects to the post
 * page, on the frontend.
 */
class LB_Save_And_Then_Action_View extends LB_Save_And_Then_Action {

	/**
	 * @see LB_Save_And_Then_Action
	 */	
	function get_name() {
		return __('Save and View', 'lb-save-and-then');
	}

	/**
	 * @see LB_Save_And_Then_Action
	 */
	function get_id() {
		return 'labelblanc.view';
	}

	/**
	 * @see LB_Save_And_Then_Action
	 */
	function get_description() {
		return __('Shows the <strong>post itself</strong> after save. The same window is used.', 'lb-save-and-then');
	}

	/**
	 * @see LB_Save_And_Then_Action
	 */
	function get_button_label_pattern( $post ) {
		return __('%s and View', 'lb-save-and-then');
	}

	/**
	 * Returns a title attribute that simply informs the
	 * user the post will open in the same window.
	 * 
	 * @see LB_Save_And_Then_Action
	 * @param WP_Post $post
	 */	
	function get_button_title( $post ) {
		return __('Post will be shown in this window', 'lb-save-and-then');
	}

	/**
	 * Returns the URL of the post's page on the frontend.
	 *
	 * @see LB_Save_And_Then_Action
	 * @param  string $current_url
	 * @param  WP_Post $post
	 * @return string
	 */
	function get_redirect_url( $current_url, $post ) {
		return get_permalink( $post->id );
	}
}