<?php
	/*	
	*	Utility function for uses
	*/

	if( !function_exists('tourmaster_get_country_list') ){
		function tourmaster_get_country_list( $with_none = false, $single = '' ){
			$ret = array(
				'United States of America (USA)' => esc_html__('United States of America (USA)', 'tourmaster'),
				'Afghanistan' => esc_html__('Afghanistan', 'tourmaster'),
				'Albania' => esc_html__('Albania', 'tourmaster'),
				'Algeria' => esc_html__('Algeria', 'tourmaster'),
				'Andorra' => esc_html__('Andorra', 'tourmaster'),
				'Angola' => esc_html__('Angola', 'tourmaster'),
				'Antigua and Barbuda' => esc_html__('Antigua and Barbuda', 'tourmaster'),
				'Argentina' => esc_html__('Argentina', 'tourmaster'),
				'Armenia' => esc_html__('Armenia', 'tourmaster'),
				'Australia' => esc_html__('Australia', 'tourmaster'),
				'Austria' => esc_html__('Austria', 'tourmaster'),
				'Azerbaijan' => esc_html__('Azerbaijan', 'tourmaster'),
				'Bahamas' => esc_html__('Bahamas', 'tourmaster'),
				'Bahrain' => esc_html__('Bahrain', 'tourmaster'),
				'Bangladesh' => esc_html__('Bangladesh', 'tourmaster'),
				'Barbados' => esc_html__('Barbados', 'tourmaster'),
				'Belarus' => esc_html__('Belarus', 'tourmaster'),
				'Belgium' => esc_html__('Belgium', 'tourmaster'),
				'Belize' => esc_html__('Belize', 'tourmaster'),
				'Benin' => esc_html__('Benin', 'tourmaster'),
				'Bhutan' => esc_html__('Bhutan', 'tourmaster'),
				'Bolivia' => esc_html__('Bolivia', 'tourmaster'),
				'Bosnia and Herzegovina' => esc_html__('Bosnia and Herzegovina', 'tourmaster'),
				'Botswana' => esc_html__('Botswana', 'tourmaster'),
				'Brazil' => esc_html__('Brazil', 'tourmaster'),
				'Brunei' => esc_html__('Brunei', 'tourmaster'),
				'Bulgaria' => esc_html__('Bulgaria', 'tourmaster'),
				'Burkina Faso' => esc_html__('Burkina Faso', 'tourmaster'),
				'Burundi' => esc_html__('Burundi', 'tourmaster'),
				'Cabo Verde' => esc_html__('Cabo Verde', 'tourmaster'),
				'Cambodia' => esc_html__('Cambodia', 'tourmaster'),
				'Cameroon' => esc_html__('Cameroon', 'tourmaster'),
				'Canada' => esc_html__('Canada', 'tourmaster'),
				'Central African Republic (CAR)' => esc_html__('Central African Republic (CAR)', 'tourmaster'),
				'Chad' => esc_html__('Chad', 'tourmaster'),
				'Chile' => esc_html__('Chile', 'tourmaster'),
				'China' => esc_html__('China', 'tourmaster'),
				'Colombia' => esc_html__('Colombia', 'tourmaster'),
				'Comoros' => esc_html__('Comoros', 'tourmaster'),
				'Democratic Republic of the Congo' => esc_html__('Democratic Republic of the Congo', 'tourmaster'),
				'Republic of the Congo' => esc_html__('Republic of the Congo', 'tourmaster'),
				'Costa Rica' => esc_html__('Costa Rica', 'tourmaster'),
				'Cote d\'Ivoire' => esc_html__('Cote d\'Ivoire', 'tourmaster'),
				'Croatia' => esc_html__('Croatia', 'tourmaster'),
				'Cuba' => esc_html__('Cuba', 'tourmaster'),
				'Cyprus' => esc_html__('Cyprus', 'tourmaster'),
				'Czech Republic' => esc_html__('Czech Republic', 'tourmaster'),
				'Denmark' => esc_html__('Denmark', 'tourmaster'),
				'Djibouti' => esc_html__('Djibouti', 'tourmaster'),
				'Dominica' => esc_html__('Dominica', 'tourmaster'),
				'Dominican Republic' => esc_html__('Dominican Republic', 'tourmaster'),
				'Ecuador' => esc_html__('Ecuador', 'tourmaster'),
				'Egypt' => esc_html__('Egypt', 'tourmaster'),
				'El Salvador' => esc_html__('El Salvador', 'tourmaster'),
				'Equatorial Guinea' => esc_html__('Equatorial Guinea', 'tourmaster'),
				'Eritrea' => esc_html__('Eritrea', 'tourmaster'),
				'Estonia' => esc_html__('Estonia', 'tourmaster'),
				'Ethiopia' => esc_html__('Ethiopia', 'tourmaster'),
				'Fiji' => esc_html__('Fiji', 'tourmaster'),
				'Finland' => esc_html__('Finland', 'tourmaster'),
				'France' => esc_html__('France', 'tourmaster'),
				'Gabon' => esc_html__('Gabon', 'tourmaster'),
				'Gambia' => esc_html__('Gambia', 'tourmaster'),
				'Georgia' => esc_html__('Georgia', 'tourmaster'),
				'Germany' => esc_html__('Germany', 'tourmaster'),
				'Ghana' => esc_html__('Ghana', 'tourmaster'),
				'Greece' => esc_html__('Greece', 'tourmaster'),
				'Grenada' => esc_html__('Grenada', 'tourmaster'),
				'Guatemala' => esc_html__('Guatemala', 'tourmaster'),
				'Guinea' => esc_html__('Guinea', 'tourmaster'),
				'Guinea-Bissau' => esc_html__('Guinea-Bissau', 'tourmaster'),
				'Guyana' => esc_html__('Guyana', 'tourmaster'),
				'Haiti' => esc_html__('Haiti', 'tourmaster'),
				'Honduras' => esc_html__('Honduras', 'tourmaster'),
				'Hungary' => esc_html__('Hungary', 'tourmaster'),
				'Iceland' => esc_html__('Iceland', 'tourmaster'),
				'India' => esc_html__('India', 'tourmaster'),
				'Indonesia' => esc_html__('Indonesia', 'tourmaster'),
				'Iran' => esc_html__('Iran', 'tourmaster'),
				'Iraq' => esc_html__('Iraq', 'tourmaster'),
				'Ireland' => esc_html__('Ireland', 'tourmaster'),
				'Israel' => esc_html__('Israel', 'tourmaster'),
				'Italy' => esc_html__('Italy', 'tourmaster'),
				'Jamaica' => esc_html__('Jamaica', 'tourmaster'),
				'Japan' => esc_html__('Japan', 'tourmaster'),
				'Jordan' => esc_html__('Jordan', 'tourmaster'),
				'Kazakhstan' => esc_html__('Kazakhstan', 'tourmaster'),
				'Kenya' => esc_html__('Kenya', 'tourmaster'),
				'Kiribati' => esc_html__('Kiribati', 'tourmaster'),
				'Kosovo' => esc_html__('Kosovo', 'tourmaster'),
				'Kuwait' => esc_html__('Kuwait', 'tourmaster'),
				'Kyrgyzstan' => esc_html__('Kyrgyzstan', 'tourmaster'),
				'Laos' => esc_html__('Laos', 'tourmaster'),
				'Latvia' => esc_html__('Latvia', 'tourmaster'),
				'Lebanon' => esc_html__('Lebanon', 'tourmaster'),
				'Lesotho' => esc_html__('Lesotho', 'tourmaster'),
				'Liberia' => esc_html__('Liberia', 'tourmaster'),
				'Libya' => esc_html__('Libya', 'tourmaster'),
				'Liechtenstein' => esc_html__('Liechtenstein', 'tourmaster'),
				'Lithuania' => esc_html__('Lithuania', 'tourmaster'),
				'Luxembourg' => esc_html__('Luxembourg', 'tourmaster'),
				'Macedonia' => esc_html__('Macedonia', 'tourmaster'),
				'Madagascar' => esc_html__('Madagascar', 'tourmaster'),
				'Malawi' => esc_html__('Malawi', 'tourmaster'),
				'Malaysia' => esc_html__('Malaysia', 'tourmaster'),
				'Maldives' => esc_html__('Maldives', 'tourmaster'),
				'Mali' => esc_html__('Mali', 'tourmaster'),
				'Malta' => esc_html__('Malta', 'tourmaster'),
				'Marshall Islands' => esc_html__('Marshall Islands', 'tourmaster'),
				'Mauritania' => esc_html__('Mauritania', 'tourmaster'),
				'Mauritius' => esc_html__('Mauritius', 'tourmaster'),
				'Mexico' => esc_html__('Mexico', 'tourmaster'),
				'Micronesia' => esc_html__('Micronesia', 'tourmaster'),
				'Moldova' => esc_html__('Moldova', 'tourmaster'),
				'Monaco' => esc_html__('Monaco', 'tourmaster'),
				'Mongolia' => esc_html__('Mongolia', 'tourmaster'),
				'Montenegro' => esc_html__('Montenegro', 'tourmaster'),
				'Morocco' => esc_html__('Morocco', 'tourmaster'),
				'Mozambique' => esc_html__('Mozambique', 'tourmaster'),
				'Myanmar (Burma)' => esc_html__('Myanmar (Burma)', 'tourmaster'),
				'Namibia' => esc_html__('Namibia', 'tourmaster'),
				'Nauru' => esc_html__('Nauru', 'tourmaster'),
				'Nepal' => esc_html__('Nepal', 'tourmaster'),
				'Netherlands' => esc_html__('Netherlands', 'tourmaster'),
				'New Zealand' => esc_html__('New Zealand', 'tourmaster'),
				'Nicaragua' => esc_html__('Nicaragua', 'tourmaster'),
				'Niger' => esc_html__('Niger', 'tourmaster'),
				'Nigeria' => esc_html__('Nigeria', 'tourmaster'),
				'North Korea' => esc_html__('North Korea', 'tourmaster'),
				'Norway' => esc_html__('Norway', 'tourmaster'),
				'Oman' => esc_html__('Oman', 'tourmaster'),
				'Pakistan' => esc_html__('Pakistan', 'tourmaster'),
				'Palau' => esc_html__('Palau', 'tourmaster'),
				'Palestine' => esc_html__('Palestine', 'tourmaster'),
				'Panama' => esc_html__('Panama', 'tourmaster'),
				'Papua New Guinea' => esc_html__('Papua New Guinea', 'tourmaster'),
				'Paraguay' => esc_html__('Paraguay', 'tourmaster'),
				'Peru' => esc_html__('Peru', 'tourmaster'),
				'Philippines' => esc_html__('Philippines', 'tourmaster'),
				'Poland' => esc_html__('Poland', 'tourmaster'),
				'Portugal' => esc_html__('Portugal', 'tourmaster'),
				'Puerto Rico' => esc_html__('Puerto Rico', 'tourmaster'),
				'Qatar' => esc_html__('Qatar', 'tourmaster'),
				'Romania' => esc_html__('Romania', 'tourmaster'),
				'Russia' => esc_html__('Russia', 'tourmaster'),
				'Rwanda' => esc_html__('Rwanda', 'tourmaster'),
				'Saint Kitts and Nevis' => esc_html__('Saint Kitts and Nevis', 'tourmaster'),
				'Saint Lucia' => esc_html__('Saint Lucia', 'tourmaster'),
				'Saint Vincent and the Grenadines' => esc_html__('Saint Vincent and the Grenadines', 'tourmaster'),
				'Samoa' => esc_html__('Samoa', 'tourmaster'),
				'San Marino' => esc_html__('San Marino', 'tourmaster'),
				'Sao Tome and Principe' => esc_html__('Sao Tome and Principe', 'tourmaster'),
				'Saudi Arabia' => esc_html__('Saudi Arabia', 'tourmaster'),
				'Senegal' => esc_html__('Senegal', 'tourmaster'),
				'Serbia' => esc_html__('Serbia', 'tourmaster'),
				'Seychelles' => esc_html__('Seychelles', 'tourmaster'),
				'Sierra Leone' => esc_html__('Sierra Leone', 'tourmaster'),
				'Singapore' => esc_html__('Singapore', 'tourmaster'),
				'Slovakia' => esc_html__('Slovakia', 'tourmaster'),
				'Slovenia' => esc_html__('Slovenia', 'tourmaster'),
				'Solomon Islands' => esc_html__('Solomon Islands', 'tourmaster'),
				'Somalia' => esc_html__('Somalia', 'tourmaster'),
				'South Africa' => esc_html__('South Africa', 'tourmaster'),
				'South Korea' => esc_html__('South Korea', 'tourmaster'),
				'South Sudan' => esc_html__('South Sudan', 'tourmaster'),
				'Spain' => esc_html__('Spain', 'tourmaster'),
				'Sri Lanka' => esc_html__('Sri Lanka', 'tourmaster'),
				'Sudan' => esc_html__('Sudan', 'tourmaster'),
				'Suriname' => esc_html__('Suriname', 'tourmaster'),
				'Swaziland' => esc_html__('Swaziland', 'tourmaster'),
				'Sweden' => esc_html__('Sweden', 'tourmaster'),
				'Switzerland' => esc_html__('Switzerland', 'tourmaster'),
				'Syria' => esc_html__('Syria', 'tourmaster'),
				'Taiwan' => esc_html__('Taiwan', 'tourmaster'),
				'Tajikistan' => esc_html__('Tajikistan', 'tourmaster'),
				'Tanzania' => esc_html__('Tanzania', 'tourmaster'),
				'Thailand' => esc_html__('Thailand', 'tourmaster'),
				'Timor-Leste' => esc_html__('Timor-Leste', 'tourmaster'),
				'Togo' => esc_html__('Togo', 'tourmaster'),
				'Tonga' => esc_html__('Tonga', 'tourmaster'),
				'Trinidad and Tobago' => esc_html__('Trinidad and Tobago', 'tourmaster'),
				'Tunisia' => esc_html__('Tunisia', 'tourmaster'),
				'Turkey' => esc_html__('Turkey', 'tourmaster'),
				'Turkmenistan' => esc_html__('Turkmenistan', 'tourmaster'),
				'Tuvalu' => esc_html__('Tuvalu', 'tourmaster'),
				'Uganda' => esc_html__('Uganda', 'tourmaster'),
				'Ukraine' => esc_html__('Ukraine', 'tourmaster'),
				'United Arab Emirates (UAE)' => esc_html__('United Arab Emirates (UAE)', 'tourmaster'),
				'United Kingdom (UK)' => esc_html__('United Kingdom (UK)', 'tourmaster'),
				'United States of America (USA)' => esc_html__('United States of America (USA)', 'tourmaster'),
				'Uruguay' => esc_html__('Uruguay', 'tourmaster'),
				'Uzbekistan' => esc_html__('Uzbekistan', 'tourmaster'),
				'Vanuatu' => esc_html__('Vanuatu', 'tourmaster'),
				'Vatican City (Holy See)' => esc_html__('Vatican City (Holy See)', 'tourmaster'),
				'Venezuela' => esc_html__('Venezuela', 'tourmaster'),
				'Vietnam' => esc_html__('Vietnam', 'tourmaster'),
				'Yemen' => esc_html__('Yemen', 'tourmaster'),
				'Zambia' => esc_html__('Zambia', 'tourmaster'),
				'Zimbabwe' => esc_html__('Zimbabwe', 'tourmaster')
			);

			if( $with_none ){
				$ret = array( '' => esc_html__('None', 'tourmaster') ) + $ret;
			} 

			if( !empty($single) && !empty($ret[$single]) ){
				return $ret[$single];
			}

			return $ret;
		}
	}

	if( !function_exists('tourmaster_user_content_block_start') ){
		function tourmaster_user_content_block_start( $settings = array() ){
			echo '<div class="tourmaster-user-content-block" >';
			if( !empty($settings['title']) ){
				echo '<div class="tourmaster-user-content-title-wrap" >';
				echo '<h3 class="tourmaster-user-content-title">' . $settings['title'] . '</h3>';
				
				if( !empty($settings['title-link']) ){
					echo '<a class="tourmaster-user-content-title-link" href="' . esc_url($settings['title-link']) . '" >';
					echo $settings['title-link-text'];
					echo '</a>';
				}
				echo '</div>'; // tourmaster-user-content-title-wrap

				echo '<div class="tourmaster-user-content-block-content" >';
			}
			
		} // tourmaster_user_content_block_start
	}

	if( !function_exists('tourmaster_user_content_block_2') ){
		function tourmaster_user_content_block_2( $settings = array() ){
			if( !empty($settings['title']) ){
				echo '<div class="tourmaster-user-content-title-wrap" >';
				echo '<h3 class="tourmaster-user-content-title">' . $settings['title'] . '</h3>';
				
				if( !empty($settings['title-link']) ){
					echo '<a class="tourmaster-user-content-title-link" href="' . esc_url($settings['title-link']) . '" >';
					echo $settings['title-link-text'];
					echo '</a>';
				}
				echo '</div>'; // tourmaster-user-content-title-wrap

				echo '<div class="tourmaster-user-content-block-content" >';
			}
			
		} // tourmaster_user_content_block_start
	}

	if( !function_exists('tourmaster_update_profile_avatar') ){
		function tourmaster_update_profile_avatar(){
			
			// upload the file
			if( !empty($_FILES['profile-image']['size']) ){
				if ( !function_exists('wp_handle_upload') ) {
				    require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				add_filter('upload_dir', 'tourmaster_set_avatar_upload_folder');
				$uploaded_file = wp_handle_upload($_FILES['profile-image'], array('test_form' => false));
				remove_filter('upload_dir', 'tourmaster_set_avatar_upload_folder');
			}

			// upload error
			if( !empty($uploaded_file) && empty($uploaded_file['error']) ){
				$avatar = array();
				$avatar['local_url'] = $uploaded_file['file'];
				$avatar['file_url'] = $uploaded_file['url'];

				$cropped_image = wp_get_image_editor($avatar['local_url']);
				if( !is_wp_error($cropped_image) ) {
					$orig_info = pathinfo($avatar['local_url']);
					$dir = $orig_info['dirname'];
					$ext = $orig_info['extension'];	
					$name = wp_basename($avatar['local_url'], ".{$ext}");
					$destfilename = "{$dir}/{$name}-150x150.{$ext}";

					$cropped_image->resize(150, 150, true);
					$cropped_image->save($destfilename);
					$avatar['thumbnail'] = str_replace($name, $name . '-150x150', $avatar['file_url']);
				}

				global $current_user;
				$user_id = $current_user->ID;
				update_user_meta($user_id, 'tourmaster-user-avatar', $avatar);
			}

		} // tourmaster_update_profile_avatar
	}

	if( !function_exists('tourmaster_user_update_notification') ){
		function tourmaster_user_update_notification( $content, $success = true ){

			echo '<div class="tourmaster-user-update-notification tourmaster-' . ($success? 'success': 'failure') . '" >';
			if( $success ){
				echo '<i class="fa fa-check" ></i>';
			}else if( $success == 'fail' ){
				echo '<i class="fa fa-remove" ></i>';
			}
			echo $content;
			echo '</div>';

		} // tourmaster_user_update_notification
	}

	if( !function_exists('tourmaster_user_content_block_end') ){
		function tourmaster_user_content_block_end(){

			echo '</div>'; // tourmaster-user-content-block-content
			echo '</div>'; // tourmaster-user-content-block

		} // tourmaster_user_content_block_end
	}

	if( !function_exists('tourmaster_get_user_meta') ){
		function tourmaster_get_user_meta( $user_id = null, $type = 'full_name', $default = ''){

			if( $type == 'full_name' ){
				$name  = get_the_author_meta('first_name', $user_id);
				if( !empty($name) ){
					$name .= ' ' . get_the_author_meta('last_name', $user_id);
				}else{
					$name  = get_the_author_meta('display_name', $user_id);
				}

				if( !empty($name) ){
					return $name;
				}
			}else{
				$user_meta = get_the_author_meta($type, $user_id);

				if( !empty($user_meta) ){
					return $user_meta;
				}
			}

			return $default;

		} // tourmaster_get_user_meta
	}

	if( !function_exists('tourmaster_validate_profile_field') ){
		function tourmaster_validate_profile_field( $fields ){

			$error = new WP_ERROR();

			foreach( $fields as $slug => $field ){
				$error_message = $error->get_error_message('1');
				if( !empty($field['required']) && empty($_POST[$slug]) && empty($error_message) ){
					$error->add('1', esc_html__('Please fill all required fields.', 'tourmaster'));
				}

				if( !empty($field['type']) && $field['type'] == 'email' && !is_email($_POST[$slug]) ){
					$error->add('2', esc_html__('Incorrect email address.', 'tourmaster'));
				}

				if( $slug == 'phone' ){
					if( !preg_match('/^[\d\+\-\s\(\)\.]*$/', $_POST[$slug]) ){
						$error->add('4', sprintf(esc_html__('Invalid phone number, please try again.', 'tourmaster'), $field['title']));
					}
				}else if( !empty($_POST[$slug]) && !in_array($field['type'], array('email', 'password', 'confirm-password', 'custom', 'url')) ){
					if( preg_match('/[£^$%&*}{@#~?><|=+]/', $_POST[$slug]) ){
						$error->add('3', sprintf(esc_html__('Special characters is not allowed in "%s".', 'tourmaster'), $field['title']));
					}
				}
				
			}

			$error_message = $error->get_error_message();
			if( !empty($error_message) ){
				return $error;
			}else{
				return true;
			}

		} // tourmaster_validate_profile_field
	}

	if( !function_exists('tourmaster_update_profile_field') ){
		function tourmaster_update_profile_field( $fields, $user_id = '' ){
			global $current_user;

			if( empty($user_id) ){
				$user_id = $current_user->ID;
			}

			foreach( $fields as $slug => $field ){
				
				if( $slug == 'email' ){
					if( !empty($_POST['email']) ){
			        	wp_update_user(array(
			                'ID' => $user_id,
			                'user_email' => $_POST['email']
			            ));
			        }
				} else {
					if ($slug == 'image-guide') {
						
					}
					$value = empty($_POST[$slug])? '': $_POST[$slug];
					if($slug == "header-slider"){
						update_user_meta($user_id, 'header-slider', json_decode($value, true));
					}
					update_user_meta($user_id, $slug, $value);
					if ($value == "user-guide") {
						// update_user_meta($user_id, 'guide-info', '');
						// update_user_meta($user_id, 'guide-company', '');
					}
				}
				
			}
			

		} // tourmaster_update_profile_field
	}

	if( !function_exists('tourmaster_get_profile_fields') ){
		function tourmaster_get_profile_fields(){
			return apply_filters('tourmaster_profile_fields', array(
				'first_name' => array(
					'title' => esc_html__('First Name', 'tourmaster'),
					'type' => 'text',
					'required' => true
				),
				'last_name' => array(
					'title' => esc_html__('Last Name', 'tourmaster'),
					'type' => 'text',
					'required' => true
				),
				/* 'gender' => array(
					'title' => esc_html__('Gender', 'tourmaster'),
					'type' => 'combobox',
					'options' => array(
						'' => '-',
						'male' => esc_html__('Male', 'tourmaster'),
						'female' => esc_html__('Female', 'tourmaster')
					),
					'required' => true
				),
				'birth_date' => array(
					'title' => esc_html__('Birth Date', 'tourmaster'),
					'type' => 'date',
					'required' => true
				), */
				'email' => array(
					'title' => esc_html__('Email', 'tourmaster'),
					'type' => 'email',
					'required' => true
				),
				'phone' => array(
					'title' => esc_html__('Phone', 'tourmaster'),
					'type' => 'text',
					'required' => true
				),
				'country' => array(
					'title' => esc_html__('Country', 'tourmaster'),
					'type' => 'combobox',
					'options' => tourmaster_get_country_list(),
					'required' => true,
					'default' => esc_html__('United States of America (USA)', 'tourmaster')
				),
				/* 'contact_address' => array(
					'title' => esc_html__('Bussiness Address', 'tourmaster'),
					'type' => 'textarea',
					'required' => true
				), */
				
				'user-type' => array(
					'title' => esc_html__('Choose type of user', 'tourmaster'),
					'type' => 'combobox',
					'options' => array(
								// 'user-traveler' => esc_html__('Traveler', 'tourmaster'),
								'user-guide' => esc_html__('Guide', 'tourmaster')),
					'required' => true,
					'default' => esc_html__('Guide', 'tourmaster')
				)
			));
		}
	}	

	if( !function_exists('tourmaster_get_profile_guide_fields') ){
		function tourmaster_get_profile_guide_fields(){
			return apply_filters('tourmaster_profile_fields', array(
				'guide-company' => array(
					'title' => esc_html__('Company Name', 'tourmaster'),
					'type' => 'text',
				),
				'guide-info' => array(
					'title' => esc_html__('About Paragraph', 'tourmaster'),
					'type' => 'textarea'
				),
				'youtube' => array(
					'title' => esc_html__('URL Youtube Video', 'torumaster'),
					'type' => 'url',

				),
				'image-custom-guide' => array(
					'title' => esc_html__('Image (if not have a video)', 'tourmaster'),
					'type' => 'upload'
				),
				'image-guide' => array(
					'title' => esc_html__('Banner Image (1440x720)', 'tourmaster'),
					'type' => 'upload'
				),
				'tour_category' => array(
					'title' => esc_html__('Category', 'tourmaster'),
					'type' => 'combobox',
					'options' => array(
						'fishing' => esc_html__('Fishing', 'tourmaster')),
						'hunting' => esc_html__('Hunting', 'tourmaster'),
				),
				/* 'tour-activity' => array(
					'title' =>  esc_html__('Tour Activity', 'tourmaster'),
					'type' => 'multi-combobox',
					'options' => 'taxonomy',
					'options-data' => 'tour-activity',
				), */
				'header-slider' => array(
					'title' => esc_html__('Images', 'tourmaster'),
					'type' => 'custom',
					'item-type' => 'gallery',
					'wrapper-class' => 'tourmaster-fullsize',
				),
				'address' => array(
					'title' => esc_html__('Address', 'tourmaster'),
					'type' => 'text',
				),
				'country' => array(
					'title' => esc_html__('Country', 'tourmaster'),
					'type' => 'text',
				),
				'state' => array(
					'title' => esc_html__('State', 'tourmaster'),
					'type' => 'text',
				),
				'city' => array(
					'title' => esc_html__('City', 'tourmaster'),
					'type' => 'text',
				),
				'latitude' => array(
					'title' => esc_html__('Latitude', 'tourmaster'),
					'type' => 'text',
				),
				'longitude' => array(
					'title' => esc_html__('Longitude', 'tourmaster'),
					'type' => 'text',
				),
			));
		}
	}

	// user nav list
	if( !function_exists('tourmaster_get_user_nav_list') ){
		function tourmaster_get_user_nav_list(){
			return apply_filters('tourmaster_user_nav_list', array(
				'my-account-title' => array(
					'type' => 'title',
					'title' => esc_html__('My Account', 'tourmaster')
				),
				'dashboard' => array(
					'title' => esc_html__('Dashboard', 'tourmaster'),
					'icon' => 'fa fa-dashboard',
					'top-bar' => true,
				),
				'edit-profile' => array(
					'title' => esc_html__('Edit Profile', 'tourmaster'),
					'icon' => 'fa fa-edit',
					'top-bar' => true,
				),
				'change-password' => array(
					'title' => esc_html__('Change Password', 'tourmaster'),
					'icon' => 'fa fa-unlock-alt'
				),
				'tour-booking-title' => array(
					'type' => 'title',
					'title' => esc_html__('Tour Booking', 'tourmaster')
				),
				'my-booking' => array(
					'title' => esc_html__('My Bookings', 'tourmaster'),
					'icon' => 'icon_document_alt'
				),
				'invoices' => array(
					'title' => esc_html__('Invoices', 'tourmaster'),
					'icon' => 'icon_wallet'
				),
				'reviews' => array(
					'title' => esc_html__('Reviews', 'tourmaster'),
					'icon' => 'fa fa-star'
				),
				'wish-list' => array(
					'title' => esc_html__('Wish List', 'tourmaster'),
					'icon' => 'fa fa-heart-o',
					'top-bar' => true,
				),
				'sign-out' => array(
					'title' => esc_html__('Sign Out', 'tourmaster'),
					'icon' => 'icon_lock-open_alt',
					'link' => wp_logout_url( home_url('/') ),
					'top-bar' => true,
				),
			));
		}
	}

	if( !function_exists('tourmaster_get_user_guide_nav_list') ){
		function tourmaster_get_user_guide_nav_list(){
			return apply_filters('tourmaster_user_nav_list', array(
				'my-account-title' => array(
					'type' => 'title',
					'title' => esc_html__('My Account', 'tourmaster')
				),
				'dashboard' => array(
					'title' => esc_html__('Dashboard', 'tourmaster'),
					'icon' => 'fa fa-dashboard',
					'top-bar' => true,
				),
				'edit-profile' => array(
					'title' => esc_html__('Edit Profile', 'tourmaster'),
					'icon' => 'fa fa-edit',
					'top-bar' => true,
				),
				'change-password' => array(
					'title' => esc_html__('Change Password', 'tourmaster'),
					'icon' => 'fa fa-unlock-alt'
				),
				'guide-options' => array(
					'type' => 'title',
					'title' => esc_html__('Guide Options', 'tourmaster')
				),
				'my-information' => array(
					'icon' => 'icon_document_alt',
					'title' => esc_html__('My Information', 'tourmaster')
				),
				'my-tours' => array(
					'title' => esc_html__('My Trips', 'tourmaster'),
					'icon' => 'icon_document_alt'
				),
				'my-orders' => array(
					'title' => esc_html__('My Orders', 'tourmaster'),
					'icon' => 'icon_wallet'
				),
				'my-payments' => array(
					'title' => esc_html__('My Payments', 'tourmaster'),
					'icon' => 'fa fa-star'
				),
				'sign-out' => array(
					'title' => esc_html__('Sign Out', 'tourmaster'),
					'icon' => 'icon_lock-open_alt',
					'link' => wp_logout_url( home_url('/') ),
					'top-bar' => true,
				),
			));
		}
	}

	// user page breadcrumbs
	if( !function_exists('tourmaster_get_user_breadcrumb') ){
		function tourmaster_get_user_breadcrumb(){

			$main_page = empty($_GET['page_type'])? 'dashboard': $_GET['page_type'];
			$sub_page = empty($_GET['sub_page'])? '': $_GET['sub_page'];
			global $current_user;
			$current_user = wp_get_current_user();
			$userlogin = get_user_meta($current_user->ID, 'user-type', true);

			if($userlogin == "user-traveler"){
				$nav_list = tourmaster_get_user_nav_list();
			} else {
				$nav_list = tourmaster_get_user_guide_nav_list();
			}

			echo '<div class="tourmaster-user-breadcrumbs" >';

			// dashboard
			if( !empty($nav_list['dashboard']['title']) ){
				$page_link = tourmaster_get_template_url('user', array('page_type'=>'dashboard'));

				echo '<a class="tourmaster-user-breadcrumbs-item ' . (($main_page == 'dashboard')? 'tourmaster-active': '') . '" href="' . esc_url($page_link) . '" >';
				echo $nav_list['dashboard']['title'];
				echo '</a>';

				if( $main_page != 'dashboard' ){
					echo '<span class="tourmaster-sep" >></span>';
				}
			}

			// main navigation
			if( $main_page != 'dashboard' ){
				if( !empty($nav_list[$main_page]['title']) ){
					$main_nav_title = $nav_list[$main_page]['title'];
				}else{
					$main_nav_title = $main_page;
				}

				if( empty($sub_page) ){
					echo '<span class="tourmaster-user-breadcrumbs-item tourmaster-active" >' . $main_nav_title . '</span>';
				}else{
					$page_link = tourmaster_get_template_url('user', array('page_type'=>$main_page));
					echo '<a class="tourmaster-user-breadcrumbs-item" href="' . $page_link . '" >' . $main_nav_title . '</a>';
					echo '<span class="tourmaster-sep" >></span>';
				}	
			}

			// sub navigation
			if( !empty($sub_page) ){
				if( !empty($_GET['tour_id']) ){
					$sub_nav_title = get_the_title($_GET['tour_id']);
				}else{
					if( !empty($_GET['id']) ){
						$sub_nav_title = get_the_title($_GET['id']);
					}else{
						$sub_nav_title = $sub_page;
					}
				}
				//custom code for GUIDE dashboard
				


				echo '<span class="tourmaster-user-breadcrumbs-item tourmaster-active" >' . $sub_nav_title . '</span>';
			}

			echo '</div>';

		} // tourmaster_get_user_breadcrumb
	}	

	// for user top bar
	if( !function_exists('tourmaster_user_top_bar') ){
		function tourmaster_user_top_bar(){
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');
			if( $enable_membership == 'disable' ){
				return;
			}

			if( is_user_logged_in() ){
				global $current_user;
				$avatar = get_the_author_meta('tourmaster-user-avatar', $current_user->data->ID);

				$ret  = '<div class="tourmaster-user-top-bar tourmaster-user" >';
				if( !empty($avatar['thumbnail']) ){
					$ret .= '<img src="' . esc_url($avatar['thumbnail']) . '" alt="profile-image" />';
				}else if( !empty($avatar['file_url']) ){
					$ret .= '<img src="' . esc_url($avatar['file_url']) . '" alt="profile-image" />';
				}else{
					$ret .= get_avatar($current_user->data->ID, 30);
				}	
				$ret .= '<span class="tourmaster-user-top-bar-name" >' . tourmaster_get_user_meta($current_user->data->ID, 'full_name') . '</span>';
				$ret .= '<i class="fa fa-sort-down" ></i>';

				$nav_list = tourmaster_get_user_nav_list();
				$user_page = tourmaster_get_template_url('user');
				$ret .= '<div class="tourmaster-user-top-bar-nav" >';
				$ret .= '<div class="tourmaster-user-top-bar-nav-inner" >';
				foreach( $nav_list as $nav_slug => $nav ){
					if( !empty($nav['top-bar']) && !empty($nav['title']) ){
						$nav_link = empty($nav['link'])? add_query_arg(array('page_type'=>$nav_slug), $user_page): $nav['link'];

						$ret .= '<div class="tourmaster-user-top-bar-nav-item tourmaster-nav-' . esc_attr($nav_slug) . '" >';
						$ret .= '<a href="' . esc_url($nav_link) . '" >' . $nav['title'] . '</a>';
						$ret .= '</div>';
					}
				}
				$ret .= '</div>'; // tourmaster-user-top-bar-nav-inner
				$ret .= '</div>'; // tourmaster-user-top-bar-nav
				$ret .= '</div>'; // tourmaster-user-top-bar
			}else{

				$recaptcha = tourmaster_get_option('general', 'enable-recaptcha', 'disable');

				if( $recaptcha == 'enable' ){
					$login_url = tourmaster_get_template_url('login');
					$register_url = tourmaster_get_template_url('register');

					$ret  = '<div class="tourmaster-user-top-bar tourmaster-guest" >';
					$ret .= '<a class="tourmaster-user-top-bar-login" href="' . esc_url($login_url) . '" >';
					$ret .= '<i class="icon_lock_alt" ></i>';
					$ret .= '<span class="tourmaster-text" >' . esc_html__('Login', 'tourmaster') . '</span>';
					$ret .= '</a>';
					$ret .= '<a class="tourmaster-user-top-bar-signup" href="' . esc_url($register_url) . '" >';
					$ret .= '<i class="fa fa-user" ></i>';
					$ret .= '<span class="tourmaster-text" >' . esc_html__('Sign Up', 'tourmaster') . '</span>';
					$ret .= '</a>';
					$ret .= '</div>';

				}else{

					$mobile_login_link = tourmaster_get_option('general', 'mobile-login-link', 'disable');
					$mobile_login_link = ($mobile_login_link == 'enable')? true: false;

					$ret  = '<div class="tourmaster-user-top-bar tourmaster-guest" >';
					$ret .= '<span class="tourmaster-user-top-bar-login ';
					$ret .= ($mobile_login_link)? 'tourmaster-hide-on-mobile': '';
					$ret .= '" data-tmlb="login" >';
					$ret .= '<i class="icon_lock_alt" ></i>';
					$ret .= '<span class="tourmaster-text" >' . esc_html__('Login', 'tourmaster') . '</span>';
					$ret .= '</span>';
					$ret .= tourmaster_lightbox_content(array(
						'id' => 'login',
						'title' => esc_html__('Login', 'tourmaster'),
						'content' => tourmaster_get_login_form(false)
					));
					$ret .= '<span class="tourmaster-user-top-bar-signup ';
					$ret .= ($mobile_login_link)? 'tourmaster-hide-on-mobile': '';
					$ret .= '" data-tmlb="signup" >';
					$ret .= '<i class="fa fa-user" ></i>';
					$ret .= '<span class="tourmaster-text" >' . esc_html__('Sign Up', 'tourmaster') . '</span>';
					$ret .= '</span>';
					$ret .= tourmaster_lightbox_content(array(
						'id' => 'signup',
						'title' => esc_html__('Sign Up', 'tourmaster'),
						'content' => tourmaster_get_registration_form(false)
					));

					if( $mobile_login_link ){
						$login_url = tourmaster_get_template_url('login');
						$ret .= '<a class="tourmaster-user-top-bar-login tourmaster-show-on-mobile" href="' . esc_url($login_url) . '" >';
						$ret .= '<i class="icon_lock_alt" ></i>';
						$ret .= '<span class="tourmaster-text" >' . esc_html__('Login', 'tourmaster') . '</span>';
						$ret .= '</a>';

						$register_url = tourmaster_get_template_url('register');
						$ret .= '<a class="tourmaster-user-top-bar-signup tourmaster-show-on-mobile" href="' . esc_url($register_url) . '" >';
						$ret .= '<i class="fa fa-user" ></i>';
						$ret .= '<span class="tourmaster-text" >' . esc_html__('Sign Up', 'tourmaster') . '</span>';
						$ret .= '</a>';
					}
					$ret .= '</div>'; 

				}

			}
			
			return $ret;
		}
	}
	add_shortcode('tourmaster_login_bar', 'tourmaster_user_top_bar_shortcode');
	if( !function_exists('tourmaster_user_top_bar_shortcode') ){
		function tourmaster_user_top_bar_shortcode($atts){
			$atts = wp_parse_args($atts, array());

			$ret  = '<div class="tourmaster-login-bar-shortcode clearfix" >';
			$ret .= tourmaster_user_top_bar();
			$ret .= '</div>';

			return $ret;
		}
	}

	// login form
	if( !function_exists('tourmaster_get_login_form') ){
		function tourmaster_get_login_form( $echo = true ){
			if( !$echo ){
				ob_start();
			}
?>
<form class="tourmaster-login-form tourmaster-form-field tourmaster-with-border" method="post" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>">
	<div class="tourmaster-login-form-fields clearfix" >
		<p class="tourmaster-login-user">
			<label><?php echo esc_html__('Username or E-Mail', 'tourmaster'); ?></label>
			<input type="text" name="log" />
		</p>
		<p class="tourmaster-login-pass">
			 <label><?php echo esc_html__('Password', 'tourmaster'); ?></label>
			 <input type="password" name="pwd" />
		</p>
	</div>
	<?php do_action('login_form'); ?> 
	<p class="tourmaster-login-submit" >
		<input type="submit" name="wp-submit" class="tourmaster-button" value="<?php echo esc_html__('Sign In!', 'tourmaster'); ?>" />
	</p>
	<p class="tourmaster-login-lost-password" >
		<a href="<?php echo add_query_arg(array(
			'source'=>'tm', 
			'lang'=>(defined('ICL_LANGUAGE_CODE')? ICL_LANGUAGE_CODE: '')
		), wp_lostpassword_url()); ?>" ><?php echo esc_html__('Forget Password?','tourmaster'); ?></a>
	</p>

	<input type="hidden" name="rememberme"  value="forever" />
	<input type="hidden" name="redirect_to" value="<?php 
		if( !empty($_GET['redirect']) ){
			if( is_numeric($_GET['redirect']) ){
				$redirect_url = get_permalink($_GET['redirect']);
			}else{
				$redirect_url = tourmaster_get_template_url($_GET['redirect']);
				$redirect_url = empty($redirect_url)? $_GET['redirect']: $redirect_url;
			}

			echo esc_url($redirect_url);
		}else{
			echo esc_url(add_query_arg(null, null));
		}
	?>" />
	<input type="hidden" name="redirect" value="<?php echo empty($_GET['redirect'])? '': esc_attr($_GET['redirect']); ?>" />
	<input type="hidden" name="source"  value="tm" />
</form>

<div class="tourmaster-login-bottom" >
	<h3 class="tourmaster-login-bottom-title" ><?php echo esc_html__('Do not have an account?', 'tourmaster'); ?></h3>
	<a class="tourmaster-login-bottom-link" href="<?php echo tourmaster_get_template_url('register'); ?>" ><?php echo esc_html__('Create an Account', 'tourmaster'); ?></a>
</div>
<?php
			if( !$echo ){
				$ret = ob_get_contents();
				ob_end_clean();

				return $ret;
			}
		} // tourmaster_get_login_form
	} 

	// login form
	if( !function_exists('tourmaster_get_login_form2') ){
		function tourmaster_get_login_form2( $echo = true, $settings = array() ){

			$recaptcha = tourmaster_get_option('general', 'enable-recaptcha', 'disable');

			if( !$echo ){
				ob_start();
			}
?>
<div class="tourmaster-login-form2-wrap clearfix" >
<form class="tourmaster-login-form2 tourmaster-form-field tourmaster-with-border" method="post" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>">
	<h3 class="tourmaster-login-title"><?php esc_html_e('Already A Member?', 'tourmaster'); ?></h3>
	<?php
		if( $recaptcha == 'enable' ){

			$login_url = tourmaster_get_template_url('login');
			$login_url = add_query_arg(array('redirect' => 'payment'), $login_url);
			echo '<a class="tourmaster-button" href="' . esc_url($login_url) . '" >';
			echo '<span class="tourmaster-text" >' . esc_html__('Login', 'tourmaster') . '</span>';
			echo '</a>';

		}else{

			?>
				<div class="tourmaster-login-form-fields clearfix" >
					<p class="tourmaster-login-user">
						<label><?php echo esc_html__('Username or E-mail', 'tourmaster'); ?></label>
						<input type="text" name="log" />
					</p>
					<p class="tourmaster-login-pass">
						 <label><?php echo esc_html__('Password', 'tourmaster'); ?></label>
						 <input type="password" name="pwd" />
					</p>
				</div>
				<p class="tourmaster-login-submit" >
					<input type="submit" name="wp-submit" class="tourmaster-button" value="<?php echo esc_html__('Sign In!', 'tourmaster'); ?>" />
				</p>
				<p class="tourmaster-login-lost-password" >
					<a href="<?php echo add_query_arg(array(
						'source'=>'tm', 
						'lang'=>(defined('ICL_LANGUAGE_CODE')? ICL_LANGUAGE_CODE: '')
					), wp_lostpassword_url()); ?>" ><?php echo esc_html__('Forget Password?','tourmaster'); ?></a>
				</p>

				<input type="hidden" name="rememberme"  value="forever" />
				<input type="hidden" name="redirect_to" value="<?php 
					if( !empty($settings['redirect']) ){
						$redirect_url = tourmaster_get_template_url($settings['redirect']);
						$redirect_url = empty($redirect_url)? $settings['redirect']: $redirect_url;
						echo esc_url($redirect_url);
					}else{
						echo esc_url(add_query_arg(null, null)); 
					}	
				?>" />
				<input type="hidden" name="source"  value="tm" />
			<?php 
			do_action('login_form'); 
		}
	?>
