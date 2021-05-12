<?php
	/*	
	*	Goodlayers Html Option File
	*	---------------------------------------------------------------------
	*	This file create the class that help you create the input form element
	*	---------------------------------------------------------------------
	*/	
	
	if( !class_exists('tourmaster_html_option') ){
		
		class tourmaster_html_option{

			// call this function on wp_enqueue_script hook
			static function include_script($elements = array()){
				 
				$elements = wp_parse_args($elements, array(
					'style' => 'html-option-meta'
				));				

				tourmaster_include_utility_script();

				wp_enqueue_media();
				wp_enqueue_style('wp-color-picker');
				wp_enqueue_style('tourmaster-html-option', TOURMASTER_URL . '/framework/css/' . $elements['style'] . '.css');
				
				// enqueue the script
				wp_enqueue_script('tourmaster-html-option', TOURMASTER_URL . '/framework/js/html-option.js', array(
					'jquery', 'jquery-effects-core', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-slider'
				), false, true);	
				
				// localize the script
				$html_option_val =  array();
				$html_option_val['text'] = array(
					'ajaxurl' => TOURMASTER_AJAX_URL,
					'error_head' => esc_html__('An error occurs', 'tourmaster'),
					'error_message' => esc_html__('Please refresh the page to try a	in. If the problem still persists, please contact administrator for this.', 'tourmaster'),
					'nonce' => wp_create_nonce('tourmaster_html_option'),
					'upload_media' => esc_html__('Select or Upload Media', 'tourmaster'),
					'choose_media' => esc_html__('Use this media', 'tourmaster'),
				);
				$html_option_val['tabs'] = array(
					'title_text' => esc_html__('Item\'s Title', 'tourmaster'),
					'tab_checkbox_on' => esc_html__('On', 'tourmaster'),
					'tab_checkbox_off' => esc_html__('Off', 'tourmaster')
				);
				wp_localize_script('tourmaster-html-option', 'html_option_val', $html_option_val);

			}
			
			// use to obtain input elements based on the settings variable
			static function get_element($settings){
				
				if( empty($settings['type']) || $settings['type'] == 'customizer-description' ) return;

				// column opening
				if( $settings['type'] == 'column' ){

					$column_class  = 'tourmaster-column-' . (empty($settings['column-size'])? 6: $settings['column-size']) . ' ';
					$column_class .= empty($settings['bottom-divider'])? '': 'tourmaster-column-bottom-divider';

					$ret  = '<div class="' . esc_attr($column_class) . '">';
					if( !empty($settings['right-divider']) ){
						$ret .= '<div class="tourmaster-column-right-divider" ></div>';

						if( $settings['right-divider'] !== true ){
							$ret .= '<i class="tourmaster-column-right-divider-icon ' . esc_attr($settings['right-divider']) . '" ></i>';
						}
					}

					return $ret;

				// closing column
				}else if( $settings['type'] == 'column-close' ){
					$ret  = '</div>';
					$ret .= empty($settings['clear'])? '': '<div class="clear"></div>';

					return $ret;

				// normal elements	
				}else{

					$wrapper_class  = empty($settings['wrapper-class'])? '': $settings['wrapper-class'];
					$wrapper_class .= ' tourmaster-html-option-' . trim($settings['type']);
					if( $settings['type'] == 'custom' && !empty($settings['item-type']) ){
						$wrapper_class .= ' tourmaster-html-option-custom-' . $settings['item-type'];
					}

					$condition = '';
					if( !empty($settings['condition']) ){
						$condition  = 'data-condition="' . esc_attr(json_encode($settings['condition'])) . '" ';
						$condition .= (empty($settings['condition-wrapper']))? '': 'data-condition-wrapper="' . esc_attr($settings['condition-wrapper']) . '" ';
					}
					
					
					$ret  = '<div class="tourmaster-html-option-item ' . esc_attr($wrapper_class) . '-item" ' . $condition . ' >';
					if ($settings['slug']=="address") {
						$ret .= '<h6>Select your address</h6>';
						$ret .= '<div id="map-settings" style="height:350px; width:45%"></div>';
						$ret .= '</br>';
					}

					if( !empty($settings['title']) ){
						$ret .= '<div class="tourmaster-html-option-item-title" >' . $settings['title'] . '</div>';
					}
					
					if( !empty($settings['description']) && ($settings['type'] == 'custom' && $settings['item-type'] == 'group-discount') ){
						$ret .= '<div class="tourmaster-html-option-item-description" >' . $settings['description'] . '</div>';
					}

					$ret .= '<div class="tourmaster-html-option-item-input">';
					switch($settings['type']){
						case 'text': 
							$ret .= self::text($settings);
							break;
						case 'button': 
							$ret .= self::button($settings);
							break;
						case 'time': 
							$ret .= self::time($settings);
							break;
						case 'datepicker': 
							$ret .= self::datepicker($settings);
							break;
						case 'textarea': 
							$ret .= self::textarea($settings);
							break;
						case 'combobox':
							$ret .= self::combobox($settings);
							break;
						case 'multi-combobox':
							$ret .= self::multi_combobox($settings);
							break;
						case 'checkbox': 
							$ret .= self::checkbox($settings);
							break;
						case 'radioimage': 
							$ret .= self::radioimage($settings);
							break;
						case 'upload': 
							$ret .= self::upload($settings);
							break;
						case 'colorpicker': 
							$ret .= self::colorpicker($settings);
							break;
						case 'fontslider': 
							$ret .= self::fontslider($settings);
							break;
						case 'custom': 
							$ret .= self::custom($settings);
						 	break;
						case 'manage-review': 
							$ret .= self::manage_review($settings);
						 	break;
						case 'add-review': 
							$ret .= self::add_review($settings);
						 	break;
						case 'import': 
							$ret .= self::import($settings);
							break;
						case 'export': 
							$ret .= self::export($settings);
							break;
						default: break;
					}
					$ret .= '</div>';
										
					if( !empty($settings['description']) && !($settings['type'] == 'custom' && $settings['item-type'] == 'group-discount') ){
						$ret .= '<div class="tourmaster-html-option-item-description" >' . $settings['description'] . '</div>';
					}
					
					if( !empty($settings['options']) && $settings['options'] == 'skin' ){
						$ret .= '<div class="tourmaster-html-option-skin-edit" >' . esc_html__('Create Skin', 'tourmaster') . '<i class="fa fa-plus-circle" ></i></div>';
					}

					$ret .= '<div class="clear"></div>';
					$ret .= '</div>'; // tourmaster-html-option-item
					
					return $ret;
				}
			}
			
			//////////////////////////
			// element started here
			//////////////////////////			
			
			// input text
			static function text($settings){
				$value = '';
				
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( isset($settings['default']) ){
					$value = $settings['default'];
				}
				
				$ret  = '<input type="text" class="tourmaster-html-option-text" data-type="text" data-slug="' . esc_attr($settings['slug']) . '" value="' . esc_attr($value) . '" ';
				$ret .= empty($settings['data-input-type'])? '': ' data-input-type="' . esc_attr($settings['data-input-type']) . '"';
				if ($settings['slug']=="latitude") {
					$ret .= 'id="latitude-map-set" ';
				}
				if ($settings['slug']=="longitude") {
					$ret .= 'id="longitude-map-set" ';
				}
				if ($settings['slug']=="country") {
					$ret .= 'id="country-map-set" readonly';
				}
				if ($settings['slug']=="address") {
					$ret .= 'id="address-map-set" readonly';
				}
				if ($settings['slug']=="state") {
					$ret .= 'id="state-map-set" readonly';
				}
				if ($settings['slug']=="city") {
					$ret .= 'id="city-map-set" readonly';
				}
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' />';
				
	
				return $ret;
			}		
			
			// input text
			static function button($settings){
				$value = '';

				$ret  = '<div class="tourmaster-html-option-button" ';
				$ret .= empty($settings['data-type'])? '': ' data-type="' . esc_attr($settings['data-type']) . '" '; 
				if( !empty($settings['data-type']) && $settings['data-type'] == 'ajax' ){
					$ret .= ' data-ajax-url="' . esc_attr(TOURMASTER_AJAX_URL) . '" ';
					$ret .= ' data-post-id="' . esc_attr(get_the_ID()) . '" '; 
				}
				$ret .= empty($settings['data-action'])? '': ' data-action="' . esc_attr($settings['data-action']) . '" '; 
				$ret .= empty($settings['data-fields'])? '': ' data-fields="' . esc_attr(json_encode($settings['data-fields'])) . '" '; 
				$ret .= ' >' . $settings['button-title'] . '</div>';
	
				return $ret;
			}				

			// input time
			static function time($settings){
				$value = '';
				
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( isset($settings['default']) ){
					$value = $settings['default'];
				}

				$time_val = explode(':', $value);
				$hh = empty($time_val[0])? '': $time_val[0];
				$mm = empty($time_val[1])? '': $time_val[1];

				$ret  = '<input type="text" class="tourmaster-html-option-time tourmaster-input-hh" ';
				$ret .= 'placeholder="' . esc_html__('HH', 'tourmaster') . '" ';
				$ret .= 'value="' . esc_attr($hh) . '" ';
				$ret .= empty($settings['data-input-type'])? '': ' data-input-type="' . esc_attr($settings['data-input-type']) . '"';
				$ret .= ' />';
				$ret .= '<span class="tourmaster-html-option-time-sep" >:</span>';
				$ret .= '<input type="text" class="tourmaster-html-option-time tourmaster-input-mm" ';
				$ret .= 'placeholder="' . esc_html__('MM', 'tourmaster') . '" ';
				$ret .= 'value="' . esc_attr($mm) . '" ';
				$ret .= empty($settings['data-input-type'])? '': ' data-input-type="' . esc_attr($settings['data-input-type']) . '"';
				$ret .= ' />';
				$ret .= '<input type="hidden" data-slug="' . esc_attr($settings['slug']) . '" data-type="text"  value="' . esc_attr($value) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' />';
				
				return $ret;
			}	

			// input datepicker
			static function datepicker($settings){
				$value = '';
				
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( isset($settings['default']) ){
					$value = $settings['default'];
				}

				$ret  = '<input type="text" class="tourmaster-html-option-text tourmaster-html-option-datepicker" data-type="text" data-slug="' . esc_attr($settings['slug']) . '" value="' . esc_attr($value) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' />';
				$ret .= '<i class="tourmaster-html-option-datepicker-icon fa fa-calendar" ></i>';
				return $ret;
			}			
			
			// textarea
			static function textarea($settings){
				$value = '';
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( !empty($settings['default']) ){
					$value = $settings['default'];
				}

				$ret  = '<textarea class="tourmaster-html-option-textarea" data-type="textarea" data-slug="' . esc_attr($settings['slug']) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' >' . esc_textarea($value) . '</textarea>';
	
				return $ret;
			}
			
			// combobox
			static function combobox($settings){
				$value = '';
				$extra_html = '';
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( !empty($settings['default']) ){
					$value = $settings['default'];
				}

				if( $settings['options'] == 'sidebar' ){
					$settings['options'] = tourmaster_get_sidebar_list(array('with-none'=>true));
				}else if( $settings['options'] == 'sidebar-default' ){
					$settings['options'] = tourmaster_get_sidebar_list(array('with-none'=>true, 'with-default'=>true));
				}else if( $settings['options'] == 'thumbnail-size' ){
					$settings['options'] = tourmaster_get_thumbnail_list();
				}
				
				$ret  = '<div class="tourmaster-custom-combobox" >';
				$ret .= '<select class="tourmaster-html-option-combobox" data-type="combobox" data-slug="' . esc_attr($settings['slug']) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' >';
				if( !empty($settings['options']) ){
					foreach($settings['options'] as $option_key => $option_value ){
						$ret .= '<option value="' . esc_attr($option_key) . '" ' . selected($value, $option_key, false) . ' >' . $option_value . '</option>';
					}
				}
				$ret .= '</select>';
				$ret .= '</div>';
				
				return $ret;
			}
			
			// multi_combobox
			static function multi_combobox($settings){

				
				$value = array();
				if( isset($settings['value']) ){
					$value = empty($settings['value'])? array(): $settings['value'];
				}else if( !empty($settings['default']) ){
					$value = $settings['default'];
				}
				
				if( !empty($settings['options']) ){
					if( $settings['options'] == 'post_type' ){
						$settings['options'] = tourmaster_get_post_list($settings['options-data']);
					}
					if( $settings['options-data'] == 'tour-activity'){
						$settings['options'] = get_terms(array(
							'taxonomy' => 'tour-activity',
							'hide_empty' => false,
						));
					}
				}

				$ret  = '<select class="tourmaster-html-option-multi-combobox" data-type="multi-combobox" data-slug="' . esc_attr($settings['slug']) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' multiple >';
				if( !empty($settings['options']) ){
					if ($settings['options-data'] == 'tour-activity') {
						foreach($settings['options'] as $option ){
							$ret .= '<option value="' . esc_attr($option->term_id) . '" ' . (in_array($option->term_id, $value)? 'selected': '') . ' >'.$option->name.'</option>';
						}
					} else {
						foreach($settings['options'] as $option_key => $option_value ){
							$ret .= '<option value="' . esc_attr($option_key) . '" ' . (in_array($option_key, $value)? 'selected': '') . ' >' . $option_value . '</option>';
						}
					}
				} 
				$ret .= '</select>';
				
				return $ret;
			}			
			
			// checkbox
			static function checkbox($settings){
				$value = '';
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( !empty($settings['default']) ){
					$value = $settings['default'];
				}else{
					$value = 'enable';
				}
				
				$ret  = '<label>';
				$ret .= '<input type="checkbox" class="tourmaster-html-option-checkbox" data-type="checkbox" data-slug="' . esc_attr($settings['slug']) . '" ' . checked($value, 'enable', false) . ' ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' />';
				$ret .= '<div class="tourmaster-html-option-checkbox-appearance tourmaster-noselect">';
				$ret .= '<span class="tourmaster-checkbox-button tourmaster-on">' . esc_html__('On', 'tourmaster') . '</span>';
				$ret .= '<span class="tourmaster-checkbox-separator"></span>';
				$ret .= '<span class="tourmaster-checkbox-button tourmaster-off">' . esc_html__('Off', 'tourmaster') . '</span>';
				$ret .= '</div>';
				$ret .= '</label>';
				
				return $ret;
			}		
			
			// radioimage
			static function radioimage($settings){

				$value = '';
				if( !empty($settings['value']) ){
					$value = $settings['value'];
				}else if( !empty($settings['default']) ){
					$value = $settings['default'];
				}else{
					reset($settings['options']);
					$value = key($settings['options']);
				}

				$max_width = empty($settings['max-width'])? '': $settings['max-width'];

				if( $settings['options'] == 'sidebar' ){
					$settings['options'] = array(
						'none' => TOURMASTER_URL . '/framework/images/sidebar/none.jpg',
						'left' => TOURMASTER_URL . '/framework/images/sidebar/left.jpg',
						'right' => TOURMASTER_URL . '/framework/images/sidebar/right.jpg',
						'both' => TOURMASTER_URL . '/framework/images/sidebar/both.jpg',
					);

					if( !empty($settings['with-default']) ){
						$settings['options'] = array_merge(array(
							'default' => TOURMASTER_URL . '/framework/images/sidebar/default.jpg',
						), $settings['options']);
					}
					if( !empty($settings['without-none']) ){
						unset($settings['options']['none']);
					}
				}

				$ret = '';
				foreach( $settings['options'] as $option_key => $option_url ){
					$ret .= '<label ' . tourmaster_esc_style(array('max-width'=> $max_width)) . ' >';
					$ret .= '<input class="tourmaster-html-option-radioimage" type="radio" name="' . esc_attr($settings['slug']) . '" data-type="radioimage" data-slug="' . esc_attr($settings['slug']) . '" value="' . esc_attr($option_key) . '" ' . checked($value, $option_key, false) . '/>';
					$ret .= '<div class="tourmaster-radioimage-checked" ></div>';
					$ret .= '<img src="' . esc_url($option_url) . '" alt="' . esc_attr($option_key) . '" />';
					$ret .= '</label>';
				}
				
				return $ret;
			}
			
			// upload
			static function upload($settings){
				$value = '';
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}
				
				$ret  = '<div class="tourmaster-html-option-upload-appearance ' . (empty($value)? '': 'tourmaster-active') . '" >';
				$ret .= '<input type="hidden" class="tourmaster-html-option-upload" data-type="upload" data-slug="' . esc_attr($settings['slug']) . '" value="' . esc_attr($value) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' />';
				$attachment_id = $value;
				$ret .= '<div class="tourmaster-upload-image-container" style="' . (empty($value)? '': 'background-image: url(\'' . esc_url(wp_get_attachment_url($value)) . '\');') . '" ></div>';
				$ret .= '<p style="display: none">'.$attachment_id.'</p>';
				$ret .= '<div class="tourmaster-upload-image-overlay" >';
				$ret .= '<div class="tourmaster-upload-image-button-hover">';
				$ret .= '<span class="tourmaster-upload-image-button tourmaster-upload-image-add"><i class="fa fa-plus" ></i></span>';
				$ret .= '<span class="tourmaster-upload-image-button tourmaster-upload-image-remove"><i class="fa fa-minus" ></i></span>';
				$ret .= '</div>'; // tourmaster-upload-image-hover
				$ret .= '</div>'; // tourmaster-upload-image-overlay
				$ret .= '</div>'; // tourmaster-html-option-upload-appearance
				
				return $ret;
			}
			
			// colorpicker
			static function colorpicker($settings){
				$value = ''; $default = '';
				if( !empty($settings['default']) ){
					$default = $settings['default'];
				}
				
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( !empty($default) ){
					$value = $default;
				}
				
				$ret = '<input type="text" class="tourmaster-html-option-colorpicker" data-type="colorpicker" data-slug="' . esc_attr($settings['slug']) . '" value="' . esc_attr($value) . '" data-default-color="' . esc_attr($default) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= ' />';
	
				return $ret;
			}

			// fontslider
			static function fontslider($settings){
				$value = '';
				if( !empty($settings['value']) || (isset($settings['value']) && $settings['value'] === '0') ){
					$value = $settings['value'];
				}else if( !empty($settings['default']) ){
					$value = $settings['default'];
				}else{
					$value = 0;
				}

				if( !empty($settings['data-type']) && $settings['data-type'] == 'opacity' ){
					$settings['data-min'] = 0;
					$settings['data-max'] = 100;
					$settings['data-suffix'] = 'none';
				}
				
				$ret  = '<input type="text" class="tourmaster-html-option-fontslider" data-type="text" value="' . esc_attr($value) . '" ';
				$ret .= 'data-slug="' . esc_attr($settings['slug']) . '" ';
				$ret .= isset($settings['data-min'])? 'data-min-value="' . esc_attr($settings['data-min']) . '" ': '';
				$ret .= isset($settings['data-max'])? 'data-max-value="' . esc_attr($settings['data-max']) . '" ': '';
				$ret .= isset($settings['data-suffix'])? ' data-suffix="' . esc_attr($settings['data-suffix']) . '" ': '';
				$ret .= ' />';
				
				return $ret;
			}			
			
			// review
			static function manage_review($settings){
				$review_num_fetch = apply_filters('tourmaster_review_num_fetch', 5);
				$review_args = array(
					'review_tour_id' => get_the_ID(), 
					'review_score' => 'IS NOT NULL'
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

					$ret  = '<div class="tourmaster-html-option-admin-manage-review" ';
					$ret .= ' data-ajax-url="' . esc_attr(TOURMASTER_AJAX_URL) . '" ';
					$ret .= ' data-tour-id="' . esc_attr(get_the_ID()) . '" >';
					$ret .= tourmaster_get_review_content_list($results, true);
					$ret .= tourmaster_get_review_content_pagination($max_num_page);
					$ret .= '</div>';
				}else{
					$ret  = '<div class="tourmaster-html-option-admin-no-review" >';
					$ret .= esc_html__('There\'re no review available for this tour. If you just added the review, please refresh the page to see the results.', 'tourmaster');
					$ret .= '</div>';
				}

				return $ret;
			}	
			static function add_review($settings){
				$ret  = '<div class="tourmaster-html-option-admin-review" >';
				$ret .= tourmaster_get_review_form( null, true );
				$ret .= '</div>';

				return $ret;
			}	
			
			// custom
			static function custom($settings){
				$value = '';
				if( isset($settings['value']) ){
					$value = $settings['value'];
				}else if( !empty($settings['default']) ){
					$value = $settings['default'];
				}

				$ret  = '<div class="tourmaster-html-option-custom" data-type="custom" data-item-type="' . esc_attr($settings['item-type']) . '" data-slug="' . esc_attr($settings['slug']) . '" ';
				$ret .= empty($settings['data-input-type'])? '': ' data-input-type="' . esc_attr($settings['data-input-type']) . '" ';
				$ret .= empty($settings['with-name'])? '': ' name="' . esc_attr($settings['slug']) . '"';
				$ret .= '>';
				if( !empty($settings['settings']) ){
					$ret .= '<span class="tourmaster-html-option-custom-settings" data-value="' . esc_attr(json_encode($settings['settings'])) . '" ></span>';
				}
				if( !empty($settings['options']) ){
					$ret .= '<span class="tourmaster-html-option-custom-options" data-value="' . esc_attr(json_encode($settings['options'])) . '" ></span>';
				}
				if( !empty($value) ){
					$ret .= '<span class="tourmaster-html-option-custom-value" data-value="' . esc_attr(json_encode($value)) . '" ></span>';
				}
				$ret .= '</div>';
	
				return $ret;
			}

			// import
			static function import($settings){

				$ret  = '<div class="tourmaster-html-option-import" >';
				$ret .= '<form method="post" enctype="multipart/form-data" >';
				$ret .= '<input class="tourmaster-html-option-import-file" type="file" name="tourmaster-import" >';
				$ret .= '<div class="tourmaster-html-option-import-button" >' . esc_html__('Import', 'tourmaster') . '</div>';
				$ret .= '</form>';
				$ret .= '</div>';
	
				return $ret;
			}

			// export
			static function export($settings){

				$ret  = '<div class="tourmaster-html-option-export" data-action="' . esc_attr($settings['action']) . '" >';
				if( !empty($settings['options']) ){
					$ret .= '<div class="tourmaster-custom-combobox" >';
					$ret .= '<select class="tourmaster-html-option-export-option tourmaster-html-option-combobox" data-type="combobox" >';
					if( !empty($settings['options']) ){
						foreach($settings['options'] as $option_key => $option_value ){
							$ret .= '<option value="' . esc_attr($option_key) . '" >' . $option_value . '</option>';
						}
					}
					$ret .= '</select>';
					$ret .= '</div>';
				}
				$ret .= '<div class="tourmaster-html-option-export-button" >' . esc_html__('Export', 'tourmaster') . '</div>';
				$ret .= '</div>';
	
				return $ret;
			}			
			
		} // tourmaster_html_option
	
	} // class_exists


	/////////////////////////////////////////////
	//		review section
	/////////////////////////////////////////////

	if( !function_exists('tourmaster_admin_lightbox_content') ){
		function tourmaster_admin_lightbox_content( $settings = array() ){

			$ret  = '<div class="tourmaster-admin-lightbox-content-wrap" data-tmlb-id="' . $settings['id'] . '" >';
			if( !empty($settings['title']) ){
				$ret .= '<div class="tourmaster-admin-lightbox-head" >';
				$ret .= '<h3 class="tourmaster-admin-lightbox-title" >' . $settings['title'] . '</h3>';
				$ret .= '<i class="tourmaster-admin-lightbox-close icon_close" ></i>';
				$ret .= '</div>';
			}

			if( !empty($settings['content']) ){
				$ret .= '<div class="tourmaster-admin-lightbox-content" >' . $settings['content'] . '</div>';
			}
			$ret .= '</div>';

			return $ret;
		} // tourmaster_lightbox_content
	}

	add_action('wp_ajax_tourmaster_admin_edit_review', 'tourmaster_admin_edit_review');
	if( !function_exists('tourmaster_admin_edit_review') ){
		function tourmaster_admin_edit_review(){

			$post_data = tourmaster_process_post_data($_POST);
			$ret = array();

			if( !empty($post_data['review_id']) ){	

				if( !empty($post_data['review-published-date']) && !empty($post_data['description']) && 
					!empty($post_data['traveller-type']) && !empty($post_data['rating']) ){

					$data = array(
						'review_score' => $post_data['rating'],
						'review_type' => $post_data['traveller-type'],
						'review_description' => $post_data['description'],
						'review_date' => $post_data['review-published-date'] . ' 00:00:00'
					);
					$format = array('%d', '%s', '%s', '%s');
					$where = array(
						'review_id' => $post_data['review_id']
					);
					$where_format = array('%d');

					if( tourmaster_update_review_data($data, $where, $format, $where_format) !== false ){
						$ret = array(
							'status' => 'success',
						);
					}else{
						$ret = array(
							'status' => 'failed',
							'message' => esc_html__('Cannot update review data, please refresh the page and try this again.', 'tourmaster')
						);
					}
				}else{
					$ret = array(
						'status' => 'failed',
						'message' => esc_html__('Please fill all required fields.', 'tourmaster')
					);
				}

			}else{
				$ret = array(
					'status' => 'failed',
					'message' => esc_html__('An error occurs, please refresh the page and try this again.', 'tourmaster')
				);
			}

			die(json_encode($ret));

		} // tourmaster_admin_edit_review
	}

	add_action('wp_ajax_tourmaster_get_edit_admin_review_item', 'tourmaster_get_edit_admin_review_item');
	if( !function_exists('tourmaster_get_edit_admin_review_item') ){
		function tourmaster_get_edit_admin_review_item(){

			$data = tourmaster_process_post_data($_POST);
			$ret = array();

			if( !empty($data['review_id']) ){	

				$result = tourmaster_get_review_data($data['review_id']);
				$value = array(
					'review-id' => $result->review_id,
					'description' => $result->review_description,
					'rating' => $result->review_score,
					'traveller-type' => $result->review_type,
					'published-date' => $result->review_date,
				);

				$ret = array(
					'status' => 'success',
					'content' => tourmaster_admin_lightbox_content(array(
						'title' => esc_html__('Edit Review', 'tourmaster'),
						'content' => '<div class="tourmaster-html-option-admin-review" >' . 
							tourmaster_get_review_form(null, 'edit', $value) . 
							'</div>'
					))
				);
			}else{
				$ret = array(
					'status' => 'failed',
					'message' => esc_html__('An error occurs, please refresh the page and try this again.', 'tourmaster')
				);
			}

			die(json_encode($ret));

		} // tourmaster_get_edit_admin_review_item
	}

	add_action('wp_ajax_tourmaster_remove_admin_review_item', 'tourmaster_remove_admin_review_item');
	if( !function_exists('tourmaster_remove_admin_review_item') ){
		function tourmaster_remove_admin_review_item(){
			
			$data = tourmaster_process_post_data($_POST);

			if( !empty($data['review_id']) ){
				tourmaster_remove_review_data($data['review_id']);
			}

			die(0);
		} // tourmaster_remove_admin_review_item
	}

	add_action('wp_ajax_tourmaster_get_admin_review_item', 'tourmaster_get_admin_review_item');
	if( !function_exists('tourmaster_get_admin_review_item') ){
		function tourmaster_get_admin_review_item(){

			$data = tourmaster_process_post_data($_POST);
			$ret = array();

			if( empty($data['tour_id']) || empty($data['paged']) ){
				
				$ret = array(
					'status' => 'failed',
					'mesage' => esc_html__('An error occurs, please refresh the page to try again.', 'tourmaster')
				);

			}else{

				$review_num_fetch = apply_filters('tourmaster_review_num_fetch', 5);
				$review_args = array(
					'review_tour_id' => $data['tour_id'], 
					'review_score' => 'IS NOT NULL'
				);
				$results = tourmaster_get_booking_data($review_args, array(
					'only-review' => true,
					'num-fetch' => $review_num_fetch,
					'paged' => $data['paged'],
					'orderby' => 'review_date',
					'order' => 'desc'
				));
				$max_num_page = intval(tourmaster_get_booking_data($review_args, array('only-review' => true), 'COUNT(*)')) / $review_num_fetch;

				if( !empty($results) ){
					$ret = array(
						'status' => 'success', 
						'content' => tourmaster_get_review_content_list($results, true) .
							tourmaster_get_review_content_pagination($max_num_page, $data['paged'])
					);
				}else{
					$ret = array(
						'status' => 'failed',
						'mesage' => esc_html__('No result found, please refresh the page to try again.', 'tourmaster')
					);
				}
			}

			die(json_encode($ret));
		} // tourmaster_get_admin_review_item
	}

	add_action('wp_ajax_tourmaster_admin_add_review', 'tourmaster_admin_add_review');
	if( !function_exists('tourmaster_admin_add_review') ){
		function tourmaster_admin_add_review(){
			
			$data = tourmaster_process_post_data($_POST);

			if( !empty($data['review-name']) && !empty($data['review-email']) && 
				!empty($data['review-published-date']) &&
				!empty($data['description']) && !empty($data['traveller-type']) && !empty($data['rating']) && !empty($data['tour_id']) ){

				if( is_email($data['review-email']) ){
					tourmaster_insert_review_data(array(
						'name' => $data['review-name'],
						'email' => $data['review-email'],

						'tour_id' => $data['tour_id'],
						'score' => $data['rating'],
						'type' => $data['traveller-type'],
						'description' => $data['description'],
						'date' => $data['review-published-date'] . ' 00:00:00'
					));

					tourmaster_update_review_score($data['tour_id']);

					$ret = json_encode(array(
						'status' => 'success',
						'message' => esc_html__('A review is successfully added.', 'tourmaster')
					));
				}else{
					$ret = json_encode(array(
						'status' => 'failed',
						'message' => esc_html__('Invalid Email, please try again.', 'tourmaster')
					));
				}
			}else{
				$ret = json_encode(array(
					'status' => 'failed',
					'message' => esc_html__('Please fill all required fields.', 'tourmaster')
				));
			}

			die($ret);

		} // tourmaster_admin_add_review
	}