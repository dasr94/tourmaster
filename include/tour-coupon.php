<?php
	/*	
	*	Tourmaster Plugin
	*	---------------------------------------------------------------------
	*	for tour post type
	*	---------------------------------------------------------------------
	*/

	// create post type
	add_action('init', 'tourmaster_tour_coupon_init');
	if( !function_exists('tourmaster_tour_coupon_init') ){
		function tourmaster_tour_coupon_init() {
			
			// custom post type
			$supports = apply_filters('tourmaster_custom_post_support', array('title', 'author', 'custom-fields'), 'coupon');

			$labels = array(
				'name'               => esc_html__('Tour Coupon', 'tourmaster'),
				'singular_name'      => esc_html__('Tour Coupon', 'tourmaster'),
				'menu_name'          => esc_html__('Tour Coupon', 'tourmaster'),
				'name_admin_bar'     => esc_html__('Tour Coupon', 'tourmaster'),
				'add_new'            => esc_html__('Add New', 'tourmaster'),
				'add_new_item'       => esc_html__('Add New Coupon', 'tourmaster'),
				'new_item'           => esc_html__('New Coupon', 'tourmaster'),
				'edit_item'          => esc_html__('Edit Coupon', 'tourmaster'),
				'view_item'          => esc_html__('View Coupon', 'tourmaster'),
				'all_items'          => esc_html__('All Coupon', 'tourmaster'),
				'search_items'       => esc_html__('Search Coupon', 'tourmaster'),
				'parent_item_colon'  => esc_html__('Parent Coupon:', 'tourmaster'),
				'not_found'          => esc_html__('No coupon found.', 'tourmaster'),
				'not_found_in_trash' => esc_html__('No coupon found in Trash.', 'tourmaster')
			);
			$args = array(
				'labels'             => $labels,
				'description'        => esc_html__('Description.', 'tourmaster'),
				'public'             => true,
				'publicly_queryable' => false,
				'exclude_from_search'=> true,
				'show_ui'            => true,
				'show_in_admin_bar'  => false,
				'show_in_nav_menus'  => false,
				'show_in_menu'       => true,
				'query_var'          => true,
				'map_meta_cap' 		 => true,
				'capabilities' => array(
					'edit_post'          => 'edit_coupon', 
					'read_post'          => 'read_coupon', 
					'delete_post'        => 'delete_coupon', 
					'delete_posts'       => 'delete_coupon', 
					'edit_posts'         => 'edit_coupons', 
					'create_posts'       => 'edit_coupons', 
					'edit_others_posts'  => 'edit_others_coupons', 
					'delete_others_posts'  	=> 'edit_others_coupons', 
					'publish_posts'      	=> 'publish_coupons',       
					'edit_published_posts'  => 'publish_coupons',       
					'read_private_posts' 	=> 'read_private_coupons', 
					'edit_private_posts' 	=> 'read_private_coupons', 
					'delete_private_posts' 	=> 'read_private_coupons', 
				),
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => $supports
			);
			register_post_type('tour_coupon', $args);

			// apply single template filter
			add_filter('single_template', 'tourmaster_tour_coupon_template');

		}
	} // tourmaster_post_type_init

	if( !function_exists('tourmaster_tour_coupon_template') ){
		function tourmaster_tour_coupon_template( $template ){

			if( get_post_type() == 'coupon' ){
				$template = get_404_template();
			}

			return $template;
		}
	}

	// create an option
	if( is_admin() ){ add_action('after_setup_theme', 'tourmaster_tour_coupon_option_init'); }
	if( !function_exists('tourmaster_tour_coupon_option_init') ){
		function tourmaster_tour_coupon_option_init(){

			if( class_exists('tourmaster_page_option') ){
				new tourmaster_page_option(array(
					'post_type' => array('tour_coupon'),
					'title' => esc_html__('Coupon Settings', 'tourmaster'),
					'title-icon' => 'fa fa-plane',
					'slug' => 'tourmaster-coupon-option',
					'options' => apply_filters('tourmaster_tour_options', array(

						'general' => array(
							'title' => esc_html__('General', 'tourmaster'),
							'options' => array(
								'coupon-code' => array(
									'title' => esc_html__('Coupon Code', 'tourmaster'),
									'type' => 'text',
									'single' => 'tourmaster-coupon-code'
								),
								'coupon-amount' => array(
									'title' => esc_html__('Coupon Amount', 'tourmaster'),
									'type' => 'text',
									'description' => esc_html__('Number of coupon available for uses. Leave this field blank for unlimited use.', 'tourmaster')
								),
								'coupon-expiry' => array(
									'title' => esc_html__('Coupon Expiry', 'tourmaster'),
									'type' => 'datepicker',
								),
								'coupon-discount-type' => array(
									'title' => esc_html__('Coupon Discount Type', 'tourmaster'),
									'type' => 'combobox',
									'options' => array(
										'percent' => esc_html__('Percent', 'tourmaster'),
										'amount' => esc_html__('Amount', 'tourmaster'),
									)
								),
								'coupon-discount-amount' => array(
									'title' => esc_html__('Coupon Discount Amount', 'tourmaster'),
									'type' => 'text',
									'description' => esc_html__('Only number is allowed here', 'tourmaster')
								),
								'apply-to-specific-tour' => array(
									'title' => esc_html__('Apply Coupon To Only Specific Tour', 'tourmaster'),
									'type' => 'textarea',
									'description' => esc_html__('Fill tour ID separated by comma', 'tourmaster')
								),

							)
						),

					)) // tourmaster_tour_options
				)); // tourmaster_page_option
			}


		}
	}	