</form>

<div class="tourmaster-login2-right" >
	<h3 class="tourmaster-login2-right-title" ><?php esc_html_e('Don\'t have an account? Create one.', 'tourmaster'); ?></h3>
	<div class="tourmaster-login2-right-content" >
		<div class="tourmaster-login2-right-description" ><?php 
			esc_html_e('When you book with an account, you will be able to track your payment status, track the confirmation and you can also rate the tour after you finished the tour.', 'tourmaster');
		?></div>
		<a class="tourmaster-button" href="<?php 
			$register_url = tourmaster_get_template_url('register');
			if( !empty($settings['redirect']) ){
				$register_url = add_query_arg(array('redirect' => $settings['redirect']), $register_url);
			}else if( get_the_ID() ){
				$register_url = add_query_arg(array('redirect' => get_the_ID()), $register_url);
			}
			echo esc_url($register_url); 
		?>" ><?php 
			esc_html_e('Sign Up', 'tourmaster');
		?></a>
	</div>
	<?php if( !empty($settings['continue-as-guest']) ){ ?>
		<h3 class="tourmaster-login2-right-title" ><?php esc_html_e('Or Continue As Guest', 'tourmaster'); ?></h3>
		<a class="tourmaster-button" href="<?php echo tourmaster_get_template_url('payment'); ?>" ><?php esc_html_e('Continue As Guest', 'tourmaster'); ?></a>
	<?php } ?>
