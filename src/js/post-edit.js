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

/**
 * All the scripts for the save and more button on the post edit page.
 */

window.LabelBlanc = window.LabelBlanc || {};
window.LabelBlanc.SaveAndThen = window.LabelBlanc.SaveAndThen || {};

(function( $ ) {
	var SAT = window.LabelBlanc.SaveAndThen;

	/**
	 * When the dom loads. Initialises everything if we are on
	 * the correct page and the config exists.
	 */
	$(function() {
		var config = SAT.config,
			$form = $('#post');

		if( config && $form.length ) {
			new SAT.PostEditForm( $form, config );
		}
	});

	/**
	 * Class that represents the post edit form.
	 * 
	 * @param {jQuery} $form  The post edit form
	 * @param {object} config The configuration object
	 */
	SAT.PostEditForm = function( $form, config ) {
		/*
		 * We start by creating all the elements and classes
		 * we need and save them in variables. The following
		 * functions will use those variables.
		 */
		this.$form = $form;
		this.config = config;
		this.$actionInput = this.createActionInput();
		this.$originalPublishButton = this.getOriginalPublishButton();
		this.newPublishButtonSet = new SAT.PublishButtonSet( this );
		this.$spinner = this.$form.find('#publishing-action .spinner');

		/*
		 * We setup different elements.
		 */
		this.setupForm();
		this.setupOriginalPublishButton();
		this.newPublishButtonSet.setAction( this.getDefaultAction() );
		this.setupFormListeners();
		this.setupWordpressListeners();
		this.newPublishButtonSet.hideMenu();
		this.insertNewPublishButtonSet();
		this.updateSpinner();
	};

	/**
	 * Cookie name used to store the last used action.
	 * 
	 * @type {String}
	 */
	SAT.PostEditForm.LAST_USED_COOKIE_NAME = 'lb-sat-last-used-action';

	SAT.PostEditForm.prototype = {
		/**
		 * Returns the publish (submit) button created by Wordpress.
		 * @return {jQuery}
		 */
		getOriginalPublishButton : function() {
			return this.$form.find('#publish');
		},

		/**
		 * Adds classes to the spinner showing the saving and
		 * also position it near the original button.
		 */
		updateSpinner : function() {
			this.$spinner.addClass('lb-sat-spinner').hide();

			// We always position the spinner just before the original
			// publish button. If we click on the new button, we will
			// move the spinner
			this.$originalPublishButton.before( this.$spinner );
		},

		/**
		 * Inserts the new publish button elements after or before
		 * the original publish button, depending on weither the
		 * new button should be displayed as the default one or not.
		 */
		insertNewPublishButtonSet : function() {
			var $container = this.newPublishButtonSet.$container,
				$separator = $('<div class="lb-sat-separator"></div>');

			if( this.config.setAsDefault ) {
				this.$originalPublishButton
					.after( $container )
					.after( $separator );
			} else {
				this.$originalPublishButton
					.before( $container )
					.before( $separator );
			}
		},

		/**
		 * Creates and returns the hidden field that will contain the action
		 * chosen by the user.
		 * 
		 * @return {jQuery}
		 */
		createActionInput : function() {
			return $('<input type="hidden" name="' + SAT.HTTP_PARAM_ACTION + '" />');
		},

		/**
		 * Initialises the form. Only preprends the hidden action input.
		 */
		setupForm : function() {
			this.$form.prepend( this.$actionInput );
		},

		/**
		 * Setups listeners on form submit.
		 */
		setupFormListeners : function() {
			var self = this;

			this.$form.on('submit', function() {
				self.newPublishButtonSet.disable( true );
			});
		},

		/**
		 * Updates the look of the original publish button, depending
		 * if the new publish button must be displayed as the default
		 * or not.
		 */
		setupOriginalPublishButton : function() {
			if( this.config.setAsDefault ) {
				this.$originalPublishButton
					.removeClass('button-primary')
					.removeAttr('accesskey');
			}
		},

		/**
		 * Saves in the form field the action selected by the user. Called
		 * just before form submit. Also saves the action in the cookie.
		 * 
		 * @param {string} newAction
		 * @requires wpCookies in wordpress/wp-includes/js/utils.js
		 */
		setAction : function( newAction ) {
			wpCookies.set( SAT.PostEditForm.LAST_USED_COOKIE_NAME, newAction.id, 365*24*3600 );
			this.$actionInput.val( newAction.id );
		},

		/**
		 * Reads and returns from the config the default action. If the default
		 * action is '_last', reads it from the cookie.
		 * 
		 * @return {string} The default action
		 */
		getDefaultAction : function() {
			var defaultActionId = this.config.defaultActionId,
				defaultAction = null,
				fallbackAction;

			/*
			 * We define the fallback action as the first action. If the
			 * normal way to determine the action gives an invalid one
			 * (not enabled, for example) this one will be returned.
			 */
			$.each( this.config.actions, function( i, action ) {
				if( action.enabled ) {
					fallbackAction = action;
					return false; // Break the $.each()
				}
			});

			// If the config doesn't even specify a default action : fallback
			if( ! defaultActionId ) {
				return fallbackAction;
			}

			// If it is '_last', we get it from the cookie. If no cookie : fallback
			if( SAT.ACTION_LAST === defaultActionId ) {
				var cookieVal = wpCookies.get( SAT.PostEditForm.LAST_USED_COOKIE_NAME );

				if( ! cookieVal ) {
					return fallbackAction;
				} else {
					defaultActionId = cookieVal;
					// Fall through the rest of code
				}
			}

			// We find the action with the defaultActionId id
			defaultAction = this.getActionFromId( defaultActionId );

			if( ! defaultAction || ! defaultAction.enabled ) {
				defaultAction = fallbackAction;
			}

			return defaultAction;
		},

		/**
		 * Returns an action's data from the config from its id.
		 * 
		 * @param  {string} id
		 * @return {object} The action information
		 */
		getActionFromId : function( id ) {
			var foundAction = null;

			$.each( this.config.actions, function( i, action ) {
				if( action.id === id ) {
					foundAction = action;
					return false; // Break the $.each()
				}
			});

			return foundAction;
		},

		/**
		 * Wordpress updates the original publish button or its label on some
		 * events. We subscribe to those same events so we can change our
		 * button or its label.
		 */
		setupWordpressListeners : function() {
			var self = this;

			// Disable button while auto saving
			// @see wordpress/wp-admin/js/post.js : 529
			$(document).on( 'autosave-disable-buttons.edit-post', function() {
				self.newPublishButtonSet.disable( true );
			}).on( 'autosave-enable-buttons.edit-post', function() {
				if ( ! wp.heartbeat || ! wp.heartbeat.hasConnectionError() ) {
					self.newPublishButtonSet.disable( false );
				}
			});

			// All the events that trigger an updateText call in post.js
			// We update the labels here.
			// @see multiple calls in wordpress/wp-admin/js/post.js
			this.$form.on( 'click',
				'#post-visibility-select .cancel-post-visibility,' +
					'#post-visibility-select .save-post-visibility,' +
					'#timestampdiv .cancel-timestamp,' +
					'#timestampdiv .save-timestamp,' +
					'#post-status-select .save-post-status,' +
					'#post-status-select .cancel-post-status',
				function() {
					self.newPublishButtonSet.updateLabels();
				});
		},
	};

	/**
	 * Class that represents the new publish button that this plugin creates.
	 *
	 * @param {LabelBlanc.SaveAndThen.PostEditForm} postEditForm The PostEditForm where we add the button
	 */
	SAT.PublishButtonSet = function( postEditForm ) {
		/*
		 * We start by creating all the elements we need
		 * and save them in variables. The following
		 * functions will use those variables.
		 */
		this.postEditForm = postEditForm;
		this.config = this.postEditForm.config;
		this.action = null;

		this.$mainButton = this.createMainButton();
		this.$dropdownButton = this.createDropdownButton();
		this.$dropdownMenu = this.createDropdownMenu();
		this.$container = this.createContainer();

		/*
		 * We setup the elements
		 */
		this.setupDocumentClickListener();
		this.setupMainButtonListeners();
		this.setupDropdownButtonListeners();
		this.setupDropdownMenuListeners();
	};

	SAT.PublishButtonSet.prototype = {

		/**
		 * Creates and returns the main button (part of the new
		 * publish button set). Sets the correct classes
		 * depending on weither it must be displayed as the
		 * default button or not.
		 * 
		 * @return {jQuery} The main button
		 */
		createMainButton : function() {
			var $mainButton = $('<input type="button" />');

			$mainButton.attr('class', 'button button-large lb-sat-main-button' );

			if( this.config.setAsDefault ) {
				$mainButton.addClass('button-primary');
			} else {
				$mainButton.removeAttr('accesskey');
			}

			return $mainButton;
		},

		/**
		 * Creates the dropdown button that opens the dropdown
		 * menu (part of the new publish button set). Only
		 * shows a down arrow.
		 * 
		 * @return {jQuery}
		 */
		createDropdownButton : function() {
			var $dropdownButton = $('<input type="button" value="&#xf140;" />');

			$dropdownButton.attr('class', this.$mainButton.attr('class') );
			$dropdownButton
				.removeClass('lb-sat-main-button')
				.addClass('lb-sat-dropdown-button');

			return $dropdownButton;
		},

		/**
		 * Creates and returns the menu element that will be used
		 * as the dropdown and that will contain all the
		 * enabled actions.
		 * 
		 * @return {jQuery}
		 */
		createDropdownMenu : function() {
			var $dropdownMenu = $('<ul class="lb-sat-dropdown-menu"></ul>'),
				self = this;

			$.each( this.config.actions, function( i, actionData ) {
				var $item = $('<li data-lb-sat-value="' + actionData.id + '">' + self.generateButtonLabel( actionData.buttonLabelPattern ) + '</li>');

				if( actionData.title ) {
					$item.attr( 'title', actionData.title );
				}

				if( actionData.enabled ) {
					$item.data( 'lbSatActionData', actionData );
				} else {
					$item.addClass('disabled');
				}

				$dropdownMenu.append( $item );
			});

			return $dropdownMenu;
		},

		/**
		 * Creates and returns the container element that encompasses
		 * the main button, the dropdown button and the dropdown
		 * menu. It is this element that is added, as a whole.
		 * 
		 * @return {jQuery}
		 */
		createContainer : function() {
			var $container = $('<span class="lb-sat-container"></span>');

			$container.append( this.$mainButton );

			if( this.config.actions.length > 1 ) {
				$container
					.addClass('lb-sat-with-dropdown')
					.append( this.$dropdownButton )
					.append( this.$dropdownMenu );
			}

			if( this.config.setAsDefault ) {
				$container.addClass('lb-sat-set-as-default');
			}

			return $container;
		},

		/**
		 * Setups click listener on the document. Used to
		 * close the dropdown menu when we click outside.
		 */
		setupDocumentClickListener : function() {
			var self = this;

			$(document).click(function() {
				if( self.menuShown() ) {
					self.hideMenu();
				}
			});
		},

		/**
		 * Setups click listener on the main button. In short,
		 * saves the current action in the form and submits it.
		 */
		setupMainButtonListeners : function() {
			var self = this;

			this.$mainButton.click(function() {
				if ( $(this).hasClass('disabled') ) {
					return;
				}
				// We move the spinner just before the new button container
				self.$container.before( self.postEditForm.$spinner );

				self.postEditForm.setAction( self.action );
				self.postEditForm.$originalPublishButton.trigger('click');
			});
		},

		/**
		 * Setups click listener on the dropdown button. In short,
		 * opens the dropdown menu.
		 */
		setupDropdownButtonListeners : function() {
			var self = this;

			this.$dropdownButton.click(function( event ) {
				if( ! self.menuShown() ) {
					self.showMenu();
					event.stopPropagation();
				}
			});
		},

		/**
		 * Setups click listeners on elements of the dropdown menu.
		 * In short, sets the action and triggers a click on the
		 * main button.
		 */
		setupDropdownMenuListeners : function() {
			var self = this;

			this.$dropdownMenu.on('click', 'li', function() {
				if( $(this).hasClass('disabled') ) {
					return;
				}
				self.setAction( $(this).data('lbSatActionData') );
				self.$mainButton.click();
			});
			
		},

		/**
		 * Returns true if the dropdown menu is shown. Else false.
		 * @return {boolean}
		 */
		menuShown : function() {
			return this.$container.hasClass('lb-sat-dropdown-menu-shown');
		},

		/**
		 * Shows the dropdown menu
		 */
		showMenu : function() {
			this.$container.addClass('lb-sat-dropdown-menu-shown');
		},

		/**
		 * Hides the dropdown menu
		 */
		hideMenu : function() {
			this.$container.removeClass('lb-sat-dropdown-menu-shown');
		},

		/**
		 * Sets the current active action (the one shown on the main button)
		 * @param {string} action The action id
		 */
		setAction : function( action ) {
			this.action = action;
			this.updateLabels();
		},

		/**
		 * Updates the label of the main menu and the dropdown menu elements
		 * based on the currently set action and the value of the original
		 * button.
		 */
		updateLabels : function() {
			var self = this;

			this.$mainButton.val( this.generateButtonLabel( this.action.buttonLabelPattern ) );

			$.each( this.config.actions, function( i, actionData ) {
				var $li = self.$dropdownMenu.find('[data-lb-sat-value=' + actionData.id + ']');
				$li.text( self.generateButtonLabel( actionData.buttonLabelPattern ) );
			});
		},

		/**
		 * Takes a string pattern and replaces '%s' with the label on
		 * the original publish button.
		 * 
		 * @param  {string} pattern
		 * @return {string}
		 */
		generateButtonLabel : function( pattern ) {
			return pattern.replace('%s', this.postEditForm.$originalPublishButton.val() );
		},

		/**
		 * Shows the button set as disabled (or not)
		 * and hides the dropdown menu.
		 * 
		 * @param  {boolean} disabled True to disable, false to enable
		 */
		disable : function( disabled ) {
			this.$mainButton.toggleClass( 'disabled', disabled );
			this.$dropdownButton.toggleClass( 'disabled', disabled );

			if( disabled ) {
				this.hideMenu();
			}
		}
	};

})( jQuery );