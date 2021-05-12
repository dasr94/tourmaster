<?php

	add_filter('goodlayers_plugin_payment_option', 'tourmaster_paypal_payment_option');
	if( !function_exists('tourmaster_paypal_payment_option') ){
		function tourmaster_paypal_payment_option( $options ){

			$options['paypal'] = array(
				'title' => esc_html__('Paypal', 'tourmaster'),
				'options' => array(
					'paypal-live-mode' => array(
						'title' => __('Paypal Live Mode', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'description' => esc_html__('Disable this option to test on sandbox mode.', 'tourmaster')
					),
					'paypal-business-email' => array(
						'title' => esc_html__('Paypal Business Email', 'tourmaster'),
						'type' => 'text'
					),
					'paypal-currency-code' => array(
						'title' => esc_html__('Paypal Currency Code', 'tourmaster'),
						'type' => 'text',	
						'default' => 'USD'
					),
					'paypal-service-fee' => array(
						'title' => esc_html__('Paypal Service Fee (%)', 'tourmaster'),
						'type' => 'text',	
						'default' => '',
						'description' => esc_html__('Fill only number here', 'tourmaster')
					),	
				)
			);

			return $options;
		} // tourmaster_paypal_payment_option
	}

	add_filter('tourmaster_paypal_button_atts', 'tourmaster_paypal_button_attribute');
	if( !function_exists('tourmaster_paypal_button_attribute') ){
		function tourmaster_paypal_button_attribute( $attributes ){
			$service_fee = tourmaster_get_option('payment', 'paypal-service-fee', '');

			return array('method' => 'ajax', 'type' => 'paypal', 'service-fee' => $service_fee);
		}
	}

	add_filter('goodlayers_paypal_payment_form', 'tourmaster_paypal_payment_form', 10, 2);
	if( !function_exists('tourmaster_paypal_payment_form') ){
		function tourmaster_paypal_payment_form( $ret = '', $tid = '' ){
			
			$live_mode = tourmaster_get_option('payment', 'paypal-live-mode', 'disable');
			$business_email = tourmaster_get_option('payment', 'paypal-business-email', '');
			$currency_code = tourmaster_get_option('payment', 'paypal-currency-code', '');
			$service_fee = tourmaster_get_option('payment', 'paypal-service-fee', '');

			$t_data = apply_filters('goodlayers_payment_get_transaction_data', array(), $tid, array('price', 'tour_id'));
			
			$price = '';
			if( $t_data['price']['deposit-price'] ){
				$price = $t_data['price']['deposit-price'];
			}else{
				$price = $t_data['price']['pay-amount'];
			}

			ob_start();
?>
<div class="goodlayers-paypal-redirecting-message" ><?php esc_html_e('Please wait while we redirect you to paypal.', 'tourmaster') ?></div>
<form id="goodlayers-paypal-redirection-form" method="post" action="<?php
		if( empty($live_mode) || $live_mode == 'disable' ){
			echo 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}else{
			echo 'https://www.paypal.com/cgi-bin/webscr';
		}
	?>" >
	<input type="hidden" name="cmd" value="_xclick" />
	<input type="hidden" name="business" value="<?php echo esc_attr(trim($business_email)); ?>" />
	<input type="hidden" name="currency_code" value="<?php echo esc_attr(trim($currency_code)); ?>" />
	<input type="hidden" name="item_name" value="<?php echo get_the_title($t_data['tour_id']); ?>" />
	<input type="hidden" name="invoice" value="<?php
		// 01 for tourmaster
		echo '01' . date('dmYHis') . $tid;
	?>" />
	<input type="hidden" name="amount" value="<?php echo esc_attr($price); ?>" />
	<input type="hidden" name="notify_url" value="<?php 
		if( function_exists('pll_home_url') ){
			$home_url = pll_home_url();
		}else{
			$home_url = apply_filters('wpml_home_url', home_url('/'));
		}
		echo add_query_arg(array('paypal'=>''), $home_url); 

	?>" />
	<input type="hidden" name="return" value="<?php
		echo add_query_arg(array('tid' => $tid, 'step' => 4, 'payment_method' => 'paypal'), tourmaster_get_template_url('payment'));
	?>" />
</form>
<script type="text/javascript">
	(function($){
		$('#goodlayers-paypal-redirection-form').submit();
	})(jQuery);
</script>
<?php
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;

		} // goodlayers_paypal_payment_form
	}

	add_action('init', 'tourmaster_paypal_process_ipn');
	if( !function_exists('tourmaster_paypal_process_ipn') ){
		function tourmaster_paypal_process_ipn(){

			if( isset($_GET['paypal']) ){

				$payment_info = array(
					'payment_method' => 'paypal',
					'submission_date' => current_time('mysql')
				);

				$live_mode = tourmaster_get_option('payment', 'paypal-live-mode', '');
				if( empty($live_mode) || $live_mode == 'disable' ){
					$paypal_action_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
				}else{
					$paypal_action_url = 'https://ipnpb.paypal.com/cgi-bin/webscr';
				}
				// read the post data
				$raw_post_data = file_get_contents('php://input');
				$raw_post_array = explode('&', $raw_post_data);
				$myPost = array();
				foreach ($raw_post_array as $keyval) {
				    $keyval = explode('=', $keyval);
				    if (count($keyval) == 2) {
				        // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
				        if ($keyval[0] === 'payment_date') {
				            if (substr_count($keyval[1], '+') === 1) {
				                $keyval[1] = str_replace('+', '%2B', $keyval[1]);
				            }
				        }
				        $myPost[$keyval[0]] = urldecode($keyval[1]);
				    }
				}

				// prepare post request
				$req = 'cmd=_notify-validate';
		        $get_magic_quotes_exists = false;
		        if (function_exists('get_magic_quotes_gpc')) {
		            $get_magic_quotes_exists = true;
		        }
		        foreach ($myPost as $key => $value) {
		            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		                $value = urlencode(stripslashes($value));
		            } else {
		                $value = urlencode($value);
		            }
		            $req .= "&$key=$value";
		        }

		        // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
		        $ch = curl_init($paypal_action_url);
		        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		        curl_setopt($ch, CURLOPT_POST, 1);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: tourmaster'));
				
				$res = curl_exec($ch);
		        if ( !$res ){ 

		            $payment_info['error'] = curl_error($ch);

		            if( !empty($_POST['invoice']) ){
		            	$tid = substr($_POST['invoice'], 16);

		            	// get old payment info
						$result = tourmaster_get_booking_data(array('id' => $tid), array('single' => true));
						$payment_infos = json_decode($result->payment_info, true);
						$payment_infos = tourmaster_payment_info_format($payment_infos, $result->order_status);
						$payment_infos[] = $payment_info;

		            	tourmaster_update_booking_data( 
							array(
								'payment_info' => json_encode($payment_infos),
							),
							array('id' => $tid),
							array('%s'),
							array('%d')
						);
		            }

		        }else if( strcmp ($res, "VERIFIED") == 0 ){

		        	$tid = substr($_POST['invoice'], 16);
		        	$payment_info['transaction_id'] = $_POST['txn_id'];
		        	$payment_info['amount'] = $_POST['mc_gross'];
		        	$payment_info['payment_status'] = 'paid';

		        	$result = tourmaster_get_booking_data(array('id'=>$tid), array('single'=>true));
		        	$pricing_info = json_decode($result->pricing_info, true);
		        	$mail_type = 'payment-made-mail';
		        	$admin_mail_type = 'admin-online-payment-made-mail';

		        	if( !empty($pricing_info['deposit-price']) && tourmaster_compare_price($pricing_info['deposit-price'], $payment_info['amount']) ){
		        		$order_status = 'deposit-paid';
		        		if( !empty($pricing_info['deposit-price-raw']) ){
		        			$payment_info['deposit_amount'] = $pricing_info['deposit-price-raw'];
						}
						if( !empty($pricing_info['deposit-paypal-service-rate']) ){
							$payment_info['deposit_paypal_service_rate'] = $pricing_info['deposit-paypal-service-rate'];
						}
						if( !empty($pricing_info['deposit-paypal-service-fee']) ){
							$payment_info['deposit_paypal_service_fee'] = $pricing_info['deposit-paypal-service-fee'];
						}
						$mail_type = 'deposit-payment-made-mail';
		        		$admin_mail_type = 'admin-deposit-payment-made-mail';
		        	}else if( tourmaster_compare_price($pricing_info['pay-amount'], $payment_info['amount']) ){
		        		$order_status = 'online-paid';
		        		if( !empty($pricing_info['pay-amount-raw']) ){
		        			$payment_info['pay_amount'] = $pricing_info['pay-amount-raw'];
						}
						if( !empty($pricing_info['pay-amount-paypal-service-rate']) ){
							$payment_info['pay_paypal_service_rate'] = $pricing_info['pay-amount-paypal-service-rate'];
						}
						if( !empty($pricing_info['pay-amount-paypal-service-fee']) ){
							$payment_info['pay_paypal_service_fee'] = $pricing_info['pay-amount-paypal-service-fee'];
						}
		        	}else if( $payment_info['amount'] > $pricing_info['total-price'] ){
		        		$order_status = 'online-paid';
		        	}else{
		        		$order_status = 'deposit-paid';
		        		$mail_type = 'deposit-payment-made-mail';
		        		$admin_mail_type = 'admin-deposit-payment-made-mail';
		        	}

		        	// get old payment info
					$payment_infos = json_decode($result->payment_info, true);
					$payment_infos = tourmaster_payment_info_format($payment_infos, $result->order_status);
					$payment_infos[] = $payment_info;

					tourmaster_update_booking_data( 
						array(
							'payment_info' => json_encode($payment_infos),
							'payment_date' => current_time('mysql'),
							'order_status' => $order_status,
						),
						array('id' => $tid),
						array('%s', '%s', '%s', '%s', '%s'),
						array('%d')
					);

					tourmaster_mail_notification($mail_type, $tid, '', array(
						'custom' => array(
							'payment-method' => $payment_info['payment_method'],
							'payment-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
							'submission-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
							'submission-amount' => tourmaster_money_format($payment_info['amount']),
							'transaction-id' => $payment_info['transaction_id']
						)
					));
					tourmaster_mail_notification($admin_mail_type, $tid, '', array(
						'custom' => array(
							'payment-method' => $payment_info['payment_method'],
							'payment-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
							'submission-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
							'submission-amount' => tourmaster_money_format($payment_info['amount']),
							'transaction-id' => $payment_info['transaction_id']
						)
					));
					tourmaster_send_email_invoice($tid);
				}
				curl_close($ch);

		        exit;
			}

		} // tourmaster_paypal_process_ipn
	}