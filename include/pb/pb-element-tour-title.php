<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_add_pb_element_tour_title');
	if( !function_exists('tourmaster_add_pb_element_tour_title') ){
		function tourmaster_add_pb_element_tour_title(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('tour_title', 'tourmaster_pb_element_tour_title'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_tour_title') ){
		class tourmaster_pb_element_tour_title{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-star',
					'title' => esc_html__('Tour Title', 'tourmaster')
				);
			}
			
			// return the element options
			static function get_options(){
				return apply_filters('tourmaster_tour_item_options', array(		
					'general' => array(
						'title' => esc_html__('General', 'tourmaster'),
						'options' => array(
							'show-review' => array(
								'title' => esc_html__('Show Review', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'enable',
								'description' => esc_html__('Only Available For Tour Post Type', 'tourmaster')
							),
						)
					),			
					'typography' => array(
						'title' => esc_html('Typography', 'tourmaster'),
						'options' => array(
							'title-font-size' => array(
								'title' => esc_html__('Title Font Size', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'title-font-weight' => array(
								'title' => esc_html__('Title Font Weight', 'tourmaster'),
								'type' => 'text',
								'description' => esc_html__('Eg. lighter, bold, normal, 300, 400, 600, 700, 800', 'tourmaster')
							),
							'title-letter-spacing' => array(
								'title' => esc_html__('Title Letter Spacing', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'title-text-transform' => array(
								'title' => esc_html__('Title Text Transform', 'tourmaster'),
								'type' => 'combobox',
								'data-type' => 'text',
								'options' => array(
									'uppercase' => esc_html__('Uppercase', 'tourmaster'),
									'lowercase' => esc_html__('Lowercase', 'tourmaster'),
									'capitalize' => esc_html__('Capitalize', 'tourmaster'),
									'none' => esc_html__('None', 'tourmaster'),
								),
								'default' => 'none'
							)
						)
					),
					'spacing' => array(
						'title' => esc_html('Spacing', 'tourmaster'),
						'options' => array(
							'padding-bottom' => array(
								'title' => esc_html__('Padding Bottom ( Item )', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'default' => '30px'
							)
						)
					),
				));
			}

			// get the preview for page builder
			static function get_preview( $settings = array() ){
				$content  = self::get_content($settings);
				return $content;
			}			

			// get the content from settings
			static function get_content( $settings = array() ){
				
				// default variable
				$settings = empty($settings)? array('num-display' => 3): $settings;
	
				$ret  = '<div class="tourmaster-tour-title-item tourmaster-item-pdlr tourmaster-item-pdb clearfix" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';
				
				if( !get_the_ID() ){
					$ret .= '<div class="gdlr-core-external-plugin-message">';
					$ret .= esc_html__('The title will be displayed when the page is refreshed.', 'tourmaster');
					$ret .= '</div>';
				}else{
					$ret .= '<h1 class="tourmaster-tour-title-item-title" ' . gdlr_core_esc_style(array(
						'font-size' => empty($settings['title-font-size'])? '': $settings['title-font-size'],
						'font-weight' => empty($settings['title-font-weight'])? '': $settings['title-font-weight'],
						'letter-spacing' => empty($settings['title-letter-spacing'])? '': $settings['title-letter-spacing'],
						'text-transform' => empty($settings['title-text-transform'])? '': $settings['title-text-transform'],
					)) . ' >' . get_the_title() . '</h1>';

					if( empty($settings['show-review']) || $settings['show-review'] == 'enable' ){
						$tour_style = new tourmaster_tour_style();
						$ret .= $tour_style->get_rating();
					}
				}
				
				$ret .= '</div>'; // tourmaster-tour-title-item
				
				return $ret;
			}		

		} // tourmaster_pb_element_tour_title
	} // class_exists