<?php
	/*	
	*	Tourmaster Plugin
	*	---------------------------------------------------------------------
	*	for tour post type
	*	---------------------------------------------------------------------
	*/

	add_action('init', 'tourmaster_add_custom_tour_tax', 99);
	if( !function_exists('tourmaster_add_custom_tour_tax') ){
		function tourmaster_add_custom_tour_tax(){
			$custom_taxs = get_option('tourmaster_custom_tour_taxs', array());

			foreach( $custom_taxs as $custom_tax_slug => $custom_tax ){

				$args = array(
					'hierarchical'      => $custom_tax['hierarchical'],
					'label'             => $custom_tax['label'],
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'capabilities'		=> array(
						'manage_terms' => 'manage_tour_category', 
						'edit_terms'   => 'manage_tour_category', 
						'delete_terms' => 'manage_tour_category', 
						'assign_terms' => 'manage_tour_category'
					)
				);
				register_taxonomy($custom_tax_slug, array('tour'), $args);
				register_taxonomy_for_object_type($custom_tax_slug, 'tour');

				new tourmaster_taxonomy_option(array(
					'taxonomy' => $custom_tax_slug,
					'options' => array(
						'thumbnail' => array(
							'title' => esc_html__('Thumbnail', 'tourmaster'),
							'type' => 'upload'
						),
						'archive-title-background' => array(
							'title' => esc_html__('Archive Title Background ( For Traveltour Theme )', 'tourmaster'),
							'type' => 'upload'
						)
					)
				));

			}
		}
	}

	add_action('admin_menu', 'tourmaster_init_tour_filter_page', 99);
	if( !function_exists('tourmaster_init_tour_filter_page') ){
		function tourmaster_init_tour_filter_page(){
			add_submenu_page(
				'edit.php?post_type=tour', 
				esc_html__('Add New Filter', 'tourmaster'), 
				esc_html__('Add New Filter', 'tourmaster'),
				'manage_tour_filter', 
				'tourmaster_add_filter_page', 
				'tourmaster_create_add_filter_page'
			);
		}
	}

	// add the script when opening the filter
	add_action('admin_enqueue_scripts', 'tourmaster_add_filter_page_script');
	if( !function_exists('tourmaster_add_filter_page_script') ){
		function tourmaster_add_filter_page_script($hook){
			
			if( strpos($hook, 'page_tourmaster_add_filter_page') !== false ){
				tourmaster_include_utility_script(array(
					'font-family' => 'Open Sans'
				));

				wp_enqueue_style('tourmaster-add-filter', TOURMASTER_URL . '/include/css/add-filter.css');
				wp_enqueue_script('tourmaster-add-filter', TOURMASTER_URL . '/include/js/add-filter.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), false, true);
				
				// action
				$custom_taxs = get_option('tourmaster_custom_tour_taxs', array());
				if( !empty($_GET['slug']) && !empty($_GET['label']) ){
					$slug = trim($_GET['slug']);
					$slug = preg_replace('/(\+|\?|\&|\s)/', '', $slug);
					$label = trim($_GET['label']);
					$hierarchical = empty($_GET['hierarchical'])? false: true;

					$custom_taxs[$slug] = array(
						'label' => $label,
						'hierarchical' => $hierarchical
					);

					update_option('tourmaster_custom_tour_taxs', $custom_taxs);
					wp_redirect(remove_query_arg(array('slug', 'label', 'hierarchical')));
				}else if( !empty($_GET['slug']) && !empty($_GET['action']) ){
					if( $_GET['action'] == 'remove' ){
						$slug = trim($_GET['slug']);
						unset($custom_taxs[$slug]);

						update_option('tourmaster_custom_tour_taxs', $custom_taxs);
						wp_redirect(remove_query_arg(array('slug', 'action')));
					}
				}
			}

		} // tourmaster_add_filter_page_script
	}	

	if( !function_exists('tourmaster_create_add_filter_page') ){
		function tourmaster_create_add_filter_page(){

			// create filter form
			tourmaster_get_add_filter_form();

			// add filter content
			echo '<div class="tourmaster-add-filter-page-wrap" >';
			echo '<div class="tourmaster-add-filter-head" >';
			echo '<i class="fa fa-check-circle-o" ></i>';
			echo esc_html__('Filter', 'tourmaster');
			echo '</div>';

			echo '<div class="tourmaster-add-filter-page-content" >';
			echo '<table>';
			echo tourmaster_get_table_head(array(
				esc_html__('Taxonomy Slug', 'tourmaster'),
				esc_html__('Taxonomy Name', 'tourmaster'),
				esc_html__('Hierarchical', 'tourmaster'),
				esc_html__('Action', 'tourmaster'),
			));

			$custom_taxs = get_option('tourmaster_custom_tour_taxs', array());
			foreach( $custom_taxs as $custom_tax_slug => $custom_tax ){

				$tax_link = admin_url('edit-tags.php?taxonomy=' . $custom_tax_slug);

				$action  = '<a href="' . add_query_arg(array('slug'=>$custom_tax_slug, 'action'=>'remove')) . '" class="tourmaster-add-filter-action" title="' . esc_html__('Remove', 'tourmaster') . '" ';
				$action .= 'data-confirm="' . esc_html__('The filter you selected will be permanently removed from the system.', 'tourmaster') . '" ';
				$action .= '>';
				$action .= '<i class="fa fa-trash-o" ></i>';
				$action .= '</a>';

				tourmaster_get_table_content(array(
					'<a href="' . esc_url($tax_link) . '" >' . $custom_tax_slug . '</a>',
					'<a href="' . esc_url($tax_link) . '" >' . $custom_tax['label'] . '</a>',
					empty($custom_tax['hierarchical'])? esc_html__('No', 'tourmaster'): esc_html__('Yes', 'tourmaster'), 
					$action
				));

			}
			echo '</table>';
			echo '</div>';
			echo '</div>';
		}
	}

	// add filter form
	if( !function_exists('tourmaster_get_add_filter_form') ){
		function tourmaster_get_add_filter_form(){

			echo '<form class="tourmaster-add-filter-form" method="GET" >';
			echo '<input type="hidden" name="post_type" value="tour" />';
			echo '<input type="hidden" name="page" value="tourmaster_add_filter_page" />';

			echo '<div class="tourmaster-add-filter-form-item clearfix" >';
			echo '<label>' . esc_html__('Custom Filter Slug :', 'tourmaster') . '</label>';
			echo '<input type="text" name="slug" value="" />';
			echo '<span class="tourmaster-add-filter-description" >';
			echo esc_html__('Please only use lowercase English character and hypen with no spaces. ( "a to z" "-" "_")', 'tourmaster');
			echo '</span>';
			echo '</div>';

			echo '<div class="tourmaster-add-filter-form-item clearfix" >';
			echo '<label>' . esc_html__('Custom Filter Label :', 'tourmaster') . '</label>';
			echo '<input type="text" name="label" value="" />';
			echo '</div>';

			echo '<div class="tourmaster-add-filter-form-item clearfix" >';
			echo '<label>' . esc_html__('Custom Filter Hierarchical :', 'tourmaster') . '</label>';
			echo '<input type="checkbox" name="hierarchical" checked />';
			echo '<span class="tourmaster-add-filter-description" >';
			echo esc_html__('Enable this option to make custom filter behave like "post category". Otherwise, it will be similar to "post tag"', 'tourmaster');
			echo '</span>';
			echo '</div>';

			echo '<div class="tourmaster-add-filter-form-submit" >';
			echo '<input class="tourmaster-button" type="submit" value="' . esc_html__('Create', 'tourmaster') . '" />';
			echo '</div>';
			echo '</form>';

		}
	}

	if( !function_exists('tourmaster_is_custom_tour_tax') ){
		function tourmaster_is_custom_tour_tax(){

			$taxs = tourmaster_get_custom_tax_list();
			foreach( $taxs as $tax_slug => $tax_label ){
				if( is_tax($tax_slug) ){
					return true;
				}
			}

			return false;
		}
	}
	if( !function_exists('tourmaster_get_custom_tax_list') ){
		function tourmaster_get_custom_tax_list(){
			$ret = array();

			$custom_taxs = get_option('tourmaster_custom_tour_taxs', array());
			foreach( $custom_taxs as $custom_tax_slug => $custom_tax ){
				$ret[$custom_tax_slug] = $custom_tax['label'];
			}

			return $ret;
		}
	}

	// get tour search fields
	if( !function_exists('tourmaster_get_tour_search_fields') ){
		function tourmaster_get_tour_search_fields( $type = 'all' ){

			if( $type == 'all' ){
				$ret = array(
					'keywords' => esc_html__('Keywords', 'tourmaster'),
					'tour_category' => esc_html__('Category', 'tourmaster'),
					'tour_tag' => esc_html__('Tag', 'tourmaster'),
				) + tourmaster_get_custom_tax_list() + array(
					'duration' => esc_html__('Duration', 'tourmaster'),
					'date' => esc_html__('Date', 'tourmaster'),
					'month' => esc_html__('Month', 'tourmaster'),
					// 'start_date' => esc_html__('Start Date', 'tourmaster'),
					// 'end_date' => esc_html__('End Date', 'tourmaster'),
					'min-price' => esc_html__('Min Price', 'tourmaster'),
					'max-price' => esc_html__('Max Price', 'tourmaster'),
				);			
			}else if( $type == 'default' ){
				$ret = array(
					'keywords' => esc_html__('Keywords', 'tourmaster'),
					'tour_category' => esc_html__('Category', 'tourmaster'),
					'tour_tag' => esc_html__('Tag', 'tourmaster'),
					'duration' => esc_html__('Duration', 'tourmaster'),
					'date' => esc_html__('Date', 'tourmaster'),
					'month' => esc_html__('Month', 'tourmaster'),
					'min-price' => esc_html__('Min Price', 'tourmaster'),
					'max-price' => esc_html__('Max Price', 'tourmaster'),
				);	
			}else if( $type == 'custom' ){
				$ret = tourmaster_get_custom_tax_list();
			}


			return $ret;
		}
	}