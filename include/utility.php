<?php
	/*	
	*	Utility function for uses
	*/
	if( !function_exists('tourmaster_set_locale') ){
		function tourmaster_set_locale($post_id){
			// global $sitepress;
			// 
			// if( function_exists('pll_get_post_language') ){
			// 	$new_locale = pll_get_post_language($post_id, 'locale');
			// }else if( !empty($sitepress) ){
			// 	$post_info = apply_filters('wpml_post_language_details', NULL, get_the_ID());
			// 	if( !empty($post_info['locale']) ){
			// 		$new_locale = $post_info['locale'];
			// 	}
			// }
			// 
			// if( !empty($new_locale) ){
			// 	switch_to_locale($new_locale);
			// 	unset($GLOBALS['tourmaster_general']);
			// }

		}
	}
	if( !function_exists('tourmaster_return_locale') ){
		function tourmaster_return_locale(){	
			// global $sitepress;
			// 	
			// if( function_exists('pll_get_post_language') || !empty($sitepress) ){
			// 	restore_previous_locale();
			// 	unset($GLOBALS['tourmaster_general']);
			// }
		}
	}


	add_action('wp_enqueue_scripts', 'tourmaster_include_lightbox');
	if( !function_exists('tourmaster_include_lightbox') ){
		function tourmaster_include_lightbox($atts){	
			if( !function_exists('gdlr_core_get_lightbox_atts') ){
				wp_enqueue_style('lightgallery', TOURMASTER_URL . '/plugins/lightgallery/lightgallery.css');
				wp_enqueue_script('lightgallery', TOURMASTER_URL . '/plugins/lightgallery/lightgallery.js', array('jquery'), false, true);
			}
		}
	}

	// lightbox 
	if( !function_exists('tourmaster_get_lightbox_atts') ){
		function tourmaster_get_lightbox_atts($atts){
			if( function_exists('gdlr_core_get_lightbox_atts') ){
				return gdlr_core_get_lightbox_atts($atts);
			}else{
				$ret  = ' class="tourmaster-lightgallery ' . (empty($atts['class'])? '': $atts['class']) . '" ';
				$ret .= empty($atts['url'])? '': ' href="' . esc_url($atts['url']) . '"';
				$ret .= empty($atts['caption'])? '': ' data-sub-html="' . esc_attr($atts['caption']) . '"';
				$ret .= empty($atts['group'])? '': ' data-lightbox-group="' . esc_attr($atts['group']) . '"';

				return $ret;
			}
		}
	}

	// price comparing function
	if( !function_exists('tourmaster_compare_price') ){
		function tourmaster_compare_price( $price1, $price2 ){

			if( abs(floatval($price1) - floatval($price2)) <= 0.01 ){
				return true;
			}else{
				return false;
			}

		}
	}

	// Function to get the client ip address
	if( !function_exists('tourmaster_get_client_ip') ){
		function tourmaster_get_client_ip(){
		    $ipaddress = '';
		    if( !empty($_SERVER['HTTP_CLIENT_IP']) ){
		        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		    }else if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ){
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    }else if( !empty($_SERVER['HTTP_X_FORWARDED']) ){
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		    }else if( !empty($_SERVER['HTTP_FORWARDED_FOR']) ){
		        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		    }else if( !empty($_SERVER['HTTP_FORWARDED']) ){
		        $ipaddress = $_SERVER['HTTP_FORWARDED'];
		    }else if( !empty($_SERVER['REMOTE_ADDR']) ){
		        $ipaddress = $_SERVER['REMOTE_ADDR'];
		    }else{
		        $ipaddress = 'UNKNOWN';
		    }
		 
		    return $ipaddress;
		}
	}
	if( !function_exists('tourmaster_money_format') ){
		function tourmaster_money_format( $amount, $digit = -1 ){
			if( $digit == -1 ){
				$digit = tourmaster_get_option('general', 'price-breakdown-decimal-digit', '2');
			
			// custom
			}else if( $digit == -2 ){
				if( $amount == intval($amount) ){
					$digit = 0;
				}else{
					$digit = tourmaster_get_option('general', 'price-breakdown-decimal-digit', '2');
				}
			
			}

			// round every number down
			// $amount = intval($amount * pow(10, 2)) / pow(10, 2);
			$amount = round($amount, $digit);

			// format number
			$thousand = tourmaster_get_option('general', 'price-thousand-separator', ',');
			$decimal_sign = tourmaster_get_option('general', 'price-decimal-separator', '.');

			$format = tourmaster_get_option('general', 'money-format', '$NUMBER');
			$amount = number_format($amount, $digit, $decimal_sign, $thousand);
			return str_replace('NUMBER', $amount, $format);
		}
	}
	if( !function_exists('tourmaster_date_format') ){
		function tourmaster_date_format( $date, $format = '' ){
			$format = empty($format)? get_option('date_format'): $format;
			$date = is_numeric($date)? $date: strtotime($date);
			return date_i18n($format, $date);
		}
	}
	if( !function_exists('tourmaster_time_format') ){
		function tourmaster_time_format( $date, $format = '' ){
			$format = empty($format)? get_option('time_format'): $format;
			$date = is_numeric($date)? $date: strtotime($date);
			return date_i18n($format, $date);
		}
	}
	if( !function_exists('tourmaster_time_offset') ){
		function tourmaster_time_offset( $time, $offset ){
			$time_offset = 0;

			// change hh:mm time to second
			if( !empty($time) ){
				$start_time = explode(':', $time);
				if( !empty($start_time[0]) ){
					$time_offset += intval($start_time[0]) * 60 * 60;
				}
				if( !empty($start_time[1]) ){
					$time_offset += intval($start_time[1]) * 60;
				}				
			}

			// last minute booking in hours
			if( !empty($offset) ){
				$time_offset -= intval($offset) * 60 * 60;
			}

			return $time_offset;
		}
	}

	if( !function_exists('tourmaster_lightbox_content') ){
		function tourmaster_lightbox_content( $settings = array() ){

			$ret  = '<div class="tourmaster-lightbox-content-wrap" data-tmlb-id="' . $settings['id'] . '" >';
			if( !empty($settings['title']) ){
				$ret .= '<div class="tourmaster-lightbox-head" >';
				$ret .= '<h3 class="tourmaster-lightbox-title" >' . $settings['title'] . '</h3>';
				$ret .= '<i class="tourmaster-lightbox-close icon_close" ></i>';
				$ret .= '</div>';
			}

			if( !empty($settings['content']) ){
				$ret .= '<div class="tourmaster-lightbox-content" >' . $settings['content'] . '</div>';
			}
			$ret .= '</div>';

			return $ret;
		} // tourmaster_lightbox_content
	} 

	//custom lightbox
	if( !function_exists('tourmaster_lightbox_content_2') ){
		function tourmaster_lightbox_content_2( $settings = array() ){

			$ret  = '<div class="tourmaster-lightbox-content-wrap" data-tmlb2-id="' . $settings['id'] . '" >';
			if( !empty($settings['title']) ){
				$ret .= '<div class="tourmaster-lightbox-head" >';
				$ret .= '<h3 class="tourmaster-lightbox-title" >' . $settings['title'] . '</h3>';
				$ret .= '<i class="tourmaster-lightbox-close icon_close" ></i>';
				$ret .= '</div>';
			}

			if( !empty($settings['content']) ){
				$ret .= '<div class="tourmaster-lightbox-content" >' . $settings['content'] . '</div>';
			}
			$ret .= '</div>';

			return $ret;
		} // tourmaster_lightbox_content
	}

	if( !function_exists('tourmaster_match_curly_braces') ){
		function tourmaster_match_curly_braces( $string ){
			$ret = array();

			$start = strpos($string, '{');
			$end = 0;
			while( $start !== false && $end !== false ){
				$string = substr($string, $start);
				$start = 0;
				$end = strpos($string, '}');

				$ret[] = substr($string, $start, $end + 1);
				$start = strpos($string, '{', $end);
			}

			return $ret;
		}
	}
	if( !function_exists('tourmaster_read_custom_fields') ){
		function tourmaster_read_custom_fields( $string ){

			$custom_fields = array();

			// match every { }
			$matches = tourmaster_match_curly_braces($string);
			if( !empty($matches) ){
				foreach( $matches as $match ){

					$field = array();

					// remove unnecessary string out and separate each attribute
					$match = str_replace(array("{", "}", "\r\n", "\n"), "", $match);
					$match = explode(',', $match);
					if( !empty($match) ){
						foreach( $match as $att ){
							$att = explode('=', $att);
							if( !empty($att) && sizeof($att) == 2 ){
								if( $att[0] == 'options' ){
									$field[$att[0]] = array();
									$options = explode('/', $att[1]);
									if( !empty($field['title']) ){
										$field[$att[0]][''] = $field['title'];
									}

									foreach($options as $option){
										$field[$att[0]][$option] = $option;
									}
								}else if( $att[0] == 'type' && $att[1] == 'country' ){
									$field['type'] = 'combobox';
									$field['options'] = tourmaster_get_country_list();
									$field['default'] = tourmaster_get_option('general', 'user-default-country', '');
								}else{
									$field[$att[0]] = $att[1];
								}
								
							}  
						}
					}

					$custom_fields[$field['slug']] = $field;	
				}
			}

			return $custom_fields;
		}
	}
	if( !function_exists('tourmaster_get_form_field') ){
		function tourmaster_get_form_field( $settings, $slug, $value = '' ){

			if( isset($settings['echo']) && $settings['echo'] === false ){
				ob_start();
			} 

			$user_id = get_current_user_id();
			$extra_class = 'tourmaster-' . $slug . '-field-' . $settings['slug'];
			$field_value = '';
			if( !empty($value) ){
				$field_value = $value;
			}else if( !empty($_POST[$settings['slug']]) ){
				$field_value = $_POST[$settings['slug']];
			}else if( !empty($user_id) ){
				$field_value = tourmaster_get_user_meta($user_id, $settings['slug']);
			}

			if( empty($field_value) && !empty($settings['default']) ){
				$field_value = $settings['default'];
			}

			$data = '';
			if( !empty($settings['data']) && !empty($settings['data']['slug']) && !empty($settings['data']['value']) ){
				$data = ' data-' . esc_attr($settings['data']['slug']) . '="' . esc_attr($settings['data']['value']) . '" ';
			}
			if( !empty($settings['type']) ){
				$extra_class .= ' tourmaster-type-' . $settings['type'];
			}

			echo '<div class="tourmaster-html-option-item tourmaster-' . esc_attr($slug) . '-field ' . esc_attr($extra_class) . ' clearfix" >';

			if ($settings['slug'] == 'address') {
				echo '<h6>Select your address</h6>';
				echo '<div id="map-settings2" style="height:350px; width:45%"></div>';
				echo '</br>';
			}
			

			echo '<div class="tourmaster-head" >';
			if( !empty($settings['title']) ){
				echo $settings['title'];
			}
			if( !empty($settings['required']) && ($settings['required'] === true || $settings['required'] == "true") ){
				echo '<span class="tourmaster-req" >*</span>';
				$data .= ' data-required ';
			}
			echo '</div>';

			echo '<div class="tourmaster-tail clearfix" >';
			if( !empty($settings['pre-input']) ){
				echo $settings['pre-input'];
			}
			
			switch($settings['type']){
				case 'textarea':
					echo '<textarea name="' . esc_attr($settings['slug']) . '" ' . $data . ' >' . esc_textarea($field_value) . '</textarea>';
					break;
				case 'email':
					echo '<input type="email" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'text':
					echo '<input id="'. esc_attr($settings['slug']) .'-map-set" type="text" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'url':
					echo '<input id="'. esc_attr($settings['slug']) .'-map-set" type="url" name="' . esc_attr($settings['slug']) . '" value="' . $field_value . '" ' . $data . ' />';
					break;
				case 'price-edit':
					echo '<div class="tourmaster-price-edit-head" >';
					echo '<input type="text" name="' . esc_attr($settings['slug']) . ((!empty($settings['data-type']) && $settings['data-type'] == 'array')? '[]': '') . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					echo '</div>';
					if( !empty($settings['description']) ){
						echo '<div class="tourmaster-price-edit-tail">' . $settings['description'] . '</div>';
					}
					break;
				case 'file':
					echo '<label class="tourmaster-file-label" >';
					echo '<span class="tourmaster-file-label-text" data-default="' . esc_attr__('Click to select a file', 'tourmaster') . '" >' . esc_html__('Click to select a file', 'tourmaster') . '</span>';
					echo '<input type="file" name="' . esc_attr($settings['slug']) . '" ' . $data . ' />';
					echo '</label>';
					break;
				case 'password':
					echo '<input type="password" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'combobox':
					echo '<div class="tourmaster-combobox-wrap" >';
					echo '<select name="' . esc_attr($settings['slug']) . '" ' . $data . ' >';
					foreach( $settings['options'] as $option_val => $option_title ){
						if ($settings['slug'] == 'billing_country') {
							if ($option_val == 'United States of America (USA)') {
								echo '<option value="' . esc_attr($option_val) . '" selected >' . $option_title . '</option>';
							} else {
								echo '<option value="' . esc_attr($option_val) . '" ' . ($field_value == $option_val? 'selected': '') . ' >' . $option_title . '</option>';
							}
						} else {
							echo '<option value="' . esc_attr($option_val) . '" ' . ($field_value == $option_val? 'selected': '') . ' >' . $option_title . '</option>';
						}
						
					}
					echo '</select>';
					echo '</div>';
					break;

				case 'datepicker':
						echo '<input type="text" class="tourmaster-datepicker" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" />';
						echo '<i class="fa fa-calendar" ></i>';
					break;

				case 'upload' : 
					$value = '';
					if( isset($settings['value']) ){
						$value = $settings['value'];
					}
					
					echo '<div class="tourmaster-html-option-upload-appearance ' . (empty($field_value)? '': 'tourmaster-active') . '" >';
					echo '<input type="hidden" class="tourmaster-html-option-upload" data-type="upload" data-slug="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ';
					echo ' name="' . esc_attr($settings['slug']) . '"';
					echo ' />';
					$attachment_id = $field_value;
					echo '<div class="tourmaster-upload-image-container" style="' . (empty($field_value)? '': 'background-image: url(\'' . esc_url(wp_get_attachment_url($field_value)) . '\');') . '" ></div>';
					echo '<p style="display: none">'.$attachment_id.'</p>';
					echo '<div class="tourmaster-upload-image-overlay" >';
					echo '<div class="tourmaster-upload-image-button-hover">';
					echo '<span class="tourmaster-upload-image-button tourmaster-upload-image-add"><i class="fa fa-plus" ></i></span>';
					echo '<span class="tourmaster-upload-image-button tourmaster-upload-image-remove"><i class="fa fa-minus" ></i></span>';
					echo '</div>'; 
					echo '</div>'; 
					echo '</div>';

					break;

				case 'custom':

					echo '<div class="tourmaster-html-option-custom" data-type="custom" data-item-type="' . esc_attr($settings['item-type']) . '" data-slug="' . esc_attr($settings['slug']) . '" >';
					echo '<input type="hidden" class="tourmaster-html-option-upload" data-type="upload" data-slug="' . esc_attr($settings['slug']) . '" value="' . esc_attr(preg_replace('/\\\"/',"\"", $field_value)) . '" name="' . esc_attr($settings['slug']) . '"';
					echo '>';
					if( !empty($settings['settings']) ){
						echo '<span class="tourmaster-html-option-custom-settings" data-value="' . esc_attr(json_encode($settings['settings'])) . '" ></span>';
					}
					if( !empty($settings['options']) ){
						echo '<span class="tourmaster-html-option-custom-options" data-value="' . esc_attr(json_encode($settings['options'])) . '" ></span>';
					}
					if( !empty($field_value) ){
						echo '<span class="tourmaster-html-option-custom-value" data-value="' . esc_attr(preg_replace('/\\\"/',"\"", $field_value)) . '" ></span>';
					}
					echo '</div>';
		
					break;
					
				case 'date':
					echo '<div class="tourmaster-date-select" >';
					$selected_date = explode('-', $field_value);

					$date = empty($selected_date[2])? '': intval($selected_date[2]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-date" >';
					echo '<select type="text" data-type="date" >';
					echo '<option value="" ' . (empty($date)? 'selected': '' ) . ' >' . esc_html__('Date', 'tourmaster') . '</option>';
					for( $i = 1; $i <= 31; $i++ ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $date)? 'selected': '' ) . ' >' . $i . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					$month = empty($selected_date[1])? '': intval($selected_date[1]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-month" >';
					echo '<select type="text" data-type="month" >';
					echo '<option value="" ' . (empty($month)? 'selected': '' ) . ' >' . esc_html__('Month', 'tourmaster') . '</option>';
					for( $i = 1; $i <= 12; $i++ ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $month)? 'selected': '' ) . ' >' . date_i18n('F', strtotime('2016-' . $i . '-1')) . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					$current_year = date('Y');
					$start_year = $current_year - 120;
					$year = empty($selected_date[0])? '': intval($selected_date[0]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-year" >';
					echo '<select type="text" data-type="year" >';
					echo '<option value="" ' . (empty($year)? 'selected': '' ) . ' >' . esc_html__('Year', 'tourmaster') . '</option>';
					for( $i = $current_year; $i >= $start_year; $i-- ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $year)? 'selected': '' ) . ' >' . $i . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					echo '</div>'; // tourmaster date select
					echo '<input type="hidden" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" />';
					break;
			}
			echo '</div>';
			echo '</div>'; // tourmaster-edit-profile-field	

			if( isset($settings['echo']) && $settings['echo'] === false ){
				$ret = ob_get_contents();
				ob_end_clean();

				return $ret;
			}
		} // tourmaster_get_form_field
	}	
	//custom code
	if( !function_exists('tourmaster_get_form_field_2') ){
		function tourmaster_get_form_field_2( $settings, $slug, $id ){

			if( isset($settings['echo']) && $settings['echo'] === false ){
				ob_start();
			} 

			$user_id = $id;
			$extra_class = 'tourmaster-' . $slug . '-field-' . $settings['slug'];
			$field_value = '';
			if( !empty($value) ){
				$field_value = $value;
			}else if( !empty($_POST[$settings['slug']]) ){
				$field_value = $_POST[$settings['slug']];
			}else if( !empty($user_id) ){
				$field_value = get_post_meta($user_id, $settings['slug'], true);
			}

			if( empty($field_value) && !empty($settings['default']) ){
				$field_value = $settings['default'];
			}

			$data = '';
			if( !empty($settings['data']) && !empty($settings['data']['slug']) && !empty($settings['data']['value']) ){
				$data = ' data-' . esc_attr($settings['data']['slug']) . '="' . esc_attr($settings['data']['value']) . '" ';
			}
			if( !empty($settings['type']) ){
				$extra_class .= ' tourmaster-type-' . $settings['type'];
			}

			echo '<div class="tourmaster-' . esc_attr($slug) . '-field ' . esc_attr($extra_class) . ' clearfix" >';
			echo '<div class="tourmaster-head" >';
			if( !empty($settings['title']) ){
				echo $settings['title'];
			}
			if( !empty($settings['required']) && ($settings['required'] === true || $settings['required'] == "true") ){
				echo '<span class="tourmaster-req" >*</span>';
				$data .= ' data-required ';
			}
			echo '</div>';

			echo '<div class="tourmaster-tail clearfix" >';
			if( !empty($settings['pre-input']) ){
				echo $settings['pre-input'];
			}
			
			switch($settings['type']){
				case 'textarea':
					echo '<textarea name="' . esc_attr($settings['slug']) . '" ' . $data . ' >' . esc_textarea($field_value) . '</textarea>';
					break;
				case 'email':
					echo '<input type="email" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'text':
					echo '<input type="text" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'price-edit':
					echo '<div class="tourmaster-price-edit-head" >';
					echo '<input type="text" name="' . esc_attr($settings['slug']) . ((!empty($settings['data-type']) && $settings['data-type'] == 'array')? '[]': '') . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					echo '</div>';
					if( !empty($settings['description']) ){
						echo '<div class="tourmaster-price-edit-tail">' . $settings['description'] . '</div>';
					}
					break;
				case 'file':
					echo '<label class="tourmaster-file-label" >';
					echo '<span class="tourmaster-file-label-text" data-default="' . esc_attr__('Click to select a file', 'tourmaster') . '" >' . esc_html__('Click to select a file', 'tourmaster') . '</span>';
					echo '<input type="file" name="' . esc_attr($settings['slug']) . '" ' . $data . ' />';
					echo '</label>';
					break;
				case 'password':
					echo '<input type="password" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'combobox':
					echo '<div class="tourmaster-combobox-wrap" >';
					echo '<select name="' . esc_attr($settings['slug']) . '" ' . $data . ' >';
					foreach( $settings['options'] as $option_val => $option_title ){
						echo '<option value="' . esc_attr($option_val) . '" ' . ($field_value == $option_val? 'selected': '') . ' >' . $option_title . '</option>';
					}
					echo '</select>';
					echo '</div>';
					break;

				case 'datepicker':
						echo '<input type="text" class="tourmaster-datepicker" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" />';
						echo '<i class="fa fa-calendar" ></i>';
					break;

				case 'date':
					echo '<div class="tourmaster-date-select" >';
					$selected_date = explode('-', $field_value);

					$date = empty($selected_date[2])? '': intval($selected_date[2]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-date" >';
					echo '<select type="text" data-type="date" >';
					echo '<option value="" ' . (empty($date)? 'selected': '' ) . ' >' . esc_html__('Date', 'tourmaster') . '</option>';
					for( $i = 1; $i <= 31; $i++ ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $date)? 'selected': '' ) . ' >' . $i . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					$month = empty($selected_date[1])? '': intval($selected_date[1]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-month" >';
					echo '<select type="text" data-type="month" >';
					echo '<option value="" ' . (empty($month)? 'selected': '' ) . ' >' . esc_html__('Month', 'tourmaster') . '</option>';
					for( $i = 1; $i <= 12; $i++ ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $month)? 'selected': '' ) . ' >' . date_i18n('F', strtotime('2016-' . $i . '-1')) . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					$current_year = date('Y');
					$start_year = $current_year - 120;
					$year = empty($selected_date[0])? '': intval($selected_date[0]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-year" >';
					echo '<select type="text" data-type="year" >';
					echo '<option value="" ' . (empty($year)? 'selected': '' ) . ' >' . esc_html__('Year', 'tourmaster') . '</option>';
					for( $i = $current_year; $i >= $start_year; $i-- ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $year)? 'selected': '' ) . ' >' . $i . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					echo '</div>'; // tourmaster date select
					echo '<input type="hidden" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" />';
					break;
			}
			echo '</div>';
			echo '</div>'; // tourmaster-edit-profile-field	

			if( isset($settings['echo']) && $settings['echo'] === false ){
				$ret = ob_get_contents();
				ob_end_clean();

				return $ret;
			}
		} // tourmaster_get_form_field
	}

	if( !function_exists('tourmaster_get_form_field_3') ){
		function tourmaster_get_form_field_3( $settings, $slug, $id ){

			if( isset($settings['echo']) && $settings['echo'] === false ){
				ob_start();
			} 

			$user_id = $id;
			$extra_class = 'tourmaster-' . $slug . '-field-' . $settings['slug'];
			$field_value = '';
			if($settings['slug']=="post_title"){
				$field_value = get_the_title($user_id);
			}else if( !empty($value) ){
				$field_value = $value;
			}else if( !empty($_POST[$settings['slug']]) ){
				$field_value = $_POST[$settings['slug']];
			}else if( !empty($user_id) ){
				$field_value = get_post_field($settings['slug'], $user_id);
			}

			if( empty($field_value) && !empty($settings['default']) ){
				$field_value = $settings['default'];
			}

			$data = '';
			if( !empty($settings['data']) && !empty($settings['data']['slug']) && !empty($settings['data']['value']) ){
				$data = ' data-' . esc_attr($settings['data']['slug']) . '="' . esc_attr($settings['data']['value']) . '" ';
			}
			if( !empty($settings['type']) ){
				$extra_class .= ' tourmaster-type-' . $settings['type'];
			}

			echo '<div class="tourmaster-' . esc_attr($slug) . '-field ' . esc_attr($extra_class) . ' clearfix" >';
			echo '<div class="tourmaster-head" >';
			if( !empty($settings['title']) ){
				echo $settings['title'];
			}
			if( !empty($settings['required']) && ($settings['required'] === true || $settings['required'] == "true") ){
				echo '<span class="tourmaster-req" >*</span>';
				$data .= ' data-required ';
			}
			echo '</div>';

			echo '<div class="tourmaster-tail clearfix" >';
			if( !empty($settings['pre-input']) ){
				echo $settings['pre-input'];
			}
			
			switch($settings['type']){
				case 'textarea':
					echo '<textarea name="' . esc_attr($settings['slug']) . '" ' . $data . ' >' . esc_textarea($field_value) . '</textarea>';
					break;
				case 'email':
					echo '<input type="email" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'text':
					echo '<input type="text" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'price-edit':
					echo '<div class="tourmaster-price-edit-head" >';
					echo '<input type="text" name="' . esc_attr($settings['slug']) . ((!empty($settings['data-type']) && $settings['data-type'] == 'array')? '[]': '') . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					echo '</div>';
					if( !empty($settings['description']) ){
						echo '<div class="tourmaster-price-edit-tail">' . $settings['description'] . '</div>';
					}
					break;
				case 'file':
					echo '<label class="tourmaster-file-label" >';
					echo '<span class="tourmaster-file-label-text" data-default="' . esc_attr__('Click to select a file', 'tourmaster') . '" >' . esc_html__('Click to select a file', 'tourmaster') . '</span>';
					echo '<input type="file" name="' . esc_attr($settings['slug']) . '" ' . $data . ' />';
					echo '</label>';
					break;
				case 'password':
					echo '<input type="password" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" ' . $data . ' />';
					break;
				case 'combobox':
					echo '<div class="tourmaster-combobox-wrap" >';
					echo '<select name="' . esc_attr($settings['slug']) . '" ' . $data . ' >';
					foreach( $settings['options'] as $option_val => $option_title ){
						echo '<option value="' . esc_attr($option_val) . '" ' . ($field_value == $option_val? 'selected': '') . ' >' . $option_title . '</option>';
					}
					echo '</select>';
					echo '</div>';
					break;

				case 'datepicker':
						echo '<input type="text" class="tourmaster-datepicker" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" />';
						echo '<i class="fa fa-calendar" ></i>';
					break;

				case 'date':
					echo '<div class="tourmaster-date-select" >';
					$selected_date = explode('-', $field_value);

					$date = empty($selected_date[2])? '': intval($selected_date[2]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-date" >';
					echo '<select type="text" data-type="date" >';
					echo '<option value="" ' . (empty($date)? 'selected': '' ) . ' >' . esc_html__('Date', 'tourmaster') . '</option>';
					for( $i = 1; $i <= 31; $i++ ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $date)? 'selected': '' ) . ' >' . $i . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					$month = empty($selected_date[1])? '': intval($selected_date[1]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-month" >';
					echo '<select type="text" data-type="month" >';
					echo '<option value="" ' . (empty($month)? 'selected': '' ) . ' >' . esc_html__('Month', 'tourmaster') . '</option>';
					for( $i = 1; $i <= 12; $i++ ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $month)? 'selected': '' ) . ' >' . date_i18n('F', strtotime('2016-' . $i . '-1')) . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					$current_year = date('Y');
					$start_year = $current_year - 120;
					$year = empty($selected_date[0])? '': intval($selected_date[0]);
					echo '<div class="tourmaster-combobox-wrap tourmaster-form-field-alt-year" >';
					echo '<select type="text" data-type="year" >';
					echo '<option value="" ' . (empty($year)? 'selected': '' ) . ' >' . esc_html__('Year', 'tourmaster') . '</option>';
					for( $i = $current_year; $i >= $start_year; $i-- ){
						echo '<option value="' . esc_attr($i) . '" ' . (($i == $year)? 'selected': '' ) . ' >' . $i . '</option>';
					}
					echo '</select>';
					echo '</div>'; // tourmaster-combobox-wrap

					echo '</div>'; // tourmaster date select
					echo '<input type="hidden" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($field_value) . '" />';
					break;
			}
			echo '</div>';
			echo '</div>'; // tourmaster-edit-profile-field	

			if( isset($settings['echo']) && $settings['echo'] === false ){
				$ret = ob_get_contents();
				ob_end_clean();

				return $ret;
			}
		} // tourmaster_get_form_field
	}

	// retrieve all categories from each post type
	if( !function_exists('tourmaster_get_term_list') ){	
		function tourmaster_get_term_list( $taxonomy, $cat = '', $with_all = false ){
			$term_atts = array(
				'taxonomy'=>$taxonomy, 
				'hide_empty'=>0,
				'number'=>999
			);
			if( !empty($cat) ){
				if( is_array($cat) ){
					$term_atts['slug'] = $cat;
				}else{
					$term_atts['parent'] = $cat;
				}
			}
			$term_list = get_categories($term_atts);
			
			$ret = array();
			if( !empty($with_all) ){
				$ret[$cat] = esc_html__('All', 'tourmaster'); 
			}

			if( !empty($term_list) ){
				foreach( $term_list as $term ){
					if( !empty($term->slug) && !empty($term->name) ){
						$ret[$term->slug] = $term->name;
					}
				}
			}

			return $ret;
		}	
	}

	// get rating
	if( !function_exists('tourmaster_get_rating') ){	
		function tourmaster_get_rating( $score ){

			$ret  = '';
			for( $i = 2; $i <= 10; $i += 2 ){
				if( $score - $i >= - 0.5 ){
					$ret .= '<i class="fa fa-star" ></i>';
				}else if( $score - $i <= -1.5 ){
					$ret .= '<i class="fa fa-star-o" ></i>';
				}else{
					$ret .= '<i class="fa fa-star-half-o" ></i>';
				}
			}

			return $ret;
		}
	}

	// get the sidebar
	if( !function_exists('tourmaster_get_sidebar_wrap_class') ){
		function tourmaster_get_sidebar_wrap_class($sidebar_type){
			return ' tourmaster-sidebar-wrap clearfix tourmaster-sidebar-style-' . $sidebar_type;
		}
	}
	if( !function_exists('tourmaster_get_sidebar_class') ){
		function tourmaster_get_sidebar_class($args){

			// set default column
			if( empty($args['column']) ){
				if( $args['sidebar-type'] == 'both' ){
					if( function_exists('traveltour_get_option') ){
						$args['column'] = traveltour_get_option('general', 'both-sidebar-width', 15);
					}else{
						$args['column'] = 15;
					}
				}else if( $args['sidebar-type'] == 'left' || $args['sidebar-type'] == 'right' ){
					if( function_exists('traveltour_get_option') ){
						$args['column'] = traveltour_get_option('general', 'sidebar-width', 20);
					}else{
						$args['column'] = 15;
					}
				}else{
					$args['column'] = 60;
				}
			}

			// if center section
			if( $args['section'] == 'center' ){
				if( $args['sidebar-type'] == 'both' ){
					$args['column'] = 60 - (2 * intval($args['column']));
				}else if( $args['sidebar-type'] == 'left' || $args['sidebar-type'] == 'right' ){
					$args['column'] = 60 - intval($args['column']);
				}
			}

			$sidebar_class  = ' tourmaster-sidebar-' . $args['section'];
			$sidebar_class .= ' tourmaster-column-' . $args['column'];

			return $sidebar_class; 
		}
	}
	if( !function_exists('tourmaster_get_sidebar') ){
		function tourmaster_get_sidebar($sidebar_type, $section, $sidebar_id){
			$sidebar_class = apply_filters('gdlr_core_sidebar_class', '');

			echo '<div class="' . tourmaster_get_sidebar_class(array('sidebar-type'=>$sidebar_type, 'section'=>$section)) . '" >';
			echo '<div class="tourmaster-sidebar-area ' . esc_attr($sidebar_class) . ' tourmaster-item-pdlr" >';
			if( is_active_sidebar($sidebar_id) ){ 
				dynamic_sidebar($sidebar_id); 
			}
			echo '</div>';
			echo '</div>';

		}
	}	