<?php
	/*	
	*	Payment Plugin
	*	---------------------------------------------------------------------
	*	creating the stripe payment option
	*	---------------------------------------------------------------------
	*/

/* 	if($_POST['option'] == 'payment-order'){
		// echo "hola";

		// $api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'stripe-secret-key'));
		$api_key = "sk_test_jHwIKYMlwiTEFB2s5yW6Fssz";

		\Stripe\Stripe::setApiKey($api_key);
		function stripe($api_key){
			$stripe = new \Stripe\StripeClient($api_key);
			return $stripe;
		}

		$stripe = stripe($api_key);

		$payment_method = $stripe->paymentMethods->create([
			'type' => 'card',
			'card' => [
				'number' => '4242424242424242',
				'exp_month' => 12,
				'exp_year' => 2021,
				'cvc' => '314',
			],
		]);

		$payment_method_id = $payment_method['id'];

		$payment = $stripe->paymentIntents->create([
			'amount' => 100,
			'currency' => 'usd',
			'application_fee_amount' => 0,
			'payment_method' => $payment_method_id
		]); 

		echo $payment;

		exit;
	} */

	add_filter('goodlayers_credit_card_payment_gateway_options', 'goodlayers_stripe_payment_gateway_options');
	if( !function_exists('goodlayers_stripe_payment_gateway_options') ){
		function goodlayers_stripe_payment_gateway_options( $options ){
			$options['stripe'] = esc_html__('Stripe', 'tourmaster'); 

			return $options;
		}
	}

	add_filter('goodlayers_plugin_payment_option', 'goodlayers_stripe_payment_option');
	if( !function_exists('goodlayers_stripe_payment_option') ){
		function goodlayers_stripe_payment_option( $options ){

			$options['stripe'] = array(
				'title' => esc_html__('Stripe', 'tourmaster'),
				'options' => array(
					'stripe-secret-key' => array(
						'title' => __('Stripe Secret Key', 'tourmaster'),
						'type' => 'text'
					),
					'stripe-publishable-key' => array(
						'title' => __('Stripe Publishable Key', 'tourmaster'),
						'type' => 'text'
					),	
					'stripe-currency-code' => array(
						'title' => __('Stripe Currency Code', 'tourmaster'),
						'type' => 'text',	
						'default' => 'usd'
					),	
				)
			);

			return $options;
		} // goodlayers_stripe_payment_option
	}

	$current_payment_gateway = apply_filters('goodlayers_payment_get_option', '', 'credit-card-payment-gateway');
	if( $current_payment_gateway == 'stripe' ){
		if( !class_exists('Stripe\Stripe') ){
			include_once(TOURMASTER_LOCAL . '/include/stripe/init.php');
		}

		add_action('goodlayers_payment_page_init', 'goodlayers_stripe_payment_page_init');
		add_filter('goodlayers_plugin_payment_attribute', 'goodlayers_stripe_payment_attribute');
		add_filter('goodlayers_stripe_payment_form', 'goodlayers_stripe_payment_form', 10, 2);

		add_action('wp_ajax_stripe_payment_charge', 'goodlayers_stripe_payment_charge');
		add_action('wp_ajax_nopriv_stripe_payment_charge', 'goodlayers_stripe_payment_charge');
	}

	// init the script on payment page head
	if( !function_exists('goodlayers_stripe_payment_page_init') ){
		function goodlayers_stripe_payment_page_init( $options ){
			add_action('wp_head', 'goodlayers_stripe_payment_script_include');
		}
	}
	if( !function_exists('goodlayers_stripe_payment_script_include') ){
		function goodlayers_stripe_payment_script_include( $options ){
			echo '<script src="https://js.stripe.com/v3/"></script>';
		}
	}	

	// add attribute for payment button
	if( !function_exists('goodlayers_stripe_payment_attribute') ){
		function goodlayers_stripe_payment_attribute( $attributes ){
			return array('method' => 'ajax', 'type' => 'stripe');
		}
	}

	// payment form
	if( !function_exists('goodlayers_stripe_payment_form') ){
		function goodlayers_stripe_payment_form( $ret = '', $tid = '' ){

			// get the price
			$api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'stripe-secret-key'));
			$currency = trim(apply_filters('goodlayers_payment_get_option', 'usd', 'stripe-currency-code'));
			
			$t_data = apply_filters('goodlayers_payment_get_transaction_data', array(), $tid, array('price'));
			$price = '';
			if( $t_data['price']['deposit-price'] ){
				$price = $t_data['price']['deposit-price'];
				if( !empty($t_data['price']['deposit-price-raw']) ){
					$deposit_amount = $t_data['price']['deposit-price-raw'];
				}
			}else if( !empty($t_data['price']['pay-amount']) ){
				$price = $t_data['price']['pay-amount'];
			}

			if( strtolower($currency) == 'jpy' ){
				$price = round(floatval($price));
			}else{
				$price = round(floatval($price) * 100);
			}
			
			// set payment intent
			\Stripe\Stripe::setAppInfo(
			  "WordPress Tourmaster Plugin",
			  "4.1.6",
			  "https://codecanyon.net/item/tour-master-tour-booking-travel-wordpress-plugin/20539780"
			);
			\Stripe\Stripe::setApiKey($api_key);
			$intent = \Stripe\PaymentIntent::create([
			    'amount' => $price,
			    'currency' => $currency,
			    'metadata' => array(
			    	'tid' => $tid
			    )
			]);

			$publishable_key = apply_filters('goodlayers_payment_get_option', '', 'stripe-publishable-key');

			ob_start();
