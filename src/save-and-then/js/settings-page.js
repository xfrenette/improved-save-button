(function($) {
	
	$(function() {
		var $form = $('form[data-lb-sat-settings=form]'),
			$actionsOptions = $form.find('[data-lb-sat-settings=action]'),
			$defaultOptions = $form.find('[data-lb-sat-settings=default]');

		$actionsOptions.change(function() {
			updateDefaultOptions( $defaultOptions, $actionsOptions );
		});

		updateDefaultOptions( $defaultOptions, $actionsOptions );
	});

	function updateDefaultOptions( $defaultOptions, $actionsOptions ) {
		$actionsOptions.each(function( i, elem ) {
			var $action = $(elem),
				action = $action.data('lbSatSettingsValue'),
				$default = $defaultOptions.filter('[value=' + action + ']');

			if( ! $action.prop('checked') && $default.prop('checked') ) {
				$defaultOptions.filter('[value=_last]').prop('checked', true);
			}

			$default.prop('disabled', ! $action.prop('checked') );
		});
	}

})( jQuery );