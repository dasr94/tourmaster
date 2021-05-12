<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_add_pb_element_tour_category');
	if( !function_exists('tourmaster_add_pb_element_tour_category') ){
		function tourmaster_add_pb_element_tour_category(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('tour_category', 'tourmaster_pb_element_tour_category'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_tour_category') ){
		class tourmaster_pb_element_tour_category{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-plane',
					'title' => esc_html__('Tour Category', 'tourmaster')
				);
			}

			// list all custom taxonomy
			static function get_tax_option_list(){
				
				$ret = array();

				$tax_fields = array( 'tour_tag' => esc_html__('Tag', 'tourmaster') );
				$tax_fields = $tax_fields + tourmaster_get_custom_tax_list();
				foreach( $tax_fields as $tax_field => $tax_title ){
					$ret[$tax_field] = array(
						'title' => $tax_title,
						'type' => 'multi-combobox',
						'options' => tourmaster_get_term_list($tax_field),
						'condition' => array( 'filter-type' => $tax_field ),
						'description' => esc_html__('You can use Ctrl/Command button to select multiple items or remove the selected item. Leave this field blank to select all items in the list.', 'tourmaster'),
					);
				}

				return $ret;
			}

			// return the element options
			static function get_options(){
				return apply_filters('tourmaster_tour_item_options', array(					
					'general' => array(
						'title' => esc_html__('General', 'tourmaster'),
						'options' => array(
							'filter-type' => array(
								'title' => esc_html__('Filter Type', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'tour_category' => esc_html__('Tour Category', 'tourmaster'),
									'tour_tag' => esc_html__('Tour Tag', 'tourmaster'),
								) + tourmaster_get_custom_tax_list()
							),
							'category' => array(
								'title' => esc_html__('Category', 'tourmaster'),
								'type' => 'multi-combobox',
								'options' => tourmaster_get_term_list('tour_category'),
								'condition' => array( 'filter-type' => 'tour_category' ),
								'description' => esc_html__('You can use Ctrl/Command button to select multiple items or remove the selected item. Leave this field blank to select all items in the list.', 'tourmaster'),
							),
						) + self::get_tax_option_list() + array(
							'num-fetch' => array(
								'title' => esc_html__('Num Fetch', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'number',
								'default' => 9,
								'description' => esc_html__('The number of posts showing on the blog item', 'tourmaster')
							),
							'orderby' => array(
								'title' => esc_html__('Order By', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'name' => esc_html__('Name', 'tourmaster'), 
									'slug' => esc_html__('Slug', 'tourmaster'), 
									'term_id' => esc_html__('Term ID', 'tourmaster'), 
								)
							),
							'order' => array(
								'title' => esc_html__('Order', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'desc'=>esc_html__('Descending Order', 'tourmaster'), 
									'asc'=> esc_html__('Ascending Order', 'tourmaster'), 
								)
							),
						),
					),
					'settings' => array(
						'title' => esc_html('Style', 'tourmaster'),
						'options' => array(

							'style' => array(
								'title' => esc_html__('Tour Category Style', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'grid' => esc_html__('Grid Style', 'tourmaster'),
									'grid-2' => esc_html__('Grid 2 Style', 'tourmaster'),
									'grid-3' => esc_html__('Grid 3 Style', 'tourmaster'),
									'widget' => esc_html__('Widget Style', 'tourmaster'),
								),
								'default' => 20,
							),
							'column-size' => array(
								'title' => esc_html__('Column Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => array( 60=>1, 30=>2, 20=>3, 15=>4, 12=>5 ),
								'default' => 20,
							),
							'thumbnail-size' => array(
								'title' => esc_html__('Thumbnail Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size'
							),
							'excerpt' => array(
								'title' => esc_html__('Excerpt Type', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'specify-number' => esc_html__('Specify Number', 'tourmaster'),
									'none' => esc_html__('Disable Exceprt', 'tourmaster'),
								),
								'condition' => array( 'style' => 'grid-3' ),
								'default' => 'specify-number',
							),
							'excerpt-number' => array(
								'title' => esc_html__('Excerpt Number', 'tourmaster'),
								'type' => 'text',
								'default' => 55,
								'condition' => array( 'style' => 'grid-3', 'excerpt' => 'specify-number' )
							),

							'with-feature' => array(
								'title' => esc_html__('With Feature', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'disable',
								'condition' => array( 'style' => 'grid-3' )
							),
							'feature-thumbnail-size' => array(
								'title' => esc_html__('Feature Thumbnail Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size',
								'condition' => array( 'style' => 'grid-3', 'with-feature' => 'enable' )
							),
							'feature-excerpt' => array(
								'title' => esc_html__('Feature Excerpt Type', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'specify-number' => esc_html__('Specify Number', 'tourmaster'),
									'none' => esc_html__('Disable Exceprt', 'tourmaster'),
								),
								'condition' => array( 'style' => 'grid-3' ),
								'default' => 'specify-number',
							),
							'feature-excerpt-number' => array(
								'title' => esc_html__('Feature Excerpt Number', 'tourmaster'),
								'type' => 'text',
								'default' => 55,
								'condition' => array( 'style' => 'grid-3', 'excerpt' => 'specify-number' )
							),

						),
					),
					'spacing' => array(
						'title' => esc_html('Spacing', 'tourmaster'),
						'options' => array(
							'border-radius' => array(
								'title' => esc_html__('Frame/Thumbnail Border Radius', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'padding-bottom' => array(
								'title' => esc_html__('Padding Bottom ( Item )', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'default' => '30px'
							),
						)
					),
					'item-title' => array(
						'title' => esc_html('Item Title', 'tourmaster'),
						'options' => array(
							'title-align' => array(
								'title' => esc_html__('Title Align', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'left' => esc_html__('Left', 'tourmaster'),
									'center' => esc_html__('Center', 'tourmaster'),
								),
								'default' => 'left',
							),
							'title' => array(
								'title' => esc_html__('Title', 'tourmaster'),
								'type' => 'text',
							),
							'caption' => array(
								'title' => esc_html__('Caption', 'tourmaster'),
								'type' => 'textarea',
							),
							'read-more-text' => array(
								'title' => esc_html__('Read More Text', 'tourmaster'),
								'type' => 'text',
								'default' => esc_html__('Read More', 'tourmaster'),
								'condition' => array( 'title-align' => 'left' )
							),
							'read-more-link' => array(
								'title' => esc_html__('Read More Link', 'tourmaster'),
								'type' => 'text',
								'condition' => array( 'title-align' => 'left' )
							),
							'read-more-target' => array(
								'title' => esc_html__('Read More Target', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'_self' => esc_html__('Current Screen', 'tourmaster'),
									'_blank' => esc_html__('New Window', 'tourmaster'),
								),
								'condition' => array( 'title-align' => 'left' )
							),
							'title-size' => array(
								'title' => esc_html__('Title Size', 'tourmaster'),
								'type' => 'fontslider',
								'default' => '41px'
							),
							'caption-size' => array(
								'title' => esc_html__('Caption Size', 'tourmaster'),
								'type' => 'fontslider',
								'default' => '16px'
							),
							'read-more-size' => array(
								'title' => esc_html__('Read More Size', 'tourmaster'),
								'type' => 'fontslider',
								'default' => '14px',
								'condition' => array( 'title-align' => 'left' )
							),
							'title-color' => array(
								'title' => esc_html__('Title Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'caption-color' => array(
								'title' => esc_html__('Caption Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'read-more-color' => array(
								'title' => esc_html__('Read More Color', 'tourmaster'),
								'type' => 'colorpicker',
								'condition' => array( 'title-align' => 'left' )
							),
							'read-more-divider-color' => array(
								'title' => esc_html__('Read More Divider Color', 'tourmaster'),
								'type' => 'colorpicker',
								'condition' => array( 'title-align' => 'left' )
							),
						)
					)
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
				if( empty($settings) ){
					$settings = array( 'category' => '' );
				}

				$settings['thumbnail-size'] = empty($settings['thumbnail-size'])? 'full': $settings['thumbnail-size'];

				// start printing item
				$extra_class  = '';
				if( !empty($settings['style']) && $settings['style'] == 'widget' ){
					$extra_class .= 'tourmaster-item-pdlr';
				}
				$title_settings = $settings;

				$ret  = '<div class="tourmaster-tour-category clearfix ' . esc_attr($extra_class) . '" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';

				// print title
				if( function_exists('gdlr_core_block_item_title') ){
					$ret .= gdlr_core_block_item_title($settings);
				}
				
				// query
				$args = array(
					'orderby' => empty($settings['orderby'])? 'name': $settings['orderby'],
					'order' => empty($settings['order'])? 'asc': $settings['order'],
					'number' => empty($settings['num-fetch'])? 0: $settings['num-fetch'],
					'hide_empty' => false
				);
				if( empty($settings['filter-type']) || $settings['filter-type'] == 'tour_category' ){
					$args['taxonomy'] = 'tour_category';

					if( !empty($settings['category']) ){
						if( !is_array($settings['category']) ){
							$settings['category'] = array_map('trim', explode(',', $settings['category']));
						}
						$args['slug'] = $settings['category'];
					}
				}else{
					$args['taxonomy'] = $settings['filter-type'];

					if( !empty($settings[$settings['filter-type']]) ){
						if( !is_array($settings[$settings['filter-type']]) ){
							$settings[$settings['filter-type']] = array_map('trim', explode(',', $settings[$settings['filter-type']]));
						}
						$args['slug'] = $settings[$settings['filter-type']];
					}
				}

				$categories = get_terms($args);

				// print 
				if( !empty($categories) && !is_wp_error($categories) ){
					if( empty($settings['style']) || $settings['style'] == 'grid' ){

						$ret .= self::get_category_grid($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'grid-2' ){

						$ret .= self::get_category_grid2($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'grid-3' ){

						$ret .= self::get_category_grid3($categories, $settings, $args['taxonomy']);

					}else if( $settings['style'] == 'widget' ){

						$ret .= self::get_category_widget($categories, $settings, $args['taxonomy']);

					}		
				}

				$ret .= '</div>'; // tourmaster-tour-category-item
				
				return $ret;
			}			
			
			static function get_category_grid( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];

				$ret = '';

				foreach( $categories as $category ){
					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';
					if( !empty($column_size) ){
						$additional_class .= ' tourmaster-column-' . $column_size;
					}

					if( $column_sum == 0 || $column_sum + intval($column_size) > 60 ){
						$column_sum = intval($column_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($column_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" >';
					$ret .= '<i class="icon_pin_alt" ></i>';
					$ret .= $category->name;
					$ret .= '</h3>';
					$ret .= '<div class="tourmaster-tour-category-count" >';
					if( $category->count <= 1 ){
						$ret .= sprintf(esc_html__('%d tour', 'tourmaster'), $category->count);
					}else{
						$ret .= sprintf(esc_html__('%d tours', 'tourmaster'), $category->count);
					}
					$ret .= '</div>'; // tourmaster-tour-category-count
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					$term_link = get_term_link($category->term_id, $taxonomy);
					if( !is_wp_error($term_link) ){
						$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url($term_link) . '" >';
						$ret .= esc_html__('View all tours', 'tourmaster');
						$ret .= '</a>';
					}
					$ret .= '<div class="tourmaster-tour-category-head-divider" ></div>';
					$ret .= '</div>'; // tourmaster-tour-category-head-animate
					$ret .= '</div>'; // tourmaster-tour-category-head
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid
				}

				return $ret;
			}

			static function get_category_grid2( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];

				$ret = '';

				foreach( $categories as $category ){
					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';
					if( !empty($column_size) ){
						$additional_class .= ' tourmaster-column-' . $column_size;
					}

					if( $column_sum == 0 || $column_sum + intval($column_size) > 60 ){
						$column_sum = intval($column_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($column_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid-2 tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" >';
					$ret .= $category->name;
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
					$ret .= esc_html__('View all tours', 'tourmaster');
					$ret .= '</a>';
					$ret .= '</div>'; // tourmaster-tour-category-head-animate
					$ret .= '</div>'; // tourmaster-tour-category-head
					
					$ret .= '<div class="tourmaster-tour-category-head-divider" ></div>';
					
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid
				}

				return $ret;
			}

			static function get_category_grid3( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];
				
				if( !empty($settings['with-feature']) && $settings['with-feature'] == 'enable' ){
					if( $column_size != 60 ){
						$column_size_feature = ($column_size * 2);
					}
				}
				
				$ret = '';

				foreach( $categories as $category ){
					if( !empty($column_size_feature) ){
						$c_size = $column_size_feature;
						$thumbnail_size = empty($settings['feature-thumbnail-size'])? 'full': $settings['feature-thumbnail-size'];
						$excerpt = empty($settings['feature-excerpt'])? '': $settings['feature-excerpt'];
						$excerpt_number = empty($settings['feature-excerpt-number'])? '': $settings['feature-excerpt-number'];
						$column_size_feature = 0;
					}else{
						$c_size = $column_size;
						$thumbnail_size = $settings['thumbnail-size'];
						$excerpt = empty($settings['excerpt'])? '': $settings['excerpt'];
						$excerpt_number = empty($settings['excerpt-number'])? '': $settings['excerpt-number'];
					}

					$additional_class  = ' tourmaster-item-pdlr tourmaster-item-mgb';
					if( !empty($c_size) ){
						$additional_class .= ' tourmaster-column-' . $c_size;
					}

					if( $column_sum == 0 || $column_sum + intval($c_size) > 60 ){
						$column_sum = intval($c_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($c_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-grid-3 tourmaster-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= '<div class="tourmaster-tour-category-item-wrap" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image" >';
						$ret .= tourmaster_get_image($thumbnail, $thumbnail_size);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-count" >';
						if( $category->count <= 1 ){
							$ret .= sprintf(esc_html__('%d tour', 'tourmaster'), $category->count);
						}else{
							$ret .= sprintf(esc_html__('%d tours', 'tourmaster'), $category->count);
						}
						$ret .= '</div>'; // tourmaster-tour-category-count
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
						$ret .= '<div class="tourmaster-tour-category-overlay-front" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-display clearfix" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" >';
					$ret .= $category->name;
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-display

					$ret .= '<div class="tourmaster-tour-category-head-animate" >';
					if( $excerpt == 'specify-number' ){
						if( !empty($excerpt_number) ){
							$ret .= '<div class="tourmaster-tour-category-description" >';
							$ret .= wp_trim_words($category->description, $excerpt_number);
							$ret .= '</div>';
						}
					}

					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
					$ret .= esc_html__('View all tours', 'tourmaster');
					$ret .= '</a>';
					$ret .= '</div>'; // tourmaster-tour-category-head-animate
					$ret .= '</div>'; // tourmaster-tour-category-head
					
					$ret .= '</div>'; // tourmaster-tour-category-item-wrap
					$ret .= '</div>'; // tourmaster-tour-category-grid
				}

				return $ret;
			}

			static function get_category_widget( $categories, $settings, $taxonomy ){

				$column_sum = 0;
				$column_size = empty($settings['column-size'])? 20: $settings['column-size'];

				$ret  = '<div class="tourmaster-tour-category-widget-holder clearfix" >';
				foreach( $categories as $category ){
					$additional_class  = '';
					if( !empty($column_size) ){
						$additional_class .= ' tourmaster-column-' . $column_size;
					}

					if( $column_sum == 0 || $column_sum + intval($column_size) > 60 ){
						$column_sum = intval($column_size);
						$additional_class .= ' tourmaster-column-first';
					}else{
						$column_sum += intval($column_size);
					}

					$thumbnail = get_term_meta($category->term_id, 'thumbnail', true);
					if( !empty($thumbnail) ){
						$additional_class .= ' tourmaster-with-thumbnail';
					}
					
					$ret .= '<div class="tourmaster-tour-category-widget tourmaster-item-list ' . esc_attr($additional_class) . '" ' . tourmaster_esc_style(array(
						'border-radius' => empty($settings['border-radius'])? '': $settings['border-radius']
					)) . ' >';
					if( !empty($thumbnail) ){
						$ret .= '<div class="tourmaster-tour-category-thumbnail tourmaster-media-image"  >';
						$ret .= tourmaster_get_image($thumbnail, $settings['thumbnail-size']);
						$ret .= '</div>';
						$ret .= '<div class="tourmaster-tour-category-overlay" ></div>';
					}
					
					$ret .= '<div class="tourmaster-tour-category-head" >';
					$ret .= '<div class="tourmaster-tour-category-head-table" >';
					$ret .= '<h3 class="tourmaster-tour-category-title" >';
					$ret .= '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
					$ret .= $category->name;
					$ret .= '</a>';
					$ret .= '</h3>';
					$ret .= '</div>'; // tourmaster-tour-category-head-table
					$ret .= '</div>'; // tourmaster-tour-category-head
					$ret .= '</div>'; // tourmaster-tour-category-widget
				}
				$ret .= '</div>'; // tourmaster-tour-category-widget-holder

				return $ret;
			}

		} // tourmaster_pb_element_tour
	} // class_exists	

	add_shortcode('tourmaster_tour_category', 'tourmaster_tour_category_shortcode');
	if( !function_exists('tourmaster_tour_category_shortcode') ){
		function tourmaster_tour_category_shortcode($atts){
			$atts = wp_parse_args($atts, array());
			$atts['column-size'] = empty($atts['column-size'])? 60: 60 / intval($atts['column-size']); 
			
			$ret  = '<div class="tourmaster-tour-category-shortcode clearfix tourmaster-item-rvpdlr" >';
			$ret .= tourmaster_pb_element_tour_category::get_content($atts);
			$ret .= '</div>';

			return $ret;
		}
	}