?>
<div class="goodlayers-payment-form goodlayers-with-border" >
	<form action="" method="POST" id="goodlayers-stripe-payment-form" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" >


		<div class="goodlayers-payment-form-field">
			<label>
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('Card Holder Name', 'tourmaster'); ?></span>
				<input id="cardholder-name" type="text">
			</label>
		</div>

		<div class="goodlayers-payment-form-field">
			<label> 
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('Card Information', 'tourmaster'); ?></span>
			</label>
			<div id="card-element"></div>
		</div>

		<input type="hidden" name="tid" value="<?php echo esc_attr($tid) ?>" />

		<!-- error message -->
		<div class="payment-errors"></div>
		<div class="goodlayers-payment-req-field" ><?php esc_html_e('Please fill all required fields', 'tourmaster'); ?></div>

		<!-- submit button -->
		<button id="card-button" data-secret="<?= $intent->client_secret ?>"><?php esc_html_e('Submit Payment', 'tourmaster'); ?></button>
		
		<!-- for proceeding to last step -->
		<div class="goodlayers-payment-plugin-complete" ></div>
	</form>
</div>
<script type="text/javascript">
	(function($){
		var form = $('#goodlayers-stripe-payment-form');
		var tid = form.find('input[name="tid"]').val();

		var stripe = Stripe('<?php echo esc_js(trim($publishable_key)); ?>', {locale: '<?php echo get_locale(); ?>'.slice(0, 2) });
		var elements = stripe.elements();
		var cardElement = elements.create('card');
		cardElement.mount('#card-element');

		var cardholderName = document.getElementById('cardholder-name');
		var cardButton = document.getElementById('card-button');
		var clientSecret = cardButton.dataset.secret;
		cardButton.addEventListener('click', function(ev){
			form.find('.payment-errors, .goodlayers-payment-req-field').slideUp(200);

			// validate empty input field
			if( !form.find('#cardholder-name').val() ){
				var req = true;
			}else{
				var req = false;
			}

			// make the payment
			if( req ){
				form.find('.goodlayers-payment-req-field').slideDown(200);
			}else{

				// prevent multiple submission
				if( $(cardButton).hasClass('now-loading') ){
					return;
				}else{
					$(cardButton).prop('disabled', true).addClass('now-loading');
				}
				
				// made a payment
				stripe.handleCardPayment(
					clientSecret, cardElement, {
						payment_method_data: {
							billing_details: {name: cardholderName.value}
						}
					}
				).then(function(result){
					if( result.error ){

						$(cardButton).prop('disabled', false).removeClass('now-loading'); 

						// Display error.message in your UI.
						var error_message = '';
						switch(result.error.code){
							case 'incomplete_number': error_message = '<?php echo trim(esc_html__('Your card number is incomplete.', 'tourmaster')); ?>'; break;
							case 'invalid_number': error_message = '<?php echo trim(esc_html__('Your card number is invalid.', 'tourmaster')); ?>'; break;
							case 'card_declined': error_message = '<?php echo trim(esc_html__('Your card was declined.', 'tourmaster')); ?>'; break;
							case 'expired_card': error_message = '<?php echo trim(esc_html__('Your card has expired.', 'tourmaster')); ?>'; break;
							case 'incomplete_expiry': error_message = '<?php echo trim(esc_html__('Your card\'s expiration date is incomplete.', 'tourmaster')); ?>'; break;
							case 'invalid_expiry_year': error_message = '<?php echo trim(esc_html__('Your card\'s expiration year is invalid.', 'tourmaster')); ?>'; break;
							case 'invalid_expiry_month_past': error_message = '<?php echo trim(esc_html__('Your card\'s expiration date is in the past.', 'tourmaster')); ?>'; break;
							case 'incomplete_cvc': error_message = '<?php echo trim(esc_html__('Your card\'s security code is incomplete.', 'tourmaster')); ?>'; break;
							case 'incorrect_cvc': error_message = '<?php echo trim(esc_html__('Your card\'s security code is incorrect.', 'tourmaster')); ?>'; break;
							case 'incomplete_zip': error_message = '<?php echo trim(esc_html__('Your postal code is incomplete.', 'tourmaster')); ?>'; break;
							case 'processing_error': error_message = '<?php echo trim(esc_html__('An error occurred while processing your card. Try again in a little bit.', 'tourmaster')); ?>'; break;
						}
						if( error_message == '' ){
							error_message = result.error.message + ' ' + result.error.code;
						}
						form.find('.payment-errors').text(error_message).slideDown(200);

					}else{

						// The payment has succeeded. Display a success message.
						$.ajax({
							type: 'POST',
							url: form.attr('data-ajax-url'),
							data: { 'action':'stripe_payment_charge', 'tid': tid, 'paymentIntent': result.paymentIntent },
							dataType: 'json',
							error: function(a, b, c){ 
								console.log(a, b, c); 

								// display error messages
								form.find('.payment-errors').text('<?php echo esc_html__('An error occurs, please refresh the page to try again.', 'tourmaster'); ?>').slideDown(200);
								form.find('.submit').prop('disabled', false).removeClass('now-loading'); 
							},
							success: function(data){
								if( data.status == 'success' ){
									form.find('.goodlayers-payment-plugin-complete').trigger('click');
								}else if( typeof(data.message) != 'undefined' ){
									form.find('.payment-errors').text(data.message).slideDown(200);
								}
							}
						});	
						
					}
				});
			}
		});
		$(cardButton).on('click', function(){
			return false;
		});
	})(jQuery);
