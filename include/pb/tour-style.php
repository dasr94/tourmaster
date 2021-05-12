<?php
	/*	
	*	Goodlayers Blog Item Style
	*/

	if( !class_exists('tourmaster_tour_style') ){
		class tourmaster_tour_style{

			// get the content of the tour item
			function get_content( $args ){

				$ret = apply_filters('tourmaster_tour_style_content', '', $args, $this);
				if( !empty($ret) ) return $ret;

				switch( $args['tour-style'] ){
					case 'modern':
					case 'modern-no-space': 
						return $this->tour_modern( $args ); 
						break;
					case 'grid':
					case 'grid-no-space': 
						return $this->tour_grid( $args ); 
						break;	
					case 'medium': 
						return $this->tour_medium( $args ); 
						break;
					case 'full': 
						return $this->tour_full( $args ); 
						break;
					case 'widget': 
						return $this->tour_widget( $args ); 
						break;
				}
				
			}

			// get blog excerpt
			function get_excerpt( $excerpt_length, $excerpt_more = ' [&hellip;]' ) {

				$post = get_post();
				if( empty($post) || post_password_required() ){ return ''; }
			
				$excerpt = $post->post_excerpt;
				if( empty($excerpt) ){
					$excerpt = get_the_content('');
					$excerpt = strip_shortcodes($excerpt);
					
					$excerpt = apply_filters('the_content', $excerpt);
					$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
				}
				
				$excerpt_more = apply_filters('excerpt_more', $excerpt_more);
				$excerpt = wp_trim_words($excerpt, $excerpt_length, $excerpt_more);

				$excerpt = apply_filters('wp_trim_excerpt', $excerpt, $post->post_excerpt);		
				$excerpt = apply_filters('get_the_excerpt', $excerpt);
				
				return $excerpt;
			}

			function tour_excerpt( $args ){

				$ret = '';

				if( $args['excerpt'] == 'specify-number' ){
					if( !empty($args['excerpt-number']) ){
						$ret = '<div class="tourmaster-tour-content" >' . $this->get_excerpt($args['excerpt-number']) . '</div>';
					}
				}else if( $args['excerpt'] != 'none' ){
					$ret = '<div class="tourmaster-tour-content" >' . tourmaster_content_filter(get_the_content(), true) . '</div>';
				}	

				return $ret;
			}			

			// get the portfolio title
			function tour_title( $args, $title_front = '', $title_back = '' ){

				$ret  = '<h3 class="tourmaster-tour-title gdlr-core-skin-title" ' . tourmaster_esc_style(array(
					'font-size' => empty($args['tour-title-font-size'])? '': $args['tour-title-font-size'],
					'font-weight' => empty($args['tour-title-font-weight'])? '': $args['tour-title-font-weight'],
					'letter-spacing' => empty($args['tour-title-letter-spacing'])? '': $args['tour-title-letter-spacing'],
					'text-transform' => empty($args['tour-title-text-transform'])? '': $args['tour-title-text-transform'],
					'margin-bottom' => empty($args['tour-title-bottom-margin'])? '': $args['tour-title-bottom-margin']
				)) . ' >';
				$ret .= '<a href="' . get_permalink() . '" >' . $title_front . get_the_title() . $title_back . '</a>';
				$ret .= '</h3>';


				return $ret;
			}

			// get tour thumbnail
			function get_thumbnail( $args, $has_content = true ){
				
				$ret = '';

				$feature_image = get_post_thumbnail_id();
				if( !empty($feature_image) ){
					$thumbnail_link_type = get_post_meta(get_the_ID(), 'tourmaster-thumbnail-link', true);

					$ret .= '<div class="tourmaster-tour-thumbnail tourmaster-media-image ';
					if( !empty($args['enable-thumbnail-zoom-on-hover']) && $args['enable-thumbnail-zoom-on-hover'] == 'enable' ){
						$ret .= 'tourmaster-zoom-on-hover';
					}
					$ret .= '" ';
					if( empty($args['with-frame']) || $args['with-frame'] == 'disable' ){
						if( !empty($args['tour-border-radius']) ){
							$css_atts['border-radius'] = $args['tour-border-radius'];
						}
						if( !empty($args['frame-shadow-size']['size']) && !empty($args['frame-shadow-color']) && !empty($args['frame-shadow-opacity']) ){
							$css_atts['background-shadow-size'] = $args['frame-shadow-size'];
							$css_atts['background-shadow-color'] = $args['frame-shadow-color'];
							$css_atts['background-shadow-opacity'] = $args['frame-shadow-opacity'];
						}
						if( !empty($css_atts) ){
							$ret .= tourmaster_esc_style($css_atts);
						}
					}
					
					$ret .= ' >';
					if( $thumbnail_link_type == 'lightbox-to-video' ){
						$video_url = get_post_meta(get_the_ID(), 'tourmaster-thumbnail-video-url', true);
						$ret .= '<a ' . tourmaster_get_lightbox_atts(array(
							'type' => 'video',
							'url' => $video_url
						)) . ' >';
						if( !empty($args['tour-style']) && in_array($args['tour-style'], array('full', 'medium', 'grid', 'grid-no-space')) ){
							$ret .= '<div class="tourmaster-tour-thumbnail-overlay" ><i class="fa fa-film" ></i></div>';
						}
						$ret .= tourmaster_get_image($feature_image, $args['thumbnail-size']);
						$ret .= '</a>';
					}else{
						// $ret .= '<a href="' . get_permalink() . '" >';
						$author_id = get_post(get_the_ID(), 'post_author');
						$guide_info = get_user_meta($author_id->post_author, 'guide-company', true);
						$guide_name = str_replace(' ', '-', strtolower($guide_info));

						// $ret .= '<a href="' . get_site_url() . '/wp-content/plugins/tourmaster/single/accion-redireccion.php?tid=' . get_the_ID() . ' - ' . $guide_name .'" >';
						$ret .= '<a href="' . get_site_url() . '/guides?guide=' . $guide_name .'" >';
						// $ret .= '<form action="' . get_site_url() . '/?tourmaster-payment" method="POST" id="tour-'.get_the_ID().'"> <input type="hidden" value="'.get_the_ID().'" name="tour-id"> <input type="submit"  name="btnsubmit" value="" id="tour-id-btn"> </form>';
						// $ret .= '<a href="javascript:redireccion('.get_the_ID().')" >';

						$ret .= tourmaster_get_image($feature_image, $args['thumbnail-size']);

						// $ret .= '</form>';
						$ret .= '</a>';
					}					
					$ret .= '</div>';

				}

				return $ret;
			}

			// get tour ribbon
			function get_tour_ribbon( $args = array() ){
				$ret = '';
				$post_meta = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');

				if( (empty($args['enable-ribbon']) || $args['enable-ribbon'] == 'enable') && !empty($post_meta['promo-text']) ){
					$ret  = '<div class="tourmaster-thumbnail-ribbon gdlr-core-outer-frame-element" ' . tourmaster_esc_style(array(
						'color' => empty($post_meta['promo-text-ribbon-text-color'])? '': $post_meta['promo-text-ribbon-text-color'],
						'background-color' => empty($post_meta['promo-text-ribbon-background'])? '': $post_meta['promo-text-ribbon-background'],
					)) .' >';
					$ret .= '<div class="tourmaster-thumbnail-ribbon-cornor" ' . tourmaster_esc_style(array(
						'border-right-color' => empty($post_meta['promo-text-ribbon-background'])? '': array($post_meta['promo-text-ribbon-background'], 0.5),
					)) .' ></div>';
					$ret .= $post_meta['promo-text	'];
					$ret .= '</div>';
				}

				return $ret;
			}

			// get tour ribbon
			function get_tour_ribbon_price( $args = array() ){
				$ret = '';
				$post_meta = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');

			
				$ret  = '<div class="tourmaster-thumbnail-ribbon-custom gdlr-core-outer-frame-element" ' . tourmaster_esc_style(array(
					'color' => empty($post_meta['promo-text-ribbon-text-color'])? '': $post_meta['promo-text-ribbon-text-color'],
					'background-color' => empty($post_meta['promo-text-ribbon-background'])? '': $post_meta['promo-text-ribbon-background'],
				)) .' >';
				$ret .= tourmaster_money_format($post_meta['tour-price-text'], $decimal_digit);
				$ret .= '</div>';
			

				return $ret;
			}

			function get_tour_ribbon_time( $tour_info ){
				$ret = '';
				$post_meta = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');

			
				$ret  = '<div class="tourmaster-thumbnail-ribbon-custom-2 gdlr-core-outer-frame-element" ' . tourmaster_esc_style(array(
					'color' => empty($post_meta['promo-text-ribbon-text-color'])? '': $post_meta['promo-text-ribbon-text-color'],
					'background-color' => empty($post_meta['promo-text-ribbon-background'])? '': $post_meta['promo-text-ribbon-background'],
				)) .' >';
	
				$ret .= $tour_info;
				$ret .= '</div>';
			

				return $ret;
			}

			// tour rating
			function get_rating( $style = 'widget' ){

				$rating = get_post_meta(get_the_ID(), 'tourmaster-tour-rating', true);
				if( empty($rating) ){ return ''; }
				
				
				if( !empty($rating['reviewer']) ){
					$ret  = '<div class="tourmaster-tour-rating" >';
					$score = intval($rating['score']) / intval($rating['reviewer']);

					if( $style == 'plain' ){
						$ret .= '<span class="tourmaster-tour-rating-text" >';
						$ret .= $rating['reviewer'] . ' ';
						$ret .= (intval($rating['reviewer']) > 1)? esc_html__('Reviews', 'tourmaster'): esc_html__('Review', 'tourmaster');
						$ret .= '</span>';
					}

					$ret .= tourmaster_get_rating($score);

					if( $style == 'widget' ){
						$ret .= '<span class="tourmaster-tour-rating-text" >(';
						$ret .= $rating['reviewer'] . ' ';
						$ret .= (intval($rating['reviewer']) > 1)? esc_html__('Reviews', 'tourmaster'): esc_html__('Review', 'tourmaster');
						$ret .= ')</span>';
					}
					$ret .= '</div>';
				}else{
					$ret  = '<div class="tourmaster-tour-rating tourmaster-tour-rating-empty" >0</div>';
				}

				return $ret;

			}

			// tour price
			function get_price( $settings = array() ){

				$ret = '';
				$post_meta = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');
				$extra_class = empty($post_meta['tour-price-discount-text'])? '': 'tourmaster-discount';
				$decimal_digit = tourmaster_get_option('general', 'header-price-decimal-digit', 0);
				if( !empty($post_meta['tour-price-text']) || !empty($post_meta['tour-price-discount-text']) ){
					$ret  .= '<div class="tourmaster-tour-price-wrap ' . esc_attr($extra_class) . '" >';
					if( !empty($post_meta['tour-price-text']) ){
						$ret .= '<span class="tourmaster-tour-price" >';
						$ret .= '<span class="tourmaster-head">';
						$ret .= empty($settings['price-prefix-text'])? esc_html__('From', 'tourmaster'): $settings['price-prefix-text'];
						$ret .= '</span>';
						$ret .= '<span class="tourmaster-tail">' . tourmaster_money_format($post_meta['tour-price-text'], $decimal_digit) . '</span>';
						$ret .= '</span>';
					}

					if( !empty($post_meta['tour-price-discount-text']) ){
						$ret .= '<span class="tourmaster-tour-discount-price" >';
						$ret .= tourmaster_money_format($post_meta['tour-price-discount-text'], $decimal_digit);
						$ret .= '</span>';
					}

					if( !empty($settings['with-info']) ){
						$ret .= '<span class="fa fa-info-circle tourmaster-tour-price-info" data-rel="tipsy" title="';
						$ret .= esc_html__('The initial price based on 1 adult with the lowest price in low season', 'tourmaster');
						$ret .= '" >';
						$ret .= '</span>';
					}
					$ret .= '</div>';
				}
				

				return $ret;

			}

			// tour info
			function get_info( $options = array(), $args = array() ){

				$ret = '';
				$post_meta = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');

				foreach( $options as $type ){
					switch( $type ){
						case 'custom-excerpt': 
							if( !empty($post_meta['custom-excerpt']) ){
								$ret .= '<div class="tourmaster-tour-info tourmaster-tour-info-custom-excerpt ' . (empty($args['info-class'])? '': esc_attr($args['info-class'])) . '" >';
								$ret .= tourmaster_content_filter($post_meta['custom-excerpt']);
								$ret .= ' </div>';
							} 
							break; 

						case 'duration-text': 
							if( !empty($post_meta['duration-text']) ){
								$ret .= '<div class="tourmaster-tour-info tourmaster-tour-info-duration-text ' . (empty($args['info-class'])? '': esc_attr($args['info-class'])) . '" >';
								if( !empty($args['icon']) && $args['icon'] == 'style-2' ){
									$ret .= tourmaster_get_svg('time-left');
								}else{
									$ret .= '<i class="icon_clock_alt" ></i>';
								}
								$ret .= tourmaster_text_filter($post_meta['duration-text']);
								$ret .= ' </div>';
							} 
							break;

						case 'availability': 
							if( !empty($post_meta['date-range']) ){
								$ret .= '<div class="tourmaster-tour-info tourmaster-tour-info-availability ' . (empty($args['info-class'])? '': esc_attr($args['info-class'])) . '" >';
								$ret .= '<i class="fa fa-calendar" ></i>';
								$ret .= esc_html__('Availability :', 'tourmaster') . ' ';
								$ret .= tourmaster_text_filter($post_meta['date-range']);
								$ret .= ' </div>';
							} 
							break;

						case 'departure-location': 
							if( !empty($post_meta['departure-location']) ){
								$ret .= '<div class="tourmaster-tour-info tourmaster-tour-info-departure-location ' . (empty($args['info-class'])? '': esc_attr($args['info-class'])) . '" >';
								$ret .= '<i class="flaticon-takeoff-the-plane" ></i>';
								$ret .= tourmaster_text_filter($post_meta['departure-location']);
								$ret .= ' </div>';
							} 
							break;

						case 'return-location':
							if( !empty($post_meta['return-location']) ){
								$ret .= '<div class="tourmaster-tour-info tourmaster-tour-info-return-location ' . (empty($args['info-class'])? '': esc_attr($args['info-class'])) . '" >';
								$ret .= '<i class="flaticon-plane-landing" ></i>';
								$ret .= tourmaster_text_filter($post_meta['return-location']);
								$ret .= ' </div>';
							} 
							break; 

						case 'minimum-age': 
							if( !empty($post_meta['minimum-age']) ){
								$ret .= '<div class="tourmaster-tour-info tourmaster-tour-info-minimum-age ' . (empty($args['info-class'])? '': esc_attr($args['info-class'])) . '" >';
								$ret .= '<i class="fa fa-user" ></i>';
								$ret .= esc_html__('Min Age :', 'tourmaster') . ' ';
								$ret .= tourmaster_text_filter($post_meta['minimum-age']);
								$ret .= ' </div>';
							} 
							break; 

						case 'maximum-people':
							$maximum_people = get_post_meta(get_the_ID(), 'tourmaster-max-people', true);
							if( !empty($maximum_people) ){
								$ret .= '<div class="tourmaster-tour-info tourmaster-tour-info-maximum-people ' . (empty($args['info-class'])? '': esc_attr($args['info-class'])) . '" >';
								$ret .= '<i class="fa fa-users" ></i>';
								$ret .= esc_html__('Max People :', 'tourmaster') . ' ';
								$ret .= tourmaster_text_filter($maximum_people);
								$ret .= ' </div>';
							} 
							break; 
					}
				}

				if( empty($args['no-wrapper']) ){
					$ret = '<div class="tourmaster-tour-info-wrap clearfix" >' . $ret . '</div>';
				}

				return $ret;
			}

			// tour widget
			function tour_widget( $args ){

				$ret  = '<div class="tourmaster-item-list tourmaster-tour-widget tourmaster-item-pdlr" >';
				$ret .= '<div class="tourmaster-tour-widget-inner clearfix" >';

				$args['thumbnail-size'] = 'thumbnail';
				$ret .= $this->get_thumbnail($args);

				$ret .= '<div class="tourmaster-tour-content-wrap" >';
				$ret .= $this->tour_title($args);

				$ribbon = $this->get_tour_ribbon($args);
				$ret .= '<div class="tourmaster-tour-content-info clearfix ' . (empty($ribbon)? '': 'tourmaster-with-ribbon') . '" >';
				$ret .= $ribbon;

				if( empty($settings['display-price']) || $settings['display-price'] == 'enable' ){
					$ret .= $this->get_price();
				}
				$ret .= '</div>'; // tourmaster-tour-content-info 
				$ret .= '</div>'; // tourmaster-tour-content-wrap 

				$ret .= '</div>'; // tourmaster-tour-widget-inner
				$ret .= '</div>'; // tourmaster-tour-widget
				
				return $ret;
			} 

			// tour full
			function tour_full( $args ){

				$extra_class = ( !empty($args['with-frame']) && $args['with-frame'] == 'enable' )? 'tourmaster-tour-frame': '';

				$ret  = '<div class="tourmaster-item-list tourmaster-tour-full tourmaster-item-mglr clearfix ' . esc_attr($extra_class) . '" >';
				$ret .= $this->get_tour_ribbon($args);

				$ret .= '<div class="tourmaster-tour-full-inner" ';
				if( $args['with-frame'] == 'enable' ){
					$css_atts = array();
					if( !empty($args['tour-border-radius']) ){
						$css_atts['border-radius'] = $args['tour-border-radius'];
					}
					if( !empty($args['frame-shadow-size']['size']) && !empty($args['frame-shadow-color']) && !empty($args['frame-shadow-opacity']) ){
						$css_atts['background-shadow-size'] = $args['frame-shadow-size'];
						$css_atts['background-shadow-color'] = $args['frame-shadow-color'];
						$css_atts['background-shadow-opacity'] = $args['frame-shadow-opacity'];
					}
					if( !empty($css_atts) ){
						$ret .= tourmaster_esc_style($css_atts);
					}
				}
				$ret .= '>';
				$ret .= $this->get_thumbnail($args);

				$ret .= '<div class="tourmaster-tour-content-wrap clearfix ' . (empty($extra_class)? '': 'gdlr-core-skin-e-background') . '" >';
				$ret .= '<div class="tourmaster-content-left" >';
				$ret .= $this->tour_title($args);

				// tour info
				if( !empty($args['tour-info']) ){
					$ret .= $this->get_info($args['tour-info']);
				}

				// excerpt
				$ret .= $this->tour_excerpt($args);
				$ret .= '</div>'; // tourmaster-content-left

				$ret .= '<div class="tourmaster-content-right tourmaster-center-tour-content" >';
				
				// price
				if( empty($settings['display-price']) || $settings['display-price'] == 'enable' ){
					$ret .= $this->get_price();
				}

				// rating
				if( !empty($args['tour-rating']) && $args['tour-rating'] == 'enable' ){
					$ret .= $this->get_rating();
				} 

				$ret .= '<a class="tourmaster-tour-view-more" href="' . get_permalink() . '" >' . esc_html__('View Details', 'tourmaster') . '</a>';
				$ret .= '</div>'; // tourmaster-tour-content-right
				$ret .= '</div>'; // tourmaster-tour-content-wrap 

				$ret .= '</div>'; // tourmaster-tour-full-inner
				$ret .= '</div>'; // tourmaster-tour-full
				
				return $ret;
			} 

			// tour medium
			function tour_medium( $args ){

				$extra_class = ( !empty($args['with-frame']) && $args['with-frame'] == 'enable' )? 'tourmaster-tour-frame': '';

				$ret  = '<div class="tourmaster-item-list tourmaster-tour-medium tourmaster-item-mglr clearfix ' . esc_attr($extra_class) . '" >';
				$ret .= $this->get_tour_ribbon($args);

				$ret .= '<div class="tourmaster-tour-medium-inner ' . (empty($extra_class)? '': 'gdlr-core-skin-e-background') . '" '; 
				if( $args['with-frame'] == 'enable' ){
					$css_atts = array();
					if( !empty($args['tour-border-radius']) ){
						$css_atts['border-radius'] = $args['tour-border-radius'];
					}
					if( !empty($args['frame-shadow-size']['size']) && !empty($args['frame-shadow-color']) && !empty($args['frame-shadow-opacity']) ){
						$css_atts['background-shadow-size'] = $args['frame-shadow-size'];
						$css_atts['background-shadow-color'] = $args['frame-shadow-color'];
						$css_atts['background-shadow-opacity'] = $args['frame-shadow-opacity'];
					}
					if( !empty($css_atts) ){
						$ret .= tourmaster_esc_style($css_atts);
					}
				}
				$ret .= ' >';

				$ret .= $this->get_thumbnail($args);

				$ret .= '<div class="tourmaster-tour-content-wrap clearfix" >';
				$ret .= '<div class="tourmaster-content-left" >';
				$ret .= $this->tour_title($args);

				// tour info
				if( !empty($args['tour-info']) ){
					$ret .= $this->get_info($args['tour-info']);
				}

				// excerpt
				$ret .= $this->tour_excerpt($args);
				$ret .= '</div>'; // tourmaster-content-left

				$ret .= '<div class="tourmaster-content-right tourmaster-center-tour-content" >';
				// price
				if( empty($settings['display-price']) || $settings['display-price'] == 'enable' ){
					$ret .= $this->get_price();
				}

				// rating
				if( !empty($args['tour-rating']) && $args['tour-rating'] == 'enable' ){
					$ret .= $this->get_rating();
				} 

				$ret .= '<a class="tourmaster-tour-view-more" href="' . get_permalink() . '" >' . esc_html__('View Details', 'tourmaster') . '</a>';
				$ret .= '</div>'; // tourmaster-tour-content-right
				$ret .= '</div>'; // tourmaster-tour-content-wrap 

				$ret .= '</div>'; // tourmaster-tour-medium-inner
				$ret .= '</div>'; // tourmaster-tour-medium
				
				return $ret;
			} 
			
			// tour modern
			function tour_modern( $args ){
				
				$css_atts = array();
				if( !empty($args['tour-border-radius']) ){
					$css_atts['border-radius'] = $args['tour-border-radius'];
					unset($args['tour-border-radius']);
				}
				if( !empty($args['frame-shadow-size']['size']) && !empty($args['frame-shadow-color']) && !empty($args['frame-shadow-opacity']) ){
					$css_atts['background-shadow-size'] = $args['frame-shadow-size'];
					$css_atts['background-shadow-color'] = $args['frame-shadow-color'];
					$css_atts['background-shadow-opacity'] = $args['frame-shadow-opacity'];
					unset($args['frame-shadow-size']);
					unset($args['frame-shadow-color']);
					unset($args['frame-shadow-opacity']);
				}

				$thumbnail = $this->get_thumbnail($args, false);
				$extra_class = empty($thumbnail)? 'tourmaster-without-thumbnail': 'tourmaster-with-thumbnail';

				// info
				$tour_info = '';
				if( !empty($args['tour-info']) ){
					$tour_info = $this->get_info($args['tour-info']);
				}
				if( !empty($tour_info) ){
					$extra_class .= ' tourmaster-with-info';
				}else{
					$extra_class .= ' tourmaster-without-info';
				}

				$ret  = '<div class="tourmaster-tour-modern ' . esc_attr($extra_class) . '" >';
				/*$ret .= $this->get_tour_ribbon($args);*/
				/**Custom*/
				$ret .= '<div class="tourmaster-tour-content-wrap-custom" >';
				$ret .= $this->tour_title($args);
				$ret .= '</div>';

				$ret .= $this->get_tour_ribbon_price($args);
				
				$ret .= $this->get_tour_ribbon_time($tour_info);
				
				$ret .= '<div class="tourmaster-tour-modern-inner" ' . tourmaster_esc_style($css_atts) . ' >';
				$ret .= $thumbnail;
				/*
				$ret .= '<div class="tourmaster-tour-content-wrap" >';
				$ret .= $this->tour_title($args);

				// price
				if( empty($settings['display-price']) || $settings['display-price'] == 'enable' ){
					$ret .= $this->get_price();
				}

				$ret .= $tour_info;*/
/*
				$ret .= '</div>'; */// tourmaster-tour-content
				$ret .= '</div>'; // tourmaster-tour-modern-inner
				$ret .= '</div>'; // tourmaster-tour-modern
				
				return $ret;
			} 
			
			// tour grid
			function tour_grid( $args ){
				
				$grid_style = empty($args['grid-style'])? 'style-1': $args['grid-style'];
				$extra_class  = ( !empty($args['with-frame']) && $args['with-frame'] == 'enable' )? ' tourmaster-tour-frame': '';
				$extra_class .= ' tourmaster-tour-grid-' . $grid_style;

				$args['price-position'] = empty($args['price-position'])? 'right-title': $args['price-position'];
				if( $args['price-position'] == 'bottom-title-center' ){
					$extra_class .= ' tourmaster-center-align';
					$args['price-position'] = 'bottom-title';
				}
				if( empty($args['display-price']) || $args['display-price'] == 'enable' ){
					$extra_class .= ' tourmaster-price-' . $args['price-position'];
				}
				
				$ret  = '<div class="tourmaster-tour-grid ' . esc_attr($extra_class) . '" >';
				if( $grid_style == 'style-1' ){
					$ret .= $this->get_tour_ribbon($args);
				}
				$ret .= '<div class="tourmaster-tour-grid-inner" ';
				if( $args['with-frame'] == 'enable' ){
					$css_atts = array();
					if( !empty($args['tour-border-radius']) ){
						$css_atts['border-radius'] = $args['tour-border-radius'];
					}
					if( !empty($args['frame-shadow-size']['size']) && !empty($args['frame-shadow-color']) && !empty($args['frame-shadow-opacity']) ){
						$css_atts['background-shadow-size'] = $args['frame-shadow-size'];
						$css_atts['background-shadow-color'] = $args['frame-shadow-color'];
						$css_atts['background-shadow-opacity'] = $args['frame-shadow-opacity'];
					}
					if( !empty($css_atts) ){
						$ret .= tourmaster_esc_style($css_atts);
					}
				}
				$ret .= ' >';

				$ret .= $this->get_thumbnail($args);

				$title_front = '';
				$title_back = '';
				$ret .= '<div class="tourmaster-tour-content-wrap ';
				if( $args['with-frame'] == 'enable' ){
					$ret .= 'gdlr-core-skin-e-background ';
					
					if( !empty($args['layout']) && $args['layout'] != 'masonry' ){
						global $tourmaster_tour_item_id;
						$ret .= 'gdlr-core-js" data-sync-height="tour-item-' . esc_attr($tourmaster_tour_item_id) . '" >';
					}else{
						$ret .= '" >';
					}
				}else{
					$ret .= '" >';
				}
				
				if( $grid_style == 'style-2' ){
					$ribbon = $this->get_tour_ribbon($args);
					$ret .= $ribbon;
					if( !empty($ribbon) ){
						$post_meta = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');
						$ribbon_background = empty($post_meta['promo-text-ribbon-background'])? '': $post_meta['promo-text-ribbon-background'];

						$title_front = tourmaster_get_svg('thunder', $ribbon_background) . '<span>';
						$title_back = '</span>';
					}
				}
				$ret .= $this->tour_title($args, $title_front, $title_back);

				// price
				if( empty($args['display-price']) || $args['display-price'] == 'enable' ){
					if( $args['price-position'] != 'bottom-bar' ){
						$ret .= $this->get_price(array(
							'price-prefix-text' => empty($args['price-prefix-text'])? '': $args['price-prefix-text']
						));
					}
				}

				// info
				if( !empty($args['tour-info']) ){
					$ret .= $this->get_info($args['tour-info'], array(
						'icon' => $grid_style
					));
				}

				// excerpt
				$ret .= $this->tour_excerpt($args);

				// rating
				if( !empty($args['tour-rating']) && $args['tour-rating'] == 'enable' ){
					$ret .= $this->get_rating();
				} 
				$ret .= '</div>'; // tourmaster-tour-content-wrap

				// price
				if( empty($args['display-price']) || $args['display-price'] == 'enable' ){
					if( $args['price-position'] == 'bottom-bar' ){
						$post_meta = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-tour-option');

						if( !empty($post_meta['tour-price-text']) ){
							$ret .= '<div class="tourmaster-tour-price-bottom-wrap clearfix ' . (empty($post_meta['tour-price-discount-text'])? '': 'tourmaster-with-discount') . '" >';
							$ret .= '<span class="tourmaster-tour-price-head" >';
							$ret .= empty($args['price-prefix-text'])? esc_html__('From', 'tourmaster'): $args['price-prefix-text'];
							$ret .= '</span>';
							$ret .= '<span class="tourmaster-tour-price-content" >';
							$ret .= '<span class="tourmaster-tour-price">' . tourmaster_money_format($post_meta['tour-price-text'], 0) . '</span>';
							if( !empty($post_meta['tour-price-discount-text']) ){
								$ret .= '<span class="tourmaster-tour-discount-price" >';
								$ret .= tourmaster_money_format($post_meta['tour-price-discount-text'], 0);
								$ret .= '</span>';
							}
							$ret .= '</span>'; // tourmaster-tour-price-content
							$ret .= '</div>'; // tourmaster-tour-price-bottom-wrap
						}
					}
				}

				$ret .= '</div>'; // tourmaster-tour-grid
				$ret .= '</div>'; // tourmaster-tour-grid
				
				return $ret;
			} 				
			
		} // tourmaster_tour_style
	} // class_exists
	
	function tourmaster_get_svg( $type = '', $background = '' ){

		ob_start();
		switch( $type ){
			case 'grid':
?><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
  <circle id="Ellipse_955_copy_2" data-name="Ellipse 955 copy 2" class="cls-1" cx="2" cy="2" r="2"/>
  <circle id="Ellipse_955_copy_3" data-name="Ellipse 955 copy 3" class="cls-1" cx="9" cy="2" r="2"/>
  <circle id="Ellipse_955_copy_4" data-name="Ellipse 955 copy 4" class="cls-1" cx="16" cy="2" r="2"/>
  <circle id="Ellipse_955_copy_5" data-name="Ellipse 955 copy 5" class="cls-1" cx="2" cy="9" r="2"/>
  <circle id="Ellipse_955_copy_5-2" data-name="Ellipse 955 copy 5" class="cls-1" cx="9" cy="9" r="2"/>
  <circle id="Ellipse_955_copy_5-3" data-name="Ellipse 955 copy 5" class="cls-1" cx="16" cy="9" r="2"/>
  <circle id="Ellipse_955_copy_6" data-name="Ellipse 955 copy 6" class="cls-1" cx="2" cy="16" r="2"/>
  <circle id="Ellipse_955_copy_6-2" data-name="Ellipse 955 copy 6" class="cls-1" cx="9" cy="16" r="2"/>
  <circle id="Ellipse_955_copy_6-3" data-name="Ellipse 955 copy 6" class="cls-1" cx="16" cy="16" r="2"/>
</svg><?php
				break;
			case 'list':
?><svg xmlns="http://www.w3.org/2000/svg" width="25" height="20" viewBox="0 0 25 20">
  <circle class="cls-1" cx="2" cy="2" r="2"/>
  <circle id="Ellipse_955_copy_2" data-name="Ellipse 955 copy 2" class="cls-1" cx="2" cy="10" r="2"/>
  <circle id="Ellipse_955_copy_3" data-name="Ellipse 955 copy 3" class="cls-1" cx="2" cy="18" r="2"/>
  <rect class="cls-1" x="6" width="19" height="4" rx="2" ry="2"/>
  <rect id="Rectangle_959_copy" data-name="Rectangle 959 copy" class="cls-1" x="6" y="8" width="19" height="4" rx="2" ry="2"/>
  <rect id="Rectangle_959_copy_2" data-name="Rectangle 959 copy 2" class="cls-1" x="6" y="16" width="19" height="4" rx="2" ry="2"/>
</svg><?php
				break;

			case 'thunder': 
?><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve" <?php 
	if( !empty($background) ){
		echo ' style="fill: ' . $background . '" ';
	}
?> >
	<path d="M397.413,199.303c-2.944-4.576-8-7.296-13.408-7.296h-112v-176c0-7.552-5.28-14.08-12.672-15.648
			c-7.52-1.6-14.88,2.272-17.952,9.152l-128,288c-2.208,4.928-1.728,10.688,1.216,15.2c2.944,4.544,8,7.296,13.408,7.296h112v176
			c0,7.552,5.28,14.08,12.672,15.648c1.12,0.224,2.24,0.352,3.328,0.352c6.208,0,12-3.616,14.624-9.504l128-288
			C400.805,209.543,400.389,203.847,397.413,199.303z"/>
</svg><?php
				break;

			case 'time-left': 
?><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 465 465" xml:space="preserve" <?php 
	if( !empty($background) ){
		echo ' style="fill: ' . $background . '" ';
	}
?> >

			<path d="M279.591,423.714c-3.836,0.956-7.747,1.805-11.629,2.52c-10.148,1.887-16.857,11.647-14.98,21.804
				c0.927,4.997,3.765,9.159,7.618,11.876c3.971,2.795,9.025,4.057,14.175,3.099c4.623-0.858,9.282-1.867,13.854-3.008
				c10.021-2.494,16.126-12.646,13.626-22.662C299.761,427.318,289.618,421.218,279.591,423.714z"/>
			<path d="M417.887,173.047c1.31,3.948,3.811,7.171,6.97,9.398c4.684,3.299,10.813,4.409,16.662,2.475
				c9.806-3.256,15.119-13.83,11.875-23.631c-1.478-4.468-3.118-8.95-4.865-13.314c-3.836-9.59-14.714-14.259-24.309-10.423
				c-9.585,3.834-14.256,14.715-10.417,24.308C415.271,165.528,416.646,169.293,417.887,173.047z"/>
			<path d="M340.36,397.013c-3.299,2.178-6.704,4.286-10.134,6.261c-8.949,5.162-12.014,16.601-6.854,25.546
				c1.401,2.433,3.267,4.422,5.416,5.942c5.769,4.059,13.604,4.667,20.127,0.909c4.078-2.352,8.133-4.854,12.062-7.452
				c8.614-5.691,10.985-17.294,5.291-25.912C360.575,393.686,348.977,391.318,340.36,397.013z"/>
			<path d="M465.022,225.279c-0.407-10.322-9.101-18.356-19.426-17.953c-10.312,0.407-18.352,9.104-17.947,19.422
				c0.155,3.945,0.195,7.949,0.104,11.89c-0.145,6.473,3.021,12.243,7.941,15.711c2.931,2.064,6.488,3.313,10.345,3.401
				c10.322,0.229,18.876-7.958,19.105-18.285C465.247,234.756,465.208,229.985,465.022,225.279z"/>
			<path d="M414.835,347.816c-8.277-6.21-19.987-4.524-26.186,3.738c-2.374,3.164-4.874,6.289-7.434,9.298
				c-6.69,7.86-5.745,19.666,2.115,26.361c0.448,0.38,0.901,0.729,1.371,1.057c7.814,5.509,18.674,4.243,24.992-3.171
				c3.057-3.59,6.037-7.323,8.874-11.102C424.767,365.735,423.089,354.017,414.835,347.816z"/>
			<path d="M442.325,280.213c-9.855-3.09-20.35,2.396-23.438,12.251c-1.182,3.765-2.492,7.548-3.906,11.253
				c-3.105,8.156-0.13,17.13,6.69,21.939c1.251,0.879,2.629,1.624,4.126,2.19c9.649,3.682,20.454-1.159,24.132-10.812
				c1.679-4.405,3.237-8.906,4.646-13.382C457.66,293.795,452.178,283.303,442.325,280.213z"/>
			<path d="M197.999,426.402c-16.72-3.002-32.759-8.114-47.968-15.244c-0.18-0.094-0.341-0.201-0.53-0.287
				c-3.584-1.687-7.162-3.494-10.63-5.382c-0.012-0.014-0.034-0.023-0.053-0.031c-6.363-3.504-12.573-7.381-18.606-11.628
				C32.24,331.86,11.088,209.872,73.062,121.901c13.476-19.122,29.784-35.075,47.965-47.719c0.224-0.156,0.448-0.311,0.67-0.468
				c64.067-44.144,151.06-47.119,219.089-1.757l-14.611,21.111c-4.062,5.876-1.563,10.158,5.548,9.518l63.467-5.682
				c7.12-0.64,11.378-6.799,9.463-13.675L387.61,21.823c-1.908-6.884-6.793-7.708-10.859-1.833l-14.645,21.161
				C312.182,7.638,252.303-5.141,192.87,5.165c-5.986,1.036-11.888,2.304-17.709,3.78c-0.045,0.008-0.081,0.013-0.117,0.021
				c-0.225,0.055-0.453,0.128-0.672,0.189C123.122,22.316,78.407,52.207,46.5,94.855c-0.269,0.319-0.546,0.631-0.8,0.978
				c-1.061,1.429-2.114,2.891-3.145,4.353c-1.686,2.396-3.348,4.852-4.938,7.308c-0.199,0.296-0.351,0.597-0.525,0.896
				C10.762,149.191-1.938,196.361,0.24,244.383c0.005,0.158-0.004,0.317,0,0.479c0.211,4.691,0.583,9.447,1.088,14.129
				c0.027,0.302,0.094,0.588,0.145,0.89c0.522,4.708,1.177,9.427,1.998,14.145c8.344,48.138,31.052,91.455,65.079,125.16
				c0.079,0.079,0.161,0.165,0.241,0.247c0.028,0.031,0.059,0.047,0.086,0.076c9.142,9.017,19.086,17.357,29.793,24.898
				c28.02,19.744,59.221,32.795,92.729,38.808c10.167,1.827,19.879-4.941,21.703-15.103
				C214.925,437.943,208.163,428.223,197.999,426.402z"/>
			<path d="M221.124,83.198c-8.363,0-15.137,6.78-15.137,15.131v150.747l137.87,71.271c2.219,1.149,4.595,1.69,6.933,1.69
				c5.476,0,10.765-2.982,13.454-8.185c3.835-7.426,0.933-16.549-6.493-20.384l-121.507-62.818V98.329
				C236.243,89.978,229.477,83.198,221.124,83.198z"/>
</svg><?php
				break;
		}
		$ret = ob_get_contents();
		ob_end_clean();

		return $ret;
	}	