<?php
	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-my-booking" >';
	tourmaster_get_user_breadcrumb();

    echo '<div>';
    
    echo '<a class="tourmaster-new-order-text add-order-btn" href="#" data-tmlb2="cnew-order">';
    echo esc_html__('Add New Order', 'tourmaster');
	echo '</a>';
	
	$args = array(
		'author' => get_current_user_id(),
		'post_status' => 'publish',
		'post_type' => 'tour',
		'posts_per_page' => 10000,
        'paged' => $paged
	);

    echo tourmaster_lightbox_content_2(array(
        'id' => 'cnew-order',
        'title' => esc_html__('Create Order', 'tourmaster'),
        'content' => tourmaster_order_edit_form2('', 'new_order', array(),  array(), get_current_user_id())
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
	<?php				
				}
				tourmaster_user_content_block_start();

				if( isset($_GET['single']) ){
					tourmaster_get_single_order_2();
				}else{
					tourmaster_get_order_list2();
				}
				tourmaster_user_content_block_end();

    echo '</div>';

	echo '</div>'; // tourmaster-user-content-inner