</script>
<?php
			$ret = ob_get_contents();
			ob_end_clean();
			return $ret;
		}
	}

	// ajax for payment submission
	if( !function_exists('goodlayers_stripe_payment_charge') ){
		function goodlayers_stripe_payment_charge(){

			$ret = array();

			if( !empty($_POST['paymentIntent']) && !empty($_POST['tid']) ){
				$payment_intent = tourmaster_process_post_data($_POST['paymentIntent']);

				if( !empty($payment_intent['id']) ){
					$api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'stripe-secret-key'));
					
					\Stripe\Stripe::setApiKey($api_key);
					$pi = \Stripe\PaymentIntent::retrieve($payment_intent['id']);

					if( $pi['status'] == 'succeeded' && $pi['metadata']->tid == $_POST['tid'] ){

						$currency = trim(apply_filters('goodlayers_payment_get_option', 'usd', 'stripe-currency-code'));
						if( strtolower($currency) == 'jpy' ){
							$paid_amount = $pi['amount'];
						}else{
							$paid_amount = $pi['amount'] / 100;
						}

						// collect payment information
						$payment_info = array(
							'payment_method' => 'stripe',
							'amount' => $paid_amount,
							'transaction_id' => $pi['id'],
							'payment_status' => 'paid',
							'submission_date' => current_time('mysql')
						);

						// additional data for payment fee
						$t_data = apply_filters('goodlayers_payment_get_transaction_data', array(), $_POST['tid'], array('price', 'email'));
						if( $t_data['price']['deposit-price'] ){
							if( !empty($t_data['price']['deposit-price-raw']) ){
								$payment_info['deposit_amount'] = $t_data['price']['deposit-price-raw'];
							}
							if( !empty($t_data['price']['deposit-credit-card-service-rate']) ){
								$payment_info['deposit_credit_card_service_rate'] = $t_data['price']['deposit-credit-card-service-rate'];
							}
							if( !empty($t_data['price']['deposit-credit-card-service-fee']) ){
								$payment_info['deposit_credit_card_service_fee'] = $t_data['price']['deposit-credit-card-service-fee'];
							}
						}else{
							if( !empty($t_data['price']['pay-amount-raw']) ){
								$payment_info['pay_amount'] = $t_data['price']['pay-amount-raw'];
							}
							if( !empty($t_data['price']['pay-amount-credit-card-service-rate']) ){
								$payment_info['pay_credit_card_service_rate'] = $t_data['price']['pay-amount-credit-card-service-rate'];
							}
							if( !empty($t_data['price']['pay-amount-credit-card-service-fee']) ){
								$payment_info['pay_credit_card_service_fee'] = $t_data['price']['pay-amount-credit-card-service-fee'];
							}
						}

						// update data
						do_action('goodlayers_set_payment_complete', $_POST['tid'], $payment_info);

						$ret['status'] = 'success';

					}
				}
			}

			die(json_encode($ret));

		} // goodlayers_stripe_payment_charge
	}

    /** 
	 * 
	 * 
	 * 
	 * STRIPE CONNECT CUSTOM CODE
	 * 
	 * 
	 * 
	 * **/

	add_filter('goodlayers_credit_card_payment_gateway_options', 'goodlayers_stripe_connect_payment_gateway_options');
	if( !function_exists('goodlayers_stripe_connect_payment_gateway_options') ){
		function goodlayers_stripe_connect_payment_gateway_options( $options ){
			$options['stripe_connect'] = esc_html__('Stripe Connect', 'tourmaster'); 

			return $options;
		}
	}

	add_filter('goodlayers_plugin_payment_option', 'goodlayers_stripe_connect_payment_option');
	if( !function_exists('goodlayers_stripe_connect_payment_option') ){
		function goodlayers_stripe_connect_payment_option( $options ){

			$options['stripe_connect'] = array(
				'title' => esc_html__('Stripe Connect', 'tourmaster'),
				'options' => array(
					'stripe-secret-key' => array(
						'title' => __('Stripe Secret Key', 'tourmaster'),
						'type' => 'text'
					),
					'stripe-publishable-key' => array(
						'title' => __('Stripe Publishable Key', 'tourmaster'),
						'type' => 'text'
					),	
					'stripe-currency-code' => array(
						'title' => __('Stripe Currency Code', 'tourmaster'),
						'type' => 'text',	
						'default' => 'usd'
					),	
				)
			);

			return $options;
		} // goodlayers_stripe_payment_option
	}

	if( $current_payment_gateway == 'stripe_connect' ){
		if( !class_exists('Stripe\Stripe') ){
			include_once(TOURMASTER_LOCAL . '/include/stripe/init.php');
		}

		add_action('goodlayers_payment_page_init', 'goodlayers_stripe_connect_payment_page_init');
		add_filter('goodlayers_plugin_payment_attribute', 'goodlayers_stripe_connect_payment_attribute');
		add_filter('goodlayers_stripe_connect_payment_form', 'goodlayers_stripe_connect_payment_form', 10, 2);

		add_action('wp_ajax_stripe_connect_payment_charge', 'goodlayers_stripe_connect_payment_charge');
		add_action('wp_ajax_nopriv_stripe_connect_payment_charge', 'goodlayers_stripe_connect_payment_charge');
	}

	// init the script on payment page head
	if( !function_exists('goodlayers_stripe_connect_payment_page_init') ){
		function goodlayers_stripe_connect_payment_page_init( $options ){
			add_action('wp_head', 'goodlayers_stripe_connect_payment_script_include');
		}
	}
	if( !function_exists('goodlayers_stripe_connect_payment_script_include') ){
		function goodlayers_stripe_connect_payment_script_include( $options ){
			echo '<script src="https://js.stripe.com/v3/"></script>';
		}
	}	

	// add attribute for payment button
	if( !function_exists('goodlayers_stripe_connect_payment_attribute') ){
		function goodlayers_stripe_connect_payment_attribute( $attributes ){
			return array('method' => 'ajax', 'type' => 'stripe_connect');
		}
	}

	// payment form
	if( !function_exists('goodlayers_stripe_connect_payment_form') ){
		function goodlayers_stripe_connect_payment_form( $ret = '', $tid = '' ){

			// get the price
			$api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'stripe-secret-key'));
			$currency = trim(apply_filters('goodlayers_payment_get_option', 'usd', 'stripe-currency-code'));
			
			$t_data = apply_filters('goodlayers_payment_get_transaction_data', array(), $tid, array('price'));
			$price = '';
			if( $t_data['price']['deposit-price'] ){
				$price = $t_data['price']['deposit-price'];
				if( !empty($t_data['price']['deposit-price-raw']) ){
					$deposit_amount = $t_data['price']['deposit-price-raw'];
				}
			}else if( !empty($t_data['price']['pay-amount']) ){
				$price = $t_data['price']['pay-amount'];
			}

			if( strtolower($currency) == 'jpy' ){
				$price = round(floatval($price));
			}else{
				$price = round(floatval($price) * 100);
			}
			
			//custom code

			$result = tourmaster_get_booking_data(array(
				'id' => $tid
			), array('single' => true));

			$auth_id = get_post_field( 'post_author', $result->tour_id );

			$stripe_act_id = get_user_meta($auth_id, 'stripe_connect_id', true);

			// set payment intent
			\Stripe\Stripe::setAppInfo(
			  "WordPress Tourmaster Plugin",
			  "4.1.6",
			  "https://codecanyon.net/item/tour-master-tour-booking-travel-wordpress-plugin/20539780"
			);
			\Stripe\Stripe::setApiKey($api_key);
			$intent = \Stripe\PaymentIntent::create([
			    'amount' => $price,
				'currency' => $currency,
				'application_fee_amount' => 2000,
				'transfer_data' => [
					'destination' => $stripe_act_id,
				],
			    'metadata' => array(
			    	'tid' => $tid
				),
				'capture_method' => 'manual'
			]);

			$publishable_key = apply_filters('goodlayers_payment_get_option', '', 'stripe-publishable-key');

			ob_start();
