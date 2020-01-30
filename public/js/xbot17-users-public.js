(function ($) {
	'use strict';

	$(function () {
		setTimeout(function () {
			$("input[name=mdp_actuel]").val("********");
		}, 2000);
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

			submit_btn.attr('disabled', 'disabled').next().css('visibility', 'visible');

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
									// displaySuccessMessage(response.data.message);
									$("#inscription-ok").modal("show");
									form.trigger('reset');
									break;

								case 'edit_profil_action':
									$("#inscription-ok").modal("show");
							}
						}
					});
				}
				else if (response && !response.success) displayErrorMessage(response.data.message);
			})
			.always(function () {
				submit_btn.removeAttr('disabled').next().css('visibility', 'hidden');
			});
		});
	});
}) (jQuery);
