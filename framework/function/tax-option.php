<?php
	/*	
	*	Goodlayers Plugin Framework
	*	---------------------------------------------------------------------
	*	creating the taxonomy option meta
	*	---------------------------------------------------------------------
	*/
	
	if( !class_exists('tourmaster_taxonomy_option') ){
		
		class tourmaster_taxonomy_option{
			
			// creating object
			private $settings = array();
			
			function __construct( $settings = array() ){
				
				$this->settings = wp_parse_args($settings, array(
					'taxonomy' => 'category',
					'slug' => 'tourmaster-meta',
					'options' => array()
				));	
				
				// create custom meta box
				add_action($this->settings['taxonomy'] . '_add_form_fields', array(&$this, 'new_taxonomy_meta'));
				add_action($this->settings['taxonomy'] . '_edit_form_fields', array(&$this, 'create_taxonomy_meta'));
				
				// save custom meta field
				add_action('edited_' . $this->settings['taxonomy'], array(&$this, 'save_taxonomy_meta'));  
				add_action('create_' . $this->settings['taxonomy'], array(&$this, 'save_taxonomy_meta'));	
				
				// add the script when opening the registered post type
				add_action('admin_enqueue_scripts', array(&$this, 'load_taxonomy_option_script') );

			}
			
			// function that enqueue page builder script
			function load_taxonomy_option_script( $hook ){
				if( $hook == 'edit-tags.php' || $hook == 'term.php' ){
					tourmaster_html_option::include_script();
				}
			}
			
			// function that creats page builder meta box
			function new_taxonomy_meta( $term = '' ){
				$this->create_taxonomy_meta( $term, 'new' );
			}
			function create_taxonomy_meta( $term = '', $page = 'edit' ){

				if( !empty($term->term_id) ){
					$term_id = $term->term_id;
				}

				// add nonce field to validate upon saving
				wp_nonce_field('tourmaster_tax_option', 'tourmaster_tax_option_nonce');

				foreach( $this->settings['options'] as $option_slug => $option_value ){

					if( $page == 'edit' ){
						echo '<tr class="form-field">';
						echo '<th scope="row" valign="top">';
						echo '<label for="' . esc_attr($option_slug) . '">' . $option_value['title'] . '</label>';
						echo '</th>';
						echo '<td>';
					}else{
						echo '<div class="form-field">';
						echo '<label for="' . esc_attr($option_slug) . '">' . $option_value['title'] . '</label>';
					}

					unset($option_value['title']);
					$option_value['slug'] = $option_slug;
					if( !empty($term_id) ){
						$option_value['value'] = get_term_meta($term_id, $option_slug, true);
					}
					$option_value['with-name'] = true;
					echo tourmaster_html_option::get_element($option_value);

					if( $page == 'edit' ){
						echo '</td>';
						echo '</tr>';
					}else{
						echo '</div>';
					}
				}
			}
			
			// test save post
			function save_taxonomy_meta($term_id){

				// check if nonce is available
				if( !isset($_POST['tourmaster_tax_option_nonce']) ){
					return;
				}

				// vertify that the nonce is vaild
				if( !wp_verify_nonce($_POST['tourmaster_tax_option_nonce'], 'tourmaster_tax_option') ) {
					return;
				}

				// check the user's permissions.
				if( !current_user_can('manage_categories') ){
					return;
				}	
				
				// start updating the meta fields
				if( !empty($this->settings['options']) ){
					foreach( $this->settings['options'] as $option_slug => $option_value ){
						if( isset($_POST[$option_slug]) ){
					        update_term_meta($term_id, $option_slug, tourmaster_process_post_data($_POST[$option_slug]));
					    }
					}
				}
				
			}

		} // tourmaster_page_option
		
	} // class_exists