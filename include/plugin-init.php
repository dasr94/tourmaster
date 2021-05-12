<?php
	/*	
	*	Tourmaster Plugin
	*/

	if( !function_exists('tourmaster_plugin_activation') ){
		function tourmaster_plugin_activation(){

			// check previous plugin version
			$current_version = 3.03;
			$prev_version = floatval(get_option('tourmaster-plugin-version', 0));

			tourmaster_table_init();

			if( $prev_version < 3.001 ){

				// init the variable if activating the plugin for the first time
				$import_options = array('tourmaster_general', 'tourmaster_payment', 'tourmaster_color', 'tourmaster_plugin');
				$default_val = '{"tourmaster_general":{"container-width":"1180px","container-padding":"20px","item-padding":"20px","datepicker-date-format":"d M yy","money-format":"$NUMBER","tax-rate":"9","login-page":"4852","register-page":"4854","user-page":"4833","user-navigation-bottom-text":"<h5>Need Help?<\/5>\n[gdlr_core_space height=\"1px\"]\n[gdlr_core_icon icon=\"fa fa-phone\" size=\"18px\" color=\"#444\" margin-right=\"10px\" ] <span style=\"font-size: 15px; font-weight: 400;\"> 1.828.456.345\n<a href=\"#\">Help@traveltourwp.com<\/a>","payment-page":"","payment-complete-bottom-text":"","search-page":"4858","search-page-num-fetch":"6","search-page-tour-style":"medium-with-frame","search-page-with-frame":"enable","search-page-column-size":"20","search-page-thumbnail-size":"Tour Category","search-page-tour-info":["duration-text","availability"],"search-page-excerpt":"specify-number","search-page-excerpt-number":"14","search-page-tour-rating":"enable","invoice-logo":"4890","invoice-logo-width":"250px","invoice-company-name":"GoodLayers Travel Tour","invoice-company-info":"11 Main Street, Kingston, London 22EPH","text":"","textarea":"","combobox":"1","multi-combobox":"","radioimage":"1","checkbox":"disable","tour-header-top-padding":"500px","tour-header-bottom-padding":"45px","single-tour-default-sidebar":"single-tour","payment-page-sidebar":"payment","archive-page":"","search-sidebar":"right","search-sidebar-left":"none","search-sidebar-right":"single-tour","system-email-address":"","admin-email-address":"","mail-header-logo":"4925","mail-footer-left":"<span style=\"font-weight: bold\" >Need help?<\/span>\n<a href=\"mailto:help@traveltourwptheme.com\">help@traveltourwptheme.com<\/a>\n1.828.344.234","mail-footer-right":"<span><\/span>\nLogin to <a href=\"http:\/\/demo.goodlayers.com\/traveltour\/login\">your account<\/a>\nCopyright \u00a9 2017, GoodLayers","enable-admin-booking-made-mail":"enable","admin-booking-made-mail-title":"A new booking has been made","admin-booking-made-mail":"<strong>Dear Admin,<\/strong>\nA new booking form {customer-name} has been made.\n\n{tour-name}\n{order-number}\n{travel-date}\n\nCustomer\'s Note: {customer-note}\n{spaces}\n\nYou can view <a href=\"{admin-transaction-link}\">the transaction here<\/a>","enable-admin-payment-submitted-mail":"enable","admin-payment-submitted-mail-title":"A new payment receipt has been submitted","admin-payment-submitted-mail":"<strong>Dear Admin,<\/strong>\nA new payment receipt has been submitted\n\n{tour-name}\n{order-number}\n{travel-date}\n{spaces}\n\nYou can view <a href=\"{admin-transaction-link}\">the transaction here<\/a>","enable-admin-online-payment-made-mail":"enable","admin-online-payment-made-mail-title":"A new booking has been made and successfully paid","admin-online-payment-made-mail":"<strong>Dear Admin,<\/strong>\nA new booking has been made and sucessfully paid.\n\n{payment-method}\n{payment-date}\n{transaction-id}\n{spaces}\n\n{tour-name}\n{order-number}\n{travel-date}\n\nCustomer\'s Note: {customer-note}\n{spaces}\n\nYou can view <a href=\"{admin-transaction-link}\">the transaction here<\/a>","enable-registration-complete-mail":"enable","registration-complete-mail-title":"Congratulations! Your account has been created","registration-complete-mail":"{header}Congratulations {customer-name}!{\/header}\n\nYour account has been successfully created. Now you can explore new tours and mange booking via the dashboard. You can also review tours and make a wishlist from the dashboard as well.\n\n<a href=\"{profile-page-link}\" >Click here to login to travel tour<\/a>","enable-booking-made-mail":"enable","booking-made-mail-title":"You have made a new booking","booking-made-mail":"<strong>Dear {customer-name}<\/strong>,\nYou have made a booking on\n\n{tour-name}\n{order-number}\n{travel-date}\n{total-price}\nCustomer\'s Note: {customer-note}\n\n<a href=\"{payment-link}\" >Make a payment<\/a>\n<a href=\"{invoice-link}\" >View Invoice<\/a>\n{divider}\nIf you wish to do the bank transfer. Please use the information below.\n\n<strong>Bank name: Center London Bank<\/strong>\n<strong>Account Number: 4455-4445-333<\/strong>\n<strong>Swift Code: XXCCVV<\/strong>\n\nAfter transferring, please submit payment receipt from your dashboard. We\'ll get back to you when the submission verified.","enable-payment-made-mail":"enable","payment-made-mail-title":"Your payment has been successfully processed","payment-made-mail":"<strong>Dear {customer-name}<\/strong>,\nCongratulations! Your payment has been sucessfully processed.\n\n{tour-name}\n{order-number}\n{travel-date}\n{total-price}\nCustomer\'s Note: {customer-note}\n\n{payment-method}\n{payment-date}\n{transaction-id}\n{spaces}\n\nYou can view <a href=\"{invoice-link}\">the receipt here<\/a>","enable-booking-cancelled-mail":"enable","booking-cancelled-mail-title":"Your booking has been cancelled","booking-cancelled-mail":"<strong>Dear {customer-name}<\/strong>,\nWe are here to inform that your booking has been cancelled.\n\n{tour-name}\n{order-number}\n{travel-date}","enable-booking-reject-mail":"enable","booking-reject-mail-title":"Your booking has been rejected","booking-reject-mail":"<strong>Dear {customer-name}<\/strong>,\nWe are sorry to inform that your booking has been rejected. Your booking was rejected because of your payment was not successfully processed or your booking might be in the pending status for too long.\n\n{tour-name}\n{order-number}\n{travel-date}","enable-booking-via-email":"enable","enable-admin-guest-booking-made-mail":"enable","admin-guest-booking-made-mail-title":"A new booking has been made (Guest booked via email)","admin-guest-booking-made-mail":"<strong>Dear Admin,<\/strong> \nA new booking form {customer-name} has been made.\n\n{tour-name}\n{order-number}\n{travel-date}\n\nCustomer\'s Note: {customer-note}\n{spaces}\n\nYou can view <a href=\"{admin-transaction-link}\">the transaction here<\/a>\n{spaces}\n\nCustomer\u2019s email : {customer-email}\nPlease contact to your customer back for further details.","enable-guest-booking-made-mail":"enable","guest-booking-made-mail-title":"You have made a new booking via email","guest-booking-made-mail":"<strong>Dear {customer-name}<\/strong>,\nYou have made a booking on\n\n{tour-name}\n{order-number}\n{travel-date}\n{total-price}\nCustomer\'s Note: {customer-note}\n{divider}\nOur team will contact you back via the email you provided,\n{customer-email}","price-breakdown-decimal-digit":"2","register-term-of-service-page":"","register-privacy-statement-page":"","user-default-country":"","cancel-booking-day":"","tour-staff-capability":["edit_tour","read_tour","delete_tour","edit_tours","edit_others_tours","publish_tours","read_private_tours","manage_tour_category","manage_tour_tag","manage_tour_filter","edit_coupon","read_coupon","delete_coupon","edit_coupons","edit_others_coupons","publish_coupons","read_private_coupons","manage_tour_order","edit_service","read_service","delete_service","edit_services","edit_others_services","publish_services","read_private_services"],"tour-author-capability":["edit_tour","read_tour","delete_tour","edit_tours","edit_others_tours","publish_tours","read_private_tours","manage_tour_category","manage_tour_tag","manage_tour_filter","edit_coupon","read_coupon","delete_coupon","edit_coupons","edit_others_coupons","publish_coupons","read_private_coupons"],"tour-search-item-style":"medium-with-frame","tour-search-item-thumbnail":"Tour Category","tour-search-item-info":["duration-text","availability"],"tour-search-item-excerpt":"specify-number","tour-search-item-excerpt-number":"14","tour-search-item-rating":"enable","tour-search-order-filterer-grid-style":"grid-with-frame","tour-search-order-filterer-grid-style-thumbnail":"Blog Column Thumbnail","tour-search-order-filterer-grid-style-column":"30","enable-tour-search-filter":"enable","tour-search-fields":["keywords","tour_category","tour_tag","duration","date","min-price","max-price"],"tour-search-rating-field":"enable","tour-search-filters":"","search-not-found-fields":"","search-not-found-style":"column","show-remaining-available-number":"disable","max-dropdown-people-amount":"5","enable-single-sidebar-widget-on-mobile":"enable","enable-single-related-tour":"enable","single-related-tour-style":"grid","single-related-tour-column-size":"30","single-related-tour-num-fetch":"2","single-related-tour-thumbnail-size":"large","single-related-tour-price-position":"right-title","single-related-tour-info":"","single-related-tour-excerpt":"none","single-related-tour-excerpt-number":"20","single-related-tour-rating":"enable","system-email-name":"WORDPRESS","admin-enquiry-mail-title":"You received a new enquiry","admin-enquiry-mail-content":"Dear Admin,\n\nYou received a new enquiry from {tour-name}\n\nFrom: {full-name}\n\nEmail: {email-address} \n\nMessage: {your-enquiry}\n\n","enquiry-mail-title":"You have submitted an enquiry","enquiry-mail-content":"Dear {full-name},\n\nYou have sumiited an enquiry from {tour-name}\n\nMessage: {your-enquiry}\n\nOur team will contact you back via the email you provided, {email-address} \n\nThank you!","tour-search-item-num-fetch":"9"},"tourmaster_payment":{"stripe-secret-key":"sk_test_sfwsrkYLeTPlbQLVoqsk9Ml6","stripe-publishable-key":"pk_test_o60RnKRvMFmKQW5nemMtjmdI","stripe-currency-code":"usd","paypal-live-mode":"disable","paypal-business-email":"","paypal-currency-code":"USD","payment-method":["booking","paypal","credit-card"],"accepted-credit-card-type":["visa","master-card","american-express","jcb"],"term-of-service-page":"","privacy-statement-page":"","credit-card-payment-gateway":"stripe","paymill-private-key":"","paymill-public-key":"","paymill-currency-code":"usd","authorize-live-mode":"disable","authorize-api-id":"","authorize-transaction-key":"","enable-deposit-payment":"enable","deposit-payment-amount":"30","display-deposit-payment-day":"","paypal-service-fee":"3"},"tourmaster_color":{"tourmaster-theme-color":"#485da1","tourmaster-rating-color":"#ffa127","user-template-background":"#f3f3f3","user-template-navigation-background":"#ffffff","user-template-navigation-title":"#3f3f3f","user-template-navigation-text":"#7d7d7d","user-template-navigation-border":"#e5e5e5","user-template-breadcrumbs-text":"#a5a5a5","user-template-content-block-background":"#ffffff","user-template-content-block-title-link":"#9e9e9e","user-template-content-block-border":"#e8e8e8","user-template-content-block-text":"#545454","tourmaster-input-box-text":"#545454","tourmaster-input-box-background":"#ffffff","tourmaster-input-box-border":"#e6e6e6","tourmaster-upload-box-background":"#f3f3f3","tourmaster-upload-box-text":"#a6a6a6","tourmaster-theme-color-link":"#4674e7","tourmaster-theme-color-light":"#4692e7","tourmaster-single-price-head-background":"#4675e7","tourmaster-single-price-head-featured-background":"#2c487a","tourmaster-single-price-head-discount-text":"#b9daff","tourmaster-remove-color":"#ba4a4a","user-login-submenu-background":"#ffffff","user-login-submenu-border":"#e6e6e6","user-login-submenu-text":"#878787","user-template-my-booking-price-text":"#424242","user-template-my-booking-filter-text":"#a5a5a5","tourmaster-booking-status-text-color":"#acacac","tourmaster-booking-status-pending-color":"#24a04a","tourmaster-booking-status-online-paid":"#cd9b45","tourmaster-booking-receipt-button-background":"#48a198","tourmaster-invoice-title-color":"#121212","tourmaster-invoice-price-head-background":"#f3f3f3","tourmaster-invoice-price-head-text":"#454545","tourmaster-invoice-price-text":"#7b7b7b","tourmaster-invoice-price-amount":"#1e1e1e","tourmaster-input-form-label":"#5c5c5c","tourmaster-datepicker-background":"#f5f5f5","tourmaster-datepicker-border":"#ebebeb","tourmaster-datepicker-head":"#808080","tourmaster-datepicker-enable-background":"#ffffff","tourmaster-datepicker-enable-text":"#5b5b5b","tourmaster-datepicker-disable-text":"#c0c0c0","tourmaster-booking-bar-background":"#ffffff","tourmaster-booking-bar-text":"#333333","tourmaster-booking-bar-wishlist-background":"#fbfbfb","tourmaster-booking-bar-wishlist-border":"#ebebeb","tourmaster-booking-bar-summary-title":"#000000","tourmaster-booking-bar-summary-text":"#414141","tourmaster-price-breakdown-color":"#515151","tourmaster-price-breakdown-total-color":"#242424","single-tour-info-background":"#f5f5f5","single-tour-info-text":"#414141","tourmaster-review-title-color":"#272727","tourmaster-review-date-color":"#a3a3a3","tourmaster-payment-complete-background":"#f5f5f5","tourmaster-payment-complete-title":"#262626","tourmaster-payment-complete-border":"#e3e3e3","tourmaster-pagination-background":"#696969","tourmaster-pagination-text":"#f3f3f3","tourmaster-payment-step-icon-background":"#161616","tour-item-frame-background":"#ffffff","tour-price":"#1b1b1b","tour-discount-price":"#aaaaaa","tourmaster-lightbox-background":"#ffffff","tourmaster-lightbox-title":"#0e0e0e","tourmaster-lightbox-form-label":"#5c5c5c","tourmaster-content-navigation-background":"#ebebeb","tourmaster-content-navigation-text":"#9a9a9a","tourmaster-content-navigation-active-text":"#1b1b1b","tourmaster-search-frame-background":"#ffffff","tourmaster-single-price-head-background-right":"#4692e7","tour-item-title-color":"#333333","tour-grid-bottom-price-background":"#e7e7e7","tour-grid-bottom-price-head":"#5c5c5c","tour-grid-bottom-discount-price":"#989898","tourmaster-input-box-background-validate-error":"#fff9f9","search-page-background":"#f3f3f3","tourmaster-search-input-background":"#f3f3f3","tourmaster-search-input-border":"#f3f3f3","tourmaster-search-input-text":"#7f7f7f","tourmaster-search-input-icon":"#383838","tourmaster-tour-review-title":"#313131","tourmaster-tour-review-user-name":"#5f5f5f","tourmaster-search-input-title":"#ffffff","tourmaster-search-input-label":"#383838","tourmaster-booking-status-deposit-paid":"#5b9dd9","tourmaster-price-breakdown-total-color-deposit":"#a1a1a1","payment-service-form-background":"#f5f5f5","payment-service-form-title":"#1a1a1a","payment-service-form-title-border":"#e3e3e3","payment-service-form-label":"#6a6a6a","search-not-found-background":"#f6f6f6","search-not-found-title":"#cccccc","search-not-found-caption":"#a8a8a8","booking-bar-tab-title-background":"#f5f5f5","booking-bar-tab-title-text":"#929292","booking-bar-tab-active-title-text":"#242424","enquery-success-message-background":"#f1f8ff","enquery-success-message-border":"#e1ebfe","enquery-success-message-text":"#758ea8","enquery-failed-message-background":"#fff1f1","enquery-failed-message-border":"#fee1e1","enquery-failed-message-text":"#ffffff","urgency-message-background":"#343434","urgency-message-text":"#a87575","tour-item-order-filterer-background":"#ffffff","tour-item-order-filterer-combobox-background":"#ffffff","tour-item-order-filterer-icon":"#adadad","tourmaster-search-filter-text":"#878787"},"tourmaster_plugin":{"font-awesome":"enable","elegant-icon":"enable"}}';
				$default_options = json_decode($default_val, true);
				foreach( $import_options as $import_option ){
					$option_val = get_option($import_option, '');
					if( empty($option_val) ){
						update_option($import_option, $default_options[$import_option]);
					}
				}

				// for tourmaster version compatibility up to 3.0.0 - 1
				tourmaster_version_plugin_init();

			}

			// next version here
			if( $prev_version < 3.1 ){
				
			}

			tourmaster_set_plugin_role();

			//wp_schedule_event(time(), 'hourly', 'tourmaster_schedule_hourly');
			//wp_schedule_event(time(), 'daily', 'tourmaster_schedule_daily');

			// update the plugin version
			update_option('tourmaster-plugin-version', 	$current_version);
		}	
	}
	if( !function_exists('tourmaster_plugin_deactivation') ){
		function tourmaster_plugin_deactivation(){
			wp_clear_scheduled_hook('tourmaster_schedule_hourly');
			wp_clear_scheduled_hook('tourmaster_schedule_daily');
		}	
	}

	add_action('init', 'tourmaster_custom_schedule');
	if( !function_exists('tourmaster_custom_schedule') ){
		function tourmaster_custom_schedule(){
			$current_date = date('Y-m-d');
			$daily_schedule = get_option('tourmaster_daily_schedule', '');
			if( $daily_schedule != $current_date ){
				update_option('tourmaster_daily_schedule', $current_date);
				do_action('tourmaster_schedule_daily');
			}
			
			$current_time = date('Y-m-d H');
			$hourly_schedule = get_option('tourmaster_hourly_schedule', '');
			if( $hourly_schedule != $current_time ){
				update_option('tourmaster_hourly_schedule', $current_time);
				do_action('tourmaster_schedule_hourly');
			}
		}
	}

	if( !function_exists('tourmaster_table_init') ){
		function tourmaster_table_init(){

			// require necessary function
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			// order table
			$sql = "CREATE TABLE {$wpdb->prefix}tourmaster_order (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id bigint(20) UNSIGNED DEFAULT NULL,
				booking_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				tour_id bigint(20) UNSIGNED DEFAULT NULL,
				travel_date date DEFAULT '0000-00-00' NOT NULL,
				package_group_slug varchar(100) DEFAULT '' NOT NULL,
				traveller_amount tinyint UNSIGNED DEFAULT NULL,
				male_amount tinyint UNSIGNED DEFAULT NULL,
				female_amount tinyint UNSIGNED DEFAULT NULL,
				contact_info longtext DEFAULT NULL,
				billing_info longtext DEFAULT NULL,
				traveller_info longtext DEFAULT NULL,
				coupon_code varchar(20) DEFAULT NULL,
				order_status varchar(20) DEFAULT NULL,
				payment_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				total_price decimal(19,4) DEFAULT NULL,
				pricing_info longtext DEFAULT NULL,
				payment_info longtext DEFAULT NULL,
				booking_detail longtext DEFAULT NULL,
				PRIMARY KEY  (id)
			) {$charset_collate};";
			dbDelta($sql);

			// review table
			$sql = "CREATE TABLE {$wpdb->prefix}tourmaster_review (
				review_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				review_tour_id bigint(20) UNSIGNED NOT NULL,
				order_id bigint(20) UNSIGNED DEFAULT NULL,
				review_score tinyint DEFAULT NULL,
				review_type varchar(20) DEFAULT NULL,
				review_description longtext DEFAULT NULL,
				review_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				reviewer_name varchar(100) DEFAULT NULL,
				reviewer_email varchar(100) DEFAULT NULL,
				PRIMARY KEY  (review_id)
			) {$charset_collate};";
			dbDelta($sql);
		}
	}

	if( !function_exists('tourmaster_version_plugin_init') ){
		function tourmaster_version_plugin_init(){

			// version 2.0.0
			global $wpdb;

			$sql  = "SELECT * FROM {$wpdb->postmeta} ";
			$sql .= "WHERE meta_key = 'tourmaster-tour-option' ";
			$results = $wpdb->get_results($sql);

			foreach( $results as $result ){
				$tour_option = maybe_unserialize($result->meta_value);
				if( !empty($tour_option['tour-price-discount-text']) ){
					update_post_meta($result->post_id, 'tourmaster-tour-discount', 'true');
				}else{
					delete_post_meta($result->post_id, 'tourmaster-tour-discount');
				}
			}

			// version 3.0.0

			// calculate rating score again
			$sql  = "SELECT * FROM {$wpdb->postmeta} ";
			$sql .= "WHERE meta_key = 'tourmaster-tour-rating' ";
			$results = $wpdb->get_results($sql);

			foreach( $results as $result ){
				$rating = maybe_unserialize($result->meta_value);
				if( !empty($rating['reviewer']) ){
					$score = intval($rating['score']) / intval($rating['reviewer']);
					update_post_meta($result->post_id, 'tourmaster-tour-rating-score', $score);
				}else{
					delete_post_meta($result->post_id, 'tourmaster-tour-rating-score');
				}
			}

			// ver 3.0.0 - 1
			// move review table 
			$results = tourmaster_get_booking_data();
			foreach( $results as $result ){

				// insert review data if exists
				if( !empty($result->review_date) && $result->review_date != '0000-00-00 00:00:00' ){
					tourmaster_insert_review_data(array(
						'tour_id' => $result->tour_id,
						'score' => $result->review_score,
						'type' =>  $result->review_type,
						'description' => $result->review_description,
						'date' => $result->review_date,
						'order_id' => $result->id
					));
				}
			}

			// drop the old column out
			$wpdb->query("ALTER TABLE {$wpdb->prefix}tourmaster_order DROP COLUMN review_score");
			$wpdb->query("ALTER TABLE {$wpdb->prefix}tourmaster_order DROP COLUMN review_type");
			$wpdb->query("ALTER TABLE {$wpdb->prefix}tourmaster_order DROP COLUMN review_description");
			$wpdb->query("ALTER TABLE {$wpdb->prefix}tourmaster_order DROP COLUMN review_date");

		} // tourmaster_version_plugin_init
	}

	add_action('tourmaster_after_save_plugin_option', 'tourmaster_set_plugin_custom_role', 99);
	if( !function_exists('tourmaster_set_plugin_custom_role') ){
		function tourmaster_set_plugin_custom_role(){ 

			// role/capability
			remove_role('tour_staff'); 
			remove_role('tour_author'); 

			// for tour staff
			$staff_cap = tourmaster_get_option('general', 'tour-staff-capability', '');
			$staff_capability = array( 'read' => true );
			if( !empty($staff_cap) ){
				foreach( $staff_cap as $cap ){
					$staff_capability[$cap] = true;
				}
			}
			$staff_capability['manage_woocommerce'] = true;
 			add_role('tour_staff', esc_html__('Tour Staff', 'tourmaster'), $staff_capability);

			// for tour author
			$author_cap = tourmaster_get_option('general', 'tour-author-capability', '');
			$author_capability = array( 'read' => true );
			if( !empty($author_cap) ){
				foreach( $author_cap as $cap ){
					$author_capability[$cap] = true;
				}
			}
			$author_capability['manage_woocommerce'] = true;
			add_role('tour_author', esc_html__('Tour Author', 'tourmaster'), $author_capability);

		}
	}
	if( !function_exists('tourmaster_set_plugin_role') ){
		function tourmaster_set_plugin_role(){

			// for administrator
			$post_type_cap = array('edit_%s', 'read_%s', 'delete_%s',  
				'edit_%ss', 'edit_others_%ss', 'publish_%ss', 'read_private_%ss', 'delete_%ss');

			$admin = get_role('administrator');
			foreach( $post_type_cap as $cap ){
				$admin->add_cap(str_replace('%s', 'tour', $cap));
				$admin->add_cap(str_replace('%s', 'coupon', $cap));
				$admin->add_cap(str_replace('%s', 'service', $cap));
			}
			$admin->add_cap('manage_tour_category');
			$admin->add_cap('manage_tour_tag');
			$admin->add_cap('manage_tour_filter');
			$admin->add_cap('manage_tour_order');

			// custom role
			tourmaster_set_plugin_custom_role();

		} // tourmaster_set_plugin_role
	}