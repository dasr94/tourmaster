<?php
	/*	
	*	Ordering Page
	*/

	add_action('admin_menu', 'tourmaster_init_order_page', 99);
	if( !function_exists('tourmaster_init_order_page') ){
		function tourmaster_init_order_page(){
			add_menu_page(
				esc_html__('Transaction Order', 'tourmaster'), 
				esc_html__('Transaction Order', 'tourmaster'),
				'manage_tour_order', 
				'tourmaster_order', 
				'tourmaster_create_order_page',
				TOURMASTER_URL . '/framework/images/admin-option-icon.png',
				120
			);
		}
	}

	// add the script when opening the theme option page
	add_action('admin_enqueue_scripts', 'tourmaster_order_page_script');
	if( !function_exists('tourmaster_order_page_script') ){
		function tourmaster_order_page_script($hook){
			if( strpos($hook, 'page_tourmaster_order') !== false ){
				tourmaster_include_utility_script(array(
					'font-family' => 'Open Sans'
				));

				wp_enqueue_style('tourmaster-order', TOURMASTER_URL . '/include/css/order.css');
				wp_enqueue_script('tourmaster-order', TOURMASTER_URL . '/include/js/order.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), false, true);
			}
		}
	}

	if( !function_exists('tourmaster_order_csv_export') ){
		function tourmaster_order_csv_export( $results ){

			// define constant
			$current_url = (is_ssl()? "https": "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$statuses = array(
				'all' => __('All', 'tourmaster'),
				'pending' => __('Pending', 'tourmaster'),
				'approved' => __('Approved', 'tourmaster'),
				'receipt-submitted' => __('Receipt Submitted', 'tourmaster'),
				'online-paid' => __('Online Paid', 'tourmaster'),
				'deposit-paid' => __('Deposit Paid', 'tourmaster'),
				'departed' => __('Departed', 'tourmaster'),
				'rejected' => __('Rejected', 'tourmaster'),
				'cancel' => __('Cancel', 'tourmaster'),
				'wait-for-approval' => __('Wait For Approval', 'tourmaster'),
			);

			// print it as file
			$fp = fopen(TOURMASTER_LOCAL . '/include/js/order.csv', 'w');
			fputcsv($fp, array(
				__('Order ID', 'tourmaster'),
				__('Tour Name', 'tourmaster'),
				__('Contact Name', 'tourmaster'),
				__('Contact Email', 'tourmaster'),
				__('Contact Number', 'tourmaster'),
				__('Customer\'s Note', 'tourmaster'),
				__('Booking Date', 'tourmaster'),
				__('Travel Date', 'tourmaster'),
				__('Total Price', 'tourmaster'),
				__('Traveller Info', 'tourmaster'),
				__('Payment Status', 'tourmaster'),
				__('Link To Transaction', 'tourmaster'),
			));
			foreach( $results as $result ){
				$contact_info = json_decode($result->contact_info, true);

				$traveller_detail = '';
				$traveller_info = json_decode($result->traveller_info, true);

				$tour_option = tourmaster_get_post_meta($result->tour_id, 'tourmaster-tour-option');
				$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);
				if( !empty($tour_option['additional-traveller-fields']) ){
					$additional_traveller_fields = $tour_option['additional-traveller-fields'];
				}else{
					$additional_traveller_fields = tourmaster_get_option('general', 'additional-traveller-fields', '');
				}
				if( !empty($additional_traveller_fields) ){
					$additional_traveller_fields = tourmaster_read_custom_fields($additional_traveller_fields);
				}

				if( !empty($traveller_info['first_name']) ){
					$traveller_num = sizeof($traveller_info['first_name']);

					for( $i = 0; $i < $traveller_num; $i++ ){
						if( !empty($traveller_detail) ){
							$traveller_detail .= ", \n";
						}

						foreach( $traveller_info as $key => $traveller ){
							if( !empty($traveller[$i]) ){
								$traveller_detail .= $traveller[$i] . ' ';
							}
						}

						if( !empty($additional_traveller_fields) ){
							foreach( $additional_traveller_fields as $field ){
								if( !empty($booking_detail['traveller_' . $field['slug']][$i]) ){
									$traveller_detail .= ' | ' . $booking_detail['traveller_' . $field['slug']][$i];
								}
							}
						}
					}
				}

				fputcsv($fp, array(
					'#' . $result->id,
					html_entity_decode(get_the_title($result->tour_id)),
					$contact_info['first_name'] . ' ' . $contact_info['last_name'],
					$contact_info['email'],
					$contact_info['phone'],
					empty($contact_info['additional_notes'])? ' ': $contact_info['additional_notes'],
					tourmaster_date_format($result->booking_date),
					tourmaster_date_format($result->travel_date),
					tourmaster_money_format($result->total_price),
					$traveller_detail,
					$statuses[$result->order_status],
					add_query_arg(
						array('single'=>$result->id), 
						remove_query_arg(array('order_id', 'from_date', 'to_date', 'action', 'id', 'export'), $current_url)
					)
				));
			}
			fclose($fp);

// script for user to download
?><script>
	jQuery(document).ready(function(){
		var element = document.createElement('a');
		element.setAttribute('href', '<?php echo esc_js(TOURMASTER_URL . '/include/js/order.csv'); ?>');
		element.setAttribute('download', 'transaction.csv');
		
		element.style.display = 'none';
		document.body.appendChild(element);

		element.click();
		document.body.removeChild(element);
	});
</script><?php

		} // tourmaster_order_csv_export
	}

	if( !function_exists('tourmaster_create_order_page') ){
		function tourmaster_create_order_page(){

			// new order
			echo '<a class="tourmaster-new-order-text" href="#" data-tmlb="new-order">';
			echo esc_html__('Add New Booking', 'tourmaster');
			echo '</a>'; 

			echo tourmaster_lightbox_content(array(
				'id' => 'new-order',
				'title' => esc_html__('Edit Order', 'tourmaster'),
				'content' => tourmaster_order_edit_form('', 'new_order', array())
			));	

			// print order
			if( !isset($_GET['single']) ){
				$action_url = remove_query_arg(array('order_id', 'from_date', 'to_date', 'action', 'id', 'export'));

				$statuses = array(
					'all' => esc_html__('All', 'tourmaster'),
					'pending' => esc_html__('Pending', 'tourmaster'),
					'approved' => esc_html__('Approved', 'tourmaster'),
					'receipt-submitted' => esc_html__('Receipt Submitted', 'tourmaster'),
					'online-paid' => esc_html__('Online Paid', 'tourmaster'),
					'deposit-paid' => esc_html__('Deposit Paid', 'tourmaster'),
					'departed' => esc_html__('Departed', 'tourmaster'),
					'rejected' => esc_html__('Rejected', 'tourmaster'),
					'cancel' => esc_html__('Cancel', 'tourmaster'),
					'wait-for-approval' => __('Wait For Approval', 'tourmaster'),
				);
?>
<div class="tourmaster-order-filter-wrap" >
	<form class="tourmaster-order-search-form" method="get" action="<?php echo esc_url($action_url); ?>" >
		<label><?php esc_html_e('Search by order id :', 'tourmaster'); ?></label>
		<input type="text" name="order_id" value="<?php echo empty($_GET['order_id'])? '': esc_attr($_GET['order_id']); ?>" />
		<input type="hidden" name="page" value="tourmaster_order" />
		<input type="submit" value="<?php esc_html_e('Search', 'tourmaster'); ?>" />
	</form>
	<form class="tourmaster-order-search-form" method="get" action="<?php echo esc_url($action_url); ?>" >
		<div style="margin-bottom: 10px;" >
			<label><?php esc_html_e('Select Tour :', 'tourmaster'); ?></label>
			<select name="tour_id" ><?php
				$tour_list = tourmaster_get_post_list('tour');
				echo '<option value="" >' . esc_html__('All', 'tourmaster') . '</option>';
				foreach( $tour_list as $tour_id => $tour_name ){
					echo '<option value="' . esc_attr($tour_id) . '" ' . ((!empty($_GET['tour_id']) && $_GET['tour_id'] == $tour_id)? 'selected': '') . ' >' . esc_html($tour_name) . '</option>';
				}	
			?></select>
			<br>
		</div>
		<label><?php esc_html_e('Date Filter :', 'tourmaster'); ?></label>
		<span class="tourmaster-separater" ><?php esc_html_e('From', 'tourmaster') ?></span>
		<input class="tourmaster-datepicker" type="text" name="from_date" value="<?php echo empty($_GET['from_date'])? '': esc_attr($_GET['from_date']); ?>" />
		<span class="tourmaster-separater" ><?php esc_html_e('To', 'tourmaster') ?></span>
		<input class="tourmaster-datepicker" type="text" name="to_date" value="<?php echo empty($_GET['to_date'])? '': esc_attr($_GET['to_date']); ?>" />
		<input type="hidden" name="page" value="tourmaster_order" />
		<input type="hidden" name="export" value="0" />
		<input type="submit" value="<?php esc_html_e('Filter', 'tourmaster'); ?>" />
		<input id="tourmaster-csv-export" type="button" value="<?php esc_html_e('Export To CSV', 'tourmaster'); ?>" />
	</form>
	<div class="tourmaster-order-filter" >
	<?php
		$order_status = empty($_GET['order_status'])? 'all': $_GET['order_status'];
		foreach( $statuses as $status_slug => $status ){
			echo '<span class="tourmaster-separator" >|</span>';
			echo '<a href="' . esc_url(add_query_arg(array('order_status'=>$status_slug), $action_url)) . '" ';
			echo 'class="tourmaster-order-filter-status ' . ($status_slug == $order_status? 'tourmaster-active': '') . '" >';
			echo $status;
			echo '</a>';
		}
	?>
	</div>
</div>
<?php				
			}

			echo '<div class="tourmaster-order-page-wrap" >';
			echo '<div class="tourmaster-order-page-head" >';
			echo '<i class="fa fa-check-circle-o" ></i>';
			echo esc_html__('Transaction Order', 'tourmaster');
			echo '</div>'; // tourmaster-order-page-head

			echo '<div class="tourmaster-order-page-content clearfix" >';
			if( isset($_GET['single']) ){
				tourmaster_get_single_order();
			}else{
				tourmaster_get_order_list();
			}

			echo '</div>'; // tourmaster-order-page-content
			echo '</div>'; // tourmaster-order-page-wrap
		}
	}

	if( !function_exists('tourmaster_get_order_list') ){
		function tourmaster_get_order_list(){

			// order action
			if( !empty($_GET['action']) && !empty($_GET['id']) ){
 				if( $_GET['action'] == 'remove' ){
 					$result = tourmaster_get_booking_data(array('id' => $_GET['id']), array('single' => true));
					tourmaster_set_locale($result->tour_id);

 					tourmaster_mail_notification('booking-reject-mail', $_GET['id']);
 					tourmaster_remove_booking_data($_GET['id']);

 					tourmaster_return_locale();

 				}else if( in_array($_GET['action'], array('approved', 'rejected')) ){

 					// check old status first
 					$result = tourmaster_get_booking_data(array('id' => $_GET['id']), array('single' => true));
 					if( $_GET['action'] == 'approved' ){
 						if( $result->order_status == 'wait-for-approval' ){
 							$order_status = 'pending';
 						}else{
 							$order_status = 'approved';
 						}
 					}else{
 						$order_status = 'rejected';
 					}

 					$updated = tourmaster_update_booking_data(
 						array('order_status' => $order_status),
 						array('id' => $_GET['id']),
 						array('%s'),
 						array('%d')
 					);
 					
 					// send the mail
 					if( !empty($updated) ){
 						tourmaster_set_locale($result->tour_id);
 						if( in_array($order_status, array('approved', 'online-paid', 'deposit-paid')) ){
 							tourmaster_mail_notification('payment-made-mail', $_GET['id']);
 							tourmaster_send_email_invoice($_GET['id']);
 						}else if( $order_status == 'rejected' ){
 							tourmaster_mail_notification('booking-reject-mail', $_GET['id']);
 						}else if( $order_status == 'pending' ){
 							tourmaster_mail_notification('booking-approve-mail', $_GET['id']);
 						}
 						tourmaster_return_locale();
 					}
 				}
 			}

			// print the order
 			$paged = empty($_GET['paged'])? 1: $_GET['paged'];
 			$num_fetch = 20;
 			$query_args = array();
			if( !empty($_GET['order_status']) && $_GET['order_status'] != 'all' ){
				$query_args['order_status'] = $_GET['order_status'];
			}
			if( !empty($_GET['order_id']) ){
				$query_args['id'] = $_GET['order_id'];
			}
			if( !empty($_GET['tour_id']) ){
				$query_args['tour_id'] = $_GET['tour_id'];
			}
			if( !empty($_GET['from_date']) ){
				$custom_condition = ' >= \'' . esc_sql($_GET['from_date']) . '\''; 
				if( !empty($_GET['to_date']) ){
					$custom_condition .= ' AND travel_date <= \'' . esc_sql($_GET['to_date']) . '\' ';
				}
				$query_args['travel_date'] = array( 
					'custom' => $custom_condition
				);
			}

			$results = tourmaster_get_booking_data($query_args, array(
				'paged' => $paged,
				'num-fetch' => $num_fetch
			));
			$max_num_page = ceil(tourmaster_get_booking_data($query_args, array(), 'COUNT(*)') / $num_fetch);
			if( !empty($_GET['export']) ){
				$export_results = tourmaster_get_booking_data($query_args, array(
					'num-fetch' => 9999
				));
				tourmaster_order_csv_export($export_results);
			}


			echo '<table>';
			echo tourmaster_get_table_head(array(
				esc_html__('Order', 'tourmaster'),
				esc_html__('Contact Detail', 'tourmaster'),
				esc_html__('Customer\'s Note', 'tourmaster'),
				esc_html__('Travel Date', 'tourmaster'),
				esc_html__('Total', 'tourmaster'),
				esc_html__('Payment Status', 'tourmaster'),
				esc_html__('Action', 'tourmaster'),
			));
			$statuses = array(
				'all' => esc_html__('All', 'tourmaster'),
				'pending' => esc_html__('Pending', 'tourmaster'),
				'approved' => esc_html__('Approved', 'tourmaster'),
				'receipt-submitted' => esc_html__('Receipt Submitted', 'tourmaster'),
				'online-paid' => esc_html__('Online Paid', 'tourmaster'),
				'deposit-paid' => esc_html__('Deposit Paid', 'tourmaster'),
				'departed' => esc_html__('Departed', 'tourmaster'),
				'rejected' => esc_html__('Rejected', 'tourmaster'),
				'cancel' => esc_html__('Cancel', 'tourmaster'),
				'wait-for-approval' => __('Wait For Approval', 'tourmaster'),
			);

			foreach( $results as $result ){

				$order_title  = '<div class="tourmaster-head" >#' . $result->id . '<span class="tourmaster-travel-date" > - ' . tourmaster_date_format($result->travel_date) . '</span>' . '</div>';
				$order_title .= '<div class="tourmaster-content" ><a href="' . add_query_arg(array('single'=>$result->id), remove_query_arg(array('order_id', 'from_date', 'to_date', 'action', 'id', 'export'))) . '" >';
				$order_title .= get_the_title($result->tour_id);
				$order_title .= '</a></div>';

				$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
				$buyer_info  = '<div class="tourmaster-head" >';
				$buyer_info .= empty($contact_detail['first_name'])? '': $contact_detail['first_name'] . ' ';
				$buyer_info .= empty($contact_detail['last_name'])? '': $contact_detail['last_name'] . ' ';
				$buyer_info .= '</div>';
				$buyer_info .= '<div class="tourmaster-content" >';
				$buyer_info .= empty($contact_detail['phone'])? '': $contact_detail['phone'] . ' ';
				$buyer_info .= empty($contact_detail['email'])? '': '<a href="mailto:' . esc_attr($contact_detail['email']) . '" ><i class="fa fa-envelope-o" ></i></a>';
				$buyer_info .= '</div>';

				$additional_note = '';
				if( !empty($contact_detail['additional_notes']) ){
					$additional_note  = wp_trim_words($contact_detail['additional_notes'], 15);
				}

				$travel_date = $result->travel_date;

				$tour_price = tourmaster_money_format($result->total_price);

				$order_status  = '<span class="tourmaster-order-status tourmaster-status-' . esc_attr($result->order_status) . '" >';
				if( $result->order_status == 'approved' ){
					$order_status .= '<i class="fa fa-check" ></i>';
				}else if( $result->order_status == 'departed' ){
					$order_status .= '<i class="fa fa-check-circle-o" ></i>';
				}else if( $result->order_status == 'rejected' || $result->order_status == 'cancel' ){
					$order_status .= '<i class="fa fa-remove" ></i>';
				}	
				$order_status .= $statuses[$result->order_status];
				if( $result->order_status == 'pending' && empty($result->user_id) ){
					$order_status .= ' <br>' . esc_html__('(Via E-mail)', 'tourmaster');
				}
				$order_status .= '</span>';

				$action  = '<a href="' . add_query_arg(array('single'=>$result->id), remove_query_arg(array('id','action'))) . '" class="tourmaster-order-action" title="' . esc_html__('View', 'tourmaster') . '" >';
				$action .= '<i class="fa fa-eye" ></i>';
				$action .= '</a>';
				$action .= '<a href="' . add_query_arg(array('id'=>$result->id, 'action'=>'approved')) . '" class="tourmaster-order-action" title="' . esc_html__('Approve', 'tourmaster') . '" ';
				$action .= 'data-confirm="' . esc_html__('After approving the transaction, invoice and payment receipt will be sent to customer\'s billing email.', 'tourmaster') . '" ';
				$action .= '>';
				$action .= '<i class="fa fa-check" ></i>';
				$action .= '</a>';
				$action .= '<a href="' . add_query_arg(array('id'=>$result->id, 'action'=>'rejected')) . '" class="tourmaster-order-action" title="' . esc_html__('Reject', 'tourmaster') . '" ';
				$action .= 'data-confirm="' . esc_html__('After rejected the transaction, the rejection message will be sent to customer\'s contact email.', 'tourmaster') . '" ';
				$action .= '>';
				$action .= '<i class="fa fa-remove" ></i>';
				$action .= '</a>';
				$action .= '<a href="' . add_query_arg(array('id'=>$result->id, 'action'=>'remove')) . '" class="tourmaster-order-action" title="' . esc_html__('Remove', 'tourmaster') . '" ';
				$action .= 'data-confirm="' . esc_html__('The transaction you selected will be permanently removed from the system.', 'tourmaster') . '" ';
				$action .= '>';
				$action .= '<i class="fa fa-trash-o" ></i>';
				$action .= '</a>';

				tourmaster_get_table_content(array($order_title, $buyer_info, $additional_note, $travel_date, $tour_price, $order_status, $action));
			}

			echo '</table>';

			if( !empty($max_num_page) && $max_num_page > 1 ){
				echo '<div class="tourmaster-transaction-pagination" >';
				for( $i = 1; $i <= $max_num_page; $i++ ){
					if( $i == $paged ){
						echo '<span class="tourmaster-transaction-pagination-item tourmaster-active" >' . $i . '</span>';
					}else{
						echo '<a href="' . add_query_arg(array('paged'=>$i), remove_query_arg(array('action'))) . '" class="tourmaster-transaction-pagination-item" >' . $i . '</a>';
					}
				}
				echo '</div>';
			}

		}
	}


	//custom table 
	if( !function_exists('tourmaster_get_order_list2') ){
		function tourmaster_get_order_list2(){
			$current_user = wp_get_current_user();
			// order action
			if( !empty($_GET['action']) && !empty($_GET['id']) ){
 				if( $_GET['action'] == 'remove' ){
 					$result = tourmaster_get_booking_data(array('id' => $_GET['id']), array('single' => true));
					tourmaster_set_locale($result->tour_id);

 					tourmaster_mail_notification('booking-reject-mail', $_GET['id']);
 					tourmaster_remove_booking_data($_GET['id']);

 					tourmaster_return_locale();

 				}else if( in_array($_GET['action'], array('approved', 'rejected')) ){

 					// check old status first
 					$result = tourmaster_get_booking_data(array('id' => $_GET['id']), array('single' => true));
 					if( $_GET['action'] == 'approved' ){
 						if( $result->order_status == 'wait-for-approval' ){
 							$order_status = 'pending';
 						}else{
 							$order_status = 'approved';
 						}
 					}else{
 						$order_status = 'rejected';
 					}

 					$updated = tourmaster_update_booking_data(
 						array('order_status' => $order_status),
 						array('id' => $_GET['id']),
 						array('%s'),
 						array('%d')
 					);
 					
 					// send the mail
 					if( !empty($updated) ){
 						tourmaster_set_locale($result->tour_id);
 						if( in_array($order_status, array('approved', 'online-paid', 'deposit-paid')) ){
 							tourmaster_mail_notification('payment-made-mail', $_GET['id']);
 							tourmaster_send_email_invoice($_GET['id']);
 						}else if( $order_status == 'rejected' ){
 							tourmaster_mail_notification('booking-reject-mail', $_GET['id']);
 						}else if( $order_status == 'pending' ){
 							tourmaster_mail_notification('booking-approve-mail', $_GET['id']);
 						}
 						tourmaster_return_locale();
 					}
 				}
 			}

			// print the order
 			$paged = empty($_GET['paged'])? 1: $_GET['paged'];
 			$num_fetch = 20;
 			$query_args = array();
			if( !empty($_GET['order_status']) && $_GET['order_status'] != 'all' ){
				$query_args['order_status'] = $_GET['order_status'];
			}
			if( !empty($_GET['order_id']) ){
				$query_args['id'] = $_GET['order_id'];
			}
			if( !empty($_GET['tour_id']) ){
				$query_args['tour_id'] = $_GET['tour_id'];
			}
			if( !empty($_GET['from_date']) ){
				$custom_condition = ' >= \'' . esc_sql($_GET['from_date']) . '\''; 
				if( !empty($_GET['to_date']) ){
					$custom_condition .= ' AND travel_date <= \'' . esc_sql($_GET['to_date']) . '\' ';
				}
				$query_args['travel_date'] = array( 
					'custom' => $custom_condition
				);
			}

			$results = tourmaster_get_booking_data($query_args, array(
				'paged' => $paged,
				'num-fetch' => $num_fetch
			));
			$max_num_page = ceil(tourmaster_get_booking_data($query_args, array(), 'COUNT(*)') / $num_fetch);
			if( !empty($_GET['export']) ){
				$export_results = tourmaster_get_booking_data($query_args, array(
					'num-fetch' => 9999
				));
				tourmaster_order_csv_export($export_results);
			}


			echo '<table class="tourmaster-table">';
			echo tourmaster_get_table_head(array(
				esc_html__('Order', 'tourmaster'),
				esc_html__('Contact Detail', 'tourmaster'),
				esc_html__('Travel Date', 'tourmaster'),
				esc_html__('Total', 'tourmaster'),
				esc_html__('Payment Status', 'tourmaster'),
				esc_html__('Action', 'tourmaster'),
			));
			$statuses = array(
				'all' => esc_html__('All', 'tourmaster'),
				'pending' => esc_html__('Pending', 'tourmaster'),
				'approved' => esc_html__('Approved', 'tourmaster'),
				'receipt-submitted' => esc_html__('Receipt Submitted', 'tourmaster'),
				'online-paid' => esc_html__('Online Paid', 'tourmaster'),
				'deposit-paid' => esc_html__('Deposit Paid', 'tourmaster'),
				'departed' => esc_html__('Departed', 'tourmaster'),
				'rejected' => esc_html__('Rejected', 'tourmaster'),
				'cancel' => esc_html__('Cancel', 'tourmaster'),
				'wait-for-approval' => __('Wait For Approval', 'tourmaster'),
			);

			foreach( $results as $result ){
				$auth_id = get_post_field( 'post_author', $result->tour_id );
				if($auth_id == $current_user->data->ID){ 
					$order_title  = '<div class="tourmaster-head" >#' . $result->id . '<span class="tourmaster-travel-date" > - ' . tourmaster_date_format($result->travel_date) . '</span>' . '</div>';
					$order_title .= '<div class="tourmaster-content" ><a href="' . add_query_arg(array('single'=>$result->id), remove_query_arg(array('order_id', 'from_date', 'to_date', 'action', 'id', 'export'))) . '" >';
					$order_title .= get_the_title($result->tour_id);
					$order_title .= '</a></div>';

					$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
					$buyer_info  = '<div class="tourmaster-head" >';
					$buyer_info .= empty($contact_detail['first_name'])? '': $contact_detail['first_name'] . ' ';
					$buyer_info .= empty($contact_detail['last_name'])? '': $contact_detail['last_name'] . ' ';
					$buyer_info .= '</div>';
					$buyer_info .= '<div class="tourmaster-content" >';
					$buyer_info .= empty($contact_detail['phone'])? '': $contact_detail['phone'] . ' ';
					$buyer_info .= empty($contact_detail['email'])? '': '<a href="mailto:' . esc_attr($contact_detail['email']) . '" ><i class="fa fa-envelope-o" ></i></a>';
					$buyer_info .= '</div>';

					$travel_date = $result->travel_date;

					$tour_price = tourmaster_money_format($result->total_price);

					$order_status  = '<span class="tourmaster-order-status tourmaster-status-' . esc_attr($result->order_status) . '" >';
					if( $result->order_status == 'approved' ){
						$order_status .= '<i class="fa fa-check" ></i>';
					}else if( $result->order_status == 'departed' ){
						$order_status .= '<i class="fa fa-check-circle-o" ></i>';
					}else if( $result->order_status == 'rejected' || $result->order_status == 'cancel' ){
						$order_status .= '<i class="fa fa-remove" ></i>';
					}	
					$order_status .= $statuses[$result->order_status];
					if( $result->order_status == 'pending' && empty($result->user_id) ){
						$order_status .= ' <br>' . esc_html__('(Via E-mail)', 'tourmaster');
					}
					$order_status .= '</span>';

					$action  = '<a href="' . add_query_arg(array('single'=>$result->id), remove_query_arg(array('id','action'))) . '" class="tourmaster-order-action-view" title="' . esc_html__('View', 'tourmaster') . '" >';
					$action .= '<i class="fa fa-eye" ></i>';
					$action .= '</a>';
					$action .= '<a href="' . add_query_arg(array('id'=>$result->id, 'action'=>'approved')) . '" class="tourmaster-order-action-approve" title="' . esc_html__('Approve', 'tourmaster') . '" ';
					$action .= 'data-confirm2="' . esc_html__('After approving the transaction, invoice and payment receipt will be sent to customer\'s billing email.', 'tourmaster') . '" ';
					$action .= '>';
					$action .= '<i class="fa fa-check" ></i>';
					$action .= '</a>';
					$action .= '<a href="' . add_query_arg(array('id'=>$result->id, 'action'=>'rejected')) . '" class="tourmaster-order-action-reject" title="' . esc_html__('Reject', 'tourmaster') . '" ';
					$action .= 'data-confirm2="' . esc_html__('After rejected the transaction, the rejection message will be sent to customer\'s contact email.', 'tourmaster') . '" ';
					$action .= '>';
					$action .= '<i class="fa fa-remove" ></i>';
					$action .= '</a>';
					$action .= '<a href="' . add_query_arg(array('id'=>$result->id, 'action'=>'remove')) . '" class="tourmaster-order-action-remove" title="' . esc_html__('Remove', 'tourmaster') . '" ';
					$action .= 'data-confirm2="' . esc_html__('The transaction you selected will be permanently removed from the system.', 'tourmaster') . '" ';
					$action .= '>';
					$action .= '<i class="fa fa-trash-o" ></i>';
					$action .= '</a>';

					tourmaster_get_table_content(array($order_title, $buyer_info, $travel_date, $tour_price, $order_status, $action));
				}

				
			}

			echo '</table>';

			if( !empty($max_num_page) && $max_num_page > 1 ){
				echo '<div class="tourmaster-transaction-pagination" >';
				for( $i = 1; $i <= $max_num_page; $i++ ){
					if( $i == $paged ){
						echo '<span class="tourmaster-transaction-pagination-item tourmaster-active" >' . $i . '</span>';
					}else{
						echo '<a href="' . add_query_arg(array('paged'=>$i), remove_query_arg(array('action'))) . '" class="tourmaster-transaction-pagination-item" >' . $i . '</a>';
					}
				}
				echo '</div>';
			}

		}
	}

	if( !function_exists('tourmaster_single_order_payment_action') ){
		function tourmaster_single_order_payment_action(){

			// for payment status
			if( !empty($_GET['single']) && isset($_GET['payment_info']) && $_GET['action'] ){
				$result = tourmaster_get_booking_data(array(
					'id' => $_GET['single']
				), array('single' => true));

				$order_status = $result->order_status;
				$payment_infos = json_decode($result->payment_info, true);
				$payment_infos = tourmaster_payment_info_format($payment_infos, $result->order_status);

				// email info
				$submission_date = '';
				$submission_amount = 0;
				$submission_transaction_id = '';
				if( !empty($payment_infos[$_GET['payment_info']]) ){
					if( !empty($payment_infos[$_GET['payment_info']]['submission_date']) ){
						$submission_date = $payment_infos[$_GET['payment_info']]['submission_date'];
					}	
					if( !empty($payment_infos[$_GET['payment_info']]['transaction_id']) ){
						$submission_transaction_id = $payment_infos[$_GET['payment_info']]['transaction_id'];
					}	
					if( !empty($payment_infos[$_GET['payment_info']]['deposit_price']) ){
						$submission_amount = $payment_infos[$_GET['payment_info']]['deposit_price'];
					}else if( !empty($payment_infos[$_GET['payment_info']]['amount']) ){
						$submission_amount = $payment_infos[$_GET['payment_info']]['amount'];
					}
				}
				
				// do an action
				if( $_GET['action'] == 'approve' ){
					$payment_infos[$_GET['payment_info']]['payment_status'] = 'paid';
				}else if( $_GET['action'] == 'remove' ){
					unset($payment_infos[$_GET['payment_info']]);
				}

				if( sizeof($payment_infos) == 0 ){
					$order_status = 'pending';
				}else{
					$paid_amount = 0;
					$payment_method = '';
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

						$payment_method = empty($payment_info['payment_method'])? $payment_method: $payment_info['payment_method'];
					}
				
					if( tourmaster_compare_price($result->total_price, $paid_amount) || $paid_amount >= $result->total_price ){
						if( $payment_method == 'receipt' ){
							$order_status = 'approved';
						}else{
							$order_status = 'online-paid';
						}
					}else{
						$order_status = 'deposit-paid';
					}
				}

				tourmaster_update_booking_data(
					array(
						'payment_info' => json_encode(array_values($payment_infos)),
						'order_status' => $order_status
					),
					array('id' => $_GET['single']),
					array('%s'),
					array('%d')
				); 

				tourmaster_set_locale($result->tour_id);
				if( $_GET['action'] == 'approve' ){
					tourmaster_mail_notification('receipt-approve-mail', $_GET['single'], '', array(
						'custom' => array(
							'payment-method' => 'receipt',
							'submission-date' => tourmaster_time_format($submission_date) . ' ' . tourmaster_date_format($submission_date),
							'submission-amount' => tourmaster_money_format($submission_amount),
							'transaction-id' => $submission_transaction_id
						)
					));
				}else if( $_GET['action'] == 'remove' ){
					tourmaster_mail_notification('receipt-reject-mail', $_GET['single'], '', array(
						'custom' => array(
							'payment-method' => 'receipt',
							'submission-date' => tourmaster_time_format($submission_date) . ' ' . tourmaster_date_format($submission_date),
							'submission-amount' => tourmaster_money_format($submission_amount),
							'transaction-id' => $submission_transaction_id
						)
					));
				}
				tourmaster_return_locale();

				wp_redirect(remove_query_arg(array('payment_info', 'action')));
			}
		}
	}

	if( !function_exists('tourmaster_get_single_order') ){
		function tourmaster_get_single_order(){

			tourmaster_single_order_payment_action();

			if( !empty($_GET['single']) && !empty($_GET['status']) ){

				$updated = tourmaster_update_booking_data(
					array('order_status' => $_GET['status']),
					array('id' => $_GET['single']),
					array('%s'),
					array('%d')
				);

				// send the mail
				if( !empty($updated) ){
					$result = tourmaster_get_booking_data(array('id' => $_GET['single']), array('single' => true));
					tourmaster_set_locale($result->tour_id);
					if( in_array($_GET['status'], array('approved', 'online-paid', 'deposit-paid')) ){
						tourmaster_mail_notification('payment-made-mail', $_GET['single']);
						tourmaster_send_email_invoice($_GET['single']);
					}else if( $_GET['status'] == 'rejected' ){
						tourmaster_mail_notification('booking-reject-mail', $_GET['single']);
					}else if( $_GET['status'] == 'pending' && $result->order_status == 'wait-for-approval' ){
						tourmaster_mail_notification('booking-approve-mail', $_GET['single']);
					}
					tourmaster_return_locale();
				}
			}else if( !empty($_GET['single']) && !empty($_GET['action']) && $_GET['action'] == 'send-invoice' ){
				$result = tourmaster_get_booking_data(array('id' => $_GET['single']), array('single' => true));
				tourmaster_set_locale($result->tour_id);
				tourmaster_send_email_invoice($_GET['single']);
				tourmaster_return_locale();
			}

			$result = tourmaster_get_booking_data(array(
				'id' => $_GET['single']
			), array('single' => true));

			$tour_option = tourmaster_get_post_meta($result->tour_id, 'tourmaster-tour-option');

			// from my-booking-single.php
			$contact_fields = tourmaster_get_payment_contact_form_fields($result->tour_id);
			$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
			$billing_detail = empty($result->billing_info)? array(): json_decode($result->billing_info, true);
			$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);

			// sidebar
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
				'cancel' => esc_html__('Cancel', 'tourmaster'),
				'wait-for-approval' => __('Wait For Approval', 'tourmaster'),
			);
			echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Order Status', 'tourmaster') . '</h3>';
			echo '<div class="tourmaster-booking-status tourmaster-status-' . esc_attr($result->order_status) . '" >';
			echo '<form action="' . add_query_arg(array('action' => 'update-status')) . '" method="GET" >';
			echo '<div class="tourmaster-custom-combobox" >';
			echo '<select name="status" >';
			foreach( $statuses as $status_slug => $status_title ){
				if( $status_slug == 'all' ) continue;
				echo '<option value="' . esc_attr($status_slug) . '" ' . ($status_slug == $result->order_status? 'selected': '') . '>';
				echo esc_html($status_title);
				if( $status_slug == 'pending' && empty($result->user_id) ){
					echo ' ' . esc_html__('(Via E-mail)', 'tourmaster');
				}
				echo '</option>';
			}
			echo '</select>';
			echo '</div>'; // tourmaster-combobox
			echo '<input class="tourmaster-button" id="tourmaster-update-booking-status" type="submit" value="' . esc_html__('Update Status', 'tourmaster') . '" />';
			if( !empty($_GET['page']) ){
				echo '<input name="page" type="hidden" value="' . esc_attr($_GET['page']) . '" />';
			}
			if( !empty($_GET['single']) ){
				echo '<input name="single" type="hidden" value="' . esc_attr($_GET['single']) . '" />';
			}
			echo '</form>';
			echo '</div>'; // tourmaster-booking-status
			
			$payment_infos = array();
			if( !empty($result->payment_info) ){

				echo '<h3 class="tourmaster-my-booking-single-sub-title">' . esc_html__('Bank Payment Receipt', 'tourmaster') . '</h3>';

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
					if( $payment_info['payment_status'] == 'pending' ){
						echo '<a href="' . add_query_arg(array('payment_info'=>($count-1), 'action'=>'approve')) . '" >';
						echo '<i class="fa fa-check-circle-o" ></i>' . esc_html__('Approve', 'tourmaster');
						echo '</a><br>';
					}
					echo '<a class="tourmaster-remove" href="' . esc_url(add_query_arg(array('payment_info'=>($count-1), 'action'=>'remove'))) . '" data-confirm >';
					echo '<i class="fa fa-times-circle-o" ></i>' . esc_html__('Reject / Remove', 'tourmaster');
					echo '</a><br><br>';
					tourmaster_deposit_item_content($result, $payment_info);
					echo '</div>';
					echo '</div>';
				}
			}
			echo '</div>'; // tourmaster-my-booking-single-sidebar

			// content
			echo '<div class="tourmaster-my-booking-single-content clearfix" >';
			echo '<div class="tourmaster-item-rvpdlr clearfix" >';
			echo '<div class="tourmaster-my-booking-single-order-summary-column tourmaster-column-20 tourmaster-item-pdlr" >';
			echo '<h3 class="tourmaster-my-booking-single-title">';
			echo esc_html__('Order Summary', 'tourmaster');
			echo tourmaster_order_edit_text('new-order');
			echo tourmaster_lightbox_content(array(
				'id' => 'new-order',
				'title' => esc_html__('Edit Order', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'new_order', $result)
			));	
			echo '</h3>';

			if( $result->order_status == 'pending' && empty($result->user_id) ){
				echo '<div class="tourmaster-my-booking-pending-via-email" >';
				echo esc_html__('This booking has been made manually via email. Customer won\'t see from their dashboard. You should contact back to customer manually.', 'tourmaster');
				echo '</div>';
			}

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
			echo '<span class="tourmaster-tail"><a href="' . get_permalink($result->tour_id) . '" target="_blank">' . get_the_title($result->tour_id) . '</a></span>';
			echo '</div>';

			echo '<div class="tourmaster-my-booking-single-field clearfix" >';
			echo '<span class="tourmaster-head">' . esc_html__('Travel Date', 'tourmaster') . ' :</span> ';
			echo '<span class="tourmaster-tail">' . tourmaster_date_format($result->travel_date) . '</span>';
			echo '</div>';

			if( !empty($booking_detail['package']) ){
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

			echo '<div class="tourmaster-my-booking-single-field tourmaster-additional-note clearfix" >';
			echo '<span class="tourmaster-head">';
			echo esc_html__('Customer\'s Note', 'tourmaster') . ' : ';
			echo tourmaster_order_edit_text('edit-additional-notes');
			echo tourmaster_lightbox_content(array(
				'id' => 'edit-additional-notes',
				'title' => esc_html__('Customer\'s Note', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'additional_notes', $result)
			));	
			echo '</span> ';
			echo '<span class="tourmaster-tail">';
			echo empty($contact_detail['additional_notes'])? '': $contact_detail['additional_notes'];
			echo '</span>';
			echo '</div>';
			//}
			echo '</div>'; // tourmaster-my-booking-single-order-summary-column

			echo '<div class="tourmaster-my-booking-single-contact-detail-column tourmaster-column-20 tourmaster-item-pdlr" >';
			echo '<h3 class="tourmaster-my-booking-single-title">';
			echo esc_html__('Contact Detail', 'tourmaster');
			echo tourmaster_order_edit_text('edit-contact-details');
			echo tourmaster_lightbox_content(array(
				'id' => 'edit-contact-details',
				'title' => esc_html__('Contact Details', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'contact_details', $result)
			));	
			echo '</h3>';
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
			echo '<h3 class="tourmaster-my-booking-single-title">';
			echo esc_html__('Billing Detail', 'tourmaster');
			echo tourmaster_order_edit_text('edit-billing-details');
			echo tourmaster_lightbox_content(array(
				'id' => 'edit-billing-details',
				'title' => esc_html__('Billing Details', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'billing_details', $result)
			));	
			echo '</h3>';
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
			if( !empty($tour_option['require-each-traveller-info']) && $tour_option['require-each-traveller-info'] == 'enable' ){
				echo '<div class="tourmaster-my-booking-single-traveller-info" >';
				echo '<h3 class="tourmaster-my-booking-single-title">';
				echo esc_html__('Traveller Info', 'tourmaster');
				echo tourmaster_order_edit_text('edit-traveller');
				echo tourmaster_lightbox_content(array(
					'id' => 'edit-traveller',
					'title' => esc_html__('Traveller', 'tourmaster'),
					'content' => tourmaster_order_edit_form($_GET['single'], 'traveller', $result, $tour_option)
				));	
				echo '</h3>';

				if( !empty($traveller_info['first_name']) ){
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
				echo '<h3 class="tourmaster-my-booking-single-title">';
				echo esc_html__('Price Breakdown', 'tourmaster');
				echo tourmaster_order_edit_text('edit-price');
				echo tourmaster_lightbox_content(array(
					'id' => 'edit-price',
					'title' => esc_html__('Price', 'tourmaster'),
					'content' => tourmaster_order_edit_form($_GET['single'], 'price', $result, $tour_option)
				));	
				echo '</h3>';
				echo tourmaster_get_tour_price_breakdown($pricing_info['price-breakdown']);

				echo '<div class="tourmaster-my-booking-single-total-price clearfix" >';
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Total', 'tourmaster') . '</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_money_format($result->total_price) . '</span>';
				echo '</div>';
				echo '</div>';
				echo '</div>'; // tourmaster-my-booking-single-traveller-info
			}

			echo '<a class="tourmaster-button tourmaster-resend-invoice" href="' . esc_url(add_query_arg(array('action'=>'send-invoice'))) . '" >' . esc_html__('Resend Invoice', 'tourmaster') . '</a>';

			echo '</div>'; // tourmaster-my-booking-single-content

		}
	}

	if( !function_exists('tourmaster_get_single_order_2') ){
		function tourmaster_get_single_order_2(){

			tourmaster_single_order_payment_action();

			if( !empty($_GET['single']) && !empty($_GET['status']) ){

				$updated = tourmaster_update_booking_data(
					array('order_status' => $_GET['status']),
					array('id' => $_GET['single']),
					array('%s'),
					array('%d')
				);

				// send the mail
				if( !empty($updated) ){
					$result = tourmaster_get_booking_data(array('id' => $_GET['single']), array('single' => true));
					if( in_array($_GET['status'], array('approved', 'online-paid', 'deposit-paid')) ){
						tourmaster_mail_notification('payment-made-mail', $_GET['single']);
						tourmaster_send_email_invoice($_GET['single']);
					}else if( $_GET['status'] == 'rejected' ){
						tourmaster_mail_notification('booking-reject-mail', $_GET['single']);
					}else if( $_GET['status'] == 'pending' && $result->order_status == 'wait-for-approval' ){
						tourmaster_mail_notification('booking-approve-mail', $_GET['single']);
					}
				}
			}else if( !empty($_GET['single']) && !empty($_GET['action']) && $_GET['action'] == 'send-invoice' ){
				$result = tourmaster_get_booking_data(array('id' => $_GET['single']), array('single' => true));
				tourmaster_set_locale($result->tour_id);
				tourmaster_send_email_invoice($_GET['single']);
				tourmaster_return_locale();
			}

			$result = tourmaster_get_booking_data(array(
				'id' => $_GET['single']
			), array('single' => true));

			$tour_option = tourmaster_get_post_meta($result->tour_id, 'tourmaster-tour-option');

			// from my-booking-single.php
			$contact_fields = tourmaster_get_payment_contact_form_fields($result->tour_id);
			$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
			$billing_detail = empty($result->billing_info)? array(): json_decode($result->billing_info, true);
			$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);

			// sidebar
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
				'cancel' => esc_html__('Cancel', 'tourmaster'),
				'wait-for-approval' => __('Wait For Approval', 'tourmaster'),
			);
			echo '<h3 class="tourmaster-my-booking-single-title">' . esc_html__('Order Status', 'tourmaster') . '</h3>';
			echo '<div class="tourmaster-booking-status tourmaster-status-' . esc_attr($result->order_status) . '" >';
			echo '<form action="' . add_query_arg(array('action' => 'update-status')) . '" method="GET" >';
			if( !empty($_GET['page_type']) ){
				echo '<input name="page_type" type="hidden" value="' . esc_attr($_GET['page_type']) . '" />';
			}
			if( !empty($_GET['single']) ){
				echo '<input name="single" type="hidden" value="' . esc_attr($_GET['single']) . '" />';
			}
			echo '<div class="tourmaster-custom-combobox" >';
			echo '<select name="status" >';
			foreach( $statuses as $status_slug => $status_title ){
				if( $status_slug == 'all' ) continue;
				echo '<option value="' . esc_attr($status_slug) . '" ' . ($status_slug == $result->order_status? 'selected': '') . '>';
				echo esc_html($status_title);
				if( $status_slug == 'pending' && empty($result->user_id) ){
					echo ' ' . esc_html__('(Via E-mail)', 'tourmaster');
				}
				echo '</option>';
			}
			echo '</select>';
			echo '</div>'; // tourmaster-combobox
			echo '<input class="tourmaster-button" id="tourmaster-update-booking-status" type="submit" value="' . esc_html__('Update Status', 'tourmaster') . '" />';
			echo '</form>';
			echo '</div>'; // tourmaster-booking-status
			
			$payment_infos = array();
			if( !empty($result->payment_info) ){

				echo '<h3 class="tourmaster-my-booking-single-sub-title">' . esc_html__('Bank Payment Receipt', 'tourmaster') . '</h3>';

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
					if( $payment_info['payment_status'] == 'pending' ){
						echo '<a href="' . add_query_arg(array('payment_info'=>($count-1), 'action'=>'approve')) . '" >';
						echo '<i class="fa fa-check-circle-o" ></i>' . esc_html__('Approve', 'tourmaster');
						echo '</a><br>';
					}
					echo '<a class="tourmaster-remove" href="' . esc_url(add_query_arg(array('payment_info'=>($count-1), 'action'=>'remove'))) . '" data-confirm >';
					echo '<i class="fa fa-times-circle-o" ></i>' . esc_html__('Reject / Remove', 'tourmaster');
					echo '</a><br><br>';
					tourmaster_deposit_item_content($result, $payment_info);
					echo '</div>';
					echo '</div>';
				}
			}
			echo '</div>'; // tourmaster-my-booking-single-sidebar

			// content
			echo '<div class="tourmaster-my-booking-single-content clearfix" >';
			echo '<div class="tourmaster-item-rvpdlr clearfix" >';
			echo '<div class="tourmaster-my-booking-single-order-summary-column tourmaster-column-20 tourmaster-item-pdlr" >';
			echo '<h3 class="tourmaster-my-booking-single-title">';
			echo esc_html__('Order Summary', 'tourmaster');
			echo tourmaster_order_edit_text_2('new-order');
			echo tourmaster_lightbox_content_2(array(
				'id' => 'new-order',
				'title' => esc_html__('Edit Order', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'new_order', $result)
			));	
			echo '</h3>';

			if( $result->order_status == 'pending' && empty($result->user_id) ){
				echo '<div class="tourmaster-my-booking-pending-via-email" >';
				echo esc_html__('This booking has been made manually via email. Customer won\'t see from their dashboard. You should contact back to customer manually.', 'tourmaster');
				echo '</div>';
			}

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
			echo '<span class="tourmaster-tail"><a href="' . get_permalink($result->tour_id) . '" target="_blank">' . get_the_title($result->tour_id) . '</a></span>';
			echo '</div>';

			echo '<div class="tourmaster-my-booking-single-field clearfix" >';
			echo '<span class="tourmaster-head">' . esc_html__('Travel Date', 'tourmaster') . ' :</span> ';
			echo '<span class="tourmaster-tail">' . tourmaster_date_format($result->travel_date) . '</span>';
			echo '</div>';

			if( !empty($booking_detail['package']) ){
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

			echo '<div class="tourmaster-my-booking-single-field tourmaster-additional-note clearfix" >';
			echo '<span class="tourmaster-head">';
			echo esc_html__('Customer\'s Note', 'tourmaster') . ' : ';
			echo tourmaster_order_edit_text_2('edit-additional-notes');
			echo tourmaster_lightbox_content_2(array(
				'id' => 'edit-additional-notes',
				'title' => esc_html__('Customer\'s Note', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'additional_notes', $result)
			));	
			echo '</span> ';
			echo '<span class="tourmaster-tail">';
			echo empty($contact_detail['additional_notes'])? '': $contact_detail['additional_notes'];
			echo '</span>';
			echo '</div>';
			//}
			echo '</div>'; // tourmaster-my-booking-single-order-summary-column

			echo '<div class="tourmaster-my-booking-single-contact-detail-column tourmaster-column-20 tourmaster-item-pdlr" >';
			echo '<h3 class="tourmaster-my-booking-single-title">';
			echo esc_html__('Contact Detail', 'tourmaster');
			echo tourmaster_order_edit_text_2('edit-contact-details');
			echo tourmaster_lightbox_content_2(array(
				'id' => 'edit-contact-details',
				'title' => esc_html__('Contact Details', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'contact_details', $result)
			));	
			echo '</h3>';
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
			echo '<h3 class="tourmaster-my-booking-single-title">';
			echo esc_html__('Billing Detail', 'tourmaster');
			echo tourmaster_order_edit_text_2('edit-billing-details');
			echo tourmaster_lightbox_content_2(array(
				'id' => 'edit-billing-details',
				'title' => esc_html__('Billing Details', 'tourmaster'),
				'content' => tourmaster_order_edit_form($_GET['single'], 'billing_details', $result)
			));	
			echo '</h3>';
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
			if( !empty($tour_option['require-each-traveller-info']) && $tour_option['require-each-traveller-info'] == 'enable' ){
				echo '<div class="tourmaster-my-booking-single-traveller-info" >';
				echo '<h3 class="tourmaster-my-booking-single-title">';
				echo esc_html__('Traveller Info', 'tourmaster');
				echo tourmaster_order_edit_text_2('edit-traveller');
				echo tourmaster_lightbox_content_2(array(
					'id' => 'edit-traveller',
					'title' => esc_html__('Traveller', 'tourmaster'),
					'content' => tourmaster_order_edit_form($_GET['single'], 'traveller', $result, $tour_option)
				));	
				echo '</h3>';

				if( !empty($traveller_info['first_name']) ){
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
				echo '<h3 class="tourmaster-my-booking-single-title">';
				echo esc_html__('Price Breakdown', 'tourmaster');
				echo tourmaster_order_edit_text_2('edit-price');
				echo tourmaster_lightbox_content_2(array(
					'id' => 'edit-price',
					'title' => esc_html__('Price', 'tourmaster'),
					'content' => tourmaster_order_edit_form($_GET['single'], 'price', $result, $tour_option)
				));	
				echo '</h3>';
				echo tourmaster_get_tour_price_breakdown($pricing_info['price-breakdown']);

				echo '<div class="tourmaster-my-booking-single-total-price clearfix" >';
				echo '<div class="tourmaster-my-booking-single-field clearfix" >';
				echo '<span class="tourmaster-head">' . esc_html__('Total', 'tourmaster') . '</span> ';
				echo '<span class="tourmaster-tail">' . tourmaster_money_format($result->total_price) . '</span>';
				echo '</div>';
				echo '</div>';
				echo '</div>'; // tourmaster-my-booking-single-traveller-info
			}

			echo '<a class="tourmaster-button tourmaster-resend-invoice" href="' . esc_url(add_query_arg(array('action'=>'send-invoice'))) . '" >' . esc_html__('Resend Invoice', 'tourmaster') . '</a>';

			echo '</div>'; // tourmaster-my-booking-single-content

		}
	}