?>
<div class="goodlayers-payment-form goodlayers-with-border" >
	<form action="" method="POST" id="goodlayers-stripe-connect-payment-form" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" >


		<div class="goodlayers-payment-form-field">
			<label>
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('Card Holder Name', 'tourmaster'); ?></span>
				<input id="cardholder-name" type="text">
			</label>
		</div>

		<div class="goodlayers-payment-form-field">
			<label> 
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('Card Information', 'tourmaster'); ?></span>
			</label>
			<div id="card-element"></div>
		</div>

		<input type="hidden" name="tid" value="<?php echo esc_attr($tid) ?>" />

		<!-- error message -->
		<div class="payment-errors"></div>
		<div class="goodlayers-payment-req-field" ><?php esc_html_e('Please fill all required fields', 'tourmaster'); ?></div>

		<!-- submit button -->
		<button id="card-button" data-secret="<?= $intent->client_secret ?>"><?php esc_html_e('Submit Payment', 'tourmaster'); ?></button>
		
		<!-- for proceeding to last step -->
		<div class="goodlayers-payment-plugin-complete" ></div>
	</form>
</div>
<script type="text/javascript">
	(function($){
		var form = $('#goodlayers-stripe-connect-payment-form');
		var tid = form.find('input[name="tid"]').val();

		var stripe = Stripe('<?php echo esc_js(trim($publishable_key)); ?>', {locale: '<?php echo get_locale(); ?>'.slice(0, 2) });
		var elements = stripe.elements();
		var cardElement = elements.create('card');
		cardElement.mount('#card-element');

		var cardholderName = document.getElementById('cardholder-name');
		var cardButton = document.getElementById('card-button');
		var clientSecret = cardButton.dataset.secret;
		cardButton.addEventListener('click', function(ev){
			form.find('.payment-errors, .goodlayers-payment-req-field').slideUp(200);

			// validate empty input field
			if( !form.find('#cardholder-name').val() ){
				var req = true;
			}else{
				var req = false;
			}

			// make the payment
			if( req ){
				form.find('.goodlayers-payment-req-field').slideDown(200);
			}else{

				// prevent multiple submission
				if( $(cardButton).hasClass('now-loading') ){
					return;
				}else{
					$(cardButton).prop('disabled', true).addClass('now-loading');
				}
				
				// made a payment
				stripe.handleCardPayment(
					clientSecret, cardElement, {
						payment_method_data: {
							billing_details: {name: cardholderName.value}
						}
					}
				).then(function(result){
					if( result.error ){

						$(cardButton).prop('disabled', false).removeClass('now-loading'); 

						// Display error.message in your UI.
						var error_message = '';
						switch(result.error.code){
							case 'incomplete_number': error_message = '<?php echo trim(esc_html__('Your card number is incomplete.', 'tourmaster')); ?>'; break;
							case 'invalid_number': error_message = '<?php echo trim(esc_html__('Your card number is invalid.', 'tourmaster')); ?>'; break;
							case 'card_declined': error_message = '<?php echo trim(esc_html__('Your card was declined.', 'tourmaster')); ?>'; break;
							case 'expired_card': error_message = '<?php echo trim(esc_html__('Your card has expired.', 'tourmaster')); ?>'; break;
							case 'incomplete_expiry': error_message = '<?php echo trim(esc_html__('Your card\'s expiration date is incomplete.', 'tourmaster')); ?>'; break;
							case 'invalid_expiry_year': error_message = '<?php echo trim(esc_html__('Your card\'s expiration year is invalid.', 'tourmaster')); ?>'; break;
							case 'invalid_expiry_month_past': error_message = '<?php echo trim(esc_html__('Your card\'s expiration date is in the past.', 'tourmaster')); ?>'; break;
							case 'incomplete_cvc': error_message = '<?php echo trim(esc_html__('Your card\'s security code is incomplete.', 'tourmaster')); ?>'; break;
							case 'incorrect_cvc': error_message = '<?php echo trim(esc_html__('Your card\'s security code is incorrect.', 'tourmaster')); ?>'; break;
							case 'incomplete_zip': error_message = '<?php echo trim(esc_html__('Your postal code is incomplete.', 'tourmaster')); ?>'; break;
							case 'processing_error': error_message = '<?php echo trim(esc_html__('An error occurred while processing your card. Try again in a little bit.', 'tourmaster')); ?>'; break;
						}
						if( error_message == '' ){
							error_message = result.error.message + ' ' + result.error.code;
						}
						form.find('.payment-errors').text(error_message).slideDown(200);

					}else{

						// The payment has succeeded. Display a success message.
						$.ajax({
							type: 'POST',
							url: form.attr('data-ajax-url'),
							data: { 'action':'stripe_connect_payment_charge', 'tid': tid, 'paymentIntent': result.paymentIntent },
							dataType: 'json',
							error: function(a, b, c){ 
								console.log(a, b, c); 

								// display error messages
								form.find('.payment-errors').text('<?php echo esc_html__('An error occurs, please refresh the page to try again.', 'tourmaster'); ?>').slideDown(200);
								form.find('.submit').prop('disabled', false).removeClass('now-loading'); 
							},
							success: function(data){
								if( data.status == 'success' ){
									form.find('.goodlayers-payment-plugin-complete').trigger('click');
								}else if( typeof(data.message) != 'undefined' ){
									form.find('.payment-errors').text(data.message).slideDown(200);
								}
							}
						});	
						
					}
				});
			}
		});
		$(cardButton).on('click', function(){
			return false;
		});
	})(jQuery);
