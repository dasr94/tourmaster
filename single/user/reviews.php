<?php
	// print the page content
	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-review" >';
	tourmaster_get_user_breadcrumb();

	// booking table block
	tourmaster_user_content_block_start();

	// query 
	$conditions = array(
		'user_id' => $current_user->data->ID,
		'order_status' => 'departed'
	);
	$results = tourmaster_get_booking_data($conditions, array('with-review' => true));

	if( !empty($results) ){

		echo '<table class="tourmaster-user-review-table tourmaster-table" >';
		tourmaster_get_table_head(array(
			esc_html__('Tour Name', 'tourmaster'),
			esc_html__('Status', 'tourmaster'),
			esc_html__('Action', 'tourmaster'),
		));		
		foreach( $results as $result ){
			$tour_option = tourmaster_get_post_meta($result->tour_id, 'tourmaster-tour-option');

			if( empty($tour_option['enable-review']) || $tour_option['enable-review'] == 'enable' ){
				$title = get_the_title($result->tour_id);
				
				if( $result->review_score == '' ){
					$status  = '<span class="tourmaster-user-review-status tourmaster-status-pending" >';	
					$status .= esc_html__('Pending', 'tourmaster');
					$status .= '</span>';

					$action  = '<span class="tourmaster-user-review-action" data-tmlb="submit-review" >' . esc_html__('Submit Review', 'tourmaster') . '</span>';
					$action .= tourmaster_lightbox_content(array(
						'id' => 'submit-review',
						'title' => esc_html__('Submit Your Review', 'tourmaster'),
						'content' => tourmaster_get_review_form( $result )
					));
				}else{
					$status  = '<span class="tourmaster-user-review-status tourmaster-status-submitted" >';	
					$status .= esc_html__('Submitted', 'tourmaster');
					$status .= '</span>';

					$action  = '<span class="tourmaster-user-review-action" data-tmlb="view-review" >' . esc_html__('View Review', 'tourmaster') . '</span>';		
					$action .= tourmaster_lightbox_content(array(
						'id' => 'view-review',
						'title' => esc_html__('Your Review', 'tourmaster'),
						'content' => tourmaster_get_submitted_review( $result )
					));
				}

				tourmaster_get_table_content(array($title, $status, $action));
			}
		}
		echo '</table>';

	}else{
		// no review message
	}
	
	tourmaster_user_content_block_end();

	echo '</div>'; // tourmaster-user-content-inner