<?php
	
	// update the user data for change password page
	if( !empty($_GET['page_type']) && $_GET['page_type'] == 'change-password' ){

		if( isset($_POST['security']) ){
			global $current_user, $tourmaster_updated_status;

			if( wp_verify_nonce($_POST['security'], 'tourmaster-change-password') ){
				
				// check if every field is filled
				if( empty($_POST['old-password']) || empty($_POST['new-password']) || empty($_POST['confirm-password']) ){
					$tourmaster_updated_status = new WP_ERROR('1', esc_html__('Please fill all required fields.', 'tourmaster'));

				// check if new password is matched
				}else if( $_POST['new-password'] != $_POST['confirm-password'] ){
					$tourmaster_updated_status = new WP_ERROR('3', esc_html__('Password does not match the confirm password.', 'tourmaster'));
				
				// check if old password is correct
				}else if( !wp_check_password($_POST['old-password'], $current_user->data->user_pass, $current_user->data->ID) ){
					$tourmaster_updated_status = new WP_ERROR('4', esc_html__('Old password incorrect.', 'tourmaster'));
				
				// update the data
				}else{
					wp_update_user(array( 
						'ID' => $current_user->ID, 
						'user_pass' => $_POST['new-password']
					));
					$tourmaster_updated_status = true;
				}
			}else{
				$tourmaster_updated_status = new WP_ERROR('5', esc_html__('The session is expired. Please refesh the page to try again.', 'tourmaster'));
			}

			unset($_POST['security']);
			unset($_POST['old-password']);
			unset($_POST['new-password']);
			unset($_POST['confirm-password']);
		}

	
	}

	// remove booking data
	if( !empty($_GET['action']) && $_GET['action'] == 'remove' && !empty($_GET['id'])){
		global $current_user;

		if( is_numeric($_GET['id']) ){
			$updated = tourmaster_update_booking_data(
				array('order_status' => 'cancel'),
				array('id' => $_GET['id'], 'user_id' => $current_user->data->ID),
				array('%s'),
				array('%d', '%d')
			);

			if( $updated ){
				tourmaster_mail_notification('booking-cancelled-mail', $_GET['id']);
			}
		}

		wp_redirect(remove_query_arg(array('action', 'id')));
	}

	// submit payment evidence
	if( !empty($_POST['action']) && $_POST['action'] == 'payment-receipt' ){
		global $current_user, $wpdb;

		if( !empty($_POST['id']) ){

			// upload the file
			if( !empty($_FILES['receipt']['size']) ){
				if ( !function_exists('wp_handle_upload') ) {
				    require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				add_filter('upload_dir', 'tourmaster_set_receipt_upload_folder');
				$uploaded_file = wp_handle_upload($_FILES['receipt'],  array('test_form' => false));
				remove_filter('upload_dir', 'tourmaster_set_receipt_upload_folder');
			}

			// upload error
			if( empty($uploaded_file) || !empty($uploaded_file['error']) ){
				wp_redirect(add_query_arg(array('error_code'=>'cannot_upload_file')));

			// upload success
			}else{
				// get old payment info
				$result = tourmaster_get_booking_data(array('id' => $_POST['id']), array('single' => true));
				$payment_infos = json_decode($result->payment_info, true);
				$payment_infos = tourmaster_payment_info_format($payment_infos, $result->order_status);
				$price_settings = tourmaster_get_price_settings($result->tour_id, $payment_infos, $result->total_price, $result->travel_date);

				// payment info
				$payment_info = array(
					'payment_method' => 'receipt'
				);

				if( !empty($_POST['transaction-id']) ){
					$payment_info['transaction_id'] = $_POST['transaction-id'];
				}

				if( empty($_POST['payment-type']) || $_POST['payment-type'] == 'full' ){
					$payment_info['amount'] = $price_settings['full-payment-amount'];
					$submission_amount = $payment_info['amount'];
				}else{
					$payment_info['deposit_rate'] = $price_settings['next-deposit-percent'];
					$payment_info['deposit_price'] = $price_settings['next-deposit-amount'];
					$submission_amount = $payment_info['deposit_price'];
				}

				$payment_info['local_url'] = $uploaded_file['file'];
				$payment_info['file_url'] = $uploaded_file['url'];
				$payment_info['submission_date'] = current_time('mysql'); 
				$payment_info['payment_status'] = 'pending';
				$payment_infos[] = $payment_info;

				// update database
				$update_status = $wpdb->update( "{$wpdb->prefix}tourmaster_order", 
					array(
						'order_status' => 'receipt-submitted',
						'payment_info' => json_encode($payment_infos),
						'payment_date' => current_time('mysql')
					), 
					array( 
						'id' => $_POST['id'], 
						'user_id' => $current_user->data->ID 
					), 
					array('%s', '%s'), 
					array('%d', '%d')
				);

				tourmaster_mail_notification('receipt-submission-mail', $_POST['id'], '', array(
					'custom' => array(
						'payment-method' => 'receipt',
						'submission-date' => tourmaster_time_format($payment_info['submission_date']) . ' ' . tourmaster_date_format($payment_info['submission_date']),
						'submission-amount' => tourmaster_money_format($submission_amount),
						'transaction-id' => empty($payment_info['transaction_id'])? '': $payment_info['transaction_id']
					)
				));
				tourmaster_mail_notification('admin-payment-submitted-mail', $_POST['id']);
				
				wp_redirect(add_query_arg(array()));
			}
		}
	}

	// process the submitted review
	if( !empty($_POST['review_id']) && is_numeric($_POST['review_id'])){
		global $current_user;
		
		$result = tourmaster_get_booking_data(array( 
			'id' => sanitize_text_field($_POST['review_id'])
		), array('single' => true) );

		if( !empty($result) && $result->order_status == 'departed' && $result->user_id == $current_user->data->ID ){
			$review_score = empty($_POST['rating'])? 0: sanitize_text_field(tourmaster_process_post_data($_POST['rating']));
			$review_type = empty($_POST['traveller-type'])? 'solo': sanitize_text_field(tourmaster_process_post_data($_POST['traveller-type']));
			$review_description = empty($_POST['description'])? '': sanitize_textarea_field(tourmaster_process_post_data($_POST['description']));

			tourmaster_insert_review_data(array(
				'tour_id' => $result->tour_id,
				'score' => $review_score,
				'type' =>  $review_type,
				'description' => $review_description,
				'order_id' => $_POST['review_id']
			));
		}

		// update the review score for each post
		if( !empty($_POST['tour_id']) && is_numeric($_POST['tour_id']) ){
			tourmaster_update_review_score($_POST['tour_id']);
		}

		wp_redirect(add_query_arg(array()));
	}		

?>