</script>
<?php
			$ret = ob_get_contents();
			ob_end_clean();
			return $ret;
		}
	}

	// ajax for payment submission
	if( !function_exists('goodlayers_stripe_connect_payment_charge') ){
		function goodlayers_stripe_connect_payment_charge(){

			$ret = array();

			if( !empty($_POST['paymentIntent']) && !empty($_POST['tid']) ){
				$payment_intent = tourmaster_process_post_data($_POST['paymentIntent']);

				if( !empty($payment_intent['id']) ){
					$api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'stripe-secret-key'));
					
					\Stripe\Stripe::setApiKey($api_key);
					$pi = \Stripe\PaymentIntent::retrieve($payment_intent['id']);

					if( $pi['status'] == 'succeeded' && $pi['metadata']->tid == $_POST['tid'] ){

						$currency = trim(apply_filters('goodlayers_payment_get_option', 'usd', 'stripe-currency-code'));
						if( strtolower($currency) == 'jpy' ){
							$paid_amount = $pi['amount'];
						}else{
							$paid_amount = $pi['amount'] / 100;
						}

						// collect payment information
						$payment_info = array(
							'payment_method' => 'stripe',
							'amount' => $paid_amount,
							'transaction_id' => $pi['id'],
							'payment_status' => 'paid',
							'submission_date' => current_time('mysql')
						);

						// additional data for payment fee
						$t_data = apply_filters('goodlayers_payment_get_transaction_data', array(), $_POST['tid'], array('price', 'email'));
						if( $t_data['price']['deposit-price'] ){
							if( !empty($t_data['price']['deposit-price-raw']) ){
								$payment_info['deposit_amount'] = $t_data['price']['deposit-price-raw'];
							}
							if( !empty($t_data['price']['deposit-credit-card-service-rate']) ){
								$payment_info['deposit_credit_card_service_rate'] = $t_data['price']['deposit-credit-card-service-rate'];
							}
							if( !empty($t_data['price']['deposit-credit-card-service-fee']) ){
								$payment_info['deposit_credit_card_service_fee'] = $t_data['price']['deposit-credit-card-service-fee'];
							}
						}else{
							if( !empty($t_data['price']['pay-amount-raw']) ){
								$payment_info['pay_amount'] = $t_data['price']['pay-amount-raw'];
							}
							if( !empty($t_data['price']['pay-amount-credit-card-service-rate']) ){
								$payment_info['pay_credit_card_service_rate'] = $t_data['price']['pay-amount-credit-card-service-rate'];
							}
							if( !empty($t_data['price']['pay-amount-credit-card-service-fee']) ){
								$payment_info['pay_credit_card_service_fee'] = $t_data['price']['pay-amount-credit-card-service-fee'];
							}
						}

						// update data
						do_action('goodlayers_set_payment_complete', $_POST['tid'], $payment_info);

						$ret['status'] = 'success';

					}
				}
			}

			die(json_encode($ret));

		} // goodlayers_stripe_payment_charge
	}

