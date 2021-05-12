<?php
	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-my-booking-single" >';
	tourmaster_get_user_breadcrumb();

	if( !empty($_GET['error_code']) && $_GET['error_code'] == 'cannot_upload_file' ){ 
		echo '<div class="tourmaster-notification-box tourmaster-failure" >';
		echo esc_html__('Cannot upload a media file, please try uploading it again.', 'tourmaster');
		echo '</div>';
	}

	// booking table block
	tourmaster_user_content_block_start();

	global $current_user;
	$result = tourmaster_get_booking_data(array(
		'id' => $_GET['id'],
		'order_status' => array(
			'condition' => '!=',
			'value' => 'cancel'
		)
	), array('single' => true));

	$contact_fields = tourmaster_get_payment_contact_form_fields($result->tour_id);
	$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
	$billing_detail = empty($result->billing_info)? array(): json_decode($result->billing_info, true);
	$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);

	// sidebar
	echo '<div class="tourmaster-my-booking-single-content-wrap" >';
	echo '<div class="tourmaster-my-booking-single-sidebar" >';
	$statuses = array(
		'all' => esc_html__('All', 'tourmaster'),
		'pending' => esc_html__('Pending', 'tourmaster'),
		'approved' => esc_html__('Approved', 'tourmaster'),
		'receipt-submitted' => esc_html__('Receipt Submitted', 'tourmaster'),
		'online-paid' => esc_html__('Online Paid', 'tourmaster'),
		'deposit-paid' => esc_html__('Deposit Paid', 'tourmaster'),
		'departed' => esc_html__('Departed', 'tourmaster'),
		'rejected' => esc_html__('Rejected', 'tourmaster'),
		'wait-for-approval' => esc_html__('Wait For Approval', 'tourmaster'),
	);
	echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Order Status', 'tourmaster') . '</h3>';
	echo '<div class="tourmaster-booking-status tourmaster-status-' . esc_attr($result->order_status) . '" >' . $statuses[$result->order_status] . '</div>';
	
	$tour_option = tourmaster_get_post_meta($booking_detail['tour-id'], 'tourmaster-tour-option');
	if( empty($tour_option['enable-payment']) ){
		$enable_payment = tourmaster_get_option('payment', 'enable-payment', 'enable');
	}else{
		$enable_payment = $tour_option['enable-payment'];
	}
	if( $enable_payment == 'enable' && $result->order_status != 'wait-for-approval' ){

		echo '<h3 class="tourmaster-my-booking-single-sub-title">' . esc_html__('Bank Payment Receipt', 'tourmaster') . '</h3>';
		
		$payment_infos = array();
		if( !empty($result->payment_info) ){

			// print payment info
			$payment_infos = json_decode($result->payment_info, true);
			$payment_infos = tourmaster_payment_info_format($payment_infos, $result->order_status);

			$count = 0;
			$total_paid_amount = 0;
			foreach( $payment_infos as $payment_info ){ $count++;

				$paid_amount = 0;
				if( !empty($payment_info['deposit_amount']) ){
					$paid_amount = floatval($payment_info['deposit_amount']);
				}else if( !empty($payment_info['pay_amount']) ){
					$paid_amount = floatval($payment_info['pay_amount']);
				}else if( !empty($payment_info['amount']) ){
					$paid_amount = floatval($payment_info['amount']);
				}else if( !empty($payment_info['deposit_price']) ){
					$paid_amount = $payment_info['deposit_price'];
				}

				$total_paid_amount += $paid_amount;

				echo '<div class="tourmaster-deposit-item ' . ($count == sizeof($payment_infos)? 'tourmaster-active': '') . '" >';
				echo '<div class="tourmaster-deposit-item-head" ><i class="icon_plus" ></i>';
				if( tourmaster_compare_price($total_paid_amount, $result->total_price) || $total_paid_amount > $result->total_price ){
					echo sprintf(esc_html__('Final Payment : %s', 'tourmaster'), tourmaster_money_format($paid_amount));
				}else{
					echo sprintf(esc_html__('Deposit %d : %s', 'tourmaster'), $count, tourmaster_money_format($paid_amount));
				}
				echo '</div>';

				echo '<div class="tourmaster-deposit-item-content" >';
				tourmaster_deposit_item_content($result, $payment_info);
				echo '</div>';
				echo '</div>';
			}
		}

		// check if allow second deposit
		$more_payment = false;
		$price_settings = tourmaster_get_price_settings($result->tour_id, $payment_infos, $result->total_price, $result->travel_date);

		if( in_array($result->order_status, array('pending', 'rejected', 'deposit-paid')) || 
			($result->order_status == 'deposit-paid' && $price_settings['more-payment'] == true) ){
	
			echo '<a data-tmlb="payment-receipt" class="tourmaster-my-booking-single-receipt-button tourmaster-button" >' . esc_html__('Submit Payment Receipt', 'tourmaster') . '</a>';
			echo tourmaster_lightbox_content(array(
				'id' => 'payment-receipt',
				'title' => esc_html__('Submit Bank Payment Receipt', 'tourmaster'),
				'content' => tourmaster_lb_payment_receipt($result->id, $price_settings)
			));

			$payment_method = tourmaster_get_option('payment', 'payment-method', array('booking', 'paypal', 'credit-card'));
			$paypal_enable = in_array('paypal', $payment_method);
			$credit_card_enable = in_array('credit-card', $payment_method);
			$hipayprofessional_enable = in_array('hipayprofessional', $payment_method);

			if( $paypal_enable || $credit_card_enable || $hipayprofessional_enable ){
				echo '<a href="';
				echo esc_url(add_query_arg(array('tid'=>$result->id, 'step'=>3), tourmaster_get_template_url('payment')));
				echo '" class="tourmaster-my-booking-single-payment-button tourmaster-button" >' . esc_html__('Make an Online Payment', 'tourmaster') . '</a>';
			}
		}
	} // enable payment
	echo '</div>'; // tourmaster-my-booking-single-sidebar

	// content
	echo '<div class="tourmaster-my-booking-single-content" >';
	echo '<div class="tourmaster-item-rvpdlr clearfix" >';
	echo '<div class="tourmaster-my-booking-single-order-summary-column tourmaster-column-20 tourmaster-item-pdlr" >';
	echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Order Summary', 'tourmaster') . '</h3>';

	echo '<div class="tourmaster-my-booking-single-field clearfix" >';
	echo '<span class="tourmaster-head">' . esc_html__('Order Number', 'tourmaster') . ' :</span> ';
	echo '<span class="tourmaster-tail">#' . $result->id . '</span>';
	echo '</div>';

	echo '<div class="tourmaster-my-booking-single-field clearfix" >';
	echo '<span class="tourmaster-head">' . esc_html__('Booking Date', 'tourmaster') . ' :</span> ';
	echo '<span class="tourmaster-tail">' . tourmaster_date_format($result->booking_date) . '</span>';
	echo '</div>';

	echo '<div class="tourmaster-my-booking-single-field clearfix" >';
	echo '<span class="tourmaster-head">' . esc_html__('Tour', 'tourmaster') . ' :</span> ';
	echo '<span class="tourmaster-tail"><a href="' . get_permalink($result->tour_id) . '" target="_blank" >' . get_the_title($result->tour_id) . '</a></span>';
	echo '</div>';

	echo '<div class="tourmaster-my-booking-single-field clearfix" >';
	echo '<span class="tourmaster-head">' . esc_html__('Travel Date', 'tourmaster') . ' :</span> ';
	echo '<span class="tourmaster-tail">' . tourmaster_date_format($result->travel_date) . '</span>';
	echo '</div>';

	if( !empty($booking_detail['package']) ){
		$tour_option = tourmaster_get_post_meta($result->tour_id, 'tourmaster-tour-option');
		$date_price = tourmaster_get_tour_date_price($tour_option, $result->tour_id, $result->travel_date);

		echo '<div class="tourmaster-my-booking-single-field clearfix" >';
		echo '<span class="tourmaster-head">' . esc_html__('Package', 'tourmaster') . ' :</span> ';
		echo '<span class="tourmaster-tail">' . $booking_detail['package'];
		if( !empty($date_price['package']) ){
			foreach($date_price['package'] as $package){
				if( $package['title'] == $booking_detail['package'] ){
					echo '<span class="tourmaster-my-booking-package-detail" >';
					echo '<span>' . $package['caption'] . '</span>';
					if( !empty($package['start-time']) ){
						echo '<span>' . esc_html__('Start Time: ', 'tourmaster') . $package['start-time'] . '</span>';
					}
					echo '</span>';
				}
			}
		}
		echo '</span>';
		echo '</div>';
	}

	$extra_booking_info = get_post_meta($booking_detail['tour-id'], 'tourmaster-extra-booking-info', true);
	if( empty($extra_booking_info) ){
		$extra_booking_info = tourmaster_get_option('general', 'single-tour-extra-booking-info', '');
	}
	if( !empty($extra_booking_info) ){
		$extra_booking_info = tourmaster_read_custom_fields($extra_booking_info);

		foreach( $extra_booking_info as $slug => $extra_field ){

			if( !empty($booking_detail[$slug]) ){
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . $extra_field['title'] . ' :</span> ';
				echo '<span class="tourmaster-tail">' . $booking_detail[$slug] . '</span>';
				echo '</div>';
			}
			
		}
	}

	if( !empty($contact_detail['additional_notes']) ){
		echo '<div class="tourmaster-my-booking-single-field tourmaster-additional-note clearfix" >';
		echo '<span class="tourmaster-head">' . esc_html__('Customer\'s Note', 'tourmaster') . ' :</span> ';
		echo '<span class="tourmaster-tail">' . $contact_detail['additional_notes'] . '</span>';
		echo '</div>';
	}
	echo '</div>'; // tourmaster-my-booking-single-order-summary-column

	echo '<div class="tourmaster-my-booking-single-contact-detail-column tourmaster-column-20 tourmaster-item-pdlr" >';
	echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Contact Detail', 'tourmaster') . '</h3>';
	foreach( $contact_fields as $field_slug => $contact_field ){
		if( !empty($contact_detail[$field_slug]) ){
			echo '<div class="tourmaster-my-booking-single-field clearfix" >';
			echo '<span class="tourmaster-head">' . $contact_field['title'] . ' :</span> ';
			if( $field_slug == 'country' ){
				echo '<span class="tourmaster-tail">' . tourmaster_get_country_list('', $contact_detail[$field_slug]) . '</span>';
			}else{
				echo '<span class="tourmaster-tail">' . $contact_detail[$field_slug] . '</span>';
			}
			echo '</div>';
		}
	}
	echo '</div>'; // tourmaster-my-booking-single-contact-detail-column

	echo '<div class="tourmaster-my-booking-single-billing-detail-column tourmaster-column-20 tourmaster-item-pdlr" >';
	echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Billing Detail', 'tourmaster') . '</h3>';
	foreach( $contact_fields as $field_slug => $contact_field ){
		if( !empty($billing_detail[$field_slug]) ){
			echo '<div class="tourmaster-my-booking-single-field clearfix" >';
			echo '<span class="tourmaster-head">' . $contact_field['title'] . ' :</span> ';
			if( $field_slug == 'country' ){
				echo '<span class="tourmaster-tail">' . tourmaster_get_country_list('', $billing_detail[$field_slug]) . '</span>';
			}else{
				echo '<span class="tourmaster-tail">' . $billing_detail[$field_slug] . '</span>';
			}
			echo '</div>';
		}
	}
	echo '</div>'; // tourmaster-my-booking-single-billing-detail-column
	echo '</div>'; // tourmaster-item-rvpdl

	// traveller info
	if( !empty($result->traveller_info) ){
		$title_types = tourmaster_payment_traveller_title();
		$traveller_info = json_decode($result->traveller_info, true);
		if( !empty($tour_option['additional-traveller-fields']) ){
			$additional_traveller_fields = $tour_option['additional-traveller-fields'];
		}else{
			$additional_traveller_fields = tourmaster_get_option('general', 'additional-traveller-fields', '');
		}
		if( !empty($additional_traveller_fields) ){
			$additional_traveller_fields = tourmaster_read_custom_fields($additional_traveller_fields);
		}

		if( !empty($traveller_info) ){
			echo '<div class="tourmaster-my-booking-single-traveller-info" >';
			echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Traveller Info', 'tourmaster') . '</h3>';
			for( $i=0; $i<sizeof($traveller_info['first_name']); $i++ ){
				if( !empty($traveller_info['first_name'][$i]) || !empty($traveller_info['last_name'][$i]) ){
					echo '<div class="tourmaster-my-booking-single-field clearfix" >';
					echo '<span class="tourmaster-head">' . esc_html__('Traveller', 'tourmaster') . ' ' . ($i+1) . ' :</span> ';
					echo '<span class="tourmaster-tail">';
					if( !empty($traveller_info['title'][$i]) ){
						if( !empty($title_types[$traveller_info['title'][$i]]) ){
							echo $title_types[$traveller_info['title'][$i]] . ' ';
						}
					}
					echo $traveller_info['first_name'][$i] . ' ' . $traveller_info['last_name'][$i];
					if( !empty($traveller_info['passport'][$i]) ){
						echo '<br>' . esc_html__('Passport ID :', 'tourmaster') . ' ' . $traveller_info['passport'][$i];
					}
					if( !empty($additional_traveller_fields) ){
						foreach( $additional_traveller_fields as $field ){
							if( !empty($booking_detail['traveller_' . $field['slug']][$i]) ){
								echo '<br>' . $field['title'] . ' ' . $booking_detail['traveller_' . $field['slug']][$i];
							}
						}
					}
					echo '</span>';
					echo '</div>';				
				}
			}
			echo '</div>'; // tourmaster-my-booking-single-traveller-info
		}
	}

	// price breakdown
	if( !empty($result->pricing_info) ){
		$pricing_info = json_decode($result->pricing_info, true);
		echo '<div class="tourmaster-my-booking-single-price-breakdown" >';
		echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Price Breakdown', 'tourmaster') . '</h3>';
		echo tourmaster_get_tour_price_breakdown($pricing_info['price-breakdown']);

		echo '<div class="tourmaster-my-booking-single-total-price clearfix" >';
		echo '<div class="tourmaster-my-booking-single-field clearfix" >';
		echo '<span class="tourmaster-head">' . esc_html__('Total', 'tourmaster') . '</span> ';
		echo '<span class="tourmaster-tail">' . tourmaster_money_format($result->total_price) . '</span>';
		echo '</div>';
		echo '</div>';
		echo '</div>'; // tourmaster-my-booking-single-traveller-info
	}

	echo '</div>'; // tourmaster-my-booking-single-content
	echo '</div>'; // tourmaster-my-booking-single-content-wrap

	tourmaster_user_content_block_end();

	echo '</div>'; // tourmaster-user-content-inner