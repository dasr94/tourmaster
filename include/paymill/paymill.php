<?php
	/*	
	*	Payment Plugin
	*	---------------------------------------------------------------------
	*	creating the paymill payment option
	*	---------------------------------------------------------------------
	*/

	add_filter('goodlayers_credit_card_payment_gateway_options', 'goodlayers_paymill_payment_gateway_options');
	if( !function_exists('goodlayers_paymill_payment_gateway_options') ){
		function goodlayers_paymill_payment_gateway_options( $options ){
			$options['paymill'] = esc_html__('Paymill', 'tourmaster'); 

			return $options;
		}
	}	

	add_filter('goodlayers_plugin_payment_option', 'goodlayers_paymill_payment_option');
	if( !function_exists('goodlayers_paymill_payment_option') ){
		function goodlayers_paymill_payment_option( $options ){

			$options['paymill'] = array(
				'title' => esc_html__('Paymill', 'tourmaster'),
				'options' => array(
					'paymill-private-key' => array(
						'title' => __('Paymill Private Key', 'tourmaster'),
						'type' => 'text'
					),
					'paymill-public-key' => array(
						'title' => __('Paymill Public Key', 'tourmaster'),
						'type' => 'text'
					),	
					'paymill-currency-code' => array(
						'title' => __('Paymill Currency Code', 'tourmaster'),
						'type' => 'text',	
						'default' => 'usd'
					),	
				)
			);

			return $options;
		} // goodlayers_paymill_payment_option
	}

	$current_payment_gateway = apply_filters('goodlayers_payment_get_option', '', 'credit-card-payment-gateway');
	if( $current_payment_gateway == 'paymill' ){
		include_once(TOURMASTER_LOCAL . '/include/paymill/autoload.php');

		add_action('goodlayers_payment_page_init', 'goodlayers_paymill_payment_page_init');
		add_filter('goodlayers_plugin_payment_attribute', 'goodlayers_paymill_payment_attribute');
		add_filter('goodlayers_paymill_payment_form', 'goodlayers_paymill_payment_form', 10, 2);

		add_action('wp_ajax_paymill_payment_charge', 'goodlayers_paymill_payment_charge');
		add_action('wp_ajax_nopriv_paymill_payment_charge', 'goodlayers_paymill_payment_charge');
	}	

	// init the script on payment page head
	if( !function_exists('goodlayers_paymill_payment_page_init') ){
		function goodlayers_paymill_payment_page_init( $options ){
			add_action('wp_head', 'goodlayers_paymill_payment_script_include');
		}
	}
	if( !function_exists('goodlayers_paymill_payment_script_include') ){
		function goodlayers_paymill_payment_script_include( $options ){
			echo '<script type="text/javascript" src="https://bridge.paymill.com/"></script>';
		}
	}	

	// add attribute for payment button
	if( !function_exists('goodlayers_paymill_payment_attribute') ){
		function goodlayers_paymill_payment_attribute( $attributes ){
			return array('method' => 'ajax', 'type' => 'paymill');
		}
	}

	// payment form
	if( !function_exists('goodlayers_paymill_payment_form') ){
		function goodlayers_paymill_payment_form( $ret = '', $tid = '' ){
			$public_key = apply_filters('goodlayers_payment_get_option', '', 'paymill-public-key');
			$currency = apply_filters('goodlayers_payment_get_option', 'usd', 'paymill-currency-code');

			// get the price
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
			$price = round(floatval($price) * 100);

			ob_start();
?>
<div class="goodlayers-payment-form goodlayers-with-border" >
	<form action="" method="POST" id="goodlayers-paymill-payment-form" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" >
		<div class="goodlayers-payment-form-field">
			<label>
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('Card Holder Name', 'tourmaster'); ?></span>
				<input type="text" data-paymill="name">
			</label>
		</div>
		<div class="goodlayers-payment-form-field">
			<label>
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('Card Number', 'tourmaster'); ?></span>
				<input type="text" data-paymill="number">
			</label>
		</div>
		<div class="goodlayers-payment-form-field">
			<label>
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('Expiration (MM/YYYY)', 'tourmaster'); ?></span>
				<input class="goodlayers-size-small" type="text" size="2" data-paymill="exp_month" />
			</label>
			<span class="goodlayers-separator" >/</span>
			<input class="goodlayers-size-small" type="text" size="2" data-paymill="exp_year" />
		</div>
		<div class="goodlayers-payment-form-field">
			<label>
				<span class="goodlayers-payment-field-head" ><?php esc_html_e('CVC', 'tourmaster'); ?></span>
				<input class="goodlayers-size-small" type="text" size="4" data-paymill="cvc" />
			</label>
		</div>
		<div class="now-loading"></div>
		<div class="payment-errors"></div>
		<div class="goodlayers-payment-req-field" ><?php esc_html_e('Please fill all required fields', 'tourmaster'); ?></div>
		<input type="hidden" name="tid" value="<?php echo esc_attr($tid) ?>" />
		<input class="goodlayers-payment-button submit" type="submit" value="<?php esc_html_e('Submit Payment', 'tourmaster'); ?>" />
		
		<!-- for proceeding to last step -->
		<div class="goodlayers-payment-plugin-complete" ></div>
	</form>
</div>
<script type="text/javascript">
	var PAYMILL_PUBLIC_KEY = '<?php echo esc_js(trim($public_key)); ?>';

	(function($){
		var form = $('#goodlayers-paymill-payment-form');

		function paymillResponseHandler(error, result){
			if( error ){
				console.log(error);
				form.find('.payment-errors').text(error.apierror).slideDown(200);
				form.find('.submit').prop('disabled', false).removeClass('now-loading'); 
			}else{
				var token = result.token;
				var tid = form.find('input[name="tid"]').val();

				$.ajax({
					type: 'POST',
					url: form.attr('data-ajax-url'),
					data: { 'action':'paymill_payment_charge','token': token, 'tid': tid },
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

						form.find('.submit').prop('disabled', false).removeClass('now-loading'); 
					}
				});	
			}
		};
		
		form.submit(function(event){
			var req = false;
			form.find('input').each(function(){
				if( !$(this).val() ){
					req = true;
				}
			});

			if( req ){
				form.find('.goodlayers-payment-req-field').slideDown(200)
			}else{
				form.find('.submit').prop('disabled', true).addClass('now-loading');
				form.find('.payment-errors, .goodlayers-payment-req-field').slideUp(200);

				paymill.createToken({
					cardholder: form.find('[data-paymill="name"]').val(), 
					number: form.find('[data-paymill="number"]').val(), 
					exp_month: form.find('[data-paymill="exp_month"]').val(),   
					exp_year: form.find('[data-paymill="exp_year"]').val(),     
					cvc: form.find('[data-paymill="cvc"]').val(),
					amount_int: '<?php echo esc_js(trim($price)); ?>',
					currency: '<?php echo esc_js(trim($currency)); ?>',
				}, paymillResponseHandler); 
			}

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
	if( !function_exists('goodlayers_paymill_payment_charge') ){
		function goodlayers_paymill_payment_charge(){

			$ret = array();

			if( !empty($_POST['token']) && !empty($_POST['tid']) ){
				$api_key = trim(apply_filters('goodlayers_payment_get_option', '', 'paymill-private-key'));
				$currency = trim(apply_filters('goodlayers_payment_get_option', 'usd', 'paymill-currency-code'));

				$t_data = apply_filters('goodlayers_payment_get_transaction_data', array(), $_POST['tid'], array('price', 'email'));
				$price = '';
				if( $t_data['price']['deposit-price'] ){
					$price = $t_data['price']['deposit-price'];
				}else{
					$price = $t_data['price']['pay-amount'];
				}

				if( empty($price) ){
					$ret['status'] = 'failed';
					$ret['message'] = esc_html__('Cannot retrieve pricing data, please try again.', 'tourmaster');
				}else{
					$price = round(floatval($price) * 100);

					try{
						$request = new Paymill\Request($api_key);

						$payment = new Paymill\Models\Request\Payment();
						$payment->setToken($_POST['token']);
						$response_request  = $request->create($payment);

						$transaction = new Paymill\Models\Request\Transaction();
						$transaction->setAmount($price)
								->setCurrency($currency)
								->setPayment($response_request->getId())
								->setDescription($t_data['email']);
						
						$request->create($transaction);
						$response = $request->getLastResponse();

						$payment_info = array(
							'payment_method' => 'paymill',
							'amount' => intval($response['body']['data']['amount']) / 100,
							'transaction_id' => $response['body']['data']['id'],
							'payment_status' => 'paid',
							'submission_date' => current_time('mysql')
						);
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
						do_action('goodlayers_set_payment_complete', $_POST['tid'], $payment_info);

						$ret['status'] = 'success';

					}catch( Exception $e ){
						$ret['status'] = 'failed';
						$ret['message'] = $e->getMessage();
					}
				}
			}

			die(json_encode($ret));

		} // goodlayers_paymill_payment_charge
	}
