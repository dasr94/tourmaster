<?php
	/* function necessary for deposit payment */

	// update payment info format ( for version 5.0 )
	if( !function_exists('tourmaster_payment_info_format') ){
		function tourmaster_payment_info_format( $payment_info, $order_status ){

			// fix inconsistence data
			if( !empty($payment_info['deposit-rate']) ){
				$payment_info['deposit_rate'] = $payment_info['deposit-rate'];
				unset($payment_info['deposit-rate']);
			}
			if( !empty($payment_info['deposit-price']) ){
				$payment_info['deposit_price'] = $payment_info['deposit-price'];
				unset($payment_info['deposit-price']);
			}

			// add to array if not in right format
			if( !empty($payment_info) ){
				if( empty($payment_info[0]) ){
					if( in_array($order_status, array('approved', 'online-paid', 'deposit-paid')) ){
						$payment_info['payment_status'] = 'paid';
					}else{
						$payment_info['payment_status'] = 'pending';
					}

					$payment_info = array($payment_info);
				}
			}

			return $payment_info;
		}
	}


	// get price settings
	if( !function_exists('tourmaster_get_price_settings') ){
		function tourmaster_get_price_settings( $tour_id, $payment_infos, $total_price, $travel_date ){
			
			$ret = array();
			$tour_option = tourmaster_get_post_meta($tour_id, 'tourmaster-tour-option');
			$enable_deposit = tourmaster_get_option('payment', 'enable-deposit-payment', 'enable');

			$paid_amount = 0;
			if( !empty($payment_infos) ){
				foreach( $payment_infos as $payment_info ){
					if( !empty($payment_info['deposit_amount']) ){
						$paid_amount += floatval($payment_info['deposit_amount']);
					}else if( !empty($payment_info['pay_amount']) ){
						$paid_amount += floatval($payment_info['pay_amount']);
					}else if( !empty($payment_info['amount']) ){
						$paid_amount += floatval($payment_info['amount']);

					// receipt
					}else if( !empty($payment_info['deposit_price']) ){
						$paid_amount += $payment_info['deposit_price'];
					}
				}
			}
			$ret['paid-amount'] = $paid_amount;
			
			// deposit percent
			$nth_deposit = empty($payment_infos)? 0: sizeof($payment_infos);
			$deposit_percent = array();
			$next_deposit_percent = 0;
			$total_deposit_percent = 0;
			for( $i=1; $i<=5; $i++ ){
				if( empty($tour_option['deposit-booking']) || $tour_option['deposit-booking'] == 'default' ){
					if( $enable_deposit == 'enable' ){
						if( $i == 1 ){
							$percent = tourmaster_get_option('payment', 'deposit-payment-amount', 0);
						}else{
							$percent = tourmaster_get_option('payment', 'deposit' . $i . '-payment-amount', 0);
						}
					}
				}else if( $tour_option['deposit-booking'] == 'enable' ){
					if( $i == 1 ){
						$percent = empty($tour_option['deposit-amount'])? 0: $tour_option['deposit-amount'];
					}else{
						$percent = empty($tour_option['deposit' . $i . '-amount'])? 0: $tour_option['deposit' . $i . '-amount'];
					}
				}

				if( !empty($percent) ){
					$deposit_percent[] = $percent;
					$total_deposit_percent += floatval($percent);

					if( $i <= $nth_deposit + 1 ){
						$next_deposit_percent += floatval($percent);
					}
				}
				
			}
			$ret['deposit-percent'] = $deposit_percent;
			$ret['total-deposit-percent'] = $total_deposit_percent;

			// check if there're more payment
			$ret['more-payment'] = false;
			$ret['full-payment'] = false;
			$ret['deposit-payment'] = false;
			if( !tourmaster_compare_price($paid_amount, $total_price) && $paid_amount < $total_price ){
				$allow_full_payment = tourmaster_get_option('payment', 'enable-full-payment', 'enable');

				// full payment check
				if( $allow_full_payment == 'enable' ){
					if( !tourmaster_compare_price($total_price, $paid_amount) && $paid_amount < $total_price ){
						$ret['more-payment'] = true;
						$ret['full-payment'] = true;
						$ret['full-payment-amount'] = $total_price - $paid_amount;
					}
				}

				// deposit payment check
				$total_deposit_amount = $total_price * ($total_deposit_percent / 100);
				if( !tourmaster_compare_price($total_deposit_amount, $paid_amount) && $paid_amount < $total_deposit_amount ){
					$current_date = current_time('Y-m-d');
					$deposit_before_days = intval(tourmaster_get_option('payment', 'display-deposit-payment-day', '0'));
					if( strtotime($current_date) + ($deposit_before_days * 86400) <= strtotime($travel_date) ){
						$ret['more-payment'] = true;
						$ret['deposit-payment'] = true;
						$ret['next-deposit-amount'] = ($total_price * ($next_deposit_percent / 100)) - $paid_amount;
						$ret['next-deposit-percent'] = round(($ret['next-deposit-amount'] / $total_price) * 100);
					}
				}
			}else{
				$ret['more-payment'] = false;
			}

			return $ret;
		}
	}

	// display deposit content
	if( !function_exists('tourmaster_deposit_item_content') ){
		function tourmaster_deposit_item_content( $result, $payment_info ){

			// file
			if( !empty($payment_info['file_url']) ){
				echo '<div class="tourmaster-my-booking-single-payment-receipt" >';
				if( strpos($payment_info['file_url'], '.pdf') ){
					echo '<a href="' . esc_url($payment_info['file_url']) . '" target="_blank" >';
					echo '<i class="fa fa-file" style="margin-right: 10px;" ></i>' . esc_html__('Download', 'tourmaster');
					echo '</a>';
				}else{
					echo '<a href="' . esc_url($payment_info['file_url']) . '" >';
					echo '<img src="' . esc_url($payment_info['file_url']) . '" alt="receipt" />';
					echo '</a>';
				}
				echo '</div>';			
			}

			// date
			if( !empty($payment_info['submission_date']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Submission Date', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_date_format($payment_info['submission_date']) . ' ' . tourmaster_time_format($payment_info['submission_date']) . '</span>';
				echo '</div>';			
			}else if( !empty($result->payment_date) && $result->payment_date != '0000-00-00 00:00:00' ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Payment Date', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_date_format($result->payment_date) . ' ' . tourmaster_time_format($result->payment_date) . '</span>';
				echo '</div>';				
			} 
			
			// payment method
			if( !empty($payment_info['payment_method']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Payment Method', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">';
				if( $payment_info['payment_method'] == 'receipt' ){
					echo esc_html__('Receipt Submission', 'tourmaster');
				}else{
					echo $payment_info['payment_method'];
				}
				echo '</span>';
				echo '</div>';			
			}
			
			// deposit price
			if( !empty($payment_info['deposit_rate']) && !empty($payment_info['deposit_price']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Deposit Rate', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . $payment_info['deposit_rate'] . '%</span>';
				echo '</div>';			

				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Deposit Price', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_money_format($payment_info['deposit_price']) . '</span>';
				echo '</div>';			
			}
			
			// transaction id
			if( !empty($payment_info['transaction_id']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Transaction ID', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . $payment_info['transaction_id'] . '</span>';
				echo '</div>';			
			}

			// status			
			if( $result->order_status == 'deposit-paid' ){
				$pricing_info = json_decode($result->pricing_info, true);
				
				if( !empty($pricing_info['deposit-price']) && !empty($pricing_info['deposit-paypal-amount']) && 
					tourmaster_compare_price($pricing_info['deposit-paypal-amount'], $payment_info['amount']) ){
					
					echo '<div class="tourmaster-my-booking-single-field clearfix" >';
					echo '<span class="tourmaster-head">' . sprintf(esc_html__('Deposit Amount', 'tourmaster'), $pricing_info['deposit-paypal-service-rate']) . ' :</span> ';
					echo '<span class="tourmaster-tail">' . tourmaster_money_format($pricing_info['deposit-price']) . '</span>';
					echo '</div>';

					echo '<div class="tourmaster-my-booking-single-field clearfix" >';
					echo '<span class="tourmaster-head">' . sprintf(esc_html__('Paypal Fee (%d%%)', 'tourmaster'), $pricing_info['deposit-paypal-service-rate']) . ' :</span> ';
					echo '<span class="tourmaster-tail">' . tourmaster_money_format($pricing_info['deposit-paypal-service-fee']) . '</span>';
					echo '</div>';
				}		
			}

			// amount
			if( !empty($payment_info['deposit_amount']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Deposit Amount', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_money_format($payment_info['deposit_amount']) . '</span>';
				echo '</div>';
			}else if( !empty($payment_info['pay_amount']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Total Price', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_money_format($payment_info['pay_amount']) . '</span>';
				echo '</div>';
			}
			if( !empty($payment_info['deposit_paypal_service_rate']) && !empty($payment_info['deposit_paypal_service_fee']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<div class="tourmaster-head" >' . sprintf(esc_html__('Paypal Fee (%s%%)', 'tourmaster'), $payment_info['deposit_paypal_service_rate']) . '</div>';
				echo '<div class="tourmaster-tail" >' . tourmaster_money_format($payment_info['deposit_paypal_service_fee']) . '</div>';	
				echo '</div>'; // tourmaster-invoice-payment-info-item
			}else if( !empty($payment_info['pay_paypal_service_rate']) && !empty($payment_info['pay_paypal_service_fee']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<div class="tourmaster-head" >' . sprintf(esc_html__('Paypal Fee (%s%%)', 'tourmaster'), $payment_info['pay_paypal_service_rate']) . '</div>';
				echo '<div class="tourmaster-tail" >' . tourmaster_money_format($payment_info['pay_paypal_service_fee']) . '</div>';	
				echo '</div>'; // tourmaster-invoice-payment-info-item
			}else if( !empty($payment_info['deposit_credit_card_service_rate']) && !empty($payment_info['deposit_credit_card_service_fee']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<div class="tourmaster-head" >' . sprintf(esc_html__('Credit Card Fee (%s%%)', 'tourmaster'), $payment_info['deposit_credit_card_service_rate']) . '</div>';
				echo '<div class="tourmaster-tail" >' . tourmaster_money_format($payment_info['deposit_credit_card_service_fee']) . '</div>';	
				echo '</div>'; // tourmaster-invoice-payment-info-item
			}else if( !empty($payment_info['pay_credit_card_service_rate']) && !empty($payment_info['pay_credit_card_service_fee']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<div class="tourmaster-head" >' . sprintf(esc_html__('Credit Card Fee (%s%%)', 'tourmaster'), $payment_info['pay_credit_card_service_rate']) . '</div>';
				echo '<div class="tourmaster-tail" >' . tourmaster_money_format($payment_info['pay_credit_card_service_fee']) . '</div>';	
				echo '</div>'; // tourmaster-invoice-payment-info-item
			}


			if( !empty($payment_info['amount']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Paid Amount', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_money_format($payment_info['amount']) . '</span>';
				echo '</div>';
			}

			// payment status
			if( !empty($payment_info['payment_status']) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Payment Status', 'tourmaster') . ' :</span> ';
				echo '<span class="tourmaster-tail">';
				if( $payment_info['payment_status'] == 'paid' ){
					esc_html_e('Paid', 'tourmaster');
				}else if( $payment_info['payment_status'] == 'pending' ){
					esc_html_e('Pending', 'tourmaster');
				}else{
					echo $payment_info['payment_status'];
				}
				echo '</span>';
				echo '</div>';
			}

		}
	}