</div>
</div>
<?php
			if( !$echo ){
				$ret = ob_get_contents();
				ob_end_clean();

				return $ret;
			}
		} // tourmaster_get_login_form
	} 

	// registration form
	if( !function_exists('tourmaster_get_registration_form') ){
		function tourmaster_get_registration_form( $echo = true, $query_args = array() ){
			if( !$echo ){
				ob_start();
			}

			$profile_fields = array_merge(array(
				'username' => array(
					'title' => esc_html__('Username', 'tourmaster'),
					'type' => 'text',
					'required' => true
				),
				'password' => array(
					'title' => esc_html__('Password', 'tourmaster'),
					'type' => 'password',
					'required' => true
				),
				'confirm-password' => array(
					'title' => esc_html__('Confirm Password', 'tourmaster'),
					'type' => 'password',
					'required' => true
				)
			), 
			tourmaster_get_profile_fields());

			$action_url = tourmaster_get_template_url('register');
			if( !empty($query_args) ){
				$action_url = add_query_arg($query_args, $action_url);
			}
			echo '<form class="tourmaster-register-form tourmaster-form-field tourmaster-with-border" action="' . esc_url($action_url) . '" method="post" >';

			echo '<div class="tourmaster-register-message" >';
			echo esc_html__('After creating an account, you\'ll be able to track your payment status, track the confirmation and you can also rate the tour after you finished the tour.', 'tourmaster');
			echo '</div>';

			echo '<div class="tourmaster-register-form-fields clearfix" >';
			foreach( $profile_fields as $slug => $profile_field ){
				if( !empty($profile_field['required']) ){
					$profile_field['slug'] = $slug;
					tourmaster_get_form_field($profile_field, 'profile');
				}
			}
			echo '</div>';

			echo '<input type="hidden" name="redirect" value="';
			if( !empty($_GET['redirect']) ){
				echo esc_attr($_GET['redirect']);
			}else if( !empty($_POST['redirect']) ){
				echo esc_attr($_POST['redirect']);
			}else{
				global $tourmaster_template;
				if( empty($tourmaster_template) ){
					echo add_query_arg(array());
				}
			}
			echo '" >';
			
			$recaptcha = tourmaster_get_option('general', 'enable-recaptcha', 'disable');
			if( $recaptcha == 'enable' ){
				echo apply_filters('gglcptch_display_recaptcha', '', 'registration_form');
			}

			$our_term = tourmaster_get_option('general', 'register-term-of-service-page', '#');
			$our_term = is_numeric($our_term)? get_permalink($our_term): $our_term; 
			$privacy = tourmaster_get_option('general', 'register-privacy-statement-page', '#');
			$privacy = is_numeric($privacy)? get_permalink($privacy): $privacy; 
			echo '<div class="tourmaster-register-term" >';
			echo '<input type="checkbox" name="tourmaster-require-acceptance" />';
			echo sprintf(wp_kses(
				__('* Creating an account means you\'re okay with our <a href="%s" target="_blank">Terms of Service</a> and <a href="%s" target="_blank">Privacy Statement</a>.', 'tourmaster'), 
				array('a' => array( 'href'=>array(), 'target'=>array() ))
			), $our_term, $privacy);
			echo '<div class="tourmaster-notification-box tourmaster-failure" >' . esc_html__('Please agree to all the terms and conditions before proceeding to the next step', 'tourmaster') . '</div>';
			echo '</div>';
			echo '<input type="submit" class="tourmaster-register-submit tourmaster-button" value="' . esc_html__('Sign Up', 'tourmaster') . '" />';

			if( class_exists('NextendSocialLogin') ){
				echo do_shortcode('[nextend_social_login]');
			}
			echo '<input type="hidden" name="security" value="' . esc_attr(wp_create_nonce('tourmaster-registration')) . '" />';
			echo '</form>';

			echo '<div class="tourmaster-register-bottom" >';
			echo '<h3 class="tourmaster-register-bottom-title" >' . esc_html__('Already a member?', 'tourmaster') . '</h3>';
			echo '<a class="tourmaster-register-bottom-link" href="' . tourmaster_get_template_url('login') . '" >' . esc_html__('Login', 'tourmaster') . '</a>';
			echo '</div>';

			if( !$echo ){
				$ret = ob_get_contents();
				ob_end_clean();

				return $ret;
			}
		} // tourmaster_get_registration_form
	}

	// add wish list ajax
	add_action('wp_ajax_tourmaster_add_wish_list', 'tourmaster_ajax_add_wish_list');
	add_action('wp_ajax_nopriv_tourmaster_add_wish_list', 'tourmaster_ajax_add_wish_list');
	if( !function_exists('tourmaster_ajax_add_wish_list') ){
		function tourmaster_ajax_add_wish_list(){

			if( is_user_logged_in() && !empty($_POST['tour-id']) ){
				global $current_user;
				
				$wish_list = get_user_meta($current_user->ID, 'tourmaster-wish-list', true);
				$wish_list = empty($wish_list)? array(): $wish_list;

				if( !in_array($_POST['tour-id'], $wish_list) ){
					$wish_list[] = $_POST['tour-id'];
					update_user_meta($current_user->ID, 'tourmaster-wish-list', $wish_list);
				}
			}

			die(json_encode($_POST));
		} // tourmaster_ajax_add_wish_list
	}

	// set upload directory to tourmaster_receipt
	if( !function_exists('tourmaster_set_receipt_upload_folder') ){
		function tourmaster_set_receipt_upload_folder( $uploads ){
			$keys = array( 'path', 'url', 'basedir', 'baseurl' );

			foreach( $keys as $key ){
				if( !empty($uploads[$key]) ){
					$uploads[$key] = str_replace('/wp-content/uploads', '/wp-content/uploads/tourmaster-receipt', $uploads[$key]);
				}
			}
			
			return $uploads;
		}
	}

	// set upload directory to tourmaster_avatar
	if( !function_exists('tourmaster_set_avatar_upload_folder') ){
		function tourmaster_set_avatar_upload_folder( $uploads ){
			$keys = array( 'path', 'url', 'basedir', 'baseurl' );

			foreach( $keys as $key ){
				if( !empty($uploads[$key]) ){
					$uploads[$key] = str_replace('/wp-content/uploads', '/wp-content/uploads/tourmaster-avatar', $uploads[$key]);
				}
			}
			
			return $uploads;
		}
	}