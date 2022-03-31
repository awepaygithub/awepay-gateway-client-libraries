/* global wc_awepay_params */
Awepay.setRcode(wc_awepay_params.rcode);
Awepay.setSid(wc_awepay_params.sid);

jQuery(function ($) {
	'use strict';

	/* Open and close for legacy class */
	$('form.checkout, form#order_review').on('change', 'input[name="wc-awepay-payment-token"]', function () {
		if ('new' === $('.awepay-legacy-payment-fields input[name="wc-awepay-payment-token"]:checked').val()) {
			$('.awepay-legacy-payment-fields #awepay-payment-data').slideDown(200);
		} else {
			$('.awepay-legacy-payment-fields #awepay-payment-data').slideUp(200);
		}
	});

	var wc_awepay_form = {
		init: function () {
			if ($('form.woocommerce-checkout').length) {
				this.form = $('form.woocommerce-checkout');
			}

			$('form.woocommerce-checkout')
					.on(
							'checkout_place_order_awepay',
							this.onSubmit
							);

			if ($('form#order_review').length) {
				this.form = $('form#order_review');
			}

			$('form#order_review')
					.on(
							'submit',
							this.onSubmit
							);

			// add payment method page
			if ($('form#add_payment_method').length) {
				this.form = $('form#add_payment_method');
			}

			$('form#add_payment_method')
					.on(
							'submit',
							this.onSubmit
							);

			$(document)
					.on(
							'change',
							'#wc-awepay-cc-form :input',
							this.onCCFormChange
							)
					.on(
							'awepayError',
							this.onError
							)
					.on(
							'checkout_error',
							this.clearToken
							);
		},
		isAwepayChosen: function () {
			return $('#payment_method_awepay').is(':checked');
		},
		hasToken: function () {
			return 0 < $('input.awepay_token').length;
		},
		block: function () {
			wc_awepay_form.form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			wc_awepay_form.form.unblock();
		},
		onError: function (e, responseObject) {
			var message = responseObject.response.error.message;

			// Customers do not need to know the specifics of the below type of errors
			// therefore return a generic localizable error message.
			if (
					'invalid_request_error' === responseObject.response.error.type ||
					'api_connection_error' === responseObject.response.error.type ||
					'api_error' === responseObject.response.error.type ||
					'authentication_error' === responseObject.response.error.type ||
					'rate_limit_error' === responseObject.response.error.type
					) {
				message = wc_awepay_params.invalid_request_error;
			}

			if ('card_error' === responseObject.response.error.type && wc_awepay_params.hasOwnProperty(responseObject.response.error.code)) {
				message = wc_awepay_params[ responseObject.response.error.code ];
			}

			$('.woocommerce-error, .awepay_token').remove();
			$('#awepay-card-number').closest('p').before('<ul class="woocommerce_error woocommerce-error"><li>' + message + '</li></ul>');
			wc_awepay_form.unblock();
		},
		onSubmit: function (e) {
			if (wc_awepay_form.isAwepayChosen() && !wc_awepay_form.hasToken()) {
				e.preventDefault();
				wc_awepay_form.block();

				var 
						first_name = $('#billing_first_name').length ? $('#billing_first_name').val() : wc_awepay_params.billing_first_name,
						last_name = $('#billing_last_name').length ? $('#billing_last_name').val() : wc_awepay_params.billing_last_name,
						data = {
							
						};

				if (first_name && last_name) {
					data.name = first_name + ' ' + last_name;
				}

				if ($('#billing_address_1').length > 0) {
					data.address_line1 = $('#billing_address_1').val();
					data.address_line2 = $('#billing_address_2').val();
					data.address_state = $('#billing_state').val();
					data.address_city = $('#billing_city').val();
					data.address_zip = $('#billing_postcode').val();
					data.address_country = $('#billing_country').val();
				} else if (wc_awepay_params.billing_address_1) {
					data.address_line1 = wc_awepay_params.billing_address_1;
					data.address_line2 = wc_awepay_params.billing_address_2;
					data.address_state = wc_awepay_params.billing_state;
					data.address_city = wc_awepay_params.billing_city;
					data.address_zip = wc_awepay_params.billing_postcode;
					data.address_country = wc_awepay_params.billing_country;
				}

				Awepay.createToken(data, wc_awepay_form.onAwepayResponse);

				// Prevent form submitting
				return false;
			}
		},
		onCCFormChange: function () {
			$('.woocommerce-error, .awepay_token').remove();
		},
		onAwepayResponse: function (status, response) {
			if (response.error) {
				$(document).trigger('awepayError', {response: response});
			} else {
				if ('no' === wc_awepay_params.allow_prepaid_card && 'prepaid' === response.card.funding) {
					response.error = {message: wc_awepay_params.no_prepaid_card_msg};

					$(document).trigger('awepayError', {response: response});

					return false;
				}

				var token = response.id;

				// insert the token into the form so it gets submitted to the server
				wc_awepay_form.form.append("<input type='hidden' class='awepay_token' name='awepay_token' value='" + token + "'/>");
				wc_awepay_form.form.submit();
			}
		},
		clearToken: function () {
			$('.awepay_token').remove();
		}
	};

	wc_awepay_form.init();
});
