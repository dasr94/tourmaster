<?php
	/**
	 * The template for displaying single tour posttype
	 */

	// calculate view count before printing the content
	$view_count = get_post_meta(get_the_ID(), 'tourmaster-view-count', true);
	$view_count = empty($view_count)? 0: intval($view_count);
	if( empty($_COOKIE['tourmaster-tour-' . get_the_ID()]) ){
		$view_count = $view_count + 1;
		update_post_meta(get_the_ID(), 'tourmaster-view-count', $view_count);
		setcookie('tourmaster-tour-' . get_the_ID(), 1, time() + 86400);
	}

	if( !empty($_POST['tour_temp']) ){
		$temp_data = tourmaster_process_post_data($_POST['tour_temp']);
		$temp_data = json_decode($temp_data, true);
		unset($temp_data['tour-id']);
	}

get_header();

	global $current_user;
	$tour_style = new tourmaster_tour_style();
	$tour_option = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');
	$tour_option['form-settings'] = empty($tour_option['form-settings'])? 'booking': $tour_option['form-settings'];

	echo '<div class="tourmaster-page-wrapper tourmaster-tour-style-1 ';
	echo ($tour_option['form-settings'] == 'none')? 'tourmaster-without-sidebar': 'tourmaster-with-sidebar';
	echo '" id="tourmaster-page-wrapper" >';
	
	// tour schema / structure data
	$enable_schema = tourmaster_get_option('general', 'enable-tour-schema', 'enable');
	if( $enable_schema == 'enable' ){
		$schema = array(
			'@context' => 'http://schema.org',
			'@type' => 'Product',
			'name' => get_the_title(),
			'productID' => 'tour-' . get_the_ID(),
			'brand' => get_bloginfo('name'),
			'sku' => '1',
			'url' => get_permalink(),
			'description' => get_the_excerpt(),
		);
 
		$tour_price = get_post_meta(get_the_ID(), 'tourmaster-tour-price', true);
		if( !empty($tour_price) ){
			$schema['offers'] = array(
				'@type' => 'Offer',
				'url' => get_permalink(),
				'price' => $tour_price,
				'priceValidUntil' => date('Y-01-01', strtotime('+365 day')),
				'availability' => 'http://schema.org/InStock'
			);

			$currency = tourmaster_get_option('general', 'tour-schema-price-currency', '');
			if( !empty($currency) ){
				$schema['offers']['priceCurrency'] = $currency;
			}

			$price_range = get_post_meta(get_the_ID(), 'tourmaster-tour-price-range', true);
			if( !empty($price_range) ){
				$schema['offers']['priceRange'] = $price_range;
			}
		}		

		$feature_image = get_post_thumbnail_id();
		if( !empty($feature_image) ){
			$schema['image'] = tourmaster_get_image_url($feature_image, 'full');
		}

		$tour_rating = get_post_meta(get_the_ID(), 'tourmaster-tour-rating', true);
		if( !empty($tour_rating['reviewer']) ){
			$rating_value = intval($tour_rating['score']) / (2 * intval($tour_rating['reviewer']));
			$schema['AggregateRating'] = array(
				array(
					'@type' => 'AggregateRating',
					'ratingValue' => number_format($rating_value, 2),
					'reviewCount' => $tour_rating['reviewer']
				),
			);
		}

		// review
		$review_args = array(
			'review_tour_id' => get_the_ID(), 
			'review_score' => 'IS NOT NULL',
			'order_status' => array(
				'hide-prefix' => true,
				'custom' => ' (order_status IS NULL OR order_status != \'cancel\') '
			)
		);
		$review = tourmaster_get_booking_data($review_args, array(
			'only-review' => true,
			'num-fetch' => 1,
			'paged' => 1,
			'orderby' => 'review_date',
			'order' => 'desc',
			'single' => true
		));
		if( !empty($review) ){
			$reviewer_name = '';
			if( !empty($review->user_id) ){
				$reviewer_name = tourmaster_get_user_meta($review->user_id);
			}else if( !empty($review->reviewer_name) ){
				$reviewer_name = $review->reviewer_name;
			}

			$schema['review'] = array(
				'@type' => 'Review',
				'reviewRating' => array(
					'@type' => 'Rating',
					'ratingValue' => number_format(($review->review_score / 2), 2)
				),
				'name' => $reviewer_name,
				'author' => array(
					'@type' => 'Person',
					'name' => $reviewer_name
				),
				'datePublished' => $review->review_date,
				'reviewBody' => $review->review_description
			);
		}

		echo '<script type="application/ld+json">';
		echo json_encode($schema);
		echo '</script>';
	}

	////////////////////////////////////////////////////////////////////
	// header section
	////////////////////////////////////////////////////////////////////

	




	if( empty($tour_option['header-image']) || $tour_option['header-image'] == 'feature-image' ){
		echo '<div class="tourmaster-single-header" ' . tourmaster_esc_style(array('background-image' => get_post_thumbnail_id())) . ' >';
	}else if( $tour_option['header-image'] == 'custom-image' && !empty($tour_option['header-image-custom']) ){
		echo '<div class="tourmaster-single-header" ' . tourmaster_esc_style(array('background-image' => $tour_option['header-image-custom'])) . ' >';
	}else if( $tour_option['header-image'] == 'slider' && !empty($tour_option['header-slider']) ){
		$slides = array();
		$thumbnail_size = empty($tour_option['header-slider-thumbnail'])? 'full': $tour_option['header-slider-thumbnail'];
		foreach( $tour_option['header-slider'] as $slider ){
			$slides[] = '<div class="tourmaster-media-image" >' . tourmaster_get_image($slider['id'], $thumbnail_size) . '</div>';
		}

		echo '<div class="tourmaster-single-header tourmaster-with-slider" >';
		echo tourmaster_get_flexslider($slides, array('navigation' => 'none'));
	}else if( $tour_option['header-image'] == 'revolution-slider' ){
		echo '<div class="tourmaster-single-header tourmaster-with-slider" >';
		echo do_shortcode('[rev_slider alias="' . esc_attr($tour_option['header-revolution-slider-id']) . '"]');

	}else if( $tour_option['header-image'] == 'gallery' && !empty($tour_option['header-slider']) ){
		$header_image = $tour_option['header-slider'][0]['id'];
		echo '<div class="tourmaster-single-header" ' . tourmaster_esc_style(array('background-image' => $tour_option['header-slider'][0]['id'])) . ' >';
	}else if( $tour_option['header-image'] == 'video' ){
		echo '<div class="tourmaster-single-header tourmaster-background-video-wrap" ';
		if( !empty($tour_option['background-video-image']) ){
			echo ' data-video-fallback="' . esc_attr(tourmaster_get_image_url($tour_option['background-video-image'])) . '" ';
		}
		echo '>';
		echo '<div class="tourmaster-background-video" data-background-type="video" >';
		if( !empty($tour_option['background-video-url']) ){
			echo tourmaster_get_video(
				$tour_option['background-video-url'], 
				array('width' => '100%', 'height' => '100%'), 
				array('background' => 1)
			);
		}
		echo '</div>';
	}else if( $tour_option['header-image'] == 'html5-video' ){
		echo '<div class="tourmaster-single-header tourmaster-background-video-wrap" ';
		if( !empty($tour_option['background-video-image']) ){
			echo ' data-video-fallback="' . esc_attr(tourmaster_get_image_url($tour_option['background-video-image'])) . '" ';
		}
		echo '>';
		echo '<div class="tourmaster-background-video" data-background-type="video" >';
		echo '<video autoplay loop muted >';
		if( $tour_option['background-video-url-mp4'] ){
			echo '<source src="' . esc_url($tour_option['background-video-url-mp4']) . '" type="video/mp4">';
		}
		if($tour_option['background-video-url-webm'] ){
			echo '<source src="' . esc_url($tour_option['background-video-url-webm']) . '" type="video/webm">';
		}
		if( $tour_option['background-video-url-ogg'] ){
			echo '<source src="' . esc_url($tour_option['background-video-url-ogg']) . '" type="video/ogg">';
		}
		echo '</video>';
		echo '</div>';
	}else{
		echo '<div class="tourmaster-single-header" >';
	}

	$header_overlay = tourmaster_get_option('general', 'single-tour-header-gradient', 'both');
	if( !empty($tour_option['header-background-gradient']) && $tour_option['header-background-gradient'] != 'default' ){
		$header_overlay = $tour_option['header-background-gradient'];
	}

	echo '<div class="tourmaster-single-header-background-overlay" ' . tourmaster_esc_style(array(
		'opacity' => empty($tour_option['header-background-overlay-opacity'])? '': $tour_option['header-background-overlay-opacity']
	)) . ' ></div>';
	if( $header_overlay == 'top' || $header_overlay == 'both' ){
		echo '<div class="tourmaster-single-header-top-overlay" ></div>';
	}
	if( $header_overlay == 'bottom' || $header_overlay == 'both' ){
		echo '<div class="tourmaster-single-header-overlay" ></div>';
	}
	echo '<div class="tourmaster-single-header-container tourmaster-container" >';
	echo '<div class="tourmaster-single-header-container-inner" >';
	echo '<div class="tourmaster-single-header-title-wrap tourmaster-item-pdlr" ';
	if( empty($tour_option['header-image']) || in_array($tour_option['header-image'], array('feature-image', 'custom-image')) ){
		echo tourmaster_esc_style(array(
			'padding-top' => empty($tour_option['header-top-padding'])? '': $tour_option['header-top-padding'],
			'padding-bottom' => empty($tour_option['header-bottom-padding'])? '': $tour_option['header-bottom-padding'],
		));
	}
	echo ' >';
	if( $tour_option['header-image'] == 'gallery' && !empty($tour_option['header-slider']) ){
		$lb_group = 'tourmaster-single-header-gallery';
		$count = 0;

		echo '<div class="tourmaster-single-header-gallery-wrap" >';
		foreach($tour_option['header-slider'] as $slider){ $count++;
			$lightbox_atts = array(
				'url' => tourmaster_get_image_url($slider['id']), 
				'group' => $lb_group
			);

			if( $count == 1 ){
				$lightbox_atts['class'] = 'tourmaster-single-header-gallery-button';
				echo '<a ' . tourmaster_get_lightbox_atts($lightbox_atts) . ' >';
				echo '<i class="fa fa-image" ></i>' . esc_html__('Gallery', 'tourmaster');
				echo '</a>';
			}else{
				echo '<a ' . tourmaster_get_lightbox_atts($lightbox_atts) . ' ></a>';
			}
		}

		if( !empty($tour_option['lightbox-video-url']) ){
			echo '<a ' . tourmaster_get_lightbox_atts(array(
				'class' => 'tourmaster-single-header-gallery-button',
				'type' => 'video', 
				'url' => $tour_option['lightbox-video-url']
			)) . ' >';
			echo '<i class="fa fa-video-camera" ></i>' . esc_html__('Video', 'tourmaster');
			echo '</a>';
		}

		echo '</div>';
	}
	
	if( empty($tour_option['enable-page-title']) || $tour_option['enable-page-title'] == 'enable' ){
		echo '<h1 class="tourmaster-single-header-title" >' . get_the_title() . '</h1>';
	} 

	if( empty($tour_option['enable-header-review-number']) || $tour_option['enable-header-review-number'] == 'enable' ){
		echo $tour_style->get_rating();
	}

	echo '</div>'; // tourmaster-single-header-title-wrap

	if( $tour_option['form-settings'] != 'none' ){
		$header_price  = '<div class="tourmaster-header-price tourmaster-item-mglr" >';
		if( ($tour_option['form-settings'] == 'enquiry' && !empty($tour_option['show-price']) && $tour_option['show-price'] == 'disable') ||
			($tour_option['form-settings'] == 'custom' && !empty($tour_option['form-custom-title'])) ){
			
			$header_price .= '<div class="tourmaster-header-enquiry-ribbon" ></div>';
			$header_price .= '<div class="tourmaster-header-price-wrap" >';
			$header_price .= '<div class="tourmaster-header-price-overlay" ></div>';
			$header_price .= '<span class="tourmaster-header-enquiry" >';
			if( $tour_option['form-settings'] == 'enquiry' ){
				$header_price .= esc_html__('Send Us An Enquiry', 'tourmaster');
			}else{
				$header_price .= tourmaster_text_filter($tour_option['form-custom-title']);
			}
			
			$header_price .= '</span>';
			$header_price .= '</div>'; // tourmaster-header-price-wrap

		}else{

			$header_price .= '<div class="tourmaster-header-price-ribbon" >';
			if( !empty($tour_option['promo-text']) ){
				$header_price .= $tour_option['promo-text'];
			}else{
				$header_price .= esc_html__('Price', 'tourmaster');
			}
			$header_price .= '</div>';
			$header_price .= '<div class="tourmaster-header-price-wrap" >';
			$header_price .= '<div class="tourmaster-header-price-overlay" ></div>';
			$header_price .= $tour_style->get_price(array('with-info' => true));
			$header_price .= '</div>'; // tourmaster-header-price-wrap
		}
		$header_price .= '</div>'; // touramster-header-price 

		echo $header_price;
	}
	echo '</div>'; // tourmaster-single-header-container-inner
	echo '</div>'; // tourmaster-single-header-container
	echo '</div>'; // tourmaster-single-header


	////////////////////////////////////////////////////////////////////
	// content section
	////////////////////////////////////////////////////////////////////
	echo '<div class="tourmaster-template-wrapper" >';
	
	// tourmaster booking bar
	if( !post_password_required() && $tour_option['form-settings'] != 'none' ){
		echo '<div class="tourmaster-tour-booking-bar-container tourmaster-container" >';
		echo '<div class="tourmaster-tour-booking-bar-container-inner" >';
		echo '<div class="tourmaster-tour-booking-bar-anchor tourmaster-item-mglr" ></div>';
		echo '<div class="tourmaster-tour-booking-bar-wrap tourmaster-item-mglr" id="tourmaster-tour-booking-bar-wrap" >';
		echo '<div class="tourmaster-tour-booking-bar-outer" >';
		echo $header_price;

		echo '<div class="tourmaster-tour-booking-bar-inner" >';
		
		if(  $tour_option['form-settings'] == 'both' ){
			echo '<div class="tourmaster-booking-tab-title clearfix" id="tourmaster-booking-tab-title" >';
			echo '<div class="tourmaster-booking-tab-title-item tourmaster-active" data-tourmaster-tab="booking" >' . esc_html__('Booking Form', 'tourmaster') . '</div>';
			echo '<div class="tourmaster-booking-tab-title-item" data-tourmaster-tab="enquiry" >' . esc_html__('Enquiry Form', 'tourmaster') . '</div>';
			echo '</div>';
		}

		// custom form
		if( $tour_option['form-settings'] == 'custom' && !empty($tour_option['form-custom-code']) ){
			echo '<div class="tourmaster-tour-booking-custom-code-wrap" >';
			echo tourmaster_text_filter($tour_option['form-custom-code']);
			echo '</div>';
		}

		// enquiry form
		if( $tour_option['form-settings'] == 'enquiry' || $tour_option['form-settings'] == 'both' ){
			echo ($tour_option['form-settings'] == 'both')? '<div class="tourmaster-booking-tab-content" data-tourmaster-tab="enquiry" >': '';

			echo '<div class="tourmaster-tour-booking-enquiry-wrap" >';
			echo tourmaster_get_enquiry_form(get_the_ID());
			echo '</div>';

			echo ($tour_option['form-settings'] == 'both')? '</div>': '';
		}

		// booking form
		if( $tour_option['form-settings'] == 'booking' || $tour_option['form-settings'] == 'both' ){
			echo ($tour_option['form-settings'] == 'both')? '<div class="tourmaster-booking-tab-content tourmaster-active" data-tourmaster-tab="booking" >': '';

			// external url ( referer )
			if( !empty($tour_option['link-proceed-booking-to-external-url']) ){

				echo '<div class="tourmaster-single-tour-booking-referral" >';
				if( !empty($tour_option['external-url-text']) ){
					echo '<div class="tourmaster-single-tour-booking-referral-text" >';
					echo tourmaster_content_filter($tour_option['external-url-text']);
					echo '</div>';
				} 
				echo '<a class="tourmaster-button" href="' . esc_html($tour_option['link-proceed-booking-to-external-url']) . '" target="_blank" >' . esc_html__('Proceed Booking', 'tourmaster') . '</a>';
				echo '</div>';

			// normal form
			}else{
				$update_header_price = tourmaster_get_option('general', 'update-header-price', 'enable');
				$form_class = ($update_header_price == 'enable')? 'tourmaster-update-header-price': '';

				echo '<form class="tourmaster-single-tour-booking-fields ' . esc_attr($form_class) . ' tourmaster-form-field tourmaster-with-border" method="post" ';
				echo 'action="' . esc_url(tourmaster_get_template_url('payment')) . '" ';
				echo 'id="tourmaster-single-tour-booking-fields" data-ajax-url="' . esc_url(TOURMASTER_AJAX_URL) . '" >';

				echo '<input type="hidden" name="tour-id" value="' . esc_attr(get_the_ID()) . '" />';
				$available_date = get_post_meta(get_the_ID(), 'tourmaster-tour-date-avail', true);
				if( !empty($available_date) ){	
					$available_date = explode(',', $available_date);

					echo '<div class="tourmaster-tour-booking-date clearfix" data-step="1" >';
					echo '<i class="fa fa-calendar" ></i>';
					echo '<div class="tourmaster-tour-booking-date-input" >';

					$selected_date = $available_date[0];
					if( !empty($temp_data['tour-date']) ){
						$selected_date = $temp_data['tour-date'];
						unset($temp_data['tour-date']);
					}
					if( sizeof($available_date) == 1 ){
						echo '<div class="tourmaster-tour-booking-date-display" >' . tourmaster_date_format($selected_date) . '</div>';
						echo '<input type="hidden" name="tour-date" value="' . esc_attr($selected_date) . '" />';
					}else{
						$date_selection_type = empty($tour_option['date-selection-type'])? 'calendar': $tour_option['date-selection-type'];

						if( $date_selection_type == 'calendar' ){
							echo '<div class="tourmaster-datepicker-wrap" >';
							echo '<input type="text" class="tourmaster-datepicker" readonly ';
							echo 'value="' . esc_attr($selected_date) . '" ';
							echo 'data-date-format="' . esc_attr(tourmaster_get_option('general', 'datepicker-date-format', 'd M yy')) . '" ';
							echo 'data-tour-range="' . (empty($tour_option['multiple-duration'])? 1: intval($tour_option['multiple-duration'])) . '" ';
							echo 'data-tour-date="' . esc_attr(json_encode($available_date)) . '" />';
							echo '<input type="hidden" name="tour-date" class="tourmaster-datepicker-alt" />';
							echo '</div>';

						}else if( $date_selection_type == 'date-list'){
							echo '<div class="tourmaster-combobox-wrap tourmaster-tour-date-combobox" >';
							echo '<select name="tour-date" >';
							foreach( $available_date as $available_date_single ){
								echo '<option value="' . esc_attr($available_date_single) . '" ' . ($selected_date == $available_date_single? 'selected': '') . ' >';
								echo tourmaster_date_format($available_date_single);
								echo '</option>';
							}
							echo '</select>';
							echo '</div>';
						}
					}
					echo '</div>';
					echo '</div>'; // tourmaster-tour-booking-date

					$booking_value = array();
					if( !empty($temp_data) ){
						$booking_value = array(
							'tour-people' => empty($temp_data['tour-people'])? '': $temp_data['tour-people'],
							'tour-room' => empty($temp_data['tour-room'])? '': $temp_data['tour-room'],
							'tour-adult' => empty($temp_data['tour-adult'])? '': $temp_data['tour-adult'],
							'tour-children' => empty($temp_data['tour-children'])? '': $temp_data['tour-children'],
							'tour-student' => empty($temp_data['tour-student'])? '': $temp_data['tour-student'],
							'tour-infant' => empty($temp_data['tour-infant'])? '': $temp_data['tour-infant'],
							'package' => empty($temp_data['package'])? '': $temp_data['package'],
						);
						unset($temp_data['tour-people']);
						unset($temp_data['tour-room']);
						unset($temp_data['tour-adult']);
						unset($temp_data['tour-children']);
						unset($temp_data['tour-student']);
						unset($temp_data['tour-infant']);
						unset($temp_data['tour-infant']);
						unset($temp_data['package']);
					}else{
						$date_price = tourmaster_get_tour_date_price($tour_option, get_the_ID(), $selected_date);
						if( !empty($date_price['package']) ){
							foreach( $date_price['package'] as $package ){
								if( !empty($package['default-package']) && $package['default-package'] == 'enable' ){
									$booking_value['package'] = $package['title'];
									break;
								}
							}
						}
					}

					echo tourmaster_get_tour_booking_fields(array(
						'tour-id' => get_the_ID(),
						'tour-date' => $selected_date,
						'step' => 1
					), $booking_value);
				}else{
					echo '<div class="tourmaster-tour-booking-bar-error" data-step="999" >';
					echo apply_filters('tourmaster_tour_not_available_text', esc_html__('The tour is not available yet.', 'tourmaster'));
					echo '</div>';
				}

				// carry over data
				if( !empty($temp_data) ){
					foreach( $temp_data as $field_name => $field_value ){
						if( is_array($field_value) ){
							foreach( $field_value as $field_single_value ){
								echo '<input type="hidden" name="' . esc_attr($field_name) . '[]" value="' . esc_attr($field_single_value) . '" />';
							}
						}else{
							echo '<input type="hidden" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" />';
						}
					}
				}
				
				echo '</form>'; // tourmaster-tour-booking-fields

			} // normal form

			// if not logging in print the login before proceed form
			if( !is_user_logged_in() ){
				$guest_booking = tourmaster_get_option('general', 'enable-guest-booking', 'enable');
				$guest_booking = ($guest_booking == 'enable')? true: false;
				echo tourmaster_lightbox_content(array(
					'id' => 'proceed-without-login',
					'title' => esc_html__('Proceed Booking', 'tourmaster'),
					'content' => tourmaster_get_login_form2(false, array(
						'continue-as-guest'=>$guest_booking,
						'redirect'=>'payment'
					))
				));
			}

			echo ($tour_option['form-settings'] == 'both')? '</div>': '';

		} // booking form

		// bottom bar for wish list and view count
		echo '<div class="tourmaster-booking-bottom clearfix" >';
		
		// wishlist section
		$logged_in = is_user_logged_in();
		if( !$logged_in ){
			echo '<div class="tourmaster-save-wish-list" data-tmlb="wish-list-login" >';
		}else{
			$wish_list = get_user_meta($current_user->ID, 'tourmaster-wish-list', true);
			$wish_list = empty($wish_list)? array(): $wish_list;
			$wish_list_active = in_array(get_the_ID(), $wish_list);

			if( !$wish_list_active ){
				echo '<div class="tourmaster-save-wish-list" ';
				echo 'id="tourmaster-save-wish-list" ';
				echo 'data-ajax-url="' . esc_url(TOURMASTER_AJAX_URL) . '" ';
				echo 'data-tour-id="' . esc_attr(get_the_ID()) . '" ';
				echo '>';
			}else{
				echo '<div class="tourmaster-save-wish-list tourmaster-active" >';
			}
		}
		echo '<span class="tourmaster-save-wish-list-icon-wrap" >';
		echo '<i class="tourmaster-icon-active fa fa-heart" ></i>';
		echo '<i class="tourmaster-icon-inactive fa fa-heart-o" ></i>';
		echo '</span>';
		echo esc_html__('Save To Wish List', 'tourmaster');
		echo '</div>'; // tourmaster-save-wish-list
		if( !$logged_in ){
			echo tourmaster_lightbox_content(array(
				'id' => 'wish-list-login',
				'title' => esc_html__('Adding item to wishlist requires an account', 'tourmaster'),
				'content' => tourmaster_get_login_form2(false)
			));
		}

		echo '<div class="tourmaster-view-count" >';
		echo '<i class="fa fa-eye" ></i>';
		echo '<span class="tourmaster-view-count-text" >' . $view_count . '</span>';
		echo '</div>'; // tourmaster-view-count
		echo '</div>'; // tourmaster-booking-bottom

		echo '</div>'; // tourmaster-tour-booking-bar-inner
		echo '</div>'; // tourmaster-tour-booking-bar-outer

		// sidebar widget
		if( !empty($tour_option['sidebar-widget']) && $tour_option['sidebar-widget'] != 'none' ){
			$sidebar_class = apply_filters('gdlr_core_sidebar_class', '');

			$mobile_widget = tourmaster_get_option('general', 'enable-single-sidebar-widget-on-mobile', 'enable');
			if( $mobile_widget == 'disable' ){
				$sidebar_class .= ' tourmaster-hide-on-mobile';
			}

			echo '<div class="tourmaster-tour-booking-bar-widget ' . esc_attr($sidebar_class) . '" >';
			if( $tour_option['sidebar-widget'] == 'default' ){
				$sidebar_name = tourmaster_get_option('general', 'single-tour-default-sidebar', 'none');
				if( $sidebar_name != 'none' && is_active_sidebar($sidebar_name) ){
					dynamic_sidebar($sidebar_name); 
				}
			}else{
				if( is_active_sidebar($tour_option['sidebar-widget']) ){ 
					dynamic_sidebar($tour_option['sidebar-widget']); 
				}
			}
			echo '</div>';
		}
		echo '</div>'; // tourmaster-tour-booking-bar-wrap
		echo '</div>'; // tourmaster-tour-booking-bar-container-inner
		echo '</div>'; // tourmaster-tour-booking-bar-container
	}

	// print tour top info
	if( empty($tour_option['display-single-tour-info']) || $tour_option['display-single-tour-info'] == 'enable' ){
		echo '<div class="tourmaster-tour-info-outer" >';
		echo '<div class="tourmaster-tour-info-outer-container tourmaster-container" >';
		echo $tour_style->get_info(array( 'duration-text', 'availability', 'departure-location', 'return-location', 'minimum-age', 'maximum-people'), array(
			'info-class' => 'tourmaster-item-pdlr'
		));
		echo '</div>'; // tourmaster-tour-info-outer-container
		echo '</div>'; // tourmaster-tour-info-outer
	}
	global $post;
	echo '<div class="tourmaster-single-tour-content-wrap">';
		while( have_posts() ){ the_post();
			echo '<div class="gdlr-core-page-builder-body">';
			
			echo '<div class="gdlr-core-pbf-wrapper " style="padding: 0px 0px 0px 0px;">';
			echo '<div class="gdlr-core-pbf-background-wrap"></div>';
			echo '<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">';
			echo '<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-pbf-wrapper-full">';
			echo '<div class="gdlr-core-pbf-element">';
			echo '<div class="tourmaster-content-navigation-item-wrap clearfix" style="padding-bottom: 0px; height: auto;">';
			echo '<div class="tourmaster-content-navigation-item-outer" id="tourmaster-content-navigation-item-outer">';
			echo '<div class="tourmaster-content-navigation-item-container tourmaster-container">';
			echo '<div class="tourmaster-content-navigation-item tourmaster-item-pdlr">';
			echo '<a class="tourmaster-content-navigation-tab tourmaster-active tourmaster-slidebar-active" href="#detail" data-anchor="#detail">Detail</a>';
			echo '<a class="tourmaster-content-navigation-tab" href="#photos" data-anchor="#photos">Photos</a>';
			echo '<a class="tourmaster-content-navigation-tab" href="#map-det" data-anchor="#map-det">Map</a>';
			echo '<a class="tourmaster-content-navigation-tab" href="#company" data-anchor="#company">Business</a>';
			echo '<div class="tourmaster-content-navigation-slider" style="width: 91px; left: 15px; overflow: hidden;"></div>';
			echo '</div>'; 
			echo '</div>'; 
			echo '</div>'; 
			echo '</div>'; 
			echo '</div>'; 
			echo '</div>'; 
			echo '</div>'; 
			echo '</div>'; 

			echo '<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon" id="detail">
            <div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-element">
			<div
			class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align">
			<div class="gdlr-core-text-box-item-content">';
			ob_start();
			the_content();
			$content = ob_get_contents();
			ob_end_clean();

			echo $content;
			echo '</div>
			</div>
			</div>
			</div>
			</div>
			</div>';

			echo ' <div class="gdlr-core-pbf-wrapper " style="padding: 0px 0px 30px 0px;" data-skin="Blue Icon" id="photos">
            <div class="gdlr-core-pbf-background-wrap"></div>
			<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-element">
			<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr"
			style="padding-bottom: 35px ;">
			<div class="gdlr-core-title-item-title-wrap">
			<h6 class="gdlr-core-title-item-title gdlr-core-skin-title"
			style="font-size: 24px ;font-weight: 600 ;letter-spacing: 0px ;text-transform: none ;">
			<span class="gdlr-core-title-item-left-icon" style="font-size: 18px ;"><i
			class="icon_images"></i></span>Photos<span
			class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h6>
			</div>
			</div>
			</div>
			<div class="gdlr-core-pbf-element">
			<div
			class="gdlr-core-gallery-item gdlr-core-item-pdb clearfix  gdlr-core-gallery-item-style-slider gdlr-core-item-pdlr ">
			<div class="gdlr-core-flexslider flexslider gdlr-core-js-2 " data-type="slider"
			data-effect="default" data-nav="bullet">
			<div class="flex-viewport" style="overflow: hidden; position: relative;">
			<ul class="slides" style="width: 1200%; margin-left: -571px;">';

			$sliders = get_post_meta(get_the_ID(), 'header-slider');
			foreach($sliders[0] as $img){
				$img_cut = str_replace("-150x150","-300x300",$img['thumbnail']);
				echo '<li class="clone" aria-hidden="true"
				style="width: 571px; margin-right: 0px; float: left; display: block;">
				<div class="gdlr-core-gallery-list gdlr-core-media-image"><a
				class="gdlr-core-ilightbox gdlr-core-js "
				href="'.$img_cut.'"
				data-caption="Map" data-ilightbox-group="gdlr-core-img-group-1"
				data-type="image"><img
				src="'.$img_cut.'"
				alt="" width="450" height="400" title="Traveller"
				draggable="false"><span class="gdlr-core-image-overlay "><i
				class="gdlr-core-image-overlay-icon  gdlr-core-size-22 fa fa-search"></i></span></a>
				</div>
				</li>';
			}

			echo '</ul>
			</div>
			</div>
			</div>
			</div>
			</div>
            </div>
			</div>';

			// echo get_the_ID();

			$longitude = get_post_meta(get_the_ID(), 'longitude', true);
			$latitude = get_post_meta(get_the_ID(), 'latitude', true);
			
			echo '<p id="latitude-tour" value="'.$latitude.'"></p>';
			echo '<p id="longitude-tour" value="'.$longitude.'"></p>';

			echo '<div class="gdlr-core-pbf-wrapper " style="padding: 20px 0px 30px; position: relative; overflow: hidden;"
			data-skin="Blue Icon" id="map-det">
			<div class="gdlr-core-pbf-background-wrap"></div>
			<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-element">
			<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr"
			style="padding-bottom: 35px ;">
			<div class="gdlr-core-title-item-title-wrap">
			<h6 class="gdlr-core-title-item-title gdlr-core-skin-title"
			style="font-size: 24px ;font-weight: 600 ;letter-spacing: 0px ;text-transform: none ;">
			<span class="gdlr-core-title-item-left-icon" style="font-size: 18px ;"><i
			class="fa fa-bus"></i></span>Map<span
			class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h6>
			</div>
			</div>
			</div>
			<div class="gdlr-core-pbf-element">
			<div class="gdlr-core-toggle-box-item gdlr-core-item-pdlr gdlr-core-item-pdb  gdlr-core-toggle-box-style-background-title gdlr-core-left-align"
			style="padding-bottom: 25px ;">';
			echo '<div id="map-tour-det"></div>';
			echo '</div>
			</div>
			</div>
			</div>
			</div>';

			echo '<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon" id="company">
            <div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-element">
			<div
			class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align">
			<div class="gdlr-core-text-box-item-content">';

			$author_id = get_post_field( 'post_author', $post_id );

			$company = get_the_author_meta('guide-company', $author_id);

			$desc = get_the_author_meta('guide-info', $author_id);

			echo '<div class="gdlr-core-title-item-title-wrap">
			<h6 class="gdlr-core-title-item-title gdlr-core-skin-title"
			style="font-size: 24px ;font-weight: 600 ;letter-spacing: 0px ;text-transform: none ;">';
			echo $company;
			echo '<span
			class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h6>
			</div>';
			echo '<div class="gdlr-core-text-box-item-content">';
			echo '<p style="white-space: pre-line;">';
			echo $desc;
			echo '</p>';
			echo '</div>';
			
			echo '</div>
			</div>
			</div>
			</div>
			</div>
			</div>';



			echo '</div>';
		}
	echo '</div>';

	
	
	/*echo '<div class="tourmaster-single-tour-content-wrap" >';
	global $post;
	while( have_posts() ){ the_post();

		if( empty($tour_option['show-wordpress-editor-content']) || $tour_option['show-wordpress-editor-content'] == 'enable' ){
			ob_start();
			the_content();
			$content = ob_get_contents();
			ob_end_clean();

			if( !empty($content) ){
				echo '<div class="tourmaster-container" >';
				echo '<div class="tourmaster-page-content tourmaster-item-pdlr" >';
				echo '<div class="tourmaster-single-main-content" >' . $content . '</div>'; // tourmaster-single-main-content
				echo '</div>'; // tourmaster-page-content
				echo '</div>'; // tourmaster-container
			}
		}
	}*/

	//do_action('gdlr_core_print_page_builder');

	/*$mobile_read_more = tourmaster_get_option('general', 'mobile-content-read-more', 'enable');
	if( $mobile_read_more == 'enable' ){
		echo '<div class="tourmaster-single-tour-read-more-gradient" ></div>';
		echo '<div class="tourmaster-single-tour-read-more-wrap" >';
		echo '<div class="tourmaster-container" >';
		echo '<a class="tourmaster-button tourmaster-item-mglr" href="#" >' . esc_html__('Read More', 'tourmaster') . '</a>';
		echo '</div>';
		echo '</div>';
	}*/

	////////////////////////////////////////////////////////////////////
	// related tour section
	////////////////////////////////////////////////////////////////////
	$related_tour = tourmaster_get_option('general', 'enable-single-related-tour', 'enable');

	if( $related_tour == 'enable' ){

		$related_tour_args = apply_filters('tourmaster_single_related_tour_args', array(
			'tour-style' => tourmaster_get_option('general', 'single-related-tour-style', 'grid'),
			'grid-style' => tourmaster_get_option('general', 'single-related-tour-grid-style', 'style-2'),
			'thumbnail-size' => tourmaster_get_option('general', 'single-related-tour-thumbnail-size', 'large'),
			'excerpt' => tourmaster_get_option('general', 'single-related-tour-excerpt', 'none'),
			'excerpt-number' => tourmaster_get_option('general', 'single-related-tour-excerpt-number', '20'),
			'column-size' => tourmaster_get_option('general', 'single-related-tour-column-size', '30'),
			'price-position' => tourmaster_get_option('general', 'single-related-tour-price-position', 'right-title'),
			'tour-rating' => tourmaster_get_option('general', 'single-related-tour-rating', 'enable'),
			'tour-info' => tourmaster_get_option('general', 'single-related-tour-info', ''),
		));

		// query related portfolio
		$args = array('post_type' => 'tour', 'suppress_filters' => false);
		$args['posts_per_page'] = tourmaster_get_option('general', 'single-related-tour-num-fetch', '2');
		$args['post__not_in'] = array(get_the_ID());

		$related_terms = get_the_terms(get_the_ID(), 'tour_tag');
		$related_tags = array();
		if( !empty($related_terms) ){
			foreach( $related_terms as $term ){
				$related_tags[] = $term->term_id;
			}
			$args['tax_query'] = array(array('terms'=>$related_tags, 'taxonomy'=>'tour_tag', 'field'=>'id'));
		} 
		$query = new WP_Query($args);

		// print item
		if( $query->have_posts() ){

			$tour_style = new tourmaster_tour_style();

			echo '<div class="tourmaster-single-related-tour tourmaster-tour-item tourmaster-style-' . esc_attr($related_tour_args['tour-style']) . '">';
			echo '<div class="tourmaster-single-related-tour-container tourmaster-container">';
			echo '<h3 class="tourmaster-single-related-tour-title tourmaster-item-pdlr">' . esc_html__('Related Tours', 'tourmaster') . '</h3>';

			$column_sum = 0;
			$no_space = in_array($related_tour_args['tour-style'], array('grid-no-space', 'modern-no-space'))? 'yes': 'no';
			if( strpos($related_tour_args['tour-style'], 'with-frame') !== false ){
				$related_tour_args['with-frame'] = 'enable';
				$related_tour_args['tour-style'] = str_replace('-with-frame', '', $related_tour_args['tour-style']);
			}else{
				$related_tour_args['with-frame'] = 'disable';
			}

			echo '<div class="tourmaster-tour-item-holder clearfix ' . ($no_space == 'yes'? ' tourmaster-item-pdlr': '') . '" >';
			while( $query->have_posts() ){ $query->the_post();

				$additional_class  = ' tourmaster-column-' . $related_tour_args['column-size'];
				$additional_class .= ($no_space == 'yes')? '': ' tourmaster-item-pdlr';
				$additional_class .= in_array($related_tour_args['tour-style'], array('modern'))? ' tourmaster-item-mgb': '';

				if( $column_sum == 0 || $column_sum + intval($related_tour_args['column-size']) > 60 ){
					$column_sum = intval($related_tour_args['column-size']);
					$additional_class .= ' tourmaster-column-first';
				}else{
					$column_sum += intval($related_tour_args['column-size']);
				}
				echo '<div class="gdlr-core-item-list ' . esc_attr($additional_class) . '" >';
				echo $tour_style->get_content($related_tour_args);
				echo '</div>';
			}
			wp_reset_postdata();

			echo '</div>'; // tourmaster-tour-item-holder

			echo '</div>'; // tourmaster-container 
			echo '</div>'; // tourmaster-single-related-tour
		}
	}

	////////////////////////////////////////////////////////////////////
	// review section
	////////////////////////////////////////////////////////////////////
	if( empty($tour_option['enable-review']) || $tour_option['enable-review'] == 'enable' ){
		$review_num_fetch = apply_filters('tourmaster_review_num_fetch', 5);
		$review_args = array(
			'review_tour_id' => get_the_ID(), 
			'review_score' => 'IS NOT NULL',
			'order_status' => array(
				'hide-prefix' => true,
				'custom' => ' (order_status IS NULL OR order_status != \'cancel\') '
			)
		);
		$results = tourmaster_get_booking_data($review_args, array(
			'only-review' => true,
			'num-fetch' => $review_num_fetch,
			'paged' => 1,
			'orderby' => 'review_date',
			'order' => 'desc'
		));
		
		if( !empty($results) ){
			$max_num_page = intval(tourmaster_get_booking_data($review_args, array('only-review' => true), 'COUNT(*)')) / $review_num_fetch;

			echo '<div class="tourmaster-single-review-container tourmaster-container" >';
			echo '<div class="tourmaster-single-review-item tourmaster-item-pdlr" >';
			echo '<div class="tourmaster-single-review" id="tourmaster-single-review" >';

			echo '<div class="tourmaster-single-review-head clearfix" >';
			echo '<div class="tourmaster-single-review-head-info clearfix" >';
			echo $tour_style->get_rating('plain');

			echo '<div class="tourmaster-single-review-filter" id="tourmaster-single-review-filter" >';
			echo '<div class="tourmaster-single-review-sort-by" >';
			echo '<span class="tourmaster-head" >' . esc_html__('Sort By:', 'tourmaster') . '</span>';
			echo '<span class="tourmaster-sort-by-field" data-sort-by="rating" >' . esc_html__('Rating', 'tourmaster') . '</span>';
			echo '<span class="tourmaster-sort-by-field tourmaster-active" data-sort-by="date" >' . esc_html__('Date', 'tourmaster') . '</span>';
			echo '</div>'; // tourmaster-single-review-sort-by
			echo '<div class="tourmaster-single-review-filter-by tourmaster-form-field tourmaster-with-border" >';
			echo '<div class="tourmaster-combobox-wrap" >';
			echo '<select id="tourmaster-filter-by" >';
			echo '<option value="" >' . esc_html__('Filter By', 'tourmaster'). '</option>';
			echo '<option value="solo" >' . esc_html__('Solo', 'tourmaster'). '</option>';
			echo '<option value="couple" >' . esc_html__('Couple', 'tourmaster'). '</option>';
			echo '<option value="family" >' . esc_html__('Family', 'tourmaster'). '</option>';
			echo '<option value="group" >' . esc_html__('Group', 'tourmaster'). '</option>';
			echo '</select>';
			echo '</div>'; // tourmaster-combobox-wrap
			echo '</div>'; // tourmaster-single-review-filter-by
			echo '</div>'; // tourmaster-single-review-filter
			echo '</div>'; // tourmaster-single-review-head-info
			echo '</div>'; // tourmaster-single-review-head

			echo '<div class="tourmaster-single-review-content" id="tourmaster-single-review-content" ';
			echo 'data-tour-id="' . esc_attr(get_the_ID()) . '" ';
			echo 'data-ajax-url="' . esc_attr(TOURMASTER_AJAX_URL) . '" >';
			echo tourmaster_get_review_content_list($results);

			echo tourmaster_get_review_content_pagination($max_num_page);
			echo '</div>'; // tourmaster-single-review-content
			echo '</div>'; // tourmaster-single-review
			echo '</div>'; // tourmaster-single-review-item
			echo '</div>'; // tourmaster-single-review-container
		} 
	} 


	echo '</div>'; // tourmaster-template-wrapper

	echo '</div>'; // tourmaster-page-wrapper

	// urgent message
	if( empty($_COOKIE['tourmaster-urgency-message']) && !empty($tour_option['enable-urgency-message']) && $tour_option['enable-urgency-message'] == 'enable' ){
		$urgency_message_number = 0;
		if( !empty($tour_option['real-urgency-message']) && $tour_option['real-urgency-message'] == 'disable' ){
			$urgency_message_number = rand(intval($tour_option['urgency-message-number-from']), intval($tour_option['urgency-message-number-to']));
		}else{
			$ip_list = get_post_meta(get_the_ID(), 'tourmaster-tour-ip-list', true);
			$ip_list = empty($ip_list)? array(): $ip_list;

			$client_ip = tourmaster_get_client_ip();
			$ip_list[$client_ip] = strtotime('now');

			// remove the user which longer than 1 hour
			$current_time = strtotime('now');
			foreach( $ip_list as $client_ip => $ttl ){
				if( $current_time > $ttl + 3600 ){
					unset($ip_list[$client_ip]);
				}
			}

			$urgency_message_number = sizeof($ip_list);
			update_post_meta(get_the_ID(), 'tourmaster-tour-ip-list', $ip_list);
		}

		echo '<div class="tourmaster-urgency-message" id="tourmaster-urgency-message" data-expire="86400" >';
		echo '<i class="tourmaster-urgency-message-icon fa fa-users" ></i>';
		echo '<div class="tourmaster-urgency-message-text" >';
		echo sprintf(esc_html__('%d travellers are considering this tour right now!', 'tourmaster'), $urgency_message_number);
		echo '</div>';
		echo '</div>';
	}

get_footer(); 

?>