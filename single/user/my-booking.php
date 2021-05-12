<?php
	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-my-booking" >';
	tourmaster_get_user_breadcrumb();

	// filter
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
	echo '<div class="tourmaster-my-booking-filter" >';
	foreach( $statuses as $status_slug => $status ){
		echo '<span class="tourmaster-sep">|</span>';
		echo '<a ';
		if( $status_slug == 'all' && (empty($_GET['status']) || $_GET['status'] == 'all') ){
			echo ' class="tourmaster-active" ';
		}else if( !empty($_GET['status']) && $_GET['status'] == $status_slug ){
			echo ' class="tourmaster-active" ';
		}
		echo 'href="' . esc_url(add_query_arg(array('status'=>$status_slug))) . '" >' . $status . '</a>';
	}
	echo '</div>'; // tourmaster-my-booking-filter

	// booking table block
	tourmaster_user_content_block_start();

	echo '<table class="tourmaster-my-booking-table tourmaster-table" >';

	tourmaster_get_table_head(array(
		esc_html__('Tour Name', 'tourmaster'),
		esc_html__('Travel Date', 'tourmaster'),
		esc_html__('Total', 'tourmaster'),
		esc_html__('Payment Status', 'tourmaster'),
	));

	// query 
	global $current_user;
	$conditions = array(
		'user_id' => $current_user->data->ID,
	);
	if( !empty($_GET['status']) && $_GET['status'] != 'all' && !empty($statuses[$_GET['status']]) ){
		$conditions['order_status'] = $_GET['status'];
	}else{
		$conditions['order_status'] = array(
			'condition' => '!=',
			'value' => 'cancel'
		);
	}
	$results = tourmaster_get_booking_data($conditions);

	foreach( $results as $result ){

		$single_booking_url = add_query_arg(array(
			'page_type' => 'my-booking',
			'sub_page' => 'single',
			'id' => $result->id,
			'tour_id' => $result->tour_id
		));
		$title = '<a class="tourmaster-my-booking-title" href="' . esc_url($single_booking_url) . '" >' . get_the_title($result->tour_id) . '</a>';

		$status  = '<span class="tourmaster-my-booking-status tourmaster-booking-status tourmaster-status-' . esc_attr($result->order_status) . '" >';
		if( $result->order_status == 'approved' ){
			$status .= '<i class="fa fa-check" ></i>';
		}else if( $result->order_status == 'departed' ){
			$status .= '<i class="fa fa-check-circle-o" ></i>';
		}else if( $result->order_status == 'rejected' ){
			$status .= '<i class="fa fa-remove" ></i>';
		}		
		$status .= $statuses[$result->order_status];
		$status .= '</span>';
		if( in_array($result->order_status, array('pending', 'receipt-submitted', 'rejected')) ){
			$status .= '<a class="tourmaster-my-booking-action fa fa-dollar" title="' . esc_html__('Make Payment', 'tourmaster') . '" href="' . esc_url($single_booking_url) . '" ></a>';
		}
		if( in_array($result->order_status, array('pending', 'receipt-submitted', 'rejected')) ){
			$status .= '<a class="tourmaster-my-booking-action fa fa-remove" title="' . esc_html__('Cancel', 'tourmaster') . '" href="' . add_query_arg(array('action'=>'remove', 'id'=>$result->id)) . '" ';
			$status .= ' data-confirm="' . esc_html__('Just To Confirm', 'tourmaster') . '" ';
			$status .= ' data-confirm-yes="' . esc_html__('Yes', 'tourmaster') . '" ';
			$status .= ' data-confirm-no="' . esc_html__('No', 'tourmaster') . '" ';
			$status .= ' data-confirm-text="' . esc_html__('Are you sure you want to do this ?', 'tourmaster') . '" ';
			$status .= ' data-confirm-sub="' . esc_html__('The transaction you selected will be permanently removed from the system.', 'tourmaster') . '" ';
			$status .= ' ></a>';
		}

		tourmaster_get_table_content(array(
			$title,
			tourmaster_date_format($result->travel_date),
			'<span class="tourmaster-my-booking-price" >' . tourmaster_money_format($result->total_price) . '</span>',
			$status
		));
	}

	echo '</table>';
	tourmaster_user_content_block_end();

	echo '</div>'; // tourmaster-user-content-inner