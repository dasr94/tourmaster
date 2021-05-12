<?php
	/*	
	*	Goodlayers Widget Utility
	*/

	if( !class_exists('tourmaster_widget_util') ){
		class tourmaster_widget_util{

			// get option as html
			static function get_option($options){

				if( !empty($options) ){
					foreach($options as $option_slug => $option){

						echo '<p>';
						if( !empty($option['title']) ){
							echo '<label for="' . esc_attr($option['id']) . '" >' . $option['title'] . '</label>';
						}

						switch( $option['type'] ){

							case 'text': 
								echo '<input type="text" class="widefat" id="' . esc_attr($option['id']) . '" name="' . esc_attr($option['name']) . '" ';
								echo 'value="' . (isset($option['value'])? esc_attr($option['value']): '') . '" />';
								break;

							case 'combobox':
								if( $option['options'] == 'thumbnail-size' ){
									$option['options'] = tourmaster_get_thumbnail_list();
								}

								if( empty($option['value']) && !empty($option['default']) ){
									$option['value'] = $option['default'];
								}

								echo '<select class="widefat" id="' . esc_attr($option['id']) . '" name="' . esc_attr($option['name']) . '" >'; 
								foreach( $option['options'] as $key => $value ){
									echo '<option value="' . esc_attr($key) . '" ' . ((isset($option['value']) && $key == $option['value'])? 'selected': '') . ' >' . esc_html($value) . '</option>';
								}
								echo '</select>';
								break; 

							case 'multi-combobox':
								if( empty($option['value']) && !empty($option['default']) ){
									$option['value'] = $option['default'];
								}

								$values = empty($option['value'])? array(): explode(',', $option['value']);
								echo '<select multiple class="widefat" ';
								echo 'onChange="this.nextSibling.value = jQuery(this).val().join(',');" ';
								echo ' >'; 
								foreach( $option['options'] as $key => $value ){
									echo '<option value="' . esc_attr($key) . '" ' . (in_array($key, $values)? 'selected': '') . ' >' . esc_html($value) . '</option>';
								}
								echo '</select>';
								
								echo '<input type="hidden" id="' . esc_attr($option['id']) . '" name="' . esc_attr($option['name']) . '" value="' . esc_attr($option['value']) . '" />';
								break; 

							default: break; 

						} // switch
						echo '</p>';

					} // $option['type']
				} // $options

			}

			// option update
			static function get_option_update($instances){

				if( !empty($instances) ){
					foreach($instances as $key => $value){
						$instances[$key] = isset($value)? strip_tags($value): '';
					}
				}

				return $instances;
			}

		} // class
	} // class_exists