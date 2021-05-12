<?php
/*

	/* Custom functions and variables */
	$current_user = wp_get_current_user();
	$current_id = $current_user->ID;
	if(isset($_POST['add-trip'])){
		$new_post = array(
			'post_author'   => $current_id,
			'post_status' => 'publish',
			'post_type' => 'tour',
			'post_title' => $_POST['add-trip-value'],
			'post_content' => ' '
		);
		$new_post_i = wp_insert_post($new_post);
		$category = get_user_meta($current_id, "tour_category");
		$latitude = get_user_meta($current_id, "latitude");
		$longitude = get_user_meta($current_id, "longitude");
		$city = get_user_meta($current_id, "city");
		$state = get_user_meta($current_id, "state");
		$country = get_user_meta($current_id, "country");
		$address = get_user_meta($current_id, "address");
		add_post_meta($new_post_i, 'tour_category', $category);
		add_post_meta($new_post_i, 'latitude', $latitude[0]);
		add_post_meta($new_post_i, 'longitude', $longitude[0]);
		add_post_meta($new_post_i, 'city', $city[0]);
		add_post_meta($new_post_i, 'state', $state[0]);
		add_post_meta($new_post_i, 'country', $country[0]);
		add_post_meta($new_post_i, 'address', $address[0]);
		add_post_meta($new_post_i, 'tour-available', $arrayToUpdate[date("Y-m-d")] = array("1" => '1', "2" => '0', "3" => '0', "4" => '0' ));
		add_post_meta($new_post_i, 'tour-price-text', '');
		add_post_meta($new_post_i, 'name', $_POST['add-trip-value']);
		$new_post_url = add_query_arg(array(
			'page_type' => 'my-tours', 
			'sub_page' => 'single',
			'id' => $new_post_i
		));		
		echo '<script type="text/javascript">location.href="'.get_bloginfo('url').$new_post_url.'"</script>';	
	}


	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-my-booking" >';
	tourmaster_get_user_breadcrumb();

	//Add button

	echo '<div><input id="add-trip" type="submit" value="Add trip" /></div>';
	echo '<form method="post" id="insert-trip" style="display:none" class="tourmaster-form-field"><div class="tourmaster-head-2">Insert Title</div><div class="tourmaster-tail-2 clearfix"><input type="text" name="add-trip-value" value=""><div class="tourmaster-tail-3 clearfix"><input type="submit" name="add-trip" value="Insert"></div></div></form>';
	

	// booking table block
	tourmaster_user_content_block_start();

	echo '<table class="tourmaster-my-booking-table tourmaster-table" >';

	tourmaster_get_table_head(array(
		esc_html__('Trip Name', 'tourmaster'),
		esc_html__('Trip Category', 'tourmaster'),
		esc_html__('Trip Price', 'tourmaster'),
		esc_html__('Trip Options', 'tourmaster'),
	));

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$args = array(
		'author' => get_current_user_id(),
		'post_status' => 'publish',
		'post_type' => 'tour',
		'posts_per_page' => 10000,
        'paged' => $paged
	);
	    
	$query = new WP_Query($args);
	$results = $query->get_posts();	
 
	foreach( $results as $result ){

		$single_trip_url = add_query_arg(array(
			'page_type' => 'my-tours',
			'sub_page' => 'single',
			'id' => $result->ID
		));

		$terms_cat = get_the_terms($result->ID, 'tour_category');
		$terms_cat = get_post_meta($result->ID, 'tour_category');

		$tour_price = 0;

		$meta = get_post_meta($result->ID,"tourmaster-tour-option");
	
		foreach($meta[0] as $key=>$value){
			if($key == "tour-price-text"){
				$tour_price = $value;
			}
		} 
		
		$counter=0;
		$slug="";
		$slug1="";
		if (empty($terms_cat)) {

		} else {
			foreach( $terms_cat as $term ) {
				$counter +=	 1;
				if ($counter == 1) {
					$slug = $term->slug;
					$slug1 = $term->slug;
				} if($counter == 2) {
					$slug = $slug1."/".$term->slug;
				}
			}	
		}
		

		$category = '<a class="tourmaster-my-booking-title">'.  $terms_cat[0][0] .'</a>';


		$title = '<a class="tourmaster-my-booking-title" href="javascript:void(0);">' . get_the_title($result->ID) . '</a>';
		
		$button = '<a class="mytours-edit" href="' . esc_url($single_trip_url) . '"><i class="fa fa-edit"></i></a><a class="mytours-delete" href="' . wp_nonce_url( get_bloginfo('url') . "/wp-admin/post.php?action=delete&amp;post=" . $result->ID, 'delete-post_' . $result->ID) . '"><i class="fa fa-trash"></i></a>';

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
			$category,
			'<span class="tourmaster-my-booking-price" >' . tourmaster_money_format($tour_price) . '</span>',
			$button 
		));
	}

	echo '</table>';
	tourmaster_user_content_block_end();

	echo '</div>'; // tourmaster-user-content-inner 