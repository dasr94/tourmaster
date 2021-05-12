<?php
	/*	
	*	Goodlayers Plugin Framework
	*	---------------------------------------------------------------------
	*	creating the page option meta
	*	---------------------------------------------------------------------
	*/
	
	if( !class_exists('tourmaster_page_option') ){
		
		class tourmaster_page_option{
			 
			// creating object
			private $settings = array();
			
			function __construct( $settings = array() ){
				
				$this->settings = wp_parse_args($settings, array(
					'slug' => 'tourmaster-option',
					'post_type' => array('page'),
					'options' => array(),
					'title' => esc_html__('Plugin Options', 'tourmaster'),
					'title-icon' => 'fa fa-gears'
				));	
				
				// create custom meta box
				add_action('add_meta_boxes', array(&$this, 'init_page_option_meta_box'));
				
				// save custom metabox
				foreach( $this->settings['post_type'] as $post_type ){
					add_action('save_post_' . $post_type , array(&$this, 'save_page_option_meta_box'));
				}
				
				// ajax save
				/*add_action('wp_ajax_tourmaster_save_page_option_data', array(&$this, 'ajax_save_page_option'));*/
				add_action('wp_ajax_tourmaster_save_page_option_data', array(&$this, 'save_data'));
				// add the script when opening the registered post type
				add_action('admin_enqueue_scripts', array(&$this, 'load_page_option_script') );
			}
			
			// function that enqueue page builder script
			function load_page_option_script( $hook ){
				if( ($hook == 'post.php' || $hook == 'post-new.php') && in_array(get_post_type(), $this->settings['post_type']) ){
					tourmaster_html_option::include_script();
				}
			} 
			
			// function that creats page builder meta box
			function init_page_option_meta_box(){
				
				foreach( $this->settings['post_type'] as $post_type ){
					add_meta_box($this->settings['slug'], $this->settings['title'],
						array(&$this, 'create_page_option_meta_box'),
						$post_type, 'normal', 'high' );	
				}
			}
			function create_page_option_meta_box( $post ){

				$page_option_value = apply_filters($this->settings['slug'] . '-init-value', get_post_meta($post->ID, $this->settings['slug'], true), $post->ID);
				
				// add nonce field to validate upon saving
				wp_nonce_field('tourmaster_page_option', 'plugin_page_option_security');
				echo '<input type="hidden" class="tourmaster-page-option-value" name="' . esc_attr($this->settings['slug']). '" value="' . esc_attr(json_encode($page_option_value)) . '" />';
				
				$this->get_option_head();

				echo '<div class="tourmaster-page-option-content" >';
				$this->get_option_tab($page_option_value, $post);
				echo '</div>';
			}

			function create_page_option_meta_box_2( $post ){

				$page_option_value = apply_filters($this->settings['slug'] . '-init-value', get_post_meta($post->ID, $this->settings['slug'], true), $post->ID);
				$page_option_value_2 = array(
					'post_title' => get_the_title($post->ID),
					'post_content' => get_post_field('post_content', $post->ID),
					'post_status' => get_post_field('post_status', $post->ID)
				);
				
				// add nonce field to validate upon saving
				wp_nonce_field('tourmaster_page_option', 'plugin_page_option_security');
				echo '<input type="hidden" class="tourmaster-page-option-value" name="' . esc_attr($this->settings['slug']). '" value="' . esc_attr(json_encode($page_option_value)) . '"  value2="' . esc_attr(json_encode($page_option_value_2)) . '"/>';
				$this->get_option_head_2($post);

				echo '<div class="tourmaster-page-option-content" >';
				$this->get_option_tab($page_option_value, $post);
				echo '</div>';
			}

			function get_option_head_2($post){

				echo '<div class="tourmaster-page-option-head" >';
				echo '<div class="tourmaster-page-option-head-title">';
				echo '<i class="' . esc_attr($this->settings['title-icon']) . '"></i>' . $this->settings['title'];
				echo '</div>'; // tourmaster-page-option-head-title

				echo '<div class="tourmaster-page-option-head-save" data-post-id="' . esc_attr($post->ID) . '" ';
				echo 'data-ajax-url="' . esc_url(TOURMASTER_AJAX_URL) . '" ';
				echo 'data-failed-head="' . esc_attr__('An error occurs', 'goodlayers-core') . '" ';
				echo 'data-failed-message="' . esc_attr__('Please use wordpress update button to update the page instead.' ,'goodlayers-core') . '" ';
				echo ' >';
				echo '<i class="fa fa-save"></i>';
				echo esc_html__('Save Settings', 'goodlayers-core');
				echo '</div>';
				echo '</div>';

			}

			// page option head
			function get_option_head(){

				echo '<div class="tourmaster-page-option-head" >';
				echo '<div class="tourmaster-page-option-head-title">';
				echo '<i class="' . esc_attr($this->settings['title-icon']) . '"></i>' . $this->settings['title'];
				echo '</div>'; // tourmaster-page-option-head-title

				echo '<div class="tourmaster-page-option-head-save" data-post-id="' . esc_attr(get_the_ID()) . '" ';
				echo 'data-ajax-url="' . esc_url(TOURMASTER_AJAX_URL) . '" ';
				echo 'data-failed-head="' . esc_attr__('An error occurs', 'goodlayers-core') . '" ';
				echo 'data-failed-message="' . esc_attr__('Please use wordpress update button to update the page instead.' ,'goodlayers-core') . '" ';
				echo ' >';
				echo '<i class="fa fa-save"></i>';
				echo esc_html__('Save Settings', 'goodlayers-core');
				echo '</div>';
				echo '</div>';

			}

			// page option tab
			function get_option_tab($option_value, $post = null){

				$active = true;
				echo '<div class="tourmaster-page-option-tab-head clearfix" id="tourmaster-page-option-tab-head" >';
				foreach( $this->settings['options'] as $tab_slug => $tab_options ){
					echo '<div class="tourmaster-page-option-tab-head-item ' . ($active? 'tourmaster-active': '') . '" data-tab-slug="' . esc_attr($tab_slug) . '" >';
					echo $tab_options['title'];
					echo '</div>'; // tourmaster-page-option-tab-head-item

					$active = false;
				}
				echo '</div>'; // tourmaster-page-option-tab-head

				$active = true;
				echo '<div class="tourmaster-page-option-tab-content tourmaster-condition-wrapper" id="tourmaster-page-option-tab-content" >';
				foreach( $this->settings['options'] as $tab_slug => $tab_options ){
					echo '<div class="tourmaster-page-option-tab-content-item ' . ($active? 'tourmaster-active': '') . '" data-tab-slug="' . esc_attr($tab_slug) . '" >';
					if($tab_slug=="tour-info"){
						foreach( $tab_options['options'] as $option_slug => $option ){
							$option['slug'] = $option_slug;
							if($option['slug']=="post_title"){
								$option['value'] = get_the_title($post->ID);
							} else if($option['slug']=="latitude" || $option['slug']=="longitude") {
								$option['value'] = get_post_meta($post->ID, $option['slug'], true);
							} else if($option['slug']=="image-custom-trip"){
								$option['value'] = get_post_thumbnail_id( $post->ID );
							} else if($option['slug']=="tour-activity"){
								$option['value'] = get_post_meta($post->ID, $option['slug'], true);
							}else{
								$option['value'] = get_post_field($option['slug'], $post->ID);
							}
							echo tourmaster_html_option::get_element($option);
						}
					} else {
						foreach( $tab_options['options'] as $option_slug => $option ){
							$option['slug'] = $option_slug;
							if( !empty($option['single']) ){
								$option['value'] = get_post_meta($post->ID, $option['single'], true);
							}else if( isset($option_value[$option_slug]) ){
								$option['value'] = $option_value[$option_slug];
							}
							
							echo tourmaster_html_option::get_element($option);
						}
					}
					
					echo '</div>';
					
					$active = false;
				}
				echo '</div>'; // tourmaster-page-option-tab-content
				
			}
			
			// ajax save meta box
			function ajax_save_page_option(){

				if( !check_ajax_referer('tourmaster_page_option', 'security', false) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('Invalid Nonce', 'tourmaster'),
						'message'=> esc_html__('Please use wordpress update button to update the page instead.' ,'tourmaster')
					)));
				}
				if( !empty($_POST['name']) && $_POST['name'] == $this->settings['slug'] ){

					if( !empty($_POST['post_id']) ){
						$value = json_decode(tourmaster_process_post_data($_POST['value']), true);
						$value = wp_slash($value);

						if( !empty($value) ){
							foreach( $this->settings['options'] as $tab ){
								if ($tab[0]=="tour-info"){
									
								} else {
									foreach( $tab['options'] as $option_slug => $option ){
										if( !empty($option['single']) && isset($value[$option_slug]) ){
											update_post_meta($_POST['post_id'], $option['single'], $value[$option_slug]);
											unset($value[$option_slug]);
										}
									}
								}
							}

							update_post_meta($_POST['post_id'], $_POST['name'], $value);
							$post_data = array(
								'ID' => $_POST['post_id'],
								'post_title' => $value['post_title'],
								'post_content' => $value['post_content'],
								'post_status' => $value['post_status']
							);
							wp_update_post($post_data);
							if($value['post_status'] == 'publish'){
								wp_publish_post($_POST['post_id']);
							}
							if (!empty($value['latitude'])) {
								update_post_meta($_POST['post_id'], 'latitude', $value['latitude']);
							} 
							if (!empty($value['longitude'])) {
								update_post_meta($_POST['post_id'], 'longitude', $value['longitude']);
							}
							if (!empty($value['city'])) {
								update_post_meta($_POST['post_id'], 'city', $value['city']);
							}
							if (!empty($value['state'])) {
								update_post_meta($_POST['post_id'], 'state', $value['state']);
							}
							if (!empty($value['country'])) {
								update_post_meta($_POST['post_id'], 'country', $value['country']);
							}
							if (!empty($value['address'])) {
								update_post_meta($_POST['post_id'], 'address', $value['address']);
							}
							if (!empty($value['image-custom-trip'])) {
								set_post_thumbnail($_POST['post_id'], $value['image-custom-trip']);
							}
							if (!empty($value['header-slider'])) {
								update_post_meta($_POST['post_id'], 'header-slider', $value['header-slider']);
							}
							if (!empty($value['tour-activity'])) {
								update_post_meta($_POST['post_id'], 'tour-activity', $value['tour-activity']);
								wp_set_post_terms($_POST['post_id'], $value['tour-activity'], 'tour-activity');
							} else {
								wp_set_post_terms($_POST['post_id'], '', 'tour-activity');
								update_post_meta($_POST['post_id'], 'tour-activity', '');
							}
							wp_save_post_revision($_POST['post_id']);
						}
						die(json_encode(array(
							'status' => 'success',
							'head' => esc_html__('Successfully Saved', 'tourmaster'),
							'message'=> ''
						)));
					}

					die(json_encode(array(
						'status' => 'success',
						'head' => esc_html__('An error occurs', 'tourmaster'),
						'message' =>  esc_html__('Please use wordpress update button to update the page instead.' ,'tourmaster')
					)));
					
				}

			}

			function save_data(){
				if( !empty($_POST['name']) && $_POST['name'] == $this->settings['slug'] ){

					if( !empty($_POST['post_id']) ){
						$value = json_decode(tourmaster_process_post_data($_POST['value']), true);
						$value = wp_slash($value);
						
						if( !empty($value) ){
							foreach( $this->settings['options'] as $tab ){
								if ($tab[0]=="tour-info"){
									
								} else {
									foreach( $tab['options'] as $option_slug => $option ){
										if( !empty($option['single']) && isset($value[$option_slug]) ){
											update_post_meta($_POST['post_id'], $option['single'], $value[$option_slug]);
											unset($value[$option_slug]);
										}
									}
								}
							}

							update_post_meta($_POST['post_id'], $_POST['name'], $value);
							$post_data = array(
								'ID' => $_POST['post_id'],
								'post_title' => $value['post_title'],
								'post_content' => $value['post_content'],
								'post_status' => $value['post_status']
							);
							wp_update_post($post_data);
							if($value['post_status'] == 'publish'){
								wp_publish_post($_POST['post_id']);
							}
							if (!empty($value['latitude'])) {
								update_post_meta($_POST['post_id'], 'latitude', $value['latitude']);
							} 
							if (!empty($value['longitude'])) {
								update_post_meta($_POST['post_id'], 'longitude', $value['longitude']);
							}
							if (!empty($value['city'])) {
								update_post_meta($_POST['post_id'], 'city', $value['city']);
							}
							if (!empty($value['state'])) {
								update_post_meta($_POST['post_id'], 'state', $value['state']);
							}
							if (!empty($value['country'])) {
								update_post_meta($_POST['post_id'], 'country', $value['country']);
							}
							if (!empty($value['address'])) {
								update_post_meta($_POST['post_id'], 'address', $value['address']);
							}
							if (!empty($value['image-custom-trip'])) {
								set_post_thumbnail($_POST['post_id'], $value['image-custom-trip']);
							}
							if (!empty($value['header-slider'])) {
								update_post_meta($_POST['post_id'], 'header-slider', $value['header-slider']);
							}
							if (!empty($value['tour-activity'])) {
								update_post_meta($_POST['post_id'], 'tour-activity', $value['tour-activity']);
								wp_set_post_terms($_POST['post_id'], $value['tour-activity'], 'tour-activity');
							} else {
								wp_set_post_terms($_POST['post_id'], '', 'tour-activity');
								update_post_meta($_POST['post_id'], 'tour-activity', '');
							}
							wp_save_post_revision($_POST['post_id']);
						}

						die(json_encode(array(
							'status' => 'success',
							'head' => esc_html__('Successfully Saved', 'tourmaster'),
							'message'=> ''
						)));
					}

					die(json_encode(array(
						'status' => 'success',
						'head' => esc_html__('An error occurs', 'tourmaster'),
						'message' =>  esc_html__('Please use wordpress update button to update the page instead.' ,'tourmaster')
					)));
					
				}
			}

			// test save post
			function save_page_option_meta_box( $post_id ){

				// check if nonce is available
				if( !isset($_POST['plugin_page_option_security']) ){
					return;
				}

				// vertify that the nonce is vaild
				if( !wp_verify_nonce($_POST['plugin_page_option_security'], 'tourmaster_page_option') ) {
					return;
				}

				// ignore the auto save
				if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
					return;
				}

				// check the user's permissions.
				if( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
					if( !current_user_can('edit_page', $post_id) ){
						return;
					}
				}else{
					if( !current_user_can('edit_post', $post_id) ){
						return;
					}
				}			
				
				// start updating the meta fields
				/*
				if( !empty($_POST[$this->settings['slug']]) ){
					$value = json_decode(tourmaster_process_post_data($_POST[$this->settings['slug']]), true);
					
					foreach( $this->settings['options'] as $tab ){
						foreach( $tab['options'] as $option_slug => $option ){
							if( !empty($option['single']) && isset($value[$option_slug]) ){
								update_post_meta($post_id, $option['single'], $value[$option_slug]);
								unset($value[$option_slug]);
							}
						}
					}
					update_post_meta($post_id, $this->settings['slug'], $value);
				} */
				
			}
			
			// convert the data to read able revision format
			function convert_page_option_revision_data( $data ){
				return json_encode($data) . "\n" . '  - convert :) ';
			}

		} // tourmaster_page_option
		
	} // class_exists