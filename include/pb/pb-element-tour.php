<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_add_pb_element_tour');
	if( !function_exists('tourmaster_add_pb_element_tour') ){
		function tourmaster_add_pb_element_tour(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('tour', 'tourmaster_pb_element_tour'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_tour') ){
		class tourmaster_pb_element_tour{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-plane',
					'title' => esc_html__('Tour', 'tourmaster')
				);
			}

			// list all custom taxonomy
			static function get_tax_option_list(){
				
				$ret = array();

				$tax_fields = array();
				$tax_fields = $tax_fields + tourmaster_get_custom_tax_list();
				foreach( $tax_fields as $tax_field => $tax_title ){
					$ret[$tax_field] = array(
						'title' => $tax_title,
						'type' => 'multi-combobox',
						'options' => tourmaster_get_term_list($tax_field),
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

							'category' => array(
								'title' => esc_html__('Category', 'tourmaster'),
								'type' => 'multi-combobox',
								'options' => tourmaster_get_term_list('tour_category'),
								'description' => esc_html__('You can use Ctrl/Command button to select multiple items or remove the selected item. Leave this field blank to select all items in the list.', 'tourmaster'),
							),
							'tag' => array(
								'title' => esc_html__('Tag', 'tourmaster'),
								'type' => 'multi-combobox',
								'options' => tourmaster_get_term_list('tour_tag')
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
									'date' => esc_html__('Publish Date', 'tourmaster'), 
									'tour-date' => esc_html__('Tour Date', 'tourmaster'), 
									'title' => esc_html__('Title', 'tourmaster'), 
									'rand' => esc_html__('Random', 'tourmaster'), 
									'menu_order' => esc_html__('Menu Order', 'tourmaster'), 
									'price' => esc_html__('Price', 'tourmaster'), 
									'duration' => esc_html__('Duration', 'tourmaster'), 
									'popularity' => esc_html__('Popularity ( View Count )', 'tourmaster'), 
									'rating' => esc_html__('Rating ( Score )', 'tourmaster'), 
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
							'discount-status' => array(
								'title' => esc_html__('Tour Status', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'all'=>esc_html__('All', 'tourmaster'), 
									'discount'=> esc_html__('Discounted Tour ( tour with discount text filled )', 'tourmaster'), 
								)
							),
							'hide-not-avail' => array(
								'title' => esc_html__('Hide Not Available Tour', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'disable'
							),
							'enable-order-filterer' => array(
								'title' => esc_html__('Order Filterer', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'disable',
								'description' => esc_html__('Filter is not supported and will be automatically disabled on carousel layout.', 'tourmaster'),
							),
							'order-filterer-list-style' => array(
								'title' => esc_html__('Order Filterer List Style', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'none' => esc_html__('None', 'tourmaster'),
									'full' => esc_html__('Full', 'tourmaster'),
									'full-with-frame' => esc_html__('Full With Frame', 'tourmaster'),
									'medium' => esc_html__('Medium', 'tourmaster'),
									'medium-with-frame' => esc_html__('Medium With Frame', 'tourmaster'),
									'widget' => esc_html__('Widget', 'tourmaster'),
								),
								'default' => 'none',
								'condition' => array( 'enable-order-filterer' => 'enable' )
							),
							'order-filterer-list-style-thumbnail' => array(
								'title' => esc_html__('Order Filterer List Style Thumbnail', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size',
								'condition' => array( 'enable-order-filterer' => 'enable' )
							),
							'order-filterer-grid-style' => array(
								'title' => esc_html__('Order Filterer Grid Style', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'none' => esc_html__('None', 'tourmaster'),
									'modern' => esc_html__('Modern', 'tourmaster'),
									'modern-no-space' => esc_html__('Modern No Space', 'tourmaster'),
									'grid' => esc_html__('Grid', 'tourmaster'),
									'grid-with-frame' => esc_html__('Grid With Frame', 'tourmaster'),
									'grid-no-space' => esc_html__('Grid No Space', 'tourmaster'),
								),
								'default' => 'none',
								'condition' => array( 'enable-order-filterer' => 'enable' )
							),
							'order-filterer-grid-style-thumbnail' => array(
								'title' => esc_html__('Order Filterer Grid Style Thumbnail', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size',
								'condition' => array( 'enable-order-filterer' => 'enable' )
							),
							'filterer' => array(
								'title' => esc_html__('Category Filterer', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'none'=>esc_html__('None', 'tourmaster'), 
									'text'=>esc_html__('Filter Text Style', 'tourmaster'), 
									'button'=>esc_html__('Filter Button Style', 'tourmaster'), 
								),
								'condition' => array( 'enable-order-filterer'=>'disable' ), 
								'description' => esc_html__('Filter is not supported and will be automatically disabled on carousel layout.', 'tourmaster'),
							),
							'filterer-align' => array(
								'title' => esc_html__('Filterer Alignment', 'tourmaster'),
								'type' => 'radioimage',
								'options' => 'text-align',
								'default' => 'center',
								'condition' => array('enable-order-filterer'=>'disable', 'filterer' => array('text', 'button'))
							),
							'pagination' => array(
								'title' => esc_html__('Pagination', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'none'=>esc_html__('None', 'tourmaster'), 
									'page'=>esc_html__('Page', 'tourmaster'), 
									'load-more'=>esc_html__('Load More', 'tourmaster'), 
								),
								'description' => esc_html__('Pagination is not supported and will be automatically disabled on carousel layout.', 'tourmaster'),
							),
							'pagination-style' => array(
								'title' => esc_html__('Pagination Style', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'default' => esc_html__('Default', 'tourmaster'),
									'plain' => esc_html__('Plain', 'tourmaster'),
									'rectangle' => esc_html__('Rectangle', 'tourmaster'),
									'rectangle-border' => esc_html__('Rectangle Border', 'tourmaster'),
									'round' => esc_html__('Round', 'tourmaster'),
									'round-border' => esc_html__('Round Border', 'tourmaster'),
									'circle' => esc_html__('Circle', 'tourmaster'),
									'circle-border' => esc_html__('Circle Border', 'tourmaster'),
								),
								'default' => 'default',
								'condition' => array( 'pagination' => 'page' )
							),
							'pagination-align' => array(
								'title' => esc_html__('Pagination Alignment', 'tourmaster'),
								'type' => 'radioimage',
								'options' => 'text-align',
								'with-default' => true,
								'default' => 'default',
								'condition' => array( 'pagination' => 'page' )
							),
							
						),
					),
					'settings' => array(
						'title' => esc_html('Tour Style', 'tourmaster'),
						'options' => array(
							'tour-style' => array(
								'title' => esc_html__('Tour Style', 'tourmaster'),
								'type' => 'radioimage',
								'options' => array(
									'full' => TOURMASTER_URL . '/images/tour-style/full.jpg',
									'full-with-frame' => TOURMASTER_URL . '/images/tour-style/full-with-frame.jpg',
									'medium' => TOURMASTER_URL . '/images/tour-style/medium.jpg',
									'medium-with-frame' => TOURMASTER_URL . '/images/tour-style/medium-with-frame.jpg',
									'modern' => TOURMASTER_URL . '/images/tour-style/modern.jpg',
									'modern-no-space' => TOURMASTER_URL . '/images/tour-style/modern-no-space.jpg',
									'grid' => TOURMASTER_URL . '/images/tour-style/grid.jpg',
									'grid-with-frame' => TOURMASTER_URL . '/images/tour-style/grid-with-frame.jpg',
									'grid-no-space' => TOURMASTER_URL . '/images/tour-style/grid-no-space.jpg',
									'widget' => TOURMASTER_URL . '/images/tour-style/widget.jpg',
								),
								'default' => 'full',
								'wrapper-class' => 'gdlr-core-fullsize'
							),
							'grid-style' => array(
								'title' => esc_html__('Grid Style', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'style-1' => esc_html__('Style 1', 'tourmaster'),
									'style-2' => esc_html__('Style 2', 'tourmaster'),
								),
								'condition' => array('tour-style' => array('grid', 'grid-with-frame', 'grid-no-space'))
							),
							'column-size' => array(
								'title' => esc_html__('Column Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => array( 60=>1, 30=>2, 20=>3, 15=>4, 12=>5 ),
								'default' => 20,
								'condition' => array( 'tour-style' => array('modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space') )
							),
							'thumbnail-size' => array(
								'title' => esc_html__('Thumbnail Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size',
								'condition' => array( 'tour-style' => array('modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space', 'full', 'full-with-frame', 'medium', 'medium-with-frame') )
							),
							'enable-thumbnail-zoom-on-hover' => array(
								'title' => esc_html__('Thumbnail Zoom on Hover', 'goodlayers-core'),
								'type' => 'checkbox',
								'default' => 'disable',
								'condition' => array( 'tour-style' => array('modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space', 'full', 'full-with-frame', 'medium', 'medium-with-frame') )
							),
							'layout' => array(
								'title' => esc_html__('Layout', 'tourmaster'),
								'type' => 'combobox',
								'options' => array( 
									'fitrows' => esc_html__('Fit Rows', 'tourmaster'),
									'carousel' => esc_html__('Carousel', 'tourmaster'),
									'masonry' => esc_html__('Masonry', 'tourmaster'),
								),
								'default' => 'fitrows',
								'condition' => array( 'tour-style' => array('modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space') )
							),
							'carousel-autoslide' => array(
								'title' => esc_html__('Autoslide Carousel', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'enable',
								'condition' => array( 'tour-style' => array('modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space'), 'layout' => 'carousel' )
							),
							'carousel-navigation' => array(
								'title' => esc_html__('Carousel Navigation', 'tourmaster'),
								'type' => 'combobox',
								'options' => (function_exists('gdlr_core_get_flexslider_navigation_types')? gdlr_core_get_flexslider_navigation_types(): array()),
								'default' => 'navigation',
								'condition' => array( 'tour-style' => array('modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space'), 'layout' => 'carousel' )
							),
							'carousel-navigation-align' => (function_exists('gdlr_core_get_flexslider_navigation_align')? gdlr_core_get_flexslider_navigation_align(): array()),
							'carousel-navigation-left-icon' => (function_exists('gdlr_core_get_flexslider_navigation_left_icon')? gdlr_core_get_flexslider_navigation_left_icon(): array()),
							'carousel-navigation-right-icon' => (function_exists('gdlr_core_get_flexslider_navigation_right_icon')? gdlr_core_get_flexslider_navigation_right_icon(): array()),
							'carousel-navigation-size' => (function_exists('gdlr_core_get_flexslider_navigation_icon_size')? gdlr_core_get_flexslider_navigation_icon_size(): array()),
							'carousel-navigation-icon-color' => (function_exists('gdlr_core_get_flexslider_navigation_icon_color')? gdlr_core_get_flexslider_navigation_icon_color(): array()),
							'carousel-navigation-icon-bg' => (function_exists('gdlr_core_get_flexslider_navigation_icon_background')? gdlr_core_get_flexslider_navigation_icon_background(): array()),
							'carousel-navigation-icon-padding' => (function_exists('gdlr_core_get_flexslider_navigation_icon_padding')? gdlr_core_get_flexslider_navigation_icon_padding(): array()),
							'carousel-navigation-icon-radius' => (function_exists('gdlr_core_get_flexslider_navigation_icon_radius')? gdlr_core_get_flexslider_navigation_icon_radius(): array()),
							'carousel-navigation-margin' => (function_exists('gdlr_core_get_flexslider_navigation_margin')? gdlr_core_get_flexslider_navigation_margin(): array()),
							'carousel-navigation-icon-margin' => (function_exists('gdlr_core_get_flexslider_navigation_icon_margin')? gdlr_core_get_flexslider_navigation_icon_margin(): array()),
							'display-price' => array(
								'title' => esc_html__('Display Price', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'enable'
							),
							'price-position' => array(
								'title' => esc_html__('Price Display Position', 'tourmaster'),
								'type' => 'combobox', 
								'options' => array(
									'right-title' => esc_html__('Right Side Of The Title', 'tourmaster'),
									'bottom-title' => esc_html__('Bottom Of The Title', 'tourmaster'),
									'bottom-title-center' => esc_html__('Bottom Of The Title Center', 'tourmaster'),
									'bottom-bar' => esc_html__('As Bottom Bar', 'tourmaster'),
								),
								'condition' => array( 'display-price' => 'enable', 'tour-style' => array('grid', 'grid-with-frame', 'grid-no-space') ),
								'default' => 'right-title'
							),
							'price-prefix-text' => array(
								'title' => esc_html__('Price Prefix Text', 'tourmaster'),
								'type' => 'text', 
								'condition' => array( 'tour-style' => array('grid', 'grid-with-frame', 'grid-no-space'), 'price-position' => array('bottom-title', 'bottom-title-center', 'bottom-bar') ),
								'description' => esc_html__('Leave Blank For Default', 'tourmaster')
							),
							'tour-info' => array(
								'title' => esc_html__('Tour Info', 'tourmaster'),
								'type' => 'multi-combobox',
								'options' => array(
									'duration-text' => esc_html__('Duration', 'tourmaster'),
									'availability' => esc_html__('Availability', 'tourmaster'),
									'departure-location' => esc_html__('Departure Location', 'tourmaster'),
									'return-location' => esc_html__('Return Location', 'tourmaster'),
									'minimum-age' => esc_html__('Minimum Age', 'tourmaster'),
									'maximum-people' => esc_html__('Maximum People', 'tourmaster'),
									'custom-excerpt' => esc_html__('Custom Excerpt ( In Tour Option )', 'tourmaster'),
								),
								'condition' => array( 'tour-style' => array('modern', 'modern-no-space', 'grid', 'grid-with-frame', 'grid-no-space', 'full', 'full-with-frame', 'medium', 'medium-with-frame') ),
								'description' => esc_html__('You can use Ctrl/Command button to select multiple items or remove the selected item. Leave this field blank to select all items in the list.', 'tourmaster'),
							),
							'excerpt' => array(
								'title' => esc_html__('Excerpt Type', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'specify-number' => esc_html__('Specify Number', 'tourmaster'),
									'show-all' => esc_html__('Show All ( use <!--more--> tag to cut the content )', 'tourmaster'),
									'none' => esc_html__('Disable Exceprt', 'tourmaster'),
								),
								'condition' => array( 'tour-style' => array('full', 'full-with-frame', 'medium', 'medium-with-frame', 'grid', 'grid-with-frame', 'grid-no-space') ),
								'default' => 'specify-number',
							),
							'excerpt-number' => array(
								'title' => esc_html__('Excerpt Number', 'tourmaster'),
								'type' => 'text',
								'default' => 55,
								'condition' => array( 'tour-style' => array('full', 'full-with-frame', 'medium', 'medium-with-frame', 'grid', 'grid-with-frame', 'grid-no-space'), 'excerpt' => 'specify-number' )
							),
							'tour-rating' => array(
								'title' => esc_html__('Tour Rating', 'tourmaster'),
								'type' => 'checkbox',
								'condition' => array( 'tour-style' => array('full', 'full-with-frame', 'medium', 'medium-with-frame', 'grid', 'grid-with-frame', 'grid-no-space') ),
								'default' => 'enable'
							),
						),
					),
					'typography' => array(
						'title' => esc_html('Typography', 'tourmaster'),
						'options' => array(
							'tour-title-font-size' => array(
								'title' => esc_html__('Tour Title Font Size', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'tour-title-font-weight' => array(
								'title' => esc_html__('Tour Title Font Weight', 'tourmaster'),
								'type' => 'text',
								'description' => esc_html__('Eg. lighter, bold, normal, 300, 400, 600, 700, 800', 'tourmaster')
							),
							'tour-title-letter-spacing' => array(
								'title' => esc_html__('Tour Title Letter Spacing', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'tour-title-text-transform' => array(
								'title' => esc_html__('Tour Title Text Transform', 'tourmaster'),
								'type' => 'combobox',
								'data-type' => 'text',
								'options' => array(
									'uppercase' => esc_html__('Uppercase', 'tourmaster'),
									'lowercase' => esc_html__('Lowercase', 'tourmaster'),
									'capitalize' => esc_html__('Capitalize', 'tourmaster'),
									'none' => esc_html__('None', 'tourmaster'),
								),
								'default' => 'uppercase'
							)
						)
					),
					'shadow' => array(
						'title' => esc_html__('Color/Shadow', 'tourmaster'),
						'options' => array(
							'frame-shadow-size' => array(
								'title' => esc_html__('Shadow Size ( for image/frame )', 'tourmaster'),
								'type' => 'custom',
								'item-type' => 'padding',
								'options' => array('x', 'y', 'size'),
								'data-input-type' => 'pixel',
							),
							'frame-shadow-color' => array(
								'title' => esc_html__('Shadow Color ( for image/frame )', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'frame-shadow-opacity' => array(
								'title' => esc_html__('Shadow Opacity ( for image/frame )', 'tourmaster'),
								'type' => 'text',
								'default' => '0.2',
								'description' => esc_html__('Fill the number between 0.01 to 1', 'tourmaster')
							),
						),
					),
					'spacing' => array(
						'title' => esc_html('Spacing', 'tourmaster'),
						'options' => array(
							'tour-border-radius' => array(
								'title' => esc_html__('Tour Frame/Thumbnail Border Radius', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'padding-bottom' => array(
								'title' => esc_html__('Padding Bottom ( Item )', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'default' => '30px'
							),
							'tour-title-bottom-margin' => array(
								'title' => esc_html__('Tour Title Bottom Margin', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
						)
					),
					'item-title' => array(
						'title' => esc_html('Item Title', 'tourmaster'),
						'options' => gdlr_core_block_item_option()
					)
				));
			}

			// get the preview for page builder
			static function get_preview( $settings = array() ){
				$content  = self::get_content($settings);
				$id = mt_rand(0, 9999);
				
				ob_start();
?><script type="text/javascript" id="tourmaster-preview-tour-<?php echo esc_attr($id); ?>" >
if( document.readyState == 'complete' ){
	jQuery(document).ready(function(){
		var tour_preview = jQuery('#tourmaster-preview-tour-<?php echo esc_attr($id); ?>').parent();
		tour_preview.gdlr_core_lightbox().gdlr_core_flexslider().gdlr_core_isotope().gdlr_core_fluid_video();
	});
}else{
	jQuery(window).load(function(){
		var tour_preview = jQuery('#tourmaster-preview-tour-<?php echo esc_attr($id); ?>').parent();
		tour_preview.gdlr_core_lightbox().gdlr_core_flexslider().gdlr_core_isotope().gdlr_core_fluid_video();
	});
}
</script><?php	
				$content .= ob_get_contents();
				ob_end_clean();
				
				return $content;
			}			
			
			// get the content from settings
			static function get_content( $settings = array() ){
				
				global $tourmaster_tour_item_id;
				$tourmaster_tour_item_id = empty($tourmaster_tour_item_id)? intval(rand(1,100)): $tourmaster_tour_item_id + 1;

				// default variable
				if( empty($settings) ){
					$settings = array(
						'category' => '', 'tag' => '', 'tour-style' => 'full'
					);
				}
				
				$settings['tour-style'] = empty($settings['tour-style'])? 'full': $settings['tour-style'];
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
					$settings['column-size-temp'] = empty($settings['column-size'])? 60: $settings['column-size'];
				}else{
					$settings['has-column'] = 'no';
					$settings['column-size-temp'] = empty($settings['column-size'])? 60: $settings['column-size'];
					$settings['column-size'] = 60;
					$settings['layout'] = 'fitrows';
				}

				// start printing item
				$extra_class  = ' tourmaster-tour-item-style-' . $settings['tour-style'];

				$title_settings = $settings;
				if( $settings['no-space'] == 'yes' || $settings['layout'] == 'carousel' ){
					$title_settings['pdlr'] = false;
					$extra_class .= ' tourmaster-item-pdlr';
				}
				if( (!empty($settings['title']) || !empty($settings['caption'])) && $settings['layout'] == 'carousel' ){
					if( empty($settings['carousel-navigation']) || in_array($settings['carousel-navigation'], array('navigation', 'both')) ){
						$title_settings['carousel'] = 'enable';
					}
				}

				if( $settings['has-column'] == 'yes' ){
					$extra_class .= ' tourmaster-tour-item-column-' . intval(60 / $settings['column-size']);
				}

				$ret  = '<div class="tourmaster-tour-item clearfix ' . esc_attr($extra_class) . '" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';

				// $ret .= '<a href="http://localhost/bookdev/?tourmaster-payment&tid='.esc_attr($settings['id']).'">';

				// print title
				if( function_exists('gdlr_core_block_item_title') ){
					$ret .= gdlr_core_block_item_title($title_settings);
				}
				
				// pring tour item
				$tour_item = new tourmaster_tour_item($settings);

				$ret .= $tour_item->get_content();
				
				$ret .= '</div>'; // tourmaster-tour-item
				// $ret .= '</a>';
				
				return $ret;
			}			
			
		} // tourmaster_pb_element_tour
	} // class_exists	

	add_shortcode('tourmaster_tour', 'tourmaster_tour_shortcode');
	if( !function_exists('tourmaster_tour_shortcode') ){
		function tourmaster_tour_shortcode($atts){
			$atts = wp_parse_args($atts, array());
			$atts['column-size'] = empty($atts['column-size'])? 60: 60 / intval($atts['column-size']); 
			$atts['tour-info'] = empty($atts['tour-info'])? array(): array_map('trim', explode(',', $atts['tour-info']));
			
			$ret  = '<div class="tourmaster-tour-shortcode clearfix tourmaster-item-rvpdlr" >';
			$ret .= tourmaster_pb_element_tour::get_content($atts);
			$ret .= '</div>';

			return $ret;
		}
	}