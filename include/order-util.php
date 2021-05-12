<?php
	/*	
	*	Ordering Page
	*/

	if( !function_exists('tourmaster_order_edit_text') ){
		function tourmaster_order_edit_text($tmlb = ''){
			return '<a class="tourmaster-order-edit-text" href="#" data-tmlb="' . esc_attr($tmlb) . '" >' . esc_html__('Edit', 'tourmaster') . '<i class="fa fa-edit" ></i></a>';
		}
	}
	//custom
	if( !function_exists('tourmaster_order_edit_text_2') ){
		function tourmaster_order_edit_text_2($tmlb = ''){
			return '<a class="tourmaster-order-edit-text" href="#" data-tmlb2="' . esc_attr($tmlb) . '" >' . esc_html__('Edit', 'tourmaster') . '<i class="fa fa-edit" ></i></a>';
		}
	}


	add_action('wp_ajax_prueba_action', 'prueba_action');
	if( !function_exists('prueba_action')){
		function prueba_action(){
			$hola = "hola";
			return $hola;
		}
	}

	add_action('wp_ajax_tourmaster_tour_order_booking', 'tourmaster_tour_order_booking_ajax');
	if( !function_exists('tourmaster_tour_order_booking_ajax') ){
		function tourmaster_tour_order_booking_ajax(){
		
			$ret = array();

			if( !empty($_POST['data']) ){
				$data = tourmaster_process_post_data($_POST['data']);
				$ret['debug'] = $data;

				if( $data['step'] == 0 ){
					$available_dates = get_post_meta($data['tour-id'], 'tourmaster-tour-date-avail', true);

					if( empty($available_dates) ){
						$ret['content'] .= '<div data-step="1" >' . esc_html__('There\'re no tour available.', 'tourmaster') . '</div>';
					}else{
						$available_dates = explode(',', $available_dates);
						$ret['content'] .= tourmaster_get_tour_booking_dates($available_dates, '');
					}


				}else{
					$ret['content'] = tourmaster_get_tour_booking_fields($data, $data);
				}
			}

			die(json_encode($ret));
		}
	}

	if( !function_exists('tourmaster_get_tour_booking_dates') ){
		function tourmaster_get_tour_booking_dates( $available_dates, $tour_date ){
			$ret  = '<div class="tourmaster-tour-booking-date clearfix" data-step="1" >';
			$ret .= '<i class="fa fa-calendar" ></i>';

			$ret .= '<div class="tourmaster-tour-booking-date-input" >';
			$ret .= '<div class="tourmaster-combobox-wrap tourmaster-tour-date-combobox" >';
			$ret .= '<select name="tour-date" >';
			$ret .= '<option value="" >' . esc_html__('Select Date', 'tourmaster') . '</option>';
			foreach( $available_dates as $available_date ){
				$ret .= '<option value="' . esc_attr($available_date) . '" ' . ($tour_date == $available_date? 'selected': '') . ' >';
				$ret .= tourmaster_date_format($available_date);
				$ret .= '</option>';
			}
			$ret .= '</select>';
			$ret .= '</div>';
			$ret .= '</div>';

			$ret .= '</div>'; // tourmaster-tour-booking-date

			return $ret;
		}
	}

	if( !function_exists('tourmaster_order_new_form') ){
		function tourmaster_order_new_form( $booking_detail ){

			$ret  = '';
			$tour_id = empty($booking_detail['tour-id'])? '': $booking_detail['tour-id'];
			$ret .= tourmaster_get_form_field(array(
				'title' => esc_html__('Select Tour :', 'tourmaster'),
				'echo' => false,
				'slug' => 'tour-id',
				'type' => 'combobox',
				'options' => tourmaster_get_post_list('tour', true)
			), 'order-edit', $tour_id);
			if( empty($tour_id) ) return $ret;

			$tour_date = empty($booking_detail['tour-date'])? '': $booking_detail['tour-date'];
			$available_dates = get_post_meta($tour_id, 'tourmaster-tour-date-avail', true);
			if( empty($available_dates) ){
				$ret .= '<div data-step="1" >' . esc_html__('There\'re no tour available.', 'tourmaster') . '</div>';
					
			}else{
				$available_dates = explode(',', $available_dates);
				$booking_detail['step'] = 1;
				if( empty($tour_date) ){
					$data['tour-date'] = $available_dates[0];
				}

				$ret .= tourmaster_get_tour_booking_dates($available_dates, $tour_date);
				$ret .= tourmaster_get_tour_booking_fields($booking_detail, $booking_detail);
			}

			return $ret;
		}
	}

	if( !function_exists('tourmaster_order_new_form2') ){
		function tourmaster_order_new_form2( $booking_detail, $aid ){
			
			$ret  = '';
			$tour_id = empty($booking_detail['tour-id'])? '': $booking_detail['tour-id'];
			$ret .= tourmaster_get_form_field(array(
				'title' => esc_html__('Select Tour :', 'tourmaster'),
				'echo' => false,
				'slug' => 'tour-id',
				'type' => 'combobox',
				'options' => tourmaster_get_post_list2('tour', true, $aid)
			), 'order-edit', $tour_id);
			if( empty($tour_id) ) return $ret;

			$tour_date = empty($booking_detail['tour-date'])? '': $booking_detail['tour-date'];
			$available_dates = get_post_meta($tour_id, 'tourmaster-tour-date-avail', true);
			if( empty($available_dates) ){
				$ret .= '<div data-step="1" >' . esc_html__('There\'re no tour available.', 'tourmaster') . '</div>';
					
			}else{
				$available_dates = explode(',', $available_dates);
				$booking_detail['step'] = 1;
				if( empty($tour_date) ){
					$data['tour-date'] = $available_dates[0];
				}

				$ret .= tourmaster_get_tour_booking_dates($available_dates, $tour_date);
				$ret .= tourmaster_get_tour_booking_fields($booking_detail, $booking_detail);
			}

			return $ret;
		}
	}

	if( !function_exists('tourmaster_order_edit_form') ){
		function tourmaster_order_edit_form($tid, $type = '', $result, $tour_option = array() ){
			$ret  = '<form class="tourmaster-order-edit-form tourmaster-type-' . esc_attr($type) . '" action="" method="post" data-ajax-url="' . esc_attr(TOURMASTER_AJAX_URL) . '" >';

			if( $type == 'new_order' ){
				$ret .= '<input type="hidden" name="current_url" value="' . esc_attr(remove_query_arg(array('order_id', 'from_date', 'to_date', 'action', 'id', 'export'))) . '" />'; 

				$booking_detail = empty($result)? array(): json_decode($result->booking_detail, true);
				$ret .= tourmaster_order_new_form($booking_detail);

			}else if( $type == 'traveller' ){	

				// setup the field
				$tour_option['require-traveller-info-title'] = empty($tour_option['require-traveller-info-title'])? 'enable': $tour_option['require-traveller-info-title'];
				if( empty($tour_option['additional-traveller-fields']) ){
					$tour_option['additional-traveller-fields'] = tourmaster_get_option('general', 'additional-traveller-fields', '');
				}
				if( !empty($tour_option['additional-traveller-fields']) ){
					$tour_option['additional-traveller-fields'] = tourmaster_read_custom_fields($tour_option['additional-traveller-fields']);
				}

				$booking_detail = empty($result)? array(): json_decode($result->booking_detail, true);
				$date_price = tourmaster_get_tour_date_price($tour_option, $booking_detail['tour-id'], $booking_detail['tour-date']);
				$date_price = tourmaster_get_tour_date_price_package($date_price, $booking_detail);

				if( $date_price['pricing-method'] == 'group' ){
					$traveller_amount = $date_price['max-group-people'];

					if( $traveller_amount > 0 ){
						$required = true;
						for( $i = 0; $i < $traveller_amount; $i++ ){
							$ret .= tourmaster_payment_traveller_input($tour_option, $booking_detail, $i, $required);
							$required = false;
						}
					}
				}else{
					$traveller_amount = tourmaster_get_tour_people_amount($tour_option, $date_price, $booking_detail);
					for( $i = 0; $i < $traveller_amount; $i++ ){
						$ret .= tourmaster_payment_traveller_input($tour_option, $booking_detail, $i);
					}
				}

			}else if( $type == 'additional_notes' ){

				$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
				$value = empty($contact_detail['additional_notes'])? '': $contact_detail['additional_notes'];
				$ret .= tourmaster_get_form_field(array(
					'title' => esc_html__('Additional Notes :', 'tourmaster'),
					'echo' => false,
					'slug' => 'additional_notes',
					'type' => 'textarea'
				), 'order-edit', $value);

			}else if( $type == 'contact_details' || $type == 'billing_details' ){

				if( $type == 'contact_details' ){
					$values = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
				}else if( $type == 'billing_details' ){
					$values = empty($result->billing_info)? array(): json_decode($result->billing_info, true);
				}

				$form_fields = tourmaster_get_payment_contact_form_fields($result->tour_id);
				foreach( $form_fields as $field_slug => $field ){
					$value = empty($values[$field_slug])? '': $values[$field_slug];
					$ret .= tourmaster_get_form_field(array(
						'title' => $field['title'],
						'echo' => false,
						'slug' => $field_slug,
						'type' => $field['type'],
						'options' => empty($field['options'])? array(): $field['options'],
						'required' => empty($field['required'])? false: true,
					), 'order-edit', $value);
				}

			}else if( $type == 'price' ){

				$people_types = array(
					'traveller' => esc_html__('Traveller', 'tourmaster'),
					'adult' => esc_html__('Adult', 'tourmaster'),
					'male' => esc_html__('Male', 'tourmaster'),
					'female' => esc_html__('Female', 'tourmaster'),
					'children' => esc_html__('Children', 'tourmaster'),
					'student' => esc_html__('Student', 'tourmaster'),
					'infant' => esc_html__('Infant', 'tourmaster'),
				);
				$pricing_info = json_decode($result->pricing_info, true);

				// base price
				foreach( $people_types as $people_slug => $people_type ){
					if( isset($pricing_info['price-breakdown'][$people_slug . '-base-price']) ){
						$ret .= tourmaster_get_form_field(array(
							'title' => sprintf(esc_html__('%s Base Price', 'tourmaster'), $people_type),
							'echo' => false,
							'slug' => $people_slug . '-base-price',
							'type' => 'price-edit',
							'description' => esc_html__('Price per person (Fill only number).', 'tourmaster')
						), 'order-edit', $pricing_info['price-breakdown'][$people_slug . '-base-price']);					
					}
				}

				// group price
				if( isset($pricing_info['price-breakdown']['group-price']) ){
					$ret .= tourmaster_get_form_field(array(
						'title' => esc_html__('Group Price', 'tourmaster'),
						'echo' => false,
						'slug' => 'group-price',
						'type' => 'price-edit',
						'description' => esc_html__('Fill only number.', 'tourmaster')
					), 'order-edit', $pricing_info['price-breakdown']['group-price']);					
				}

				// room
				$count = 0;
				if( !empty($pricing_info['price-breakdown']['room']) ){
					foreach( $pricing_info['price-breakdown']['room'] as $room ){
						$ret .= '<h3 class="tourmaster-order-edit-title" >' . sprintf(esc_html__('Room %s', 'tourmaster'), $count + 1) . '</h3>';
					
						$ret .= tourmaster_get_form_field(array(
							'title' => esc_html__('Room Base Price', 'tourmaster'),
							'echo' => false,
							'slug' => 'room-base-price' . $count,
							'type' => 'price-edit',
							'description' => esc_html__('Price per person (Fill only number).', 'tourmaster')
						), 'order-edit', $room['base-price']);

						foreach( $people_types as $people_slug => $people_type ){
							if( isset($room['additional-' . $people_slug . '-price']) ){
								$ret .= tourmaster_get_form_field(array(
									'title' => sprintf(esc_html__('Additional %s Price', 'tourmaster'), $people_type),
									'echo' => false,
									'slug' => 'additional-' . $people_slug . '-price' . $count,
									'type' => 'price-edit',
									'description' => esc_html__('Price per person (Fill only number).', 'tourmaster')
								), 'order-edit', $room['additional-' . $people_slug . '-price']);
							}
						}

						$count++;
					}
				}

				// additional service
				if( !empty($tour_option['tour-service']) ){

					$pers = array(
						'person' => esc_html__('Person', 'tourmaster'),
						'group' => esc_html__('Group', 'tourmaster'),
						'room' => esc_html__('Room', 'tourmaster'),
						'unit' => esc_html__('Unit', 'tourmaster'),
					);
					$ret .= '<h3 class="tourmaster-order-edit-title" >' . esc_html__('Additional Service', 'tourmaster'). '</h3>';

					foreach( $tour_option['tour-service'] as $service_id ){
						$service_option = get_post_meta($service_id, 'tourmaster-service-option', true);
						
						$temp_price = 0;
						if( !empty($pricing_info['price-breakdown']['additional-service'][$service_id]) ){
							$temp_price = $pricing_info['price-breakdown']['additional-service'][$service_id];
						} 


						if( empty($temp_price['price-one']) ){
							$service_price = $service_option['price'];
						}else{
							$service_price = $temp_price['price-one'];
						}

						$ret .= tourmaster_get_form_field(array(
							'title' => get_the_title($service_id),
							'echo' => false,
							'slug' => 'service-price',
							'type' => 'price-edit',
							'data-type' => 'array',
							'pre-input' => '<div class="tourmaster-price-edit-amount" >' . 
								'<input type="hidden" name="service[]" value="' . esc_attr($service_id) . '" />' .
								'<input type="text" name="service-amount[]" value="' . (empty($temp_price['amount'])? '0': $temp_price['amount']) . '" />' . 
								'<span> x </span>' . 
							'</div>',
							'description' => sprintf(esc_html__('Price per %s (Fill only number).', 'tourmaster'), $service_option['per'])
						), 'order-edit', $service_price);
					} 
				}

				// group discount
				if( !empty($pricing_info['price-breakdown']['group-discount-traveller']) ){
					$ret .= '<h3 class="tourmaster-order-edit-title" >' . esc_html__('Group Discount', 'tourmaster'). '</h3>';
					$group_discount = '';
					if( !empty($pricing_info['price-breakdown']['group-discount-rate']) ){
						$group_discount = $pricing_info['price-breakdown']['group-discount-rate'];
					}

					$ret .= tourmaster_get_form_field(array(
						'title' => esc_html__('Group Discount', 'tourmaster'),
						'echo' => false,
						'slug' => 'group-discount',
						'type' => 'text'
					), 'order-edit', $group_discount);
				}

				// coupon
				$ret .= '<h3 class="tourmaster-order-edit-title" >' . esc_html__('Discount', 'tourmaster'). '</h3>';
				$coupon_code = empty($pricing_info['price-breakdown']['coupon-code'])? '': $pricing_info['price-breakdown']['coupon-code'];
				$coupon_text = '';
				if( empty($pricing_info['price-breakdown']['coupon-text']) ){
					if( !empty($pricing_info['price-breakdown']['coupon-amount']) ){
						$coupon_text = $pricing_info['price-breakdown']['coupon-amount'];
					}
				}else{
					$coupon_text = $pricing_info['price-breakdown']['coupon-text'];
				}
				$ret .= tourmaster_get_form_field(array(
					'title' => esc_html__('Coupon Code', 'tourmaster'),
					'echo' => false,
					'slug' => 'coupon-code',
					'type' => 'text'
				), 'order-edit', $coupon_code);
				$ret .= tourmaster_get_form_field(array(
					'title' => esc_html__('Coupon Discount Amount', 'tourmaster'),
					'echo' => false,
					'slug' => 'coupon-text',
					'type' => 'price-edit',
					'description' => esc_html__('With % or just number for fixed amount.', 'tourmaster')
				), 'order-edit', $coupon_text);

			} // price

			$ret .= '<div class="tourmaster-order-edit-form-load" >' . esc_html__('Now loading', 'tourmaster') . '</div>';
			$ret .= '<div class="tourmaster-order-edit-form-error" >' . esc_html__('An error occurs, please check console for more information', 'tourmaster') . '</div>';
			$ret .= '<input type="hidden" name="tid" value="' . esc_attr($tid) . '" />';
			$ret .= '<input type="hidden" name="type" value="' . esc_attr($type) . '" />';
			$ret .= '<input type="hidden" name="action" value="tourmaster_order_edit" />';
			
			if( $type != 'new_order' ){
				$ret .= '<input type="submit" class="tourmaster-order-edit-submit" value="' . esc_attr__('Submit', 'tourmaster') . '" />';
			}

			$ret .= '</form>';

			return $ret;
		}
	}
	if( !function_exists('tourmaster_order_edit_form2') ){
		function tourmaster_order_edit_form2($tid, $type = '', $result, $tour_option = array(), $aid ){
			$ret  = '<form class="tourmaster-order-edit-form tourmaster-type-' . esc_attr($type) . '" action="" method="post" data-ajax-url="' . esc_attr(TOURMASTER_AJAX_URL) . '" >';

			if( $type == 'new_order' ){
				$ret .= '<input type="hidden" name="current_url" value="' . esc_attr(remove_query_arg(array('order_id', 'from_date', 'to_date', 'action', 'id', 'export'))) . '" />'; 

				$booking_detail = empty($result)? array(): json_decode($result, true);
				$ret .= tourmaster_order_new_form2($booking_detail, $aid);

			}else if( $type == 'traveller' ){	

				// setup the field
				$tour_option['require-traveller-info-title'] = empty($tour_option['require-traveller-info-title'])? 'enable': $tour_option['require-traveller-info-title'];
				if( empty($tour_option['additional-traveller-fields']) ){
					$tour_option['additional-traveller-fields'] = tourmaster_get_option('general', 'additional-traveller-fields', '');
				}
				if( !empty($tour_option['additional-traveller-fields']) ){
					$tour_option['additional-traveller-fields'] = tourmaster_read_custom_fields($tour_option['additional-traveller-fields']);
				}

				$booking_detail = empty($result)? array(): json_decode($result->booking_detail, true);
				$date_price = tourmaster_get_tour_date_price($tour_option, $booking_detail['tour-id'], $booking_detail['tour-date']);
				$date_price = tourmaster_get_tour_date_price_package($date_price, $booking_detail);

				if( $date_price['pricing-method'] == 'group' ){
					$traveller_amount = $date_price['max-group-people'];

					if( $traveller_amount > 0 ){
						$required = true;
						for( $i = 0; $i < $traveller_amount; $i++ ){
							$ret .= tourmaster_payment_traveller_input($tour_option, $booking_detail, $i, $required);
							$required = false;
						}
					}
				}else{
					$traveller_amount = tourmaster_get_tour_people_amount($tour_option, $date_price, $booking_detail);
					for( $i = 0; $i < $traveller_amount; $i++ ){
						$ret .= tourmaster_payment_traveller_input($tour_option, $booking_detail, $i);
					}
				}

			}else if( $type == 'additional_notes' ){

				$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
				$value = empty($contact_detail['additional_notes'])? '': $contact_detail['additional_notes'];
				$ret .= tourmaster_get_form_field(array(
					'title' => esc_html__('Additional Notes :', 'tourmaster'),
					'echo' => false,
					'slug' => 'additional_notes',
					'type' => 'textarea'
				), 'order-edit', $value);

			}else if( $type == 'contact_details' || $type == 'billing_details' ){

				if( $type == 'contact_details' ){
					$values = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
				}else if( $type == 'billing_details' ){
					$values = empty($result->billing_info)? array(): json_decode($result->billing_info, true);
				}

				$form_fields = tourmaster_get_payment_contact_form_fields($result->tour_id);
				foreach( $form_fields as $field_slug => $field ){
					$value = empty($values[$field_slug])? '': $values[$field_slug];
					$ret .= tourmaster_get_form_field(array(
						'title' => $field['title'],
						'echo' => false,
						'slug' => $field_slug,
						'type' => $field['type'],
						'options' => empty($field['options'])? array(): $field['options'],
						'required' => empty($field['required'])? false: true,
					), 'order-edit', $value);
				}

			}else if( $type == 'price' ){

				$people_types = array(
					'traveller' => esc_html__('Traveller', 'tourmaster'),
					'adult' => esc_html__('Adult', 'tourmaster'),
					'male' => esc_html__('Male', 'tourmaster'),
					'female' => esc_html__('Female', 'tourmaster'),
					'children' => esc_html__('Children', 'tourmaster'),
					'student' => esc_html__('Student', 'tourmaster'),
					'infant' => esc_html__('Infant', 'tourmaster'),
				);
				$pricing_info = json_decode($result->pricing_info, true);

				// base price
				foreach( $people_types as $people_slug => $people_type ){
					if( isset($pricing_info['price-breakdown'][$people_slug . '-base-price']) ){
						$ret .= tourmaster_get_form_field(array(
							'title' => sprintf(esc_html__('%s Base Price', 'tourmaster'), $people_type),
							'echo' => false,
							'slug' => $people_slug . '-base-price',
							'type' => 'price-edit',
							'description' => esc_html__('Price per person (Fill only number).', 'tourmaster')
						), 'order-edit', $pricing_info['price-breakdown'][$people_slug . '-base-price']);					
					}
				}

				// group price
				if( isset($pricing_info['price-breakdown']['group-price']) ){
					$ret .= tourmaster_get_form_field(array(
						'title' => esc_html__('Group Price', 'tourmaster'),
						'echo' => false,
						'slug' => 'group-price',
						'type' => 'price-edit',
						'description' => esc_html__('Fill only number.', 'tourmaster')
					), 'order-edit', $pricing_info['price-breakdown']['group-price']);					
				}

				// room
				$count = 0;
				if( !empty($pricing_info['price-breakdown']['room']) ){
					foreach( $pricing_info['price-breakdown']['room'] as $room ){
						$ret .= '<h3 class="tourmaster-order-edit-title" >' . sprintf(esc_html__('Room %s', 'tourmaster'), $count + 1) . '</h3>';
					
						$ret .= tourmaster_get_form_field(array(
							'title' => esc_html__('Room Base Price', 'tourmaster'),
							'echo' => false,
							'slug' => 'room-base-price' . $count,
							'type' => 'price-edit',
							'description' => esc_html__('Price per person (Fill only number).', 'tourmaster')
						), 'order-edit', $room['base-price']);

						foreach( $people_types as $people_slug => $people_type ){
							if( isset($room['additional-' . $people_slug . '-price']) ){
								$ret .= tourmaster_get_form_field(array(
									'title' => sprintf(esc_html__('Additional %s Price', 'tourmaster'), $people_type),
									'echo' => false,
									'slug' => 'additional-' . $people_slug . '-price' . $count,
									'type' => 'price-edit',
									'description' => esc_html__('Price per person (Fill only number).', 'tourmaster')
								), 'order-edit', $room['additional-' . $people_slug . '-price']);
							}
						}

						$count++;
					}
				}

				// additional service
				if( !empty($tour_option['tour-service']) ){

					$pers = array(
						'person' => esc_html__('Person', 'tourmaster'),
						'group' => esc_html__('Group', 'tourmaster'),
						'room' => esc_html__('Room', 'tourmaster'),
						'unit' => esc_html__('Unit', 'tourmaster'),
					);
					$ret .= '<h3 class="tourmaster-order-edit-title" >' . esc_html__('Additional Service', 'tourmaster'). '</h3>';

					foreach( $tour_option['tour-service'] as $service_id ){
						$service_option = get_post_meta($service_id, 'tourmaster-service-option', true);
						
						$temp_price = 0;
						if( !empty($pricing_info['price-breakdown']['additional-service'][$service_id]) ){
							$temp_price = $pricing_info['price-breakdown']['additional-service'][$service_id];
						} 


						if( empty($temp_price['price-one']) ){
							$service_price = $service_option['price'];
						}else{
							$service_price = $temp_price['price-one'];
						}

						$ret .= tourmaster_get_form_field(array(
							'title' => get_the_title($service_id),
							'echo' => false,
							'slug' => 'service-price',
							'type' => 'price-edit',
							'data-type' => 'array',
							'pre-input' => '<div class="tourmaster-price-edit-amount" >' . 
								'<input type="hidden" name="service[]" value="' . esc_attr($service_id) . '" />' .
								'<input type="text" name="service-amount[]" value="' . (empty($temp_price['amount'])? '0': $temp_price['amount']) . '" />' . 
								'<span> x </span>' . 
							'</div>',
							'description' => sprintf(esc_html__('Price per %s (Fill only number).', 'tourmaster'), $service_option['per'])
						), 'order-edit', $service_price);
					} 
				}

				// group discount
				if( !empty($pricing_info['price-breakdown']['group-discount-traveller']) ){
					$ret .= '<h3 class="tourmaster-order-edit-title" >' . esc_html__('Group Discount', 'tourmaster'). '</h3>';
					$group_discount = '';
					if( !empty($pricing_info['price-breakdown']['group-discount-rate']) ){
						$group_discount = $pricing_info['price-breakdown']['group-discount-rate'];
					}

					$ret .= tourmaster_get_form_field(array(
						'title' => esc_html__('Group Discount', 'tourmaster'),
						'echo' => false,
						'slug' => 'group-discount',
						'type' => 'text'
					), 'order-edit', $group_discount);
				}

				// coupon
				$ret .= '<h3 class="tourmaster-order-edit-title" >' . esc_html__('Discount', 'tourmaster'). '</h3>';
				$coupon_code = empty($pricing_info['price-breakdown']['coupon-code'])? '': $pricing_info['price-breakdown']['coupon-code'];
				$coupon_text = '';
				if( empty($pricing_info['price-breakdown']['coupon-text']) ){
					if( !empty($pricing_info['price-breakdown']['coupon-amount']) ){
						$coupon_text = $pricing_info['price-breakdown']['coupon-amount'];
					}
				}else{
					$coupon_text = $pricing_info['price-breakdown']['coupon-text'];
				}
				$ret .= tourmaster_get_form_field(array(
					'title' => esc_html__('Coupon Code', 'tourmaster'),
					'echo' => false,
					'slug' => 'coupon-code',
					'type' => 'text'
				), 'order-edit', $coupon_code);
				$ret .= tourmaster_get_form_field(array(
					'title' => esc_html__('Coupon Discount Amount', 'tourmaster'),
					'echo' => false,
					'slug' => 'coupon-text',
					'type' => 'price-edit',
					'description' => esc_html__('With % or just number for fixed amount.', 'tourmaster')
				), 'order-edit', $coupon_text);

			} // price

			$ret .= '<div class="tourmaster-order-edit-form-load" >' . esc_html__('Now loading', 'tourmaster') . '</div>';
			$ret .= '<div class="tourmaster-order-edit-form-error" >' . esc_html__('An error occurs, please check console for more information', 'tourmaster') . '</div>';
			$ret .= '<input type="hidden" name="tid" value="' . esc_attr($tid) . '" />';
			$ret .= '<input type="hidden" name="type" value="' . esc_attr($type) . '" />';
			$ret .= '<input type="hidden" name="action" value="tourmaster_order_edit" />';
			
			if( $type != 'new_order' ){
				$ret .= '<input type="submit" class="tourmaster-order-edit-submit" value="' . esc_attr__('Submit', 'tourmaster') . '" />';
			}

			$ret .= '</form>';

			return $ret;
		}
	}

	add_action('wp_ajax_tourmaster_order_edit', 'tourmaster_order_edit');
	if( !function_exists('tourmaster_order_edit') ){
		function tourmaster_order_edit(){
			$data = tourmaster_process_post_data($_POST);
			// add - edit order
			if( $data['type'] == 'new_order' ){

				$result = tourmaster_get_booking_data(array('id' => $data['tid']), array('single' => true));
				$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);
				
				// get tour option
				$tour_option = tourmaster_get_post_meta($data['tour-id'], 'tourmaster-tour-option');
				$date_price = tourmaster_get_tour_date_price($tour_option, $data['tour-id'], $data['tour-date']);
				$date_price = tourmaster_get_tour_date_price_package($date_price, $data);
		
				// traveller amount
				if( $date_price['pricing-method'] == 'group' ){
					$traveller_amount = 1;
				}else{
					$traveller_amount = tourmaster_get_tour_people_amount($tour_option, $date_price, $data, 'all');
				}

				$fields = array( 
					'tid', 'tour-id', 'tour-date', 'package', 
					'group',
					'tour-people', 'tour-adult', 'tour-children', 'tour-student', 'tour-infant', 
					'tour-male', 'tour-female',
					'tour-room' 
				);
				foreach( $fields as $field ){
					if ( !empty($data[$field]) ){
						$booking_detail[$field] = $data[$field];
					}else{
						if( !empty($booking_detail[$field]) ){
							unset($booking_detail[$field]);
						}
					}
				}

				// check the service
				if( empty($booking_detail['service']) ){
					$booking_detail = tourmaster_set_mandatory_service($tour_option, $booking_detail);
				}

				// calculate the price
				$tour_price = tourmaster_get_tour_price($tour_option, $date_price, $booking_detail);
				$package_group_slug = empty($date_price['group-slug'])? '': $date_price['group-slug'];

				// built old traveller amount / contact / billing for booking_detail
				$tid = tourmaster_insert_booking_data($booking_detail, $tour_price, $traveller_amount, $package_group_slug, null, true);

				$ret = array('status' => 'success');
				if( empty($booking_detail['tid']) && !empty($data['current_url']) ){
					$ret['redirect'] = add_query_arg(array('single'=>$tid), $data['current_url']);
				}

				die(json_encode($ret));

			// traveller
			}else if( $data['type'] == 'traveller' ){

				if( !empty($_POST['traveller_first_name']) ){
					$result = tourmaster_get_booking_data(array('id' => $data['tid']), array('single' => true));
					$tour_option = tourmaster_get_post_meta($data['tour-id'], 'tourmaster-tour-option');
					$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);
					
					$traveller_info = array();
					if( !empty($data['traveller_title']) ){
						$traveller_info['title'] = $data['traveller_title'];
						$booking_detail['traveller_title'] = $data['traveller_title'];
					}
					if( !empty($data['traveller_first_name']) ){
						$traveller_info['first_name'] = $data['traveller_first_name'];
						$booking_detail['traveller_first_name'] = $data['traveller_first_name'];
					}
					if( !empty($data['traveller_last_name']) ){
						$traveller_info['last_name'] = $data['traveller_last_name'];
						$booking_detail['traveller_last_name'] = $data['traveller_last_name'];
	 				}
					if( !empty($data['traveller_passport']) ){
						$traveller_info['passport'] = $data['traveller_passport'];
						$booking_detail['traveller_passport'] = $data['traveller_passport'];
					}

					if( !empty($tour_option['additional-traveller-fields']) ){
						$additional_traveller_fields = $tour_option['additional-traveller-fields'];
					}else{
						$additional_traveller_fields = tourmaster_get_option('general', 'additional-traveller-fields', '');
					}
					if( !empty($additional_traveller_fields) ){
						$additional_traveller_fields = tourmaster_read_custom_fields($additional_traveller_fields);
						foreach( $additional_traveller_fields as $field ){
							if( !empty($data['traveller_' . $field['slug']]) ){
								$traveller_info[$field['slug']] = $data['traveller_' . $field['slug']];
								$booking_detail['traveller_' . $field['slug']] = $data['traveller_' . $field['slug']];
							}
						}
					}

					tourmaster_update_booking_data(
						array(
							'traveller_info' => json_encode($traveller_info),
							'booking_detail' => json_encode($booking_detail)
						), 
						array('id' => $data['tid']), 
						array('%s', '%s'), 
						array('%d')
					);

					die(json_encode(array('status' => 'success')));
				}

			// additional notes
			}else if( $data['type'] == 'additional_notes' ){
				
				if( !empty($data['additional_notes']) ){
					$result = tourmaster_get_booking_data(array('id' => $data['tid']), array('single' => true));
					$contact_detail = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
					$contact_detail['additional_notes'] = $data['additional_notes'];

					$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);
					$booking_detail['additional_notes'] = $data['additional_notes'];

					tourmaster_update_booking_data(
						array(
							'contact_info' => json_encode($contact_detail),
							'booking_detail' => json_encode($booking_detail)
						), 
						array('id' => $data['tid']), 
						array('%s', '%s'), 
						array('%d')
					);

					die(json_encode(array('status' => 'success')));
				}else{
					die(json_encode(array('status' => 'failed', 'message' => esc_html__('Please fill the additional notes.', 'tourmaster'))));
				}

			// contact & billing details
			}else if( $data['type'] == 'contact_details' || $data['type'] == 'billing_details' ){

				$result = tourmaster_get_booking_data(array('id' => $data['tid']), array('single' => true));
				if( $data['type'] == 'contact_details' ){
					$updated_field = 'contact_info';
					$booking_prefix = '';
					$values = empty($result->contact_info)? array(): json_decode($result->contact_info, true);
				}else if( $data['type'] == 'billing_details' ){
					$updated_field = 'billing_info';
					$booking_prefix = 'billing_';
					$values = empty($result->billing_info)? array(): json_decode($result->billing_info, true);
				}

				$form_fields = tourmaster_get_payment_contact_form_fields($result->tour_id);
				$booking_detail = empty($result->booking_detail)? array(): json_decode($result->booking_detail, true);
				
				foreach( $form_fields as $field_slug => $field ){
					if( !empty($data[$field_slug]) ){
						$values[$field_slug] = $data[$field_slug];
						$booking_detail[$booking_prefix . $field_slug] = $data[$field_slug];
					}

					// validate
					if( !empty($field['required']) && empty($data[$field_slug]) ){
						die(json_encode(array('status' => 'failed', 'message' => esc_html__('Please fill all required fields.', 'tourmaster'))));
					}
					if( $field['type'] == 'email' && !empty($data[$field_slug]) ){
						if( !is_email($data[$field_slug]) ){
							die(json_encode(array('status' => 'failed', 'message' => esc_html__('An E-mail is incorrect.', 'tourmaster'))));
						}
					}
				}

				tourmaster_update_booking_data(
					array(
						$updated_field => json_encode($values),
						'booking_detail' => json_encode($booking_detail)
					), 
					array('id' => $data['tid']), 
					array('%s', '%s'), 
					array('%d')
				);

				die(json_encode(array('status' => 'success')));

			// price
			}else if( $data['type'] == 'price' ){

				$result = tourmaster_get_booking_data(array('id' => $data['tid']), array('single' => true));
				$people_types = array(
					'traveller' => esc_html__('Traveller', 'tourmaster'),
					'adult' => esc_html__('Adult', 'tourmaster'),
					'male' => esc_html__('Male', 'tourmaster'),
					'female' => esc_html__('Female', 'tourmaster'),
					'children' => esc_html__('Children', 'tourmaster'),
					'student' => esc_html__('Student', 'tourmaster'),
					'infant' => esc_html__('Infant', 'tourmaster'),
				);
				$pricing_info = json_decode($result->pricing_info, true);
				$price_breakdown = $pricing_info['price-breakdown'];
				$price_breakdown['sub-total-price'] = 0;

				// base price
				foreach( $people_types as $people_slug => $people_type ){
					if( isset($price_breakdown[$people_slug . '-base-price']) && isset($data[$people_slug . '-base-price']) ){
						$price_breakdown[$people_slug . '-base-price'] = $data[$people_slug . '-base-price'];
					}
					if( !empty($price_breakdown[$people_slug . '-base-price']) && !empty($price_breakdown[$people_slug . '-base-price']) ){
						$price_breakdown['sub-total-price'] += $price_breakdown[$people_slug . '-base-price'] * $price_breakdown[$people_slug . '-amount'];
					}
				}

				// group price
				if( isset($price_breakdown['group-price']) && isset($data['group-price']) ){
					$price_breakdown['group-price'] = $data['group-price'];
				}
				if( !empty($price_breakdown['group-price']) ){
					$price_breakdown['sub-total-price'] += $price_breakdown['group-price'];
				}

				// room
				if( !empty($price_breakdown['room']) ){
					$count = 0;
					foreach( $price_breakdown['room'] as $room ){
						
						if( isset($room['base-price']) && isset($data['room-base-price' . $count]) ){
							$room['base-price'] = $data['room-base-price' . $count];
						}
						$price_breakdown['sub-total-price'] += $room['base-price'];

						foreach( $people_types as $people_slug => $people_type ){

							if( isset($room['additional-' . $people_slug . '-price']) && isset($data['additional-' . $people_slug . '-price' . $count]) ){
								$room['additional-' . $people_slug . '-price'] = $data['additional-' . $people_slug . '-price' . $count];
							}
							$price_breakdown['sub-total-price'] += $room['additional-' . $people_slug . '-price'] * $room['additional-' . $people_slug . '-amount'];
						}

						$price_breakdown['room'][$count] = $room;
						$count++;
					}
				}

				// additional service

				// service service-amount service-price
				if( !empty($data['service']) ){
					$price_breakdown['additional-service'] = array();

					foreach( $data['service'] as $key => $service_id ){
						if( !empty($data['service-amount'][$key]) && !empty($data['service-price'][$key]) ){
							$service_option = get_post_meta($service_id, 'tourmaster-service-option', true);
							$service_summary = array( 'per' => $service_option['per'] );
							$service_summary['amount'] = $data['service-amount'][$key];
							$service_summary['price-one'] = floatval($data['service-price'][$key]);
							$service_summary['price'] = floatval($service_summary['amount']) * $service_summary['price-one'];

							$price_breakdown['additional-service'][$service_id] = $service_summary;
						}
					}

					$count = 0;
					foreach( $price_breakdown['additional-service'] as $service_id => $service ){
						if( isset($data['additional-service'][$count]) ){
							$service['price-one'] = $data['additional-service'][$count];
							$service['price'] = $service['price-one'] * $service['amount'];
						}
						
						if( !empty($service['price']) ){
							$price_breakdown['sub-total-price'] += $service['price'];
						}
						
						$price_breakdown['additional-service'][$service_id] = $service;
						$count++;
					} 
				}else{
					unset($price_breakdown['additional-service']);
				}

				$pricing_info['total-price'] = $price_breakdown['sub-total-price'];

				// group discount
				if( isset($data['group-discount']) ){
					$price_breakdown['group-discount-rate'] = $data['group-discount'];
					if( strpos($data['group-discount'], '%') === false ){
						$pricing_info['total-price'] -= floatval($data['group-discount']);
					}else{
						$pricing_info['total-price'] = ($pricing_info['total-price'] * (100 - floatval($data['group-discount']))) / 100;
					}

					$price_breakdown['group-discounted-price'] = $pricing_info['total-price'];
				}

				// coupon
				$coupon_code = '';
				if( isset($data['coupon-code']) ){
					$price_breakdown['coupon-code'] = $data['coupon-code'];
					$coupon_code = $data['coupon-code'];
				}
				if( isset($data['coupon-text']) ){
					if( strpos($data['coupon-text'], '%') === false ){
						$price_breakdown['coupon-text'] = '';
						$price_breakdown['coupon-amount'] = floatval($data['coupon-text']);
					}else{
						$price_breakdown['coupon-text'] = $data['coupon-text'];
						$price_breakdown['coupon-amount'] = ($pricing_info['total-price'] * floatval($data['coupon-text'])) / 100;
					}

					$pricing_info['total-price'] -= $price_breakdown['coupon-amount'];
				}

				// tax
				$tax_rate = tourmaster_get_option('general', 'tax-rate', 0);
				$service_fee = tourmaster_get_option('general', 'service-fee', 0);
				if( !empty($tax_rate) ){
					$price_breakdown['tax-rate'] = $tax_rate;
					$price_breakdown['tax-due'] = ($pricing_info['total-price'] * $tax_rate) / 100;
					$pricing_info['total-price'] += $price_breakdown['tax-due'];
				}else{
					unset($price_breakdown['tax-rate']);
					unset($price_breakdown['tax-due']);
				}

				if( !empty($service_fee) ){
					$price_breakdown['service-fee'] = $service_fee;
					$price_breakdown['service-due'] = $service_fee;
					$pricing_info['total-price'] += $price_breakdown['service-due'];
				}else{
					unset($price_breakdown['service-fee']);
					unset($price_breakdown['service-due']);
				}

				// update the data
				$pricing_info['price-breakdown'] = $price_breakdown;
				
				tourmaster_update_booking_data(
					array(
						'pricing_info' => json_encode($pricing_info), 
						'total_price' => $pricing_info['total-price'], 
						'coupon_code' => $coupon_code
					), 
					array('id' => $data['tid']), 
					array('%s', '%s', '%s'), 
					array('%d')
				);

				die(json_encode(array('status' => 'success')));

			} // end if
			
		}
	}