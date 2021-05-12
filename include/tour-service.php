<?php
	/*	
	*	Tourmaster Plugin
	*	---------------------------------------------------------------------
	*	for tour post type
	*	---------------------------------------------------------------------
	*/

	// create post type
	add_action('init', 'tourmaster_tour_service_init');
	if( !function_exists('tourmaster_tour_service_init') ){
		function tourmaster_tour_service_init() {
			
			// custom post type
			$supports = apply_filters('tourmaster_custom_post_support', array('title', 'author', 'custom-fields'), 'service');

			$labels = array(
				'name'               => esc_html__('Tour Service', 'tourmaster'),
				'singular_name'      => esc_html__('Tour Service', 'tourmaster'),
				'menu_name'          => esc_html__('Tour Service', 'tourmaster'),
				'name_admin_bar'     => esc_html__('Tour Service', 'tourmaster'),
				'add_new'            => esc_html__('Add New', 'tourmaster'),
				'add_new_item'       => esc_html__('Add New Service', 'tourmaster'),
				'new_item'           => esc_html__('New Service', 'tourmaster'),
				'edit_item'          => esc_html__('Edit Service', 'tourmaster'),
				'view_item'          => esc_html__('View Service', 'tourmaster'),
				'all_items'          => esc_html__('All Service', 'tourmaster'),
				'search_items'       => esc_html__('Search Service', 'tourmaster'),
				'parent_item_colon'  => esc_html__('Parent Service:', 'tourmaster'),
				'not_found'          => esc_html__('No service found.', 'tourmaster'),
				'not_found_in_trash' => esc_html__('No service found in Trash.', 'tourmaster')
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
					'edit_post'          => 'edit_service', 
					'read_post'          => 'read_service', 
					'delete_post'        => 'delete_service', 
					'delete_posts'       => 'delete_services', 
					'edit_posts'         => 'edit_services', 
					'create_posts'       => 'edit_services', 
					'edit_others_posts'  	=> 'edit_others_services', 
					'delete_others_posts'  	=> 'edit_others_services', 
					'publish_posts'      	=> 'publish_services',       
					'edit_published_posts'  => 'publish_services',       
					'read_private_posts' 	=> 'read_private_services', 
					'edit_private_posts' 	=> 'read_private_services', 
					'delete_private_posts' 	=> 'read_private_services', 
				),
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => $supports
			);
			register_post_type('tour_service', $args);

			// apply single template filter
			add_filter('single_template', 'tourmaster_tour_service_template');

		}
	} // tourmaster_post_type_init

	if( !function_exists('tourmaster_tour_service_template') ){
		function tourmaster_tour_service_template( $template ){

			if( get_post_type() == 'service' ){
				$template = get_404_template();
			}

			return $template;
		}
	}

	// create an option
	if( is_admin() ){ add_action('after_setup_theme', 'tourmaster_tour_service_option_init'); }
	if( !function_exists('tourmaster_tour_service_option_init') ){
		function tourmaster_tour_service_option_init(){

			if( class_exists('tourmaster_page_option') ){
				new tourmaster_page_option(array(
					'post_type' => array('tour_service'),
					'title' => esc_html__('Additional Service', 'tourmaster'),
					'title-icon' => 'fa fa-plane',
					'slug' => 'tourmaster-service-option',
					'options' => apply_filters('tourmaster_tour_options', array(

						'general' => array(
							'title' => esc_html__('General', 'tourmaster'),
							'options' => array(
								'price' => array(
									'title' => esc_html__('Price', 'tourmaster'),
									'type' => 'text',
									'description' => esc_html__('Only number is allowed here', 'tourmaster')
								),
								'per' => array(
									'title' => esc_html__('Per', 'tourmaster'),
									'type' => 'combobox',
									'options' => array(
										'person' => esc_html__('Person', 'tourmaster'),
										'group' => esc_html__('Group', 'tourmaster'),
										'room' => esc_html__('Room', 'tourmaster'),
										'unit' => esc_html__('Unit', 'tourmaster'),
									)
								),
								'max-unit' => array(
									'title' => esc_html__('Max Unit', 'tourmaster'),
									'type' => 'text',
									'condition' => array('per' => 'unit'),
									'description' => esc_html__('*Per unit will allow customer to put item amount directly from front end.')
								),
								'mandatory' => array(
									'title' => esc_html__('Mandatory', 'tourmaster'),
									'type' => 'checkbox',
									'default' => 'disable'
								),

							)
						),

					)) // tourmaster_tour_options
				)); // tourmaster_page_option
			}


		}
	}	