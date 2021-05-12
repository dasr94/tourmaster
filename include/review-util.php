<?php
	/*	
	*	Utility function for uses
	*/

	// ajax review list
	add_action('wp_ajax_get_single_tour_review', 'tourmaster_get_single_tour_review');
	add_action('wp_ajax_nopriv_get_single_tour_review', 'tourmaster_get_single_tour_review');
	if( !function_exists('tourmaster_get_single_tour_review') ){
		function tourmaster_get_single_tour_review(){

			// sort_by
			// filter_by
			if( !empty($_POST['tour_id']) ){
				$paged = (empty($_POST['paged'])? '1': $_POST['paged']);
				$review_num_fetch = apply_filters('tourmaster_review_num_fetch', 5);
				$review_args = array(
					'review_tour_id' => $_POST['tour_id'], 
					'review_score' => 'IS NOT NULL',
					'order_status' => array(
						'hide-prefix' => true,
						'custom' => ' (order_status IS NULL OR order_status != \'cancel\') '
					)
				);
				$review_settings = array(
					'only-review' => true,
					'num-fetch' => $review_num_fetch,
					'paged' => $paged,
					'orderby' => 'review_date',
					'order' => 'desc'
				);
				if( !empty($_POST['filter_by']) ){
					$review_args['review_type'] = $_POST['filter_by'];
				}

				if( empty($_POST['sort_by']) || $_POST['sort_by'] == 'date' ){
					$review_settings['orderby'] = 'review_date';
				}else if( $_POST['sort_by'] == 'rating' ){
					$review_settings['orderby'] = 'review_score';
				}

				$results = tourmaster_get_booking_data($review_args, $review_settings);
				$max_num_page = intval(tourmaster_get_booking_data($review_args, array('only-review' => true), 'COUNT(*)')) / $review_num_fetch;

				die(json_encode(array(
					'content' => tourmaster_get_review_content_list($results) .
						tourmaster_get_review_content_pagination($max_num_page, $paged)
				)));
			}

			die(json_encode(array()));

		} // tourmaster_get_single_tour_review
	}

	// review content
	if( !function_exists('tourmaster_get_review_content_list') ){
		function tourmaster_get_review_content_list( $query, $editable = false ){
			
			$ret  = '';
			foreach( $query as $result ){
				
				$user_id = '';
				$avatar = '';
				if( !empty($result->user_id) ){
					$user_id = $result->user_id;
					$avatar = get_the_author_meta('tourmaster-user-avatar', $user_id);
				}else if( !empty($result->reviewer_email) ){
					$user_id = $result->reviewer_email;
				}

				$reviewer_name = '';
				if( !empty($result->user_id) ){
					$reviewer_name = tourmaster_get_user_meta($result->user_id);
				}else if( !empty($result->reviewer_name) ){
					$reviewer_name = $result->reviewer_name;
				}

				$ret .= '<div class="tourmaster-single-review-content-item clearfix" >';
				$ret .= '<div class="tourmaster-single-review-user clearfix" >';
				if( !empty($user_id) ){
					$ret .= '<div class="tourmaster-single-review-avatar tourmaster-media-image" >';
					if( !empty($avatar['thumbnail']) ){
						$ret .= '<img src="' . esc_url($avatar['thumbnail']) . '" alt="profile-image" />';
					}else if( !empty($avatar['file_url']) ){
						$ret .= '<img src="' . esc_url($avatar['file_url']) . '" alt="profile-image" />';
					}else{
						$ret .= get_avatar($user_id, 85);
					}
					$ret .= '</div>'; 
				}
				$ret .= '<h4 class="tourmaster-single-review-user-name" >' . $reviewer_name . '</h4>';
				$ret .= '<div class="tourmaster-single-review-user-type" >';
				if( $result->review_type == 'solo' ){
					$ret .= esc_html__('Solo Traveller', 'tourmaster');
				}else if( $result->review_type == 'couple' ){
					$ret .= esc_html__('Couple Traveller', 'tourmaster');
				}else if( $result->review_type == 'family' ){
					$ret .= esc_html__('Family Traveller', 'tourmaster');
				}else if( $result->review_type == 'group' ){
					$ret .= esc_html__('Group Traveller', 'tourmaster');
				}
				$ret .= '</div>'; // tourmaster-single-review-user-type
				$ret .= '</div>'; // tourmaster-single-review-user

				$ret .= '<div class="tourmaster-single-review-detail" >';
				if( !empty($result->review_description) ){
					$ret .= '<div class="tourmaster-single-review-detail-description" >' . tourmaster_content_filter($result->review_description) . '</div>';
				}
				$ret .= '<div class="tourmaster-single-review-detail-rating" >' . tourmaster_get_rating($result->review_score) . '</div>';
				$ret .= '<div class="tourmaster-single-review-detail-date" >' . tourmaster_date_format($result->review_date) . '</div>';
				
				if( $editable ){
					$ret .= '<div class="tourmaster-single-review-editable" >';
					$ret .= '<div class="tourmaster-single-review-edit" data-id="' . esc_attr($result->review_id) . '" ><i class="fa fa-edit" ></i>' . esc_html__('Edit', 'tourmaster') . '</div>';
					$ret .= '<div class="tourmaster-single-review-remove" data-id="' . esc_attr($result->review_id) . '" ><i class="fa fa-remove" ></i>' . esc_html__('Remove', 'tourmaster') . '</div>';
					$ret .= '</div>';
				}
				$ret .= '</div>'; // tourmaster-single-review-detail
				$ret .= '</div>'; // tourmaster-single-review-content-item
			}

			return $ret;
		} // tourmaster_get_review_content_list
	}
	if( !function_exists('tourmaster_get_review_content_pagination') ){
		function tourmaster_get_review_content_pagination( $max_num_page, $current_page = 1 ){

			$ret = '';
			if( !empty($max_num_page) && $max_num_page > 1 ){
				$ret .= '<div class="tourmaster-review-content-pagination" >';
				if( $current_page > 1 ){
					$ret .= '<span data-paged="' . esc_attr($current_page-1) . '" ><i class="fa fa-angle-left" ></i></span>';
				}
				for( $i = 1; $i <= $max_num_page; $i++ ){
					if( $i == $current_page ){
						$ret .= '<span class="tourmaster-active" >' . $i . '</span>';
					}else{
						$ret .= '<span data-paged="' . esc_attr($i) . '" >' . $i . '</span>';
					}
				}
				if( $current_page < $max_num_page ){
					$ret .= '<span data-paged="' . esc_attr($current_page+1) . '" ><i class="fa fa-angle-right" ></i></span>';
				}
				$ret .= '</div>';
			}

			return $ret;
		} // tourmaster_get_review_content_pagination
	}

	// review form
	if( !function_exists('tourmaster_get_review_form') ){
		function tourmaster_get_review_form( $query, $is_admin = false, $value = array() ){
			ob_start();
?>

<?php if( $is_admin ){ ?>
<div class="tourmaster-review-form tourmaster-form-field tourmaster-with-border" >
	<?php if( $is_admin === true ){ ?>
		<div class="tourmaster-review-form-name" >
			<span class="tourmaster-head" ><?php echo esc_html__('Reviewer Name', 'tourmaster'); ?></span>
			<input type="text" name="review-name" />
		</div>
		<div class="tourmaster-review-form-email" >	
			<span class="tourmaster-head" ><?php echo esc_html__('Reviewer Email (For Gravatar Profile Picture)', 'tourmaster'); ?></span>
			<input type="text" name="review-email" />
		</div>
	<?php } ?>
<?php }else{ ?> 
<form class="tourmaster-review-form tourmaster-form-field tourmaster-with-border" method="POST" >
	<div class="tourmaster-review-form-title">
		<span class="tourmaster-head" ><?php echo esc_html__('Tour Name :', 'tourmaster'); ?></span>
		<?php echo get_the_title($query->tour_id) ?>
	</div> 	
<?php } ?>
	<div class="tourmaster-review-form-description" >
		<div class="tourmaster-head" ><?php echo esc_html__('What do you say about this tour? *', 'tourmaster'); ?></div>
		<textarea name="description" ><?php echo empty($value['description'])? '': esc_textarea($value['description']); ?></textarea>
	</div>
	<div class="tourmaster-review-form-traveller-type" >
		<div class="tourmaster-head" ><?php echo esc_html__('Which traveller type were you on this tour? *', 'tourmaster'); ?></div>
		<div class="tourmaster-combobox-wrap">
			<select name="traveller-type" >
				<option value="solo" <?php echo (!empty($value['traveller-type']) && $value['traveller-type'] == 'solo')? 'selected': ''; ?> ><?php echo esc_html__('Solo', 'tourmaster'); ?></option>
				<option value="couple" <?php echo (!empty($value['traveller-type']) && $value['traveller-type'] == 'couple')? 'selected': ''; ?> ><?php echo esc_html__('Couple', 'tourmaster'); ?></option>
				<option value="family" <?php echo (!empty($value['traveller-type']) && $value['traveller-type'] == 'family')? 'selected': ''; ?> ><?php echo esc_html__('Family', 'tourmaster'); ?></option>
				<option value="group" <?php echo (!empty($value['traveller-type']) && $value['traveller-type'] == 'group')? 'selected': ''; ?> ><?php echo esc_html__('Group', 'tourmaster'); ?></option>
			</select>
		</div>
	</div>
	<div class="tourmaster-review-form-rating-wrap" >
		<div class="tourmaster-head" ><?php echo esc_html__('Rate this tour *', 'tourmaster'); ?></div>
		<div class="tourmaster-review-form-rating clearfix" >
		<?php
			$rating_value = empty($value['rating'])? 10: intval($value['rating']);

			for( $i = 1; $i <= 10; $i++ ){
				if( $i % 2 == 0 ){
					echo '<span class="tourmaster-rating-select" data-rating-score="' . esc_attr($i) . '" ></span>';
				}else{
					echo '<i class="tourmaster-rating-select ';
					if( $rating_value == $i ){
						echo 'fa fa-star-half-empty';
					}else if( $rating_value > $i ){
						echo 'fa fa-star';
					}else{
						echo 'fa fa-star-o';
					}
					echo '" data-rating-score="' . esc_attr($i) . '" ></i>';
				}
			}

			echo '<input type="hidden" name="rating" value="' . esc_attr($rating_value) . '" />';
		?>
		</div>
	</div>
<?php if( $is_admin ){ ?>	
	<div class="tourmaster-review-form-date" >	
		<span class="tourmaster-head" ><?php echo esc_html__('Published Date', 'tourmaster'); ?></span>
		<input type="text" class="tourmaster-html-option-datepicker" name="review-published-date" value="<?php
			if( !empty($value['published-date']) ){
				echo esc_attr(date('Y-m-d', strtotime($value['published-date'])));
			}
		?>" />
		<?php if( $is_admin === true ){ ?>
			<input type="hidden" name="review_action" value="tourmaster_admin_add_review" />
			<input type="hidden" name="tour_id" value="<?php echo esc_attr(get_the_ID()); ?>" />
		<?php }else{ ?>
			<input type="hidden" name="review_action" value="tourmaster_admin_edit_review" />
			<input type="hidden" name="review_id" value="<?php echo esc_attr($value['review-id']); ?>" />
		<?php } ?>
	</div>
	<input class="tourmaster-button tourmaster-submit-review" data-ajax-url="<?php echo esc_attr(TOURMASTER_AJAX_URL); ?>" type="button" value="<?php echo esc_html__('Submit Review', 'tourmaster'); ?>" />
</div>
<?php }else{ ?>
	<input type="hidden" name="review_id" value="<?php echo esc_attr($query->id); ?>" />
	<input type="hidden" name="tour_id" value="<?php echo esc_attr($query->tour_id); ?>" />
	<input class="tourmaster-button" type="submit" value="<?php echo esc_html__('Submit Review', 'tourmaster'); ?>" />
</form>
<?php }
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;
		}
	}
	if( !function_exists('tourmaster_get_submitted_review') ){
		function tourmaster_get_submitted_review( $query ){
			ob_start();
?>
<div class="tourmaster-review-form" >
	<div class="tourmaster-review-form-title">
		<span class="tourmaster-head" ><?php echo esc_html__('Tour Name :', 'tourmaster'); ?></span>
		<?php echo get_the_title($query->tour_id) ?>
	</div> 	
	<?php if( !empty($query->review_description) ){ ?>
		<div class="tourmaster-review-form-description" >
			<div class="tourmaster-head" ><?php echo esc_html__('What do you say about this tour? *', 'tourmaster'); ?></div>
			<div class="tourmaster-tail"><?php echo tourmaster_content_filter($query->review_description); ?></div>
		</div>
	<?php } ?>
	<?php if( !empty($query->review_type) ){ ?>
		<div class="tourmaster-review-form-description" >
			<div class="tourmaster-head" ><?php echo esc_html__('Which traveller type were you on this tour? *', 'tourmaster'); ?></div>
			<div class="tourmaster-tail"><?php 
				switch($query->review_type){
					case 'solo': echo esc_html__('Solo', 'tourmaster'); break;
					case 'couple': echo esc_html__('Couple', 'tourmaster'); break;
					case 'family': echo esc_html__('Family', 'tourmaster'); break;
					case 'group': echo esc_html__('Group', 'tourmaster'); break;
				}
			?></div>
		</div>
	<?php } ?>
	<?php if( !empty($query->review_score) ){ ?>
		<div class="tourmaster-review-form-rating-wrap" >
			<div class="tourmaster-head" ><?php echo esc_html__('Rate this tour *', 'tourmaster'); ?></div>
			<div class="tourmaster-review-form-rating clearfix" >	
			<?php
				$score = intval($query->review_score);
				echo tourmaster_get_rating($score);
			?>
			</div>
		</div>
	<?php } ?>
</div>
<?php			
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;
		}
	} // tourmaster_get_submitted_review

	if( !function_exists('tourmaster_update_review_score') ){
		function tourmaster_update_review_score( $tour_id ){

			$results = tourmaster_get_booking_data(array(
		    	'review_tour_id' => $tour_id,
		    	'review_score' => 'IS NOT NULL',
				'order_status' => array(
					'hide-prefix' => true,
					'custom' => ' (order_status IS NULL OR order_status != \'cancel\') '
				)
		    ), array('only-review' => true), 'review_score');

		    $review_score = 0;
		    $review_number = 0;
		    foreach( $results as $result ){
		    	if( $result->review_score != '' ){
		    		$review_score += $result->review_score;
		    		$review_number++;
		    	}
		    }

		    update_post_meta($tour_id, 'tourmaster-tour-rating', array(
		    	'score' => $review_score,
		    	'reviewer' => $review_number
		    ));

		    if( $review_number > 0 ){
		    	update_post_meta($tour_id, 'tourmaster-tour-rating-score', $review_score / $review_number);
		    }else{
		    	delete_post_meta($tour_id, 'tourmaster-tour-rating-score');
		    }

		} // tourmaster_update_review_score
	}