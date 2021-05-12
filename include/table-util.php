<?php
	/*	
	*	Tourmaster Plugin
	*	---------------------------------------------------------------------
	*	for table query
	*	---------------------------------------------------------------------
	*/

	if( !function_exists('tourmaster_insert_booking_data') ){
		function tourmaster_insert_booking_data( $booking_detail, $tour_price, $traveller_amount = 0, $package_group_slug = '', $order_status = 'pending', $admin_update = false ){

			global $wpdb;

			$tour_option = tourmaster_get_post_meta($booking_detail['tour-id'], 'tourmaster-tour-option');

			// prepare the field to be inserted
			$contact_info = array();
			$billing_info = array();
			$traveller_info = array();

			if( !empty($booking_detail['traveller_first_name']) && !empty($booking_detail['traveller_last_name']) ){


				$traveller_info = array(
					'title' => empty($booking_detail['traveller_title'])? '': $booking_detail['traveller_title'],
					'first_name' => $booking_detail['traveller_first_name'],
					'last_name' => $booking_detail['traveller_last_name'],
					'passport' => empty($booking_detail['traveller_passport'])? '': $booking_detail['traveller_passport'],
				);

				if( !empty($booking_detail['additional-traveller-fields']) ){
					$additional_traveller_fields = $booking_detail['additional-traveller-fields'];
				}else{
					$additional_traveller_fields = tourmaster_get_option('general', 'additional-traveller-fields', '');
				}				
				if( !empty($additional_traveller_fields) ){
					$additional_traveller_fields = tourmaster_read_custom_fields($additional_traveller_fields);
					foreach( $additional_traveller_fields as $field ){
						if( !empty($booking_detail['traveller_' . $field['slug']]) ){
							$traveller_info[$field['slug']] = $booking_detail['traveller_' . $field['slug']]; 
						}
					}
				}
			}
			$contact_fields = tourmaster_get_payment_contact_form_fields($booking_detail['tour-id']);
			foreach( $contact_fields as $field_slug => $contact_field ){
				if( !empty($booking_detail[$field_slug]) ){
					$contact_info[$field_slug] = $booking_detail[$field_slug];
				}

				if( !empty($booking_detail['billing_' . $field_slug]) ){
					$billing_info[$field_slug] = $booking_detail['billing_' . $field_slug];
				}
			}
			if( !empty($booking_detail['additional_notes']) ){
				$contact_info['additional_notes'] = $booking_detail['additional_notes'];
			}

			// traveller amount
			$male_amount = empty($traveller_amount['male'])? 0: $traveller_amount['male'];
			$female_amount = empty($traveller_amount['female'])? 0: $traveller_amount['female'];
			$traveller_amount = is_array($traveller_amount)? $traveller_amount['sum']: $traveller_amount;

			// inserting the data
			$data = array(
				'tour_id' => $booking_detail['tour-id'], 
				'booking_date' => current_time('mysql'),
				'travel_date' => $booking_detail['tour-date'],
				'package_group_slug' => $package_group_slug,
				'contact_info' => json_encode($contact_info),
				'billing_info' => json_encode($billing_info),
				'traveller_info' => json_encode($traveller_info),
				'order_status' => $order_status,
				'total_price' => $tour_price['total-price'],
				'pricing_info' => json_encode($tour_price),
				'booking_detail' => json_encode($booking_detail),
				'traveller_amount' => $traveller_amount,
				'male_amount' => $male_amount,
				'female_amount' => $female_amount
			);
			if( $tour_price['total-price'] <= 0 ){
				$data['order_status'] = 'approved';
			} 
			$format = array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d');

			$price_breakdown = $tour_price['price-breakdown'];
			if( !empty($price_breakdown['coupon-code']) ){
				$data = array_merge($data, array('coupon_code' => $price_breakdown['coupon-code']));
				$format = array_merge($format, array('%s'));
			}

			if( empty($booking_detail['tid']) || $booking_detail['tid'] == 'false' ){

				// insert new data
				$data = array_merge(array(
					'user_id' => get_current_user_id(),
				), $data);
				$format = array_merge(array('%d'), $format);

				if( $wpdb->insert("{$wpdb->prefix}tourmaster_order", $data, $format) ){
					return $wpdb->insert_id;
				}else{
					return false;
				}

			}else{

				// update existing data
				$where = array(
					'id' => $booking_detail['tid']
				);
				$where_format = array('%d');
				if( !$admin_update ){
					$where['user_id'] = get_current_user_id();
					$where_format[] = '%d';
				}
				unset($data['order_status']);
				unset($format[7]);

				if( tourmaster_update_booking_data($data, $where, $format, $where_format) === false ){
					return false;
				}else{
					return $booking_detail['tid'];
				}

			}

		} // tourmaster_insert_booking_data
	}

	if( !function_exists('tourmaster_get_booking_data') ){
		function tourmaster_get_booking_data( $conditions = array(), $settings = null, $column = '*' ){
			global $wpdb; 
			$first_condition = true;

			$sql  = 'SELECT ' . $column . ' FROM ' . $wpdb->prefix . 'tourmaster_order as order_table ';

			if( !empty($settings['with-review']) ){
				$sql .= 'LEFT JOIN ' . $wpdb->prefix . 'tourmaster_review as review_table ';
				$sql .= 'ON order_table.id = review_table.order_id ';
			}else if( !empty($settings['only-review']) ){
				$sql .= 'RIGHT JOIN ' . $wpdb->prefix . 'tourmaster_review as review_table ';
				$sql .= 'ON order_table.id = review_table.order_id ';
			}

			// where clause
			foreach($conditions as $condition_slug => $condition){
				if( $first_condition ){
					$first_condition = false;

					$sql .= 'WHERE ';
				}else{
					$sql .= 'AND ';
				}

				
				if( !empty($condition) && ($condition == 'IS NOT NULL' || $condition == 'IS NULL') ){
					$sql .= esc_sql($condition_slug);
					$sql .= ' ' . $condition . ' ';
				}else if( is_array($condition) ){
					if( empty($condition['hide-prefix']) ){
						$sql .= esc_sql($condition_slug);
					}

					if( !empty($condition['condition']) && !empty($condition['value']) ){
						$sql .= $condition['condition'] . '\'' . esc_sql($condition['value']) . '\' ';
					}else if( !empty($condition['custom']) ){
						$sql .= $condition['custom'];
					}
				}else{
					$sql .= esc_sql($condition_slug);
					$sql .= '=\'' . esc_sql($condition) . '\' ';
				}
			}

			// order 
			$settings['orderby'] = empty($settings['orderby'])? 'id': $settings['orderby'];
			$settings['order'] = empty($settings['order'])? 'desc': $settings['order'];
			$sql .= 'ORDER BY ' . esc_sql($settings['orderby']) . ' ' . esc_sql($settings['order']) . ' ';

			if( !empty($settings['paged']) && !empty($settings['num-fetch']) ){
				$paged = intval($settings['paged']);
				$num_fetch = intval($settings['num-fetch']);

				if( $settings['paged'] <= 1 ){
					$sql .= 'LIMIT ' . esc_sql($num_fetch);
				}else{
					$sql .= 'LIMIT ' . esc_sql(($paged - 1) * $num_fetch) . ',' . $num_fetch;
				}
			}

			// pagination	
			if( !empty($settings['single']) ){
				return $wpdb->get_row($sql);	
			}else if( $column == 'COUNT(*)' || $column == 'count(*)' || strpos($column, 'SUM') !== false ){
				return $wpdb->get_var($sql);
			}else{
				return $wpdb->get_results($sql);	
			}
			
		} // tourmaster_get_booking_data
	}

	if( !function_exists('tourmaster_update_booking_data') ){
		function tourmaster_update_booking_data( $data, $where, $format, $where_format ){
			global $wpdb;
			return $wpdb->update($wpdb->prefix . 'tourmaster_order', $data, $where, $format, $where_format);
		}
	}		

	if( !function_exists('tourmaster_remove_booking_data') ){
		function tourmaster_remove_booking_data( $id, $user_id = '' ){
			global $wpdb;

			$args = array( 'id' => $id ); 
			if( !empty($user_id) ){
				$args['user_id'] = $user_id;
			}

			return $wpdb->delete($wpdb->prefix . 'tourmaster_order', $args);
		}
	}

	if( !function_exists('tourmaster_insert_review_data') ){
		function tourmaster_insert_review_data( $review ){
		
			global $wpdb;
			$data = array(
				'review_tour_id' => $review['tour_id'],
				'review_score' => $review['score'],
				'review_type' => $review['type'],
				'review_description' => $review['description'],
				'review_date' => empty($review['date'])? current_time('mysql'): $review['date']
			);
			$format = array('%d', '%d', '%s', '%s', '%s');

			if( !empty($review['order_id']) ){
				$data['order_id'] = $review['order_id'];
				$format[] = '%d';
			}

			if( !empty($review['name']) ){
				$data['reviewer_name'] = $review['name'];
				$format[] = '%s';
			}

			if( !empty($review['email']) ){
				$data['reviewer_email'] = $review['email'];
				$format[] = '%s';
			}

			if( $wpdb->insert("{$wpdb->prefix}tourmaster_review", $data, $format) ){
				return $wpdb->insert_id;
			}else{
				return false;
			}
		} // tourmaster_insert_review_data
	}

	if( !function_exists('tourmaster_update_review_data') ){
		function tourmaster_update_review_data( $data, $where, $format, $where_format ){
			global $wpdb;
			return $wpdb->update("{$wpdb->prefix}tourmaster_review", $data, $where, $format, $where_format);
		} // tourmaster_update_review_data
	}

	if( !function_exists('tourmaster_get_review_data') ){
		function tourmaster_get_review_data( $id ){
			global $wpdb;
			return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}tourmaster_review WHERE review_id = {$id}" );
		} 
	}

	if( !function_exists('tourmaster_remove_review_data') ){
		function tourmaster_remove_review_data( $id ){
			global $wpdb;
			$args = array( 'review_id' => $id ); 
			return $wpdb->delete($wpdb->prefix . 'tourmaster_review', $args);
		}
	}