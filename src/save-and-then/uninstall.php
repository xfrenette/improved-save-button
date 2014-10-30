<?php

/**
 * Copyright 2014 Label Blanc (http://www.labelblanc.ca/)
 *
 * This file is part of the "Save then create new, show list, or more..."
 * Wordpress plugin.
 *
 * The "Save then create new, show list, or more..." Wordpress plugin
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

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

require_once('lib/class-lb-save-and-then-settings.php');

delete_option( LB_Save_And_Then_Settings::MAIN_SETTING_NAME );