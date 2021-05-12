<?php
	/*	
	*	Goodlayers Plugin Framework
	*	---------------------------------------------------------------------
	*	creating the plugin option
	*	---------------------------------------------------------------------
	*/

	if( !class_exists('tourmaster_admin_option') ){
		
		class tourmaster_admin_option{
			
			// function for elements registration
			private $theme_options = array();
			
			function add_element($options, $order = 10){
				while( !empty($this->theme_options[$order]) ){
					$order++;
				}
				$this->theme_options[$order] = $options;
				ksort($this->theme_options);
			}			
			
			function get_elements(){
				return $this->theme_options;
			}

			private $settings;	
				
			function __construct($settings = array()){

				$this->settings = wp_parse_args($settings, array(
					'page-title' => esc_html__('Goodlayers Option', 'tourmaster'),
					'menu-title' => esc_html__('Goodlayers Option', 'tourmaster'),
					'capability' => 'edit_theme_options',
					'slug' => 'goodlayers_main_menu', 
					'icon-url' => TOURMASTER_URL . '/framework/images/admin-option-icon.png',
					'position' => null,
					'filewrite' => '',
					'container-width' => ''
				));

				// add action to create dashboard
				add_action('admin_menu', array(&$this, 'register_admin_menu'));
				
				// add ajax action for the theme option script
				add_action('wp_ajax_save_tourmaster_option', array(&$this, 'save_plugin_option_ajax'));
				add_action('wp_ajax_get_tourmaster_option_tab', array(&$this, 'get_plugin_option_tab'));
				add_action('wp_ajax_get_tourmaster_option_search', array(&$this, 'get_plugin_option_search'));

				add_action('wp_ajax_tourmaster_plugin_option_export', array(&$this, 'plugin_option_export'));

				// add the script when opening the theme option page
				add_action('admin_enqueue_scripts', array(&$this, 'load_plugin_option_script'));

				// add action to force save style-custom
				add_action('tourmaster_theme_option_filewrite', array(&$this, 'after_save_plugin_option'));
			}
			
			// function that enqueue plugin option script
			function load_plugin_option_script( $hook ){
				if( strpos($hook, 'page_' . $this->settings['slug']) !== false ){
					tourmaster_include_utility_script();
					
					tourmaster_html_option::include_script(array(
						'style' => 'html-option'
					));
					
					// include the style
					wp_enqueue_style('tourmaster-plugin-option', TOURMASTER_URL . '/framework/css/plugin-option.css');
					
					// include the script
					wp_enqueue_script('tourmaster-plugin-option', TOURMASTER_URL . '/framework/js/plugin-option.js', array('jquery'), false, true);
					wp_localize_script('tourmaster-plugin-option', 'tourmaster_ajax_message', array(
						'ajaxurl' => TOURMASTER_AJAX_URL,
						'error_head' => esc_html__('An error occurs', 'tourmaster'),
						'error_message' => esc_html__('Please refresh the page to try again. If the problem still persists, please contact administrator for this.', 'tourmaster'),
						'nonce' => wp_create_nonce('tourmaster-plugin-option-nonce')
					));					
				}
			}

			function register_admin_menu(){
				add_menu_page(
					$this->settings['page-title'], 
					$this->settings['menu-title'], 
					$this->settings['capability'], 
					$this->settings['slug'],
					array(&$this, 'create_plugin_option'),
					$this->settings['icon-url'], 
					$this->settings['position']
				);
			}

			function create_plugin_option(){
				
				// decide the active theme option tab
				if( isset($_GET['nav_order']) ){
					$nav_active = trim($_GET['nav_order']);
				}
				
				if( empty($nav_active) || empty($this->theme_options[$nav_active]) ){
					reset($this->theme_options);
					$nav_active = key($this->theme_options);
				}

				// if import variable is set
				$this->theme_option_import();
				
				echo '<div class="tourmaster-admin-option-wrapper" ' . tourmaster_esc_style(array('width'=> $this->settings['container-width'])) . ' >';
				$this->create_plugin_option_head($nav_active);
				
				$this->create_plugin_option_body($nav_active);
				echo '</div>'; // tourmaster-admin-option-wrapper

			}
			
			///////////////////////
			// theme option html
			///////////////////////
			function get_plugin_option_breadcrumbs($nav_active = ''){
				$ret = '';
				if( $nav_active === '' ){
					$ret .= '<span class="tourmaster-admin-option-head-breadcrumbs-nav" >' . esc_html__('Search', 'tourmaster') . '</span>';
				}else{
					$first_sub_nav = reset($this->theme_options[$nav_active]['options']);
					$ret .= '<span class="tourmaster-admin-option-head-breadcrumbs-nav" >' . $this->theme_options[$nav_active]['title'] . '</span>';
					$ret .= '<i class="tourmaster-admin-option-head-breadcrumbs-sep fa fa-angle-right" ></i>';
					$ret .= '<span class="tourmaster-admin-option-head-breadcrumbs-subnav" >' . $first_sub_nav['title'] . '</span>';
				}
				return $ret;
			}
			function create_plugin_option_head($nav_active){
				echo '<div class="tourmaster-admin-option-head">';
				
				// head nav area
				echo '<div class="tourmaster-admin-option-head-nav">';
				
				// logo
				echo '<div class="tourmaster-admin-option-logo tourmaster-admin-option-left-column tourmaster-media-image">';
				echo '<img src="' . esc_url(TOURMASTER_URL . '/framework/images/plugin-option-logo.png') . '" alt="admin-option-logo" />';
				echo '</div>';				
				
				// navigation item
				echo '<div class="tourmaster-admin-option-nav tourmaster-admin-option-right-column" id="tourmaster-admin-option-nav">';
				echo '<div class="tourmaster-admin-option-nav-slides" id="tourmaster-admin-option-nav-slides"></div>';
				
				foreach( $this->theme_options as $nav_order => $theme_option ){
					$nav_item_class  = 'admin-option-nav-item-' . $theme_option['slug'];
					$nav_item_class .= ($nav_active == $nav_order)? ' tourmaster-active': '';
					
					echo '<div class="tourmaster-admin-option-nav-item ' . esc_attr($nav_item_class) . '" data-nav-order="' . esc_attr($nav_order) . '" >';
					if( !empty($theme_option['icon']) ){
						echo '<div class="tourmaster-admin-option-nav-item-icon tourmaster-media-image">';
						echo '<img src="' . esc_url($theme_option['icon']) . '" alt="nav-icon" />';
						echo '</div>';
					}
					if( !empty($theme_option['title']) ){
						echo '<div class="tourmaster-admin-option-nav-item-title">' . $theme_option['title'] . '</div>';
					}
					echo '</div>'; // tourmaster-admin-option-nav-item
				}
				
				echo '<div class="clear"></div>';
				echo '</div>'; // tourmaster-admin-option-nav
				
				// save button
				echo '<div class="tourmaster-admin-option-save-button" id="tourmaster-admin-option-save-button" >' . esc_html__('Save Options', 'tourmaster') . '</div>';
				echo '<div class="clear"></div>';
				echo '</div>'; // tourmaster-admin-option-head-nav
				
				// header sub area
				echo '<div class="tourmaster-admin-option-head-sub">';
				
				// bread crumbs
				echo '<div class="tourmaster-admin-option-head-breadcrumbs" id="tourmaster-admin-option-head-breadcrumbs" >';
				echo $this->get_plugin_option_breadcrumbs($nav_active);
				echo '</div>';
				
				// search section
				echo '<div class="tourmaster-admin-option-head-search" >';
				echo '<input type="text" class="tourmaster-admin-option-head-search-text" id="tourmaster-admin-option-head-search-text" placeholder="' . esc_html__('Search Options', 'tourmaster') . '" />';
				echo '<input type="button" class="tourmaster-admin-option-head-search-button" id="tourmaster-admin-option-head-search-button" data-blank-keyword="' . esc_html__('Please fill keywords to search', 'tourmaster') . '" />';
				echo '</div>'; // tourmaster-admin-option-head-search
				
				echo '<div class="clear"></div>';
				echo '</div>'; // tourmaster-admin-option-head-sub
				
				echo '</div>'; // tourmaster-admin-option-head	
				
			}
			
			// for creating the theme option body section
			function get_plugin_option_subnav($nav_active, $subnav_active = ''){
				$ret = ''; $count = 0;
				if( empty($subnav_active) ){
					reset($this->theme_options[$nav_active]['options']);
					$subnav_active = key($this->theme_options[$nav_active]['options']);
				}
			
				foreach( $this->theme_options[$nav_active]['options'] as $slug => $subnav ){ $count++;
					$subnav_item_class  = 'tourmaster-admin-option-subnav-item';
					$subnav_item_class .= ($slug == $subnav_active)? ' tourmaster-active':'';
					
					$ret .= '<div class="' . esc_attr($subnav_item_class) . '" data-subnav-slug="' . esc_attr($slug) . '" >' . $subnav['title'] . '</div>';
				}		
				return $ret;
			}			
			function get_plugin_option_section_content($nav_active, $subnav_active = ''){
				$theme_option_val = get_option($this->theme_options[$nav_active]['slug'], array());
				
				$ret = ''; $count = 0;
				
				if( empty($subnav_active) ){
					reset($this->theme_options[$nav_active]['options']);
					$subnav_active = key($this->theme_options[$nav_active]['options']);
				}
				
				foreach( $this->theme_options[$nav_active]['options'] as $slug => $subnav ){ $count++;
					$ret .= '<div class="tourmaster-admin-option-section tourmaster-condition-wrapper ' . (($count == 1)? 'tourmaster-active': '') . '" data-section-slug="' . esc_attr($slug) . '" >';
					foreach( $subnav['options'] as $option_slug => $option ){
						$option['slug'] = $option_slug;
						if( isset($theme_option_val[$option_slug]) ){
							$option['value'] = $theme_option_val[$option_slug];
						}
							
						$ret .= tourmaster_html_option::get_element($option);
					}
					$ret .= '</div>'; // tourmaster-admin-option-section
				}
				
				$ret .= '<div class="tourmaster-admin-option-body-content-save" >';
				$ret .= '<div class="tourmaster-admin-option-save-button" >' . esc_html__('Save Options', 'tourmaster') . '</div>';
				$ret .= '</div>';
				
				return $ret;
			}
			function create_plugin_option_body($nav_active){
				
				echo '<div class="tourmaster-admin-option-body">';
				
				// body nav
				echo '<div class="tourmaster-admin-option-subnav tourmaster-admin-option-left-column" id="tourmaster-admin-option-subnav" >';
				echo $this->get_plugin_option_subnav($nav_active);
				echo '</div>'; // tourmaster-admin-option-subnav
					
				// body content
				echo '<div class="tourmaster-admin-option-body-content tourmaster-admin-option-right-column" id="tourmaster-admin-option-body-content" >';
				echo $this->get_plugin_option_section_content($nav_active);		
				echo '</div>'; // tourmaster-admin-option-body-nav
				
				echo '<div class="clear"></div>';
				echo '</div>'; // tourmaster-admin-option-body
				
			}
			
			///////////////////////
			// save action
			///////////////////////
			
			// save the option
			function save_theme_option(){
				// die(json_encode($_POST));
				$theme_options_val = array();
				foreach( $_POST['option'] as $option_key => $option_value ){
					if( ($nav_order = $this->get_option_nav_order($option_key)) !== false ){
						$option_slug = $this->theme_options[$nav_order]['slug'];
						if( empty($theme_options_val[$option_slug]) ){
							$theme_options_val[$option_slug] = get_option($option_slug, array());
						}
						
						// assign values
						$theme_options_val[$option_slug][$option_key] = tourmaster_process_post_data($option_value);
					}
				}
				
				// save action
				foreach($theme_options_val as $option_slug => $option_value){
					update_option($option_slug, $option_value);
				}

				if( $this->settings['filewrite'] ){
					return $this->after_save_plugin_option();
				}else{
					return true;
				}
			}			
			
			// write data
			function after_save_plugin_option(){
				do_action('tourmaster_after_save_plugin_option');

				if( empty($this->settings['filewrite']) ){
					return true;
				}

				$data = apply_filters('tourmaster_plugin_option_top_file_write', '');

				foreach( $this->theme_options as $nav => $theme_option ){ // main nav
					$theme_option_val = get_option($theme_option['slug'], array());
					foreach( $theme_option['options'] as $options ){ // sub nav
						foreach( $options['options'] as $option_slug => $option ){ // content	

							if( empty($option['selector']) ) continue; 

							if( !empty($theme_option_val[$option_slug]) || (isset($theme_option_val[$option_slug]) && $theme_option_val[$option_slug] === '0') ){

								if( empty($option['data-type']) ){
									$option['data-type'] = 'color';
								}else if( $option['data-type'] == 'rgba' ){
									// replace the rgba first
									$value = tourmaster_format_datatype($theme_option_val[$option_slug], 'rgba');
									$option['selector'] = str_replace('#gdlra#', $value, $option['selector']);
									
									$option['data-type'] = 'color';
								}
								$value = tourmaster_format_datatype($theme_option_val[$option_slug], $option['data-type']);

								// for secondary selector
								if( !empty($option['selector-extra']) ){ 

									while( $start_extra = strpos($option['selector'], '<') ){
										$end_extra = strpos($option['selector'], '>');
										$end_alpha = strpos($option['selector'], '>a');
										$end_text = strpos($option['selector'], '>t');

										if( $start_extra !== false && $end_extra !== false ){
											$custom_slug = substr($option['selector'], ($start_extra + 1), ($end_extra - $start_extra - 1));
											
											if( $end_alpha !== false ){
												$custom_value = tourmaster_format_datatype($theme_option_val[$custom_slug], 'rgba');
												$option['selector'] = str_replace('<' . $custom_slug . '>a', $custom_value, $option['selector']);
											}else if( $end_text !== false ){
												$custom_value = tourmaster_format_datatype($theme_option_val[$custom_slug], 'text');
												$option['selector'] = str_replace('<' . $custom_slug . '>t', $custom_value, $option['selector']);
											}else{
												$custom_value = tourmaster_format_datatype($theme_option_val[$custom_slug], $option['data-type']);
												$option['selector'] = str_replace('<' . $custom_slug . '>', $custom_value, $option['selector']);
											}
										}
									}
								}

								$data .= str_replace('#gdlr#', $value, $option['selector']) . " \n";
							}
						}
					}
				}

				// for custom value
				$data .= apply_filters('tourmaster_plugin_option_bottom_file_write', '');
				
				$fs = new tourmaster_file_system();
				return $fs->write($this->settings['filewrite'], $data);
			}
			
			///////////////////////
			// ajax call
			///////////////////////
			function get_option_nav_order($option_key){
				foreach( $this->theme_options as $nav_order => $nav_options ){
					foreach( $nav_options['options'] as $key => $options ){
						if( !empty($options['options'][$option_key]) ){
							return $nav_order;
						}
					}
				}
				return false;
			}
			function save_plugin_option_ajax(){

				if( !check_ajax_referer('tourmaster-plugin-option-nonce', 'security', false) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('Invalid Nonce', 'tourmaster'),
						'message'=> esc_html__('Please refresh the page and try again.' ,'tourmaster')
					)));
				}			
				
				if( empty($_POST['option']) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('An Error Occurs', 'tourmaster'),
						'message' => esc_html__('No variable for saving process, please refresh the page to try again.', 'tourmaster')
					)));
				}else{
					$status = $this->save_theme_option();
					
					if( $status === true ){
						die(json_encode(array(
							'status' => 'success',
							'head' => esc_html__('Options Saved!', 'tourmaster')
						)));
					}else{
						die(json_encode($status));
					}
				}
			}			
			
			function get_plugin_option_tab(){

				if( !check_ajax_referer('tourmaster-plugin-option-nonce', 'security', false) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('Invalid Nonce', 'tourmaster'),
						'message'=> esc_html__('Please refresh the page and try again.' ,'tourmaster')
					)));
				}			
				
				$nav_order = empty($_POST['nav_order'])? '': trim($_POST['nav_order']);
				if( empty($this->theme_options[$nav_order]) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('An Error Occurs', 'tourmaster'),
						'message' => esc_html__('Unable to obtain the tab variable, please refresh the page to try again.', 'tourmaster')
					)));
				}else{
					
					if( !empty($_POST['option']) ){
						$this->save_theme_option();
					}
					
					$subnav_active = empty($_POST['subnav_order'])? '': trim($_POST['subnav_order']);
					die(json_encode(array(
						'status' => 'success',
						'breadcrumbs' => $this->get_plugin_option_breadcrumbs($nav_order, $subnav_active),
						'subnav' => $this->get_plugin_option_subnav($nav_order, $subnav_active),
						'content' => $this->get_plugin_option_section_content($nav_order, $subnav_active)
					)));
				}
			}
			
			function get_plugin_option_search_content($keyword = ''){
				if( empty($keyword) ) return '';
				$count = 0;
				
				$ret  = '<div class="tourmaster-admin-option-section tourmaster-active" >';
				foreach( $this->theme_options as $nav =>$theme_option ){ // main nav
					$theme_option_val = get_option($theme_option['slug'], array());
					foreach( $theme_option['options'] as $options ){ // sub nav
						foreach( $options['options'] as $option_slug => $option ){ // content
							if( stripos($option_slug, $keyword) !== false || stripos($option['title'], $keyword) !== false  ){
								$count++;
								
								$option['slug'] = $option_slug;
								if( isset($theme_option_val[$option_slug]) ){
									$option['value'] = $theme_option_val[$option_slug];
								}

								$ret .= tourmaster_html_option::get_element($option);
							}
						}
					}
				}
				
				if( $count == 0 ){
					$ret .= '<div class="tourmaster-admin-option-search-not-found">';
					$ret .= '<div class="tourmaster-head">' . esc_html__('No results match the keyword', 'tourmaster') . ' "' . esc_html($keyword) . '"</div>';
					$ret .= '<div class="tourmaster-tail">' . esc_html__('Please try again.', 'tourmaster') . '</div>';
					$ret .= '</div>';
				}
				$ret .= '</div>'; // tourmaster-admin-option-section
				
				if( $count > 0 ){
					$ret .= '<div class="tourmaster-admin-option-body-content-save" >';
					$ret .= '<div class="tourmaster-admin-option-save-button" >' . esc_html__('Save Options', 'tourmaster') . '</div>';
					$ret .= '</div>';
				}
				
				return $ret;				
			}
			function get_plugin_option_search(){

				if( !check_ajax_referer('tourmaster-plugin-option-nonce', 'security', false) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('Invalid Nonce', 'tourmaster'),
						'message'=> esc_html__('Please refresh the page and try again.' ,'tourmaster')
					)));
				}			

				if( empty($_POST['keyword']) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('An Error Occurs', 'tourmaster'),
						'message' => esc_html__('Unable to obtain the tab variable, please refresh the page to try again.', 'tourmaster')
					)));
				}else{
					
					if( !empty($_POST['option']) ){
						$this->save_theme_option();
					}
					
					die(json_encode(array(
						'status' => 'success',
						'breadcrumbs' => $this->get_plugin_option_breadcrumbs(),
						'subnav' => '',
						'content' => $this->get_plugin_option_search_content(trim($_POST['keyword']))
					)));
				}
			}

			//////////////////
			// import export
			//////////////////

			function theme_option_import(){
				
				if( !empty($_FILES['tourmaster-import']['tmp_name']) ){

					$fs = new tourmaster_file_system();
					$import_options = $fs->read($_FILES['tourmaster-import']['tmp_name']);
					$import_options = json_decode($import_options, true);
					if( is_array($import_options) ){
						foreach( $import_options as $option_slug => $option ){
							update_option($option_slug, $option);
						}
							
						if( $this->settings['filewrite'] ){
							$this->after_save_plugin_option();
						}
					}

				}

			}
			function plugin_option_export(){
				if( !check_ajax_referer('tourmaster_html_option', 'security', false) ){
					die(json_encode(array(
						'status' => 'failed',
						'head' => esc_html__('Invalid Nonce', 'tourmaster'),
						'message'=> esc_html__('Please refresh the page and try again.' ,'tourmaster')
					)));
				}

				
				if( empty($_POST['options']) || $_POST['options'] == 'all' ){
					$content = array();
					foreach( $this->theme_options as $theme_option ){
						$content[$theme_option['slug']] = get_option($theme_option['slug'], array());
					}
					$filename = 'plugin-options.json';
				}else{
					$content = array(
						$_POST['options'] => get_option($_POST['options'], array())
					);
					$filename = $_POST['options'] . '.json';
				}

				$fs = new tourmaster_file_system();
				$fs_status = $fs->write(TOURMASTER_LOCAL . '/js/admin-option.json', json_encode($content));

				if( $fs_status === true ){
					die(json_encode(array(
						'status' => 'success',
						'url' => TOURMASTER_URL . '/js/admin-option.json',
						'filename' => $filename
					)));
				}else{
					die(json_encode(array(
						'status' => 'success-2',
						'content' => json_encode($content),
						'filename' => $filename
						
					)));
				}
			}

		} // tourmaster_admin_option
		
	} // class_exists

