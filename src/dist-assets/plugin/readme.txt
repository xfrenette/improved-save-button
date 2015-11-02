=== @@plugin.name ===
Contributors: LabelBlanc
Tags: publish, save, close, list, edit, editing, return to list, close post, posts list, update, save and new, save and return, save and list, save and next, next post, save and previous, save and view, previous post, admin, administration, editor, multisite, custom post type, page, post
Requires at least: 3.5
Tested up to: 4.3
Stable tag: 1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds a "save" button to the Edit Post form which then redirects you to the posts list, the next/previous post, the New Post form or the post's page.

== Description ==

This plugin adds a new and improved "Save" button to the Post Edit screen that, in a single click, saves the current post and immediately redirects you to either:

* The posts list (Save and List, a.k.a. "Save and Close"),
* The New Post screen (Save and New),
* The Post Edit screen of the previous or next post (Save and Previous/Next)
* The post's page on the frontend (Save and View).

This plugin saves you a lot of time when you have multiple posts, pages or custom posts to create or modify!

Works with pages, posts and custom post types!

Through the plugin's settings page, choose which actions are available and which one to use as the buttons' default action.

Detail of the actions this new button allows:

* **Save and List** (a.k.a. Save and Close): in a single click, save the current post and go back to the posts list.
* **Save and New**: save the current post and go to the New Post screen.
* **Save and Previous**: save the current post and go to the previous post's Edit screen.
* **Save and Next**: save the current post and go to the next post's Edit screen.
* **Save and View** (same or new window): save the current post and show the post's page on the frontend. Can show the post in the same window or in a new one.

== Installation ==

1. Download @@plugin.name.
2. Upload the 'improved-save-button' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate @@plugin.name from your Plugins page.

= Extra =
Visit 'Settings > @@plugin.name' to adjust the configuration to your needs.

== Screenshots ==

1. The new button
2. The down arrow reveals all the possible actions
3. The settings page

== Changelog ==

= 1.1 =
Release Date: October 17, 2015

* New action: As requested, a "Save and View" action was added! This action shows the post's frontend page after the save. Two behaviors are available: show in the same window or show in a new window.
* Enhancement: A title attribute on the 'Save and next/previous' action now shows the name of the next/previous post.
* Enhancement: A big part of the code was rewritten to ease the addition of future new actions (no documentation yet, but you can develop plugins that add new actions, look in the code if interested!).
* Some bug fixes, including one with required fields of ACF.

= 1.0.2 =
Release Date: August 13, 2015

* Misc: Changed the title of the settings page from h2 to h1, like other settings pages in Wordpress 4.3

= 1.0.1 =
Release Date: April 30, 2015

* Enhancement: Post Edit Spinner: Up to date with Wordpress 4.2 behavior.
* Enhancement: Wordpress 4.2's new "removable query args" is now used.
* Bug Fix: The "1 post updated" message was not always shown after a "Save and list".
* Misc: Checked for add_query_arg() XSS attack possibility.

= 1.0 =
Release date: February 19, 2015

Initial version