$api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'stripe-secret-key'));

\Stripe\Stripe::setApiKey($api_key);

function stripe(){
	$api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'stripe-secret-key'));
	$stripe = new \Stripe\StripeClient($api_key);
	return $stripe;
} 

function add_custom_headers() {

	add_filter( 'rest_pre_serve_request', function( $value ) {
		header( 'Access-Control-Allow-Headers: Authorization, X-WP-Nonce,Content-Type, X-Requested-With');
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
		header( 'Access-Control-Allow-Credentials: true' );

		return $value;
	} );
}
add_action( 'rest_api_init', 'add_custom_headers');

add_action('rest_api_init', 'tot_stripe_get_oauth_link_endpoint');
add_action('rest_api_init', 'tot_stripe_connect_oauth_endpoint');
add_action('rest_api_init', 'tot_stripe_get_account_info');
add_action('rest_api_init', 'tot_stripe_get_all_transfers');


function tot_stripe_get_oauth_link_endpoint(){
	register_rest_route('tot/v1', 'get/oauth', array(
		'methods' => 'GET',
		'callback' => 'tot_rest_stripe_get_oauth_link_endpoint',
	));
}
  
function tot_stripe_connect_oauth_endpoint(){
	register_rest_route('tot/v1', 'connect/oauth', array(
		'methods' => 'GET',
		'callback' => 'tot_rest_stripe_connect_oauth_endpoint',
	));
}

function tot_stripe_get_account_info(){
	register_rest_route('tot/v1', 'stripe/get_acct/(?P<id>\w+)', array(
		'methods' => 'GET',
		'callback' => 'tot_rest_stripe_get_account_info',
	));
}

function tot_stripe_get_all_transfers(){
	register_rest_route('tot/v1', 'stripe/get_all_tr/(?P<id>\w+)', array(
		'methods' => 'GET',
		'callback' => 'tot_rest_stripe_get_all_transfers',
	));
}



function tot_rest_stripe_get_oauth_link_endpoint($request = null){
	$email = $_GET["email"];
	$state = $_GET["username"];

	// $url = 'https://connect.stripe.com/express/oauth/authorize?' . 'client_id=ca_AbYvq1YpGg7rRjY9T7ICBtBWj6z87bCb&state='.$state.'&suggested_capabilities[]=transfers&stripe_user[email]='.$email;
	$url = 'https://connect.stripe.com/express/oauth/authorize?' . 'client_id=ca_AbYvTPYvAwTV0gHkW6vJCkWWJqKJxlbr&state='.$state.'&suggested_capabilities[]=transfers&stripe_user[email]='.$email;
	return $url;
}

function tot_rest_stripe_connect_oauth_endpoint($request = null){
	$state = $_GET["state"];
	$code = $_GET["code"];
	$response = array();

	// Assert the state matches the state you provided in the OAuth link (optional).
	
	try {
		$stripeResponse = \Stripe\OAuth::token([
		'grant_type' => 'authorization_code',
		'code' => $code,
		]);
	} catch (\Stripe\Error\OAuth\InvalidGrant $e) {
		return $response = array('code' => 400, 'error' => 'Invalid authorization code: ' . $code);
	} catch (Exception $e) {
		return $response = array('code' => 500,'error' => 'An unknown error occurred.'.$e);
	}

	$connectedAccountId = $stripeResponse->stripe_user_id;
	$response = 'Connected account ID: ' . $connectedAccountId;

	$user = get_user_by('login', $state);

	add_user_meta($user->ID, 'stripe_connect_id', $connectedAccountId);

	function Redirect($url, $permanent = false)
	{
		header('Location: ' . $url, true, $permanent ? 301 : 302);

		exit();
	}

	// Redirect('https://theoutdoortrip.stg.elaniin.dev/dashboard/?page_type=my-payments', false);
	Redirect('https://theoutdoortrip.com/dashboard/?page_type=my-payments', false);

	return $response;
}

function tot_rest_stripe_get_account_info($request = null){
	$id = $request["id"];

	$stripe = stripe();

	$customer = $stripe->accounts->retrieve(
	$id,
	[]
	);

	return $customer;
}

function tot_rest_stripe_get_all_transfers($request = null){
	$id = $request["id"];
	$stripe = stripe();

	$response = array();

	$pis = $stripe->paymentIntents->all();
	$counter=0;
	foreach($pis as $pi){
		if ($pi['transfer_data']['destination'] == $id) {
			if ($pi['charges']['total_count'] != 0) {
				$response[$counter]['pi_id']=$pi['id'];
				$response[$counter]['amount']=$pi['amount'];
				$response[$counter]['fee']=$pi['application_fee_amount'];
				$response[$counter]['order_trip_id']=$pi['metadata']['tid'];
				$response[$counter]['payment_date']=$pi['created'];
				$counter+=1;
			}
		}
		
	}
	return $response;
}