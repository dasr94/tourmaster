<?php
	/*	
	*	Goodlayers Tour Item
	*/
	
	if( !class_exists('tourmaster_tour_item') ){
		class tourmaster_tour_item{
			
			var $settings = '';
			
			// init the variable
			function __construct( $settings = array() ){
				
				$this->settings = wp_parse_args($settings, array(
					'category' => '', 
					'tag' => '', 
					'num-fetch' => '9', 
					'layout' => 'fitrows',
					'thumbnail-size' => 'full', 
					'orderby' => 'date', 
					'order' => 'desc',
					'tour-style' => 'full', 
					'hover' => 'title-icon', 
					'hover-info' => array('title','icon'), 
					'has-column' => 'no',
					'no-space' => 'no',
					'excerpt' => 'specify-number', 
					'excerpt-number' => 55, 
					'column-size' => 60,
					'filterer' => 'none',
					'filterer-align' => 'center',
					'pagination' => 'none',
					'custom-pagination' => false
				));
				 
			}
			
			// get the content of the tour item
			function get_content(){
				
				if( function_exists('gdlr_core_set_container_multiplier') && !empty($this->settings['column-size']) ){
					gdlr_core_set_container_multiplier(intval($this->settings['column-size']) / 60, false);
				}

				$ret = '';
				if( !empty($this->settings['query']) ){
					$query = $this->settings['query'];
				}else{
					$query = $this->get_tour_query();
				}

				// carousel style
				if( $this->settings['layout'] == 'carousel' ){
					$slides = array();
					$column_no = 60 / intval($this->settings['column-size']);

					$flex_atts = array(
						'carousel' => true,
						'column' => $column_no,
						'navigation' => empty($this->settings['carousel-navigation'])? 'navigation': $this->settings['carousel-navigation'],
						'navigation-align' => empty($this->settings['carousel-navigation-align'])? '': $this->settings['carousel-navigation-align'],
						'navigation-size' => empty($this->settings['carousel-navigation-size'])? '': $this->settings['carousel-navigation-size'],
						'navigation-icon-color' => empty($this->settings['carousel-navigation-icon-color'])? '': $this->settings['carousel-navigation-icon-color'],
						'navigation-icon-background' => empty($this->settings['carousel-navigation-icon-bg'])? '': $this->settings['carousel-navigation-icon-bg'],
						'navigation-icon-padding' => empty($this->settings['carousel-navigation-icon-padding'])? '': $this->settings['carousel-navigation-icon-padding'],
						'navigation-icon-radius' => empty($this->settings['carousel-navigation-icon-radius'])? '': $this->settings['carousel-navigation-icon-radius'],
						'navigation-margin' => empty($this->settings['carousel-navigation-margin'])? '': $this->settings['carousel-navigation-margin'],
						'navigation-icon-margin' => empty($this->settings['carousel-navigation-icon-margin'])? '': $this->settings['carousel-navigation-icon-margin'],
						'navigation-left-icon' => empty($this->settings['carousel-navigation-left-icon'])? '': $this->settings['carousel-navigation-left-icon'],
						'navigation-right-icon' => empty($this->settings['carousel-navigation-right-icon'])? '': $this->settings['carousel-navigation-right-icon'],
						'nav-parent' => 'tourmaster-tour-item',
						'disable-autoslide' => (empty($this->settings['carousel-autoslide']) || $this->settings['carousel-autoslide'] == 'enable')? '': true,
						'mglr' => (($this->settings['no-space'] == 'yes')? false: true),
					);

					if( in_array($flex_atts['navigation'], array('navigation', 'both')) && empty($this->settings['title']) && empty($this->settings['caption']) ){
						$flex_atts['vcenter-nav'] = true;
						$flex_atts['additional-class'] = 'tourmaster-nav-style-rect';
					}else if( $flex_atts['navigation'] == 'navigation-outer' && empty($flex_atts['navigation-left-icon']) && empty($flex_atts['navigation-right-icon']) ){
						$flex_atts['navigation-old'] = true;
					}

					$tour_style = new tourmaster_tour_style();

					tourmaster_setup_admin_postdata();
					while($query->have_posts()){ $query->the_post();
						$slides[] = $tour_style->get_content( $this->settings );
					} // while
					wp_reset_postdata();
					tourmaster_reset_admin_postdata();
					
					$ret .= tourmaster_get_flexslider($slides, $flex_atts);

				// fitrows style
				}else{
					
					// filterer
					if( $this->settings['filterer'] != 'none' ){
						$extra_class  = ($this->settings['no-space'] == 'yes')? '': ' tourmaster-item-pdlr';
						$extra_class .= empty($this->settings['filterer'])? '': ' gdlr-core-style-' . $this->settings['filterer'];
						$extra_class .= empty($this->settings['filterer-align'])? '': ' gdlr-core-' . $this->settings['filterer-align'] . '-align';
						
						$ajax_settings =  $this->settings;
						unset($ajax_settings['query']);
						$ret .= tourmaster_get_ajax_filterer('tour', 'tour_category', $ajax_settings, 'tourmaster-tour-item-holder', $extra_class);
					}

					// order filterer
					if( !empty($this->settings['enable-order-filterer']) && $this->settings['enable-order-filterer'] == 'enable' ){
						$ret .= $this->get_tour_order_filterer();
					}

					// tour item
					tourmaster_setup_admin_postdata();
					$ret .= '<div class="tourmaster-tour-item-holder gdlr-core-js-2 clearfix" data-layout="' . $this->settings['layout'] . '" >';
					$ret .= $this->get_tour_grid_content($query);
					// $ret .= $query;
					$ret .= '</div>';
					wp_reset_postdata();
					tourmaster_reset_admin_postdata();

					// pagination
					if( $this->settings['pagination'] != 'none' ){
						$extra_class = ($this->settings['no-space'] == 'yes')? '': 'tourmaster-item-pdlr';

						if( $this->settings['pagination'] == 'page' ){
							if( (!empty($this->settings['enable-order-filterer']) && $this->settings['enable-order-filterer'] == 'enable') ||
								(!empty($this->settings['filterer']) && $this->settings['filterer'] != 'none') ){
								
								$ajax_settings =  $this->settings;
								unset($ajax_settings['query']);
								$ret .= tourmaster_get_ajax_pagination('tour', $ajax_settings, $query->max_num_pages, 'tourmaster-tour-item-holder', $extra_class);
							}else{
								$ret .= tourmaster_get_pagination($query->max_num_pages, $this->settings, $extra_class);
							}
						}else if( $this->settings['pagination'] ){
							$paged = empty($query->query['paged'])? 2: intval($query->query['paged']) + 1;
							
							$ajax_settings =  $this->settings;
							unset($ajax_settings['query']);
							$ret .= tourmaster_get_ajax_load_more('tour', $ajax_settings, $paged, $query->max_num_pages, 'tourmaster-tour-item-holder', $extra_class);
						}
					}
				}

				if( function_exists('gdlr_core_set_container_multiplier') ){
					gdlr_core_set_container_multiplier(1, false);
				}

				return $ret;
			}

			// get content of non carousel tour item
			function get_tour_grid_content( $query ){

				$ret = '';
				$column_sum = 0;
				$tour_style = new tourmaster_tour_style();
				while($query->have_posts()){ $query->the_post();

					$args = $this->settings;

					if( $this->settings['has-column'] != 'no' ){
						$additional_class  = ($this->settings['no-space'] == 'yes')? '': ' tourmaster-item-pdlr';
						$additional_class .= in_array($this->settings['tour-style'], array('modern', 'modern-desc', 'metro'))? ' tourmaster-item-mgb': '';
						if( !empty($this->settings['column-size']) ){
							$additional_class .= ' tourmaster-column-' . $this->settings['column-size'];
						}

						if( $column_sum == 0 || $column_sum + intval($this->settings['column-size']) > 60 ){
							$column_sum = intval($this->settings['column-size']);
							$additional_class .= ' tourmaster-column-first';
						}else{
							$column_sum += intval($this->settings['column-size']);
						}

						$ret .= '<div id="tour-'.get_the_ID().'" class="gdlr-core-item-list ' . esc_attr($additional_class) . '" >';
					} 

					$ret .= $tour_style->get_content( $args );
					
					if( $this->settings['has-column'] != 'no' ){
						$ret .= '</div>';
					}
				} // while
				
				return $ret;
			}
			
			// query the post
			function get_tour_query(){
				
				$args = array( 'post_type' => 'tour', 'post_status' => 'publish', 'suppress_filters' => false );

				// apply search variable
				if( !empty($this->settings['s']) ){
					$args['s'] = $this->settings['s'];
				} 

				if( !empty($this->settings['meta_query']) ){
					$args['meta_query'] = $this->settings['meta_query'];
				}else{
					$args['meta_query'] = array();

					// discounted tour
					if( !empty($this->settings['discount-status']) && $this->settings['discount-status'] == 'discount' ){
						$args['meta_query'][] = array(
							'key' => 'tourmaster-tour-discount',
							'value' => 'true'
						);
					}

					// hide unavailable tour
					if( !empty($this->settings['hide-not-avail']) && $this->settings['hide-not-avail'] == 'enable' ){
						$args['meta_query'][] = array(
							'key' => 'tourmaster-tour-date-avail',
							'compare' => 'EXISTS'
						);
					}
				}
				
				// category - tag selection
				if( !empty($this->settings['tax_query']) ){
					$args['tax_query'] = $this->settings['tax_query'];
				}else{
					$args['tax_query'] = array('relation' => 'OR');
					
					if( !empty($this->settings['category']) ){
						if( !is_array($this->settings['category']) ){
							$this->settings['category'] = array_map('trim', explode(',', $this->settings['category']));
						}
						array_push($args['tax_query'], array('terms'=>$this->settings['category'], 'taxonomy'=>'tour_category', 'field'=>'slug'));
					}
					if( !empty($this->settings['tag']) ){
						if( !is_array($this->settings['tag']) ){
							$this->settings['tag'] = array_map('trim', explode(',', $this->settings['tag']));
						}
						array_push($args['tax_query'], array('terms'=>$this->settings['tag'], 'taxonomy'=>'tour_tag', 'field'=>'slug'));
					}

					$tax_fields = tourmaster_get_custom_tax_list();
					foreach( $tax_fields as $tax_field => $tax_title ){
						if( !empty($this->settings[$tax_field]) ){
							if( !is_array($this->settings[$tax_field]) ){
								$this->settings[$tax_field] = array_map('trim', explode(',', $this->settings[$tax_field]));
							}
							$args['tax_query'][] = array(
								array('terms'=>$this->settings[$tax_field], 'taxonomy'=>$tax_field, 'field'=>'slug')
							);
						}
					}
				}
				
				// pagination
				if( empty($this->settings['paged']) ){
					if( empty($this->settings['pagination']) || $this->settings['pagination'] != 'none' ){
						$args['paged'] = (get_query_var('paged'))? get_query_var('paged') : get_query_var('page');
					}
					$args['paged'] = empty($args['paged'])? 1: $args['paged'];
				}else{
					$args['paged'] = $this->settings['paged'];
				}
				$this->settings['paged'] = $args['paged'];
				
				// variable
				$args['posts_per_page'] = empty($this->settings['num-fetch'])? 9: $this->settings['num-fetch'];
				$args['order'] = empty($this->settings['order'])? 'desc': $this->settings['order'];

				if( empty($this->settings['orderby']) ){
					$args['orderby'] = 'date';
				}else if( in_array($this->settings['orderby'], array('date', 'title')) ){
					$args['orderby'] = $this->settings['orderby'];
				}else if( $this->settings['orderby'] == 'tour-date' ){
					$args['meta_key'] = 'tourmaster-tour-date-avail';
					$args['meta_type'] = 'CHAR';
					$args['orderby'] = 'tourmaster-tour-date-avail';
				}else if( $this->settings['orderby'] == 'price' ){
					$args['meta_key'] = 'tourmaster-tour-price';
					$args['meta_type'] = 'DECIMAL';
					$args['orderby'] = 'meta_value_num';
				}else if( $this->settings['orderby'] == 'duration' ){
					$args['meta_key'] = 'tourmaster-tour-duration';
					$args['meta_type'] = 'NUMERIC';
					$args['orderby'] = 'meta_value_num';
				}else if( $this->settings['orderby'] == 'popularity' ){
					$args['meta_key'] = 'tourmaster-view-count';
					$args['meta_type'] = 'NUMERIC';
					$args['orderby'] = 'meta_value_num';
				}else if( $this->settings['orderby'] == 'rating' ){
					$args['meta_key'] = 'tourmaster-tour-rating-score';
					$args['meta_type'] = 'NUMERIC';
					$args['orderby'] = 'meta_value_num';
				}else{
					$args['orderby'] = $this->settings['orderby'];
				}

				return new WP_Query( $args );
			}

			// for getting the tour order filterer
			function get_tour_order_filterer(){

				$ajax_settings =  $this->settings;
				unset($ajax_settings['query']);

				$ret  = '<div class="tourmaster-tour-order-filterer-wrap tourmaster-item-mglr clearfix" ';
				$ret .= 'data-tm-ajax="tourmaster_tour_order_ajax" ';
				$ret .= 'data-settings="' . esc_attr(json_encode($ajax_settings)) . '" ';
				$ret .= 'data-target="tourmaster-tour-item-holder" ';
				$ret .= 'data-target-action="replace" ';
				$ret .= 'data-ajax-url="' . esc_attr(TOURMASTER_AJAX_URL) . '" ';
				$ret .= ' >';

				$ret .= '<h3 class="tourmaster-tour-order-filterer-title" >' . esc_html__('Sort by', 'tourmaster') . '</h3>';

				// orderby
				$order_options = array(
					'date' => esc_html__('Release Date', 'tourmaster'),
					'tour-date' => esc_html__('Tour Date', 'tourmaster'),
					'title' => esc_html__('Title', 'tourmaster'),
					'price' => esc_html__('Price', 'tourmaster'),
					'popularity' => esc_html__('Popularity', 'tourmaster'),
					'rating' => esc_html__('Rating', 'tourmaster'),
					'duration' => esc_html__('Duration', 'tourmaster'),
				);
				$ret .= '<div class="tourmaster-combobox-wrap" >';
				$ret .= '<select data-ajax-name="orderby" >';
				foreach( $order_options as $order_slug => $order_title ){
					$ret .= '<option value="' . esc_attr($order_slug) . '" ';
					$ret .= $this->settings['orderby'] == $order_slug? 'selected': '';
					$ret .= ' >' . $order_title . '</option>';
				}
				$ret .= '</select>';
				$ret .= '</div>';

				// order
				$ret .= '<div class="tourmaster-combobox-wrap" >';
				$ret .= '<select data-ajax-name="order" >';
				$ret .= '<option value="asc" ' . ($this->settings['order'] == 'asc'? 'selected': '') . ' >' . esc_html__('Ascending', 'tourmaster') . '</option>';
				$ret .= '<option value="desc" ' . ($this->settings['order'] == 'desc'? 'selected': '') . ' >' . esc_html__('Descending', 'tourmaster') . '</option>';
				$ret .= '</select>';
				$ret .= '</div>';

				// list style
				$ret .= '<span class="tourmaster-tour-order-filterer-style" >';
				if( !empty($this->settings['order-filterer-list-style']) && $this->settings['order-filterer-list-style'] != 'none' ){
					$temp = str_replace('-with-frame', '', $this->settings['order-filterer-list-style']);
					$ret .= '<a href="#" data-ajax-name="item-style" ';
					$ret .= ' class="' . ($temp == $this->settings['tour-style']? 'tourmaster-active': '') . '" ';
					$ret .= ' data-ajax-value="list-style" >';
					if( !empty($this->settings['filter-icon']) && $this->settings['filter-icon'] == 'svg' ){
						$ret .= tourmaster_get_svg('list');
					}else{
						$ret .= '<i class="fa fa-th-list" ></i>';
					}
					$ret .= '</a>';
				}
				if( !empty($this->settings['order-filterer-grid-style']) && $this->settings['order-filterer-grid-style'] != 'none' ){
					$temp = str_replace('-with-frame', '', $this->settings['order-filterer-grid-style']);
					$ret .= '<a href="#" data-ajax-name="item-style" ';
					$ret .= ' class="' . ($temp == $this->settings['tour-style']? 'tourmaster-active': '') . '" ';
					$ret .= ' data-ajax-value="grid-style" >';
					if( !empty($this->settings['filter-icon']) && $this->settings['filter-icon'] == 'svg' ){
						$ret .= tourmaster_get_svg('grid');
					}else{
						$ret .= '<i class="fa fa-th" ></i>';
					}
					$ret .= '</a>';
				}
				$ret .= '</span>';

				$ret .= '</div>';

				return $ret;
			}
			
		} // tourmaster_tour_item
	} // class_exists
	
	add_action('wp_ajax_gdlr_core_tour_order_ajax', 'tourmaster_tour_order_ajax');
	add_action('wp_ajax_nopriv_gdlr_core_tour_order_ajax', 'tourmaster_tour_order_ajax');

	add_action('wp_ajax_tourmaster_tour_order_ajax', 'tourmaster_tour_order_ajax');
	add_action('wp_ajax_nopriv_tourmaster_tour_order_ajax', 'tourmaster_tour_order_ajax');

	if( !function_exists('tourmaster_tour_order_ajax') ){
		function tourmaster_tour_order_ajax(){

			if( !empty($_POST['settings']) ){

				$settings = $_POST['settings'];
				if( !empty($_POST['option']['name']) && !empty($_POST['option']['value']) ){	
					if( $_POST['option']['name'] == 'orderby' ){ 
						$settings['orderby'] = $_POST['option']['value'];
					}else if( $_POST['option']['name'] == 'order' ){
						$settings['order'] = $_POST['option']['value'];
					}else if( $_POST['option']['name'] == 'item-style' ){

						if( $_POST['option']['value'] == 'list-style' ){
							$settings['tour-style'] = $settings['order-filterer-list-style'];
							$settings['thumbnail-size'] = empty($settings['order-filterer-list-style-thumbnail'])? 'full': $settings['order-filterer-list-style-thumbnail'];
						}else if( $_POST['option']['value'] == 'grid-style' ){
							$settings['tour-style'] = $settings['order-filterer-grid-style'];
							$settings['thumbnail-size'] = empty($settings['order-filterer-grid-style-thumbnail'])? 'full': $settings['order-filterer-grid-style-thumbnail'];
						}

						if( strpos($settings['tour-style'], 'with-frame') !== false ){
							$settings['with-frame'] = 'enable';
							$settings['tour-style'] = str_replace('-with-frame', '', $settings['tour-style']);
						}else{
							$settings['with-frame'] = 'disable';
						}

						$settings['no-space'] = (strpos($settings['tour-style'], 'no-space') !== false)? 'yes': 'no';
						$settings['layout'] = empty($settings['layout'])? 'fitrows': $settings['layout'];
						if( in_array($settings['tour-style'], array('modern', 'modern-no-space', 'grid', 'grid-no-space')) ){
							$settings['has-column'] = 'yes';
							$settings['column-size'] = $settings['column-size-temp'];
						}else{
							$settings['has-column'] = 'no';
							$settings['column-size'] = 60;
							$settings['layout'] = 'fitrows';
						}
					}

					$settings['paged'] = 1;
				}

				$tour_item = new tourmaster_tour_item($settings);
				$query = $tour_item->get_tour_query();

				$ret = array(
					'status'=> 'success',
					'content'=> $tour_item->get_tour_grid_content($query),
					'settings'=> $settings
				);
				if( !empty($settings['pagination']) && $settings['pagination'] != 'none' ){
					$extra_class = ($settings['no-space'] == 'yes')? '': 'tourmaster-item-pdlr';

					if( $settings['pagination'] == 'load-more' ){
						$paged = empty($query->query['paged'])? 2: intval($query->query['paged']) + 1;
						$ret['load_more'] = tourmaster_get_ajax_load_more('tour', $settings, $paged, $query->max_num_pages, 'tourmaster-tour-item-holder', $extra_class);
						$ret['load_more'] = empty($ret['load_more'])? 'none': $ret['load_more'];
					}else{
						$ret['pagination'] = tourmaster_get_ajax_pagination('tour', $settings, $query->max_num_pages, 'tourmaster-tour-item-holder', $extra_class);
						$ret['pagination'] = empty($ret['pagination'])? 'none': $ret['pagination'];
					}

				} 

				die(json_encode($ret));
			}else{
				die(json_encode(array(
					'status'=> 'failed',
					'message'=> esc_html__('Settings variable is not defined.', 'tourmaster')
				)));
			}

		} // tourmaster_tour_ajax
	} // function_exists
	
	add_action('wp_ajax_gdlr_core_tour_ajax', 'tourmaster_tour_ajax');
	add_action('wp_ajax_nopriv_gdlr_core_tour_ajax', 'tourmaster_tour_ajax');

	add_action('wp_ajax_tourmaster_tour_ajax', 'tourmaster_tour_ajax');
	add_action('wp_ajax_nopriv_tourmaster_tour_ajax', 'tourmaster_tour_ajax');
	
	if( !function_exists('tourmaster_tour_ajax') ){
		function tourmaster_tour_ajax(){

			if( !empty($_POST['settings']) ){

				$settings = $_POST['settings'];
				if( !empty($_POST['option']['name']) && !empty($_POST['option']['value']) ){	
					if( in_array($_POST['option']['name'], array('paged', 'category')) ){ 
						$settings[$_POST['option']['name']] = $_POST['option']['value'];

						if( $_POST['option']['name'] == 'category' ){
							$settings['paged'] = 1;
						}
					}
				}else{
					$settings['paged'] = 1;
				}

				$tour_item = new tourmaster_tour_item($settings);
				$query = $tour_item->get_tour_query();

				$ret = array(
					'status'=> 'success',
					'content'=> $tour_item->get_tour_grid_content($query)
				);
				if( !empty($settings['pagination']) && $settings['pagination'] != 'none' ){

					$extra_class = ($settings['no-space'] == 'yes')? '': 'tourmaster-item-pdlr';

					// always change the load more button
					if( $settings['pagination'] == 'load-more' ){
						$paged = empty($query->query['paged'])? 2: intval($query->query['paged']) + 1;
						$ret['load_more'] = tourmaster_get_ajax_load_more('tour', $settings, $paged, $query->max_num_pages, 'tourmaster-tour-item-holder', $extra_class);
						$ret['load_more'] = empty($ret['load_more'])? 'none': $ret['load_more'];

					// change pagination on category filter
					}else{
						$ret['pagination'] = tourmaster_get_ajax_pagination('tour', $settings, $query->max_num_pages, 'tourmaster-tour-item-holder', $extra_class);
						$ret['pagination'] = empty($ret['pagination'])? 'none': $ret['pagination'];
					}
				} 

				die(json_encode($ret));
			}else{
				die(json_encode(array(
					'status'=> 'failed',
					'message'=> esc_html__('Settings variable is not defined.', 'tourmaster')
				)));
			}

		} // tourmaster_tour_ajax
	} // function_exists
	
	
	
	
	
	
	