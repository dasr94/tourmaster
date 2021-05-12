<?php
	/*	
	*	Utility function for uses
	*/

	// array('title', 'sender', 'recipient', 'message')
	if( !function_exists('tourmaster_mail') ){
		function tourmaster_mail( $settings = array() ){
			$sender_name = tourmaster_get_option('general', 'system-email-name', 'WORDPRESS');
			$sender = tourmaster_get_option('general', 'system-email-address');

			if( !empty($sender) ){ 
				$headers  = "From: {$sender_name} <{$sender}>\r\n";
				if( !empty($settings['reply-to']) ){
					$headers .= "Reply-To: {$settings['reply-to']}\r\n";
				}
				if( !empty($settings['cc']) ){
					$headers .= "CC: {$settings['cc']}\r\n"; 
				}
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

				wp_mail($settings['recipient'], $settings['title'], $settings['message'], $headers);
			}

		} // tourmaster_mail
	}

	if( !function_exists('tourmaster_mail_content') ){
		function tourmaster_mail_content( $content = '', $header = true, $footer = true, $settings = array() ){

			$settings['width'] = empty($settings['width'])? '600': $settings['width'];
			$settings['padding'] = empty($settings['padding'])? '60px 60px 40px': $settings['padding'];

			ob_start();

			echo '<html><body>';
			echo '<div class="tourmaster-mail-template" style="line-height: 1.7; background: #f5f5f5; margin: 40px auto 40px; width: ' . $settings['width'] . 'px; font-size: 14px; font-family: Arial, Helvetica, sans-serif; color: #838383;" >';
			if( !empty($header) ){
				$header_logo = tourmaster_get_option('general', 'mail-header-logo', TOURMASTER_URL . '/images/logo.png');

				echo '<div class="tourmaster-mail-header" style="background: #353d46; padding: 25px 35px;" >';
				echo tourmaster_get_image($header_logo);
				echo '<div style="display: block; clear: both; visibility: hidden; line-height: 0; height: 0; zoom: 1;" ></div>'; // clear
				echo '</div>';
			}

			if( empty($settings['no-filter']) ){
				$content = tourmaster_content_filter($content);
			}

			//apply css to link and p tag
			$pointer = 0;
			while( ($new_pointer = strpos($content, '<a', $pointer)) !== false ){
				$pointer = $new_pointer + 2;

				$style_tag = strpos($content, 'style=', $pointer);
				$close_tag = strpos($content, '>', $pointer);

				if( $style_tag === false || $close_tag < $style_tag ){
					$first_section = substr($content, 0, $pointer);
					$last_section = substr($content, $pointer);
					$content  = $first_section . ' style="color: #4290de; text-decoration: none;" ' . $last_section;
				}
			}
			echo '<div class="tourmaster-mail-content" style="padding: ' . $settings['padding'] . ';" >' . $content . '</div>';

			if( !empty($footer) ){
				$footer_left = tourmaster_get_option('general', 'mail-footer-left', '');
				$footer_right = tourmaster_get_option('general', 'mail-footer-right', '');

				echo '<div class="tourmaster-mail-footer" style="background: #ebedef; font-size: 13px; padding: 25px 30px 5px;" >';
				if( !empty($footer_left) ){
					echo '<div class="tourmaster-mail-footer-left" style="float: left; text-align: left;" >' . tourmaster_content_filter($footer_left) . '</div>';
				}
				if( !empty($footer_right) ){
					echo '<div class="tourmaster-mail-footer-right" style="float: right; text-align: right;" >' . tourmaster_content_filter($footer_right) . '</div>';
				}
				echo '<div style="display: block; clear: both; visibility: hidden; line-height: 0; height: 0; zoom: 1;" ></div>'; // clear
				echo '</div>';
			}
			echo '</div>';
			echo '</body></html>';

			$message = ob_get_contents();
			ob_end_clean();

			return $message;

		} // tourmaster_mail_content
	}

	if( !function_exists('tourmaster_mail_notification') ){
		function tourmaster_mail_notification( $type, $tid = '', $user_id = '', $settings = array() ){

			if( $type == 'custom' || $type == 'admin-custom' ){
				$option_enable = 'enable';
				$mail_title = empty($settings['title'])? '': $settings['title'];
				$raw_message = empty($settings['message'])? '': $settings['message'];
			}else{
				$option_enable = tourmaster_get_option('general', 'enable-' . $type, 'enable');
				$mail_title = tourmaster_get_option('general', $type . '-title');
				$raw_message = tourmaster_get_option('general', $type);
			}

			if( $option_enable == 'enable' ){

				if( !empty($tid) ){
					$result = tourmaster_get_booking_data(array('id' => $tid), array('single' => true));
					$contact_info = json_decode($result->contact_info, true);
				}else if( !empty($settings['result']) ){
					$result = $settings['result'];
					$contact_info = json_decode($result->contact_info, true);
				}

				if( !empty($result) ){

					$mail_title = str_replace('{id}', $result->id, $mail_title);
					$raw_message = str_replace('{id}', $result->id, $raw_message);

					// customer mail
					$user_email = $contact_info['email'];
					$raw_message = str_replace('{customer-email}', $user_email, $raw_message);

					// tour-name
					$tour_name  = '<h4 class="tourmaster-mail-tour-title" style="font-size: 16px; margin-bottom: 25px; font-weight: 600;" >';
					$tour_name .= '<a href="' . get_permalink($result->tour_id) . '" >';
					$tour_name .= get_the_title($result->tour_id);
					$tour_name .= '</a>';
					$tour_name .= '</h4>';

					$booking_detail = json_decode($result->booking_detail, true);
					$tour_option = tourmaster_get_post_meta($result->tour_id, 'tourmaster-tour-option');
					$date_price = tourmaster_get_tour_date_price($tour_option, $result->tour_id, $result->travel_date);
					if( !empty($booking_detail['package']) ){
						$tour_name .= '<div class="tourmaster-mail-tour-package" style="margin-bottom: 8px;" >';
						$tour_name .= '<span>' . esc_html__('Package :', 'tourmaster') . ' </span>';
						foreach($date_price['package'] as $package){
							if( $package['title'] == $booking_detail['package'] ){
								$tour_name .= '<span>' . $package['title'] . '</span>';
								if( !empty($package['start-time']) ){
									$tour_name .= ' <span>' . sprintf(esc_html__('(Start Time: %s)', 'tourmaster'), $package['start-time']) . '</span>';
								}
							}
						}
						$tour_name .= '</div>';
					}
					
					$mail_title = str_replace('{tour-name}', $tour_name, $mail_title);
					$raw_message = str_replace('{tour-name}', $tour_name, $raw_message);

					// cc mail
					if( strpos($type, 'admin') === 0 ){
						$cc_mail = get_post_meta($result->tour_id, 'tourmaster-tour-cc-mail', true);
					}

					// customer name
					$customer_name  = '<strong>' . $contact_info['first_name'] . ' ' . $contact_info['last_name'] . '</strong>';
					$raw_message = str_replace('{customer-name}', $customer_name, $raw_message);

					// additional notes
					if( !empty($contact_info['additional_notes']) ){
						$raw_message = str_replace('{customer-note}', $contact_info['additional_notes'], $raw_message);
					}else{
						$raw_message = str_replace('{customer-note}', '', $raw_message);
					}

					// custom contact info
					$contact_fields = tourmaster_get_payment_contact_form_fields($result->tour_id);
					foreach( $contact_fields as $cfield_slug => $cfield_settigns ){
						if( !empty($contact_info[$cfield_slug]) ){
							$raw_message = str_replace('{' . $cfield_slug . '}', $contact_info[$cfield_slug], $raw_message);
						}else{
							$raw_message = str_replace('{' . $cfield_slug . '}', '', $raw_message);
						}
					}

					// extra booking info
					$extra_booking_info = get_post_meta($result->tour_id, 'tourmaster-extra-booking-info', true);
					if( empty($extra_booking_info) ){
						$extra_booking_info = tourmaster_get_option('general', 'single-tour-extra-booking-info', '');
					}
					if( !empty($extra_booking_info) ){
						$extra_booking_info = tourmaster_read_custom_fields($extra_booking_info);
						foreach( $extra_booking_info as $ebi_slug => $ebi_field ){
							if( !empty($booking_detail[$ebi_slug]) ){
								$raw_message = str_replace('{' . $ebi_slug . '}', $booking_detail[$ebi_slug], $raw_message);
							}else{
								$raw_message = str_replace('{' . $ebi_slug . '}', '', $raw_message);
							}
						}
					}

					if( !empty($result->total_price) ){
						$total_price  = '<div class="tourmaster-mail-payment-price" style="font-size: 16px; font-weight: 600; margin: 20px 0px 25px;" >';
						$total_price .= '<span class="tourmaster-head" >' . esc_html__('Total Price :', 'tourmaster') . '</span> ';
						$total_price .= '<span class="payment-method" >' . tourmaster_money_format($result->total_price) . '</span>';
						$total_price .= '</div>';
						$raw_message = str_replace('{total-price}', $total_price, $raw_message);
					}else{
						$raw_message = str_replace('{total-price}', '', $raw_message);
					}

					// order number
					$order_number  = '<div class="tourmaster-mail-order-info" style="font-style: italic; margin-bottom: 5px;" >';
					$order_number .= '<span class="tourmaster-head" >' . esc_html__('Order Number :', 'tourmaster') . '</span> ';
					$order_number .= '<span class="tourmaster-tail" >#' . $result->id . '</span>';
					$order_number .= '</div>';
					$raw_message = str_replace('{order-number}', $order_number, $raw_message);

					// travel date
					$travel_date  = '<div class="tourmaster-mail-order-info" style="font-style: italic; margin-bottom: 5px;" >';
					$travel_date .= '<span class="tourmaster-head" >' . esc_html__('Travel Date :', 'tourmaster') . '</span> ';
					$travel_date .= '<span class="tourmaster-tail" >' . tourmaster_date_format($result->travel_date) . '</span>';
					$travel_date .= '</div>';
					$raw_message = str_replace('{travel-date}', $travel_date, $raw_message);

					// traveller amount
					if( empty($booking_detail['group']) && !empty($result->traveller_amount) ){
						$raw_message = str_replace('{traveller-amount}', esc_html__('Traveller Amount :', 'tourmaster') . ' ' . $result->traveller_amount, $raw_message);
					}else{
						$raw_message = str_replace('{traveller-amount}', '', $raw_message);
					}

					// admin transaction url
					$raw_message = str_replace('{admin-transaction-link}', admin_url('admin.php?page=tourmaster_order&single=' . $result->id), $raw_message);
					
					// invoice url
					$user_url = tourmaster_get_template_url('user');
					$invoice_url = add_query_arg(array(
						'page_type' => 'invoices',
						'sub_page' => 'single',
						'id' => $result->id,
						'tour_id' => $result->tour_id
					), $user_url);
					$raw_message = str_replace('{invoice-link}', $invoice_url, $raw_message);				

					// payment url
					$user_url = tourmaster_get_template_url('user');
					$invoice_url = add_query_arg(array(
						'page_type' => 'my-booking',
						'sub_page' => 'single',
						'id' => $result->id,
						'tour_id' => $result->tour_id
					), $user_url);
					$raw_message = str_replace('{payment-link}', $invoice_url, $raw_message);

				}else if( !empty($user_id) ){

					$customer_name  = '<strong>' . tourmaster_get_user_meta($user_id) . '</strong>';
					$raw_message = str_replace('{customer-name}', $customer_name, $raw_message);

					$user_email = tourmaster_get_user_meta($user_id, 'email');
					$raw_message = str_replace('{customer-email}', $user_email, $raw_message);

					$user_phone = tourmaster_get_user_meta($user_id, 'phone');
					$user_phone = empty($user_phone)? ' -': $user_phone; 
					$raw_message = str_replace('{customer-phone}', $user_phone, $raw_message);
				}

				// for extra settings
				if( !empty($settings['custom']) ){
					foreach( $settings['custom'] as $field_key => $field_value ){
						$temp_title = '';
						if( $field_key == 'payment-method' ){
							$temp_title = esc_html__('Payment Method :', 'tourmaster');

							if( $field_value == 'paypal' ){
								$field_value = esc_html__('Paypal', 'tourmaster');
							}else if( $field_value == 'hipayprofessional' ){
								$field_value = esc_html__('Hipay Professional', 'tourmaster');
							}else if( $field_value == 'receipt' ){
								$field_value = esc_html__('Receipt Submission', 'tourmaster');
							}else{
								$field_value = esc_html__('Credit Card', 'tourmaster');
							}
						}else if( $field_key == 'transaction-id' ){
							$temp_title = esc_html__('Transaction ID :', 'tourmaster');
						}else if( $field_key == 'payment-date' ){
							$temp_title = esc_html__('Payment Date :', 'tourmaster');
						}else if( $field_key == 'submission-date' ){
							$temp_title = esc_html__('Submission Date :', 'tourmaster');
						}else if( $field_key == 'submission-amount' ){
							$temp_title = esc_html__('Amount :', 'tourmaster');
						}

						if( !empty($field_value) ){
							if( $field_key == 'submission-amount' && is_array($field_value) ){
								$temp_content = '';

								// fee
								if( !empty($field_value['deposit_amount']) ){
									$temp_content .= '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
									$temp_content .= '<span class="tourmaster-head" >' . esc_html__('Deposit Amount :', 'tourmaster') . '</span> ';
									$temp_content .= '<span class="payment-method" >' . tourmaster_money_format($field_value['deposit_amount']) . '</span>';	
									$temp_content .= '</div>';
								}else if( !empty($field_value['pay_amount']) ){
									$temp_content .= '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
									$temp_content .= '<span class="tourmaster-head" >' . esc_html__('Total Price :', 'tourmaster') . '</span> ';
									$temp_content .= '<span class="payment-method" >' . tourmaster_money_format($field_value['pay_amount']) . '</span>';	
									$temp_content .= '</div>';
								}
								if( !empty($field_value['deposit_paypal_service_rate']) && !empty($field_value['deposit_paypal_service_fee']) ){
									$temp_content .= '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
									$temp_content .= '<span class="tourmaster-head" >' . sprintf(esc_html__('Paypal Fee (%s%%) :', 'tourmaster'), $field_value['deposit_paypal_service_rate']) . '</span> ';
									$temp_content .= '<span class="payment-method" >' . tourmaster_money_format($field_value['deposit_paypal_service_fee']) . '</span>';	
									$temp_content .= '</div>';
								}else if( !empty($field_value['pay_paypal_service_rate']) && !empty($field_value['pay_paypal_service_fee']) ){
									$temp_content .= '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
									$temp_content .= '<span class="tourmaster-head" >' . sprintf(esc_html__('Paypal Fee (%s%%) :', 'tourmaster'), $field_value['pay_paypal_service_rate']) . '</span> ';
									$temp_content .= '<span class="payment-method" >' . tourmaster_money_format($field_value['pay_paypal_service_fee']) . '</span>';	
									$temp_content .= '</div>';
								}else if( !empty($field_value['deposit_credit_card_service_rate']) && !empty($field_value['deposit_credit_card_service_fee']) ){
									$temp_content .= '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
									$temp_content .= '<span class="tourmaster-head" >' . sprintf(esc_html__('Credit Card Fee (%s%%) :', 'tourmaster'), $field_value['deposit_credit_card_service_rate']) . '</span> ';
									$temp_content .= '<span class="payment-method" >' . tourmaster_money_format($field_value['deposit_credit_card_service_fee']) . '</span>';	
									$temp_content .= '</div>';
								}else if( !empty($field_value['pay_credit_card_service_rate']) && !empty($field_value['pay_credit_card_service_fee']) ){
									$temp_content .= '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
									$temp_content .= '<span class="tourmaster-head" >' . sprintf(esc_html__('Credit Card Fee (%s%%) :', 'tourmaster'), $field_value['pay_credit_card_service_rate']) . '</span> ';
									$temp_content .= '<span class="payment-method" >' . tourmaster_money_format($field_value['pay_credit_card_service_fee']) . '</span>';	
									$temp_content .= '</div>';
								}

								// paid amount
								if( !empty($field_value['amount']) || !empty($field_value['deposit_price']) ){
									$temp_content .= '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
									$temp_content .= '<span class="tourmaster-head" >' . esc_html__('Paid Amount', 'tourmaster') . '</span>';
									$temp_content .= '<span class="payment-method" >' . tourmaster_money_format($field_value['amount']) . '</span>';	
									$temp_content .= '</div>';
								}
							}else{
								$temp_content  = '<div class="tourmaster-mail-payment-info" style="font-weight: 600; margin-bottom: 5px;" >';
								if( !empty($temp_title) ){
									$temp_content .= '<span class="tourmaster-head" >' . $temp_title . '</span> ';
								}
								$temp_content .= '<span class="payment-method" >' . $field_value . '</span>';
								$temp_content .= '</div>';
								$field_value = $temp_content;
							}
							
						}
						$raw_message = str_replace('{' . $field_key . '}', $temp_content, $raw_message);
					}
				}else{
					$raw_message = str_replace('{payment-method}', '', $raw_message);
					$raw_message = str_replace('{transaction-id}', '', $raw_message);
					$raw_message = str_replace('{payment-date}', '', $raw_message);
					$raw_message = str_replace('{submission-date}', '', $raw_message);
					$raw_message = str_replace('{submission-amount}', '', $raw_message);
				}

				// profile page url
				$raw_message = str_replace('{profile-page-link}', tourmaster_get_template_url('user'), $raw_message);
				
				// html
				$raw_message = str_replace('{header}', '<h3 style="font-size: 17px; margin-bottom: 25px; font-weight: 600; margin-top: 0px; color: #515355" >', $raw_message);
				$raw_message = str_replace('{/header}', '</h3>', $raw_message);
				$raw_message = str_replace('{spaces}', '<div class="tourmaster-mail-spaces" style="margin-bottom: 25px;" ></div>', $raw_message);
				$raw_message = str_replace('{divider}', '<div class="tourmaster-mail-divider" style="border-bottom-width: 1px; border-bottom-style: solid; margin-bottom: 30px; margin-top: 30px; border-color: #d7d7d7;" ></div>', $raw_message);

				$message = tourmaster_mail_content($raw_message);

				// send the mail
				$mail_settings = array(
					'title' => $mail_title,
					'message' => $message
				);
				
				if( $type == 'admin-registration-complete-mail' ){
					$mail_settings['recipient'] = tourmaster_get_option('general', 'admin-registration-email-address');
					$mail_settings['reply-to'] = $user_email;
				}else if( strpos($type, 'admin') === 0 ){
					$mail_settings['recipient'] = tourmaster_get_option('general', 'admin-email-address');
					$mail_settings['reply-to'] = $user_email;
				}else if( !empty($user_email) ){
					$mail_settings['recipient'] = $user_email;
				}

				if( !empty($cc_mail) ){
					$mail_settings['cc'] = $cc_mail;
				}
				if( !empty($mail_settings['recipient']) ){
					tourmaster_mail($mail_settings);
				}
			}

		} // tourmaster_mail_notification
	}

	// group message
	add_action('wp_ajax_tourmaster_submit_group_message', 'tourmaster_ajax_submit_group_message');
	if( !function_exists('tourmaster_ajax_submit_group_message') ){
		function tourmaster_ajax_submit_group_message(){

			$data = tourmaster_process_post_data($_POST);

			$ret = array('data'=>$data);

			if( empty($data['group-message-date']) ){
				$ret['status'] = 'failed';
				$ret['message'] = esc_html__('Please select the date which you want to retrieve the data.', 'tourmaster');
			}else if( empty($data['group-message-mail-subject']) ){
				$ret['status'] = 'failed';
				$ret['message'] = esc_html__('Please fill in the email title.', 'tourmaster');
			}else if( empty($data['group-message-mail-message']) ){
				$ret['status'] = 'failed';
				$ret['message'] = esc_html__('Please fill in the email message.', 'tourmaster');
			}else{

				// tour id
				$results = tourmaster_get_booking_data(array(
					'tour_id' => $data['post_id'],
					'travel_date' => $data['group-message-date'],
					'order_status' => array('custom' => " IN ('approved', 'online-paid', 'deposit-paid') ")
				));

				if( !empty($results) ){

					if( !empty($data['enable-group-message-admin-copy']) && $data['enable-group-message-admin-copy'] == 'enable' ){
						$admin_copy = true;
					}else{
						$admin_copy = false;
					}
					
					foreach( $results as $result ){
						tourmaster_mail_notification('custom', '', '', array(
							'title' => $data['group-message-mail-subject'],
							'message' => $data['group-message-mail-message'],
							'result' => $result
						));

						if( $admin_copy ){							
							tourmaster_mail_notification('admin-custom', '', '', array(
								'title' => $data['group-message-mail-subject'],
								'message' => $data['group-message-mail-message'],
								'result' => $result
							));

							$admin_copy = false;
						}
					}

					$ret['status'] = 'success';
					$ret['message'] = sprintf(esc_html__('The E-mail has been sent successfully to %d customers.', 'tourmaster'), sizeof($results));
				
				}else{
					$ret['status'] = 'failed';
					$ret['message'] = esc_html__('Sorry, we couldn\'t find any customer on the selected date, please try again with different dates.', 'tourmaster');
				}

			}		

			die(json_encode($ret));

		} // tourmaster_ajax_submit_group_message
	}

	// auto mail
	add_action('tourmaster_schedule_daily', 'tourmaster_daily_mail_reminder');
	if( !function_exists('tourmaster_daily_mail_reminder') ){
		function tourmaster_daily_mail_reminder(){

			global $wpdb;

			// payment notification
			$sql  = "SELECT post_id, meta_value FROM {$wpdb->postmeta} ";
		    $sql .= "WHERE meta_key = 'tourmaster-payment-notification' ";
		    $sql .= "AND meta_value = 'enable' ";
		    $results = $wpdb->get_results($sql);

		    if( !empty($results) ){
			    foreach( $results as $result ){

			    	$tour_option = tourmaster_get_post_meta($result->post_id, 'tourmaster-tour-option');

			    	$current_date = strtotime(current_time('mysql'));
			    	$days_before_travel = intval($tour_option['payment-notification-days-before-travel']);
			    	$deposit_days_before_travel = intval($tour_option['deposit-payment-notification-days-before-travel']);
			    	
			    	$travel_date = date('Y-m-d', ($current_date + ($days_before_travel * 86400)));
			    	$deposit_travel_date = date('Y-m-d', ($current_date + ($deposit_days_before_travel * 86400)));
			  			
			  		$custom_sql = '';
			  		if( !empty($days_before_travel) ){
			  			$custom_sql = " (travel_date = '" . $travel_date 		. "' AND order_status = 'pending') ";
			  		}
			  		if( !empty($deposit_days_before_travel) ){
			  			if( empty($custom_sql) ){
			  				$custom_sql = " (travel_date = '{$deposit_travel_date}' AND order_status = 'deposit-paid') ";
			  			}else{
			  				$custom_sql = " ( {$custom_sql} OR (travel_date = '{$deposit_travel_date}' AND order_status = 'deposit-paid') ) ";
			  			}
			  		}
			  		$results2 = tourmaster_get_booking_data(array(
						'tour_id' => $result->post_id,
						'custom' => array(
							'hide-prefix' => true,
							'custom' => $custom_sql
						)
					));

					if( !empty($results2) ){

						if( !empty($tour_option['enable-payment-notification-message-admin-copy']) && $tour_option['enable-payment-notification-message-admin-copy'] == 'enable' ){
							$admin_copy = true;
						}else{
							$admin_copy = false;
						}
						
						foreach( $results2 as $result2 ){ 
							tourmaster_mail_notification('custom', '', '', array(
								'title' => $tour_option['payment-notification-mail-subject'],
								'message' => $tour_option['payment-notification-mail-message'],
								'result' => $result2
							));

							if( $admin_copy ){							
								tourmaster_mail_notification('admin-custom', '', '', array(
									'title' => $tour_option['payment-notification-mail-subject'],
									'message' => $tour_option['payment-notification-mail-message'],
									'result' => $result2
								));

								$admin_copy = false;
							}
						}

					} 

			    }
		    }

			// reminder message
			$sql  = "SELECT post_id, meta_value FROM {$wpdb->postmeta} ";
		    $sql .= "WHERE meta_key = 'tourmaster-reminder-message' ";
		    $sql .= "AND meta_value = 'enable' ";
		    $results = $wpdb->get_results($sql);

		    if( !empty($results) ){
			    foreach( $results as $result ){

			    	$tour_option = tourmaster_get_post_meta($result->post_id, 'tourmaster-tour-option');

			    	$current_date = strtotime(current_time('mysql'));
			    	$days_before_travel = intval($tour_option['reminder-message-days-before-travel']);
			    	$travel_date = date('Y-m-d', ($current_date + ($days_before_travel * 86400)));

			    	$results2 = tourmaster_get_booking_data(array(
						'tour_id' => $result->post_id,
						'travel_date' => $travel_date,
						'order_status' => array('custom' => " IN ('approved', 'online-paid', 'deposit-paid') ")
					));

					if( !empty($results2) ){

						if( !empty($tour_option['enable-reminder-message-admin-copy']) && $tour_option['enable-reminder-message-admin-copy'] == 'enable' ){
							$admin_copy = true;
						}else{
							$admin_copy = false;
						}
						
						foreach( $results2 as $result2 ){ 
							tourmaster_mail_notification('custom', '', '', array(
								'title' => $tour_option['reminder-message-mail-subject'],
								'message' => $tour_option['reminder-message-mail-message'],
								'result' => $result2
							));

							if( $admin_copy ){							
								tourmaster_mail_notification('admin-custom', '', '', array(
									'title' => $tour_option['reminder-message-mail-subject'],
									'message' => $tour_option['reminder-message-mail-message'],
									'result' => $result2
								));

								$admin_copy = false;
							}
						}

					} 

			    }
		    }

		} // tourmaster_mail_reminder
	}

	if( !function_exists('tourmaster_send_email_invoice') ){
		function tourmaster_send_email_invoice( $tid ){

			$enable_email_invoice = tourmaster_get_option('general', 'enable-customer-invoice', 'enable');
			if( $enable_email_invoice == 'disable' ) return;

			ob_start();
			$result = tourmaster_get_booking_data(array(
				'id' => $tid,
			), array('single' => true));

			echo '<div style="background: #fff; padding: 50px 50px; font-size: 14px; " >'; // tourmaster-invoice-wrap

			$invoice_logo = tourmaster_get_option('general', 'invoice-logo');
			$billing_info = empty($result->billing_info)? array(): json_decode($result->billing_info, true);
			$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);

			echo '<div style="margin-bottom: 60px; color: #121212;" >'; // tourmaster-invoice-head
			echo '<div style="float: left;" >'; // tourmaster-invoice-head-left
			echo '<div style="margin-bottom: 35px;" >'; // tourmaster-invoice-logo
			if( empty($invoice_logo) ){
				echo tourmaster_get_image(TOURMASTER_URL . '/images/invoice-logo.png');
			}else{
				echo tourmaster_get_image($invoice_logo);
			}
			echo '</div>'; // tourmaster-invoice-logo
			echo '<div style="font-size: 16px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase;" >' . esc_html__('Invoice ID :', 'tourmaster') . ' #' . $result->id . '</div>'; // tourmaster-invoice-id
			echo '<div>' . esc_html__('Invoice date :', 'tourmaster') . ' ' . tourmaster_date_format($result->booking_date) . '</div>'; // tourmaster-invoice-date
			echo '<div style="margin-top: 34px;" >'; // tourmaster-invoice-receiver
			echo '<div style="font-size: 16px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;" >' . esc_html__('Invoice To', 'tourmaster') . '</div>'; // tourmaster-invoice-receiver-head
			echo '<div>'; // tourmaster-invoice-receiver-info
			$customer_address = tourmaster_get_option('general', 'invoice-customer-address');
			if( empty($customer_address) ){
				echo '<span style="display: block; margin-bottom: 4px;" >' . $billing_info['first_name'] . ' ' . $billing_info['last_name'] . '</span>'; // tourmaster-invoice-receiver-name
				echo '<span style="display: block; max-width: 250px;" >' . (empty($billing_info['contact_address'])? '': $billing_info['contact_address']) . '</span>'; // tourmaster-invoice-receiver-address
			}else{
				echo tourmaster_content_filter(tourmaster_set_contact_form_data($customer_address, $billing_info));
			}
			echo '</div>';
			echo '</div>';
			echo '</div>'; // tourmaster-invoice-head-left
			
			$company_name = tourmaster_get_option('general', 'invoice-company-name', '');
			$company_info = tourmaster_get_option('general', 'invoice-company-info', '');
			echo '<div style="float: right; padding-top: 10px; width: 180px;" >'; // tourmaster-invoice-head-right
			echo '<div>'; // tourmaster-invoice-company-info
			echo '<div style="font-size: 16px; font-weight: bold; margin-bottom: 20px;" >' . $company_name . '</div>'; // tourmaster-invoice-company-name
			echo '<div>' . tourmaster_content_filter($company_info) . '</div>'; // tourmaster-invoice-company-info
			echo '</div>';
			echo '</div>'; // tourmaster-invoice-head-right

			echo '<div style="clear: both" ></div>';
			echo '</div>'; // tourmaster-invoice-head

			// price breakdown
			if( !empty($result->pricing_info) ){
				$pricing_info = json_decode($result->pricing_info, true);
				echo '<div>'; // tourmaster-invoice-price-breakdown
				echo '<div style="padding: 18px 25px; font-size: 14px; font-weight: 700; text-transform: uppercase; color: #454545; background-color: #f3f3f3" >'; // tourmaster-invoice-price-head
				echo '<span style="width: 80%; float: left;" >' . esc_html__('Description', 'tourmaster') . '</span>'; // tourmaster-head
				echo '<span style="overflow: hidden;" >' . esc_html__('Total', 'tourmaster') . '</span>'; // tourmaster-tail
				echo '</div>'; // tourmaster-invoice-price-head

				echo tourmaster_get_tour_invoice_price_email($result->tour_id, $pricing_info['price-breakdown'], $booking_detail);

				echo '<div style="font-weight: bold; padding: 18px 25px; border-width: 1px 0px 2px; border-style: solid; border-color: #e1e1e1;" >'; // tourmaster-invoice-total-price
				echo '<span style="float: left; margin-left: 55%; width: 25%; font-size: 15px;" >' . esc_html__('Total', 'tourmaster') . '</span> '; // tourmaster-head
				echo '<span style="display: block; overflow: hidden; font-size: 16px;" >' . tourmaster_money_format($result->total_price) . '</span>'; // tourmaster-tail
				echo '</div>'; // tourmaster-invoice-total-price
				echo '</div>'; // tourmaster-invoice-price-breakdown
			}

			if( !empty($result->order_status) && in_array($result->order_status, array('approve', 'online-paid', 'departed', 'deposit-paid')) ){
				$payment_date = tourmaster_date_format($result->payment_date);
				$payment_infos = json_decode($result->payment_info, true);
				$payment_infos = tourmaster_payment_info_format($payment_infos, $result->order_status);

				if( !empty($payment_infos) ){
					echo '<div style="padding: 22px 35px; margin-top: 40px; background: #f3f3f3; color: #454545" >'; // tourmaster-invoice-payment-info
					foreach( $payment_infos as $payment_info ){
						echo '<div style="margin-bottom: 15px;" >';
						echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >'; // tourmaster-invoice-payment-info-item
						echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . esc_html__('Payment Method', 'tourmaster') . '</div>'; // tourmaster-head
						echo '<div>'; // tourmaster-tail
						if( !empty($payment_info['payment_method']) && $payment_info['payment_method'] == 'receipt' ){
							echo esc_html__('Bank Transfer', 'tourmaster');
						}else if( !empty($payment_info['payment_method']) ){
							echo esc_html__('Online Payment', 'tourmaster');
						}
						echo '</div>';
						echo '</div>'; // tourmaster-invoice-payment-info-item

						// fee
						if( !empty($payment_info['deposit_amount']) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >';
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . esc_html__('Deposit Amount', 'tourmaster') . '</div>';
							echo '<div>' . tourmaster_money_format($payment_info['deposit_amount']) . '</div>';	
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}else if( !empty($payment_info['pay_amount']) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >';
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . esc_html__('Total Price', 'tourmaster') . '</div>';
							echo '<div>' . tourmaster_money_format($payment_info['pay_amount']) . '</div>';	
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}
						if( !empty($payment_info['deposit_paypal_service_rate']) && !empty($payment_info['deposit_paypal_service_fee']) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >';
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . sprintf(esc_html__('Paypal Fee (%s%%)', 'tourmaster'), $payment_info['deposit_paypal_service_rate']) . '</div>';
							echo '<div>' . tourmaster_money_format($payment_info['deposit_paypal_service_fee']) . '</div>';	
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}else if( !empty($payment_info['pay_paypal_service_rate']) && !empty($payment_info['pay_paypal_service_fee']) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >';
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . sprintf(esc_html__('Paypal Fee (%s%%)', 'tourmaster'), $payment_info['pay_paypal_service_rate']) . '</div>';
							echo '<div>' . tourmaster_money_format($payment_info['pay_paypal_service_fee']) . '</div>';	
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}else if( !empty($payment_info['deposit_credit_card_service_rate']) && !empty($payment_info['deposit_credit_card_service_fee']) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >';
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . sprintf(esc_html__('Credit Card Fee (%s%%)', 'tourmaster'), $payment_info['deposit_credit_card_service_rate']) . '</div>';
							echo '<div>' . tourmaster_money_format($payment_info['deposit_credit_card_service_fee']) . '</div>';	
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}else if( !empty($payment_info['pay_credit_card_service_rate']) && !empty($payment_info['pay_credit_card_service_fee']) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >';
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . sprintf(esc_html__('Credit Card Fee (%s%%)', 'tourmaster'), $payment_info['pay_credit_card_service_rate']) . '</div>';
							echo '<div>' . tourmaster_money_format($payment_info['pay_credit_card_service_fee']) . '</div>';	
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}

						// paid amount
						if( !empty($payment_info['amount']) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >'; // tourmaster-invoice-payment-info-item
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . esc_html__('Paid Amount', 'tourmaster') . '</div>'; // tourmaster-head
							echo '<div>' . tourmaster_money_format($payment_info['amount']) . '</div>';	// tourmaster-tail
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}

						echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >'; // tourmaster-invoice-payment-info-item
						echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . esc_html__('Date', 'tourmaster') . '</div>'; // tourmaster-head
						echo '<div>' . $payment_date . '</div>'; // tourmaster-tail
						echo '</div>'; // tourmaster-invoice-payment-info-item
						
						$transaction_id = '';
						if( !empty($payment_info['transaction_id']) ){
							$transaction_id = $payment_info['transaction_id'];
						}else if( !empty($payment_info['transaction-id']) ){
							$transaction_id = $payment_info['transaction-id'];
						}
						if( !empty($transaction_id) ){
							echo '<div style="float: left; margin-right: 60px; text-transform: uppercase;" >'; // tourmaster-invoice-payment-info-item
							echo '<div style="font-weight: 800; margin-bottom: 5px;" >' . esc_html__('Transaction ID', 'tourmaster') . '</div>'; // tourmaster-head
							echo '<div>' . $transaction_id . '</div>'; // tourmaster-tail
							echo '</div>'; // tourmaster-invoice-payment-info-item
						}

						echo '<div style="clear: both" ></div>';
						echo '</div>';
					}
					echo '</div>';
				}
			}

			echo '</div>'; // tourmaster-invoice-wrap

			$content = ob_get_contents();
			ob_end_clean();
			
			// send the mail
			$mail_settings = array(
				'title' => sprintf(esc_html__('Invoice From %s', 'tourmaster'), tourmaster_get_option('general', 'system-email-name', 'WORDPRESS')), 
				'message' => tourmaster_mail_content($content, true, true, array('width' => '1210', 'padding' => '0px 1px', 'no-filter' => true)),
				'recipient' => $billing_info['email']
			);
			
			if( !empty($mail_settings['recipient']) ){
				tourmaster_mail($mail_settings);
			}

		} // tourmaster_send_email_invoice
	}