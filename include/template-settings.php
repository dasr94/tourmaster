<?php
	/*	
	*	Tourmaster Plugin
	*	---------------------------------------------------------------------
	*	choosing template
	*	---------------------------------------------------------------------
	*/

	add_filter('template_include', 'tourmaster_template_registration', 9999);
	if( !function_exists('tourmaster_template_registration') ){
		function tourmaster_template_registration( $template ){

			global $tourmaster_template, $sitepress;
			$tourmaster_template = false;
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');

			// archive template
			if( is_tax('tour_category') || is_tax('tour_tag') || tourmaster_is_custom_tour_tax() ){
				$tourmaster_template = 'archive';
				$template = TOURMASTER_LOCAL . '/single/archive.php';

			// search template
			}else if( isset($_GET['tour-search']) ){
				$tourmaster_template = 'search';
				$template = TOURMASTER_LOCAL . '/single/search.php';		
			}else{

				// for search page
				$search_template = tourmaster_get_option('general', 'search-page', '');
				if( !empty($search_template) ){
					if( !empty($sitepress) ){
						$trid = $sitepress->get_element_trid($search_template, 'post_page');
						$translations = $sitepress->get_element_translations($trid,'post_page');

						$search_template = array();
						foreach( $translations as $translation ){
							$search_template[] = $translation->element_id;
						}
					}else if( function_exists('pll_get_post_translations') ){
						$pll_translations = pll_get_post_translations($search_template);
						$search_template = array();
						foreach( $pll_translations as $translation ){
							$search_template[] = $translation; 
						}
					}else{
						$search_template = array($search_template);
					}

					if( is_page() && in_array(get_the_ID(), $search_template) ){
						$tourmaster_template = 'search';
						$template = TOURMASTER_LOCAL . '/single/search.php';
					}
				}

				// for guide page
				$guide_template = tourmaster_get_option('general', 'guide-page', '');
				if( !empty($guide_template) ){
					if( !empty($sitepress) ){
						$trid = $sitepress->get_element_trid($guide_template, 'post_page');
						$translations = $sitepress->get_element_translations($trid,'post_page');

						$guide_template = array();
						foreach( $translations as $translation ){
							$guide_template[] = $translation->element_id;
						}
					}else if( function_exists('pll_get_post_translations') ){
						$pll_translations = pll_get_post_translations($guide_template);
						$guide_template = array();
						foreach( $pll_translations as $translation ){
							$guide_template[] = $translation; 
						}
					}else{
						$guide_template = array($guide_template);
					}

					if( is_page() && in_array(get_the_ID(), $guide_template) ){
						$tourmaster_template = 'guides';
						$template = TOURMASTER_LOCAL . '/single/guides.php';
					}
				}

				// for user page
				if( $enable_membership == 'enable' ){
					$user_template = tourmaster_get_option('general', 'user-page', '');
					if( empty($user_template) ){
						if( is_front_page() && isset($_GET['tourmaster-user']) ){
							$tourmaster_template = 'user';
							$template = TOURMASTER_LOCAL . '/single/user.php';
						}
					}else{
						if( !empty($sitepress) ){
							$trid = $sitepress->get_element_trid($user_template, 'post_page');
							$translations = $sitepress->get_element_translations($trid,'post_page');

							$user_template = array();
							foreach( $translations as $translation ){
								$user_template[] = $translation->element_id;
							}
						}else{
							$user_template = array($user_template);
						}

						if( is_page() && in_array(get_the_ID(), $user_template) ){
							$tourmaster_template = 'user';
							$template = TOURMASTER_LOCAL . '/single/user.php';
						}
					}
				}

				// for login page
				if( $enable_membership == 'enable' ){
					$login_template = tourmaster_get_option('general', 'login-page', '');
					if( empty($login_template) ){
						if( is_front_page() && isset($_GET['tourmaster-login']) ){
							$tourmaster_template = 'login';
							$template = TOURMASTER_LOCAL . '/single/login.php';
						}
					}else{
						if( !empty($sitepress) ){
							$trid = $sitepress->get_element_trid($login_template, 'post_page');
							$translations = $sitepress->get_element_translations($trid,'post_page');

							$login_template = array();
							foreach( $translations as $translation ){
								$login_template[] = $translation->element_id;
							}
						}else{
							$login_template = array($login_template);
						}

						if( is_page() && in_array(get_the_ID(), $login_template) ){
							$tourmaster_template = 'login';
							$template = TOURMASTER_LOCAL . '/single/login.php';
						}
					}
				}

				// for registration page
				if( $enable_membership == 'enable' ){
					$register_template = tourmaster_get_option('general', 'register-page', '');
					if( empty($register_template) ){
						if( is_front_page() && isset($_GET['tourmaster-register']) ){
							$tourmaster_template = 'register';
							$template = TOURMASTER_LOCAL . '/single/register.php';
						}
					}else{
						if( !empty($sitepress) ){
							$trid = $sitepress->get_element_trid($register_template, 'post_page');
							$translations = $sitepress->get_element_translations($trid,'post_page');

							$register_template = array();
							foreach( $translations as $translation ){
								$register_template[] = $translation->element_id;
							}
						}else{
							$register_template = array($register_template);
						}
						
						if( is_page() && in_array(get_the_ID(), $register_template) ){
							$tourmaster_template = 'register';
							$template = TOURMASTER_LOCAL . '/single/register.php';
						}
					}
				}

				// for payment page
				$payment_template = tourmaster_get_option('general', 'payment-page', '');
				if( empty($payment_template) ){
					if( is_front_page() && isset($_GET['tourmaster-payment']) ){
						$tourmaster_template = 'payment';
						$template = TOURMASTER_LOCAL . '/single/payment.php';
					}
				}else{
					if( !empty($sitepress) ){
						$trid = $sitepress->get_element_trid($payment_template, 'post_page');
						$translations = $sitepress->get_element_translations($trid,'post_page');

						$payment_template = array();
						foreach( $translations as $translation ){
							$payment_template[] = $translation->element_id;
						}
					}else{
						$payment_template = array($payment_template);
					}
					
					if( is_page() && in_array(get_the_ID(), $payment_template) ){
						$tourmaster_template = 'payment';
						$template = TOURMASTER_LOCAL . '/single/payment.php';
					}
				}

			}


			// check if is authorize for that template
			if( $tourmaster_template == 'user' && !is_user_logged_in() ){
				wp_redirect(tourmaster_get_template_url('login'));
				exit;
			}else if( ($tourmaster_template == 'login' || $tourmaster_template == 'register') && is_user_logged_in() ){
				wp_redirect(tourmaster_get_template_url('user'));
				exit;
			}

			if( $tourmaster_template == 'payment' ){
				do_action('goodlayers_payment_page_init');
			}
			return $template;
		} // tourmaster_template_registration
	} // function_exists

	if( !function_exists('tourmaster_get_template_url') ){
		function tourmaster_get_template_url( $type, $args = array() ){
			
			$base_url = '';
			if( function_exists('pll_home_url') ){
				$base_url = pll_home_url();
			}else{
				$base_url = home_url('/');
			}
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');

			// login url
			if( $type == 'login' ){

				if( $enable_membership == 'enable' ){
					$login_template = tourmaster_get_option('general', 'login-page', '');
					if( empty($login_template) ){
						$args['tourmaster-login'] = '';
					}else{
						$base_url = get_permalink($login_template);
						if( !empty($_GET['lang']) ){
							$base_url = apply_filters('wpml_permalink', $base_url , $_GET['lang']);
						}
					}
				}
				
			// register url
			}else if( $type == 'register' ){

				if( $enable_membership == 'enable' ){
					$register_template = tourmaster_get_option('general', 'register-page', '');
					if( empty($register_template) ){
						$args['tourmaster-register'] = '';
					}else{
						$base_url = get_permalink($register_template);
					}
				}

			// author url
			}else if( $type == 'user' ){

				if( $enable_membership == 'enable' ){
					$user_template = tourmaster_get_option('general', 'user-page', '');
					if( empty($user_template) ){
						$args['tourmaster-user'] = '';
					}else{
						$base_url = get_permalink($user_template);
					}
				}

			}else if( $type == 'payment' ){

				$payment_template = tourmaster_get_option('general', 'payment-page', '');
				if( empty($payment_template) ){
					$args['tourmaster-payment'] = '';
				}else{
					$base_url = get_permalink($payment_template);
				}

			}else if( $type == 'search' ){

				$search_template = tourmaster_get_option('general', 'search-page', '');
				if( !empty($search_template) ){
					$base_url = get_permalink($search_template);
				}
				
			}

			if( !empty($base_url) ){
				return add_query_arg($args, $base_url);
			}

			return false;

		} // tourmaster_get_template_url
	} // function_exists

	// add class for each plugin's template 
	add_filter('body_class', 'tourmaster_template_class');
	if( !function_exists('tourmaster_template_class') ){
		function tourmaster_template_class( $classes ){

			global $tourmaster_template;
			if( !empty($tourmaster_template) ){
				$classes[] = 'tourmaster-template-' . $tourmaster_template;
			}
			return $classes;

		}
	}

	/***********************************
	** 	Login / Lost Password Section
	**  source = tm
	************************************/

	// for redirecting the login incorrect
	add_filter('authenticate', 'tourmaster_login_error_redirect', 9999, 3);
	if( !function_exists('tourmaster_login_error_redirect') ){
		function tourmaster_login_error_redirect( $user, $username, $password ){
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');
			if( $enable_membership == 'disable' ){
				return $user;
			}

			if( !empty($_POST['source']) && $_POST['source'] == 'tm' ){
				$query_arg = array();
				if( !empty($_POST['redirect']) ){
					$query_arg['redirect'] = $_POST['redirect'];
				}

				if( empty($username) || empty($password) ){
					$query_arg['status'] = 'login_empty';
					$redirect_template = add_query_arg($query_arg, tourmaster_get_template_url('login'));
					wp_redirect($redirect_template);
					exit();
				}else if( $user == null || is_wp_error($user) ){
					$query_arg['status'] = 'login_incorrect';
					$redirect_template = add_query_arg($query_arg, tourmaster_get_template_url('login'));
					wp_redirect($redirect_template);
					exit();
				}
			}

			return $user;
		} // tourmaster_login_error_redirect
	}

	// for lost password page
	add_action('lost_password', 'tourmaster_lost_password_redirect', 1);
	if( !function_exists('tourmaster_lost_password_redirect') ){
		function tourmaster_lost_password_redirect(){
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');
			if( $enable_membership == 'disable' ){
				return;
			}

			if( !empty($_GET['source']) && $_GET['source'] == 'tm' ){
				$redirect_template = add_query_arg(array('action'=>'lostpassword'), tourmaster_get_template_url('login'));
				wp_redirect($redirect_template);
				exit();
			}
		} // tourmaster_lost_password_redirect
	}

	// lost password info incorrect
	add_action('login_form_lostpassword', 'tourmaster_lost_password_error_redirect', 1);
	if( !function_exists('tourmaster_lost_password_error_redirect') ){
		function tourmaster_lost_password_error_redirect( $errors ){
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');
			if( $enable_membership == 'disable' ){
				return;
			}

			if( !empty($_POST['source']) && $_POST['source'] == 'tm' ){
				$user_data = null;
				if( !empty($_POST['user_login']) ){
					// check if it's email
					if( strpos($_POST['user_login'], '@') ){
						$user_data = get_user_by('email', trim(wp_unslash($_POST['user_login'])));
					// check if it's user	
					}else{
						$user_data = get_user_by('login', trim($_POST['user_login']));
					}
				}

				if( empty($user_data) ){
					$redirect_template = add_query_arg(array('action'=>'lostpassword', 'status'=>'login_incorrect'), tourmaster_get_template_url('login'));
					wp_redirect($redirect_template);
					exit();
				}
			}
		} // tourmaster_lost_password_error_redirect
	}

	// modify lost password email
	add_filter('retrieve_password_message', 'tourmaster_retrieve_password_message', 9999, 4);
	if( !function_exists('tourmaster_retrieve_password_message') ){
		function tourmaster_retrieve_password_message( $message, $key, $user_login, $user_data ){
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');
			if( $enable_membership == 'disable' ){
				return $message;
			}

			if( !empty($_POST['source']) && $_POST['source'] == 'tm' ){
				$variable_location = strpos($message, 'action=rp&');
				$new_message = substr($message, 0, $variable_location) . 'source=tm&' . substr($message, $variable_location);
				$message = $new_message;
			}
			return $message;
		} // tourmaster_retrieve_password_message
	}

	// redirect to reset password page
	add_action('login_form_rp', 'tourmaster_login_form_rp_redirect', 9999);
	add_action('login_form_resetpass', 'tourmaster_login_form_rp_redirect');
	if( !function_exists('tourmaster_login_form_rp_redirect') ){
		function tourmaster_login_form_rp_redirect(){
			$enable_membership = tourmaster_get_option('general', 'enable-membership', 'enable');
			if( $enable_membership == 'disable' ){
				return;
			}

			if( !empty($_GET['source']) && $_GET['source'] == 'tm' ){
				$redirect_template = add_query_arg($_GET, tourmaster_get_template_url('login'));
				wp_redirect($redirect_template);
				exit();
			} // tourmaster_login_form_rp_redirect
		}
	}