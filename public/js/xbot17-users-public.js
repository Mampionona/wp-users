(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function () {
		var result = $('#login-message');

		function displayErrorMessage(message) {
			result.removeClass('text-success').html(message).addClass('text-danger').fadeIn();
		}

		function displaySuccessMessage(message) {
			result.removeClass('text-danger').html(message).addClass('text-success').fadeIn();
		}

		function hideError(callback) {
			result.fadeOut(200, function () {
				result.empty().removeClass('text-danger');
				callback && callback();
			});
		}

		$('.user-form').on('submit', function (e) {
			e.preventDefault();
			var form = $(this);
			var submit_btn = form.find('.submit-btn');
			var data = {};
			form.find('[name]').each(function (i, field) {
				data[field.name] = field.value;
			});

			submit_btn.attr('disabled', 'disabled');

			// async action
			$.post(xbot17_users.ajaxurl, data, function (response) {
				if (response && response.success) {
					// console.log(response);
					hideError(function () {
						if (data.action) {
							switch (data.action) {
								case 'login_action':
									window.location.href = xbot17_users.redirect_uri;
									break;

								case 'register_action':
									displaySuccessMessage(response.data.message);
									form.trigger('reset');
							}
						}
					});
				}
				else if (response && !response.success) displayErrorMessage(response.data.message);
			})
			.always(function () {
				submit_btn.removeAttr('disabled');
			});
		});
	});
})( jQuery );
