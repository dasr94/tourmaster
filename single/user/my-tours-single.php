<?php

	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-my-booking-single" >';
	tourmaster_get_user_breadcrumb();

	if( !empty($_GET['error_code']) && $_GET['error_code'] == 'cannot_upload_file' ){ 
		echo '<div class="tourmaster-notification-box tourmaster-failure" >';
		echo esc_html__('Cannot upload a media file, please try uploading it again.', 'tourmaster');
		echo '</div>';
    }
    
	$trip_id = $_GET['id'];
	$trip_post = get_post($trip_id);


	$tour_id = $trip_id;
	$ical_url = add_query_arg('download_tour_ical', $tour_id, get_permalink($tour_id));

	$header_image_options = array(
		'feature-image' => esc_html__('Feature Image', 'tourmaster'),
		'custom-image' => esc_html__('Custom Image', 'tourmaster'),
		'slider' => esc_html__('Slider', 'tourmaster'),
		'gallery' => esc_html__('Gallery', 'tourmaster'),
		'video' => esc_html__('Video ( Youtube & Vimeo )', 'tourmaster'),
		'html5-video' => esc_html__('Html5 Video', 'tourmaster'),
		'revolution-slider' => esc_html__('Revolution Slider', 'tourmaster'),
	);
	 

	if( isset($_POST['tourmaster-edit-trips']) ){
		$fields = array(
			'ID'           => $trip_id,
			'post_title'   => $_POST['post_title'],
			'post_content' => $_POST['post_content'],
			'post_status' => $_POST['post_title']
		);
		tourmaster_update_trips_field($fields);
		tourmaster_user_update_notification(esc_html__('Your trip has been successfully changed.', 'tourmaster'));
	}

	$test = new tourmaster_page_option(array(
		'post_type' => array('tour'),
		'title' => esc_html__('Trip Settings', 'tourmaster'),
		'title-icon' => 'fa fa-plane',
		'slug' => 'tourmaster-tour-option',
		'options' => apply_filters('tourmaster_tour_options', array(

			'tour-info' => array(
				'title' => esc_html__('Information', 'tourmaster'),
				'options' =>  tourmaster_get_trips_main_fields()
			),

			'tour-settings' => array(
				'title' => esc_html__('Trip Settings', 'tourmaster'),
				'options' => array(
					'ical-description' => array(
						'description' => esc_html__('ICal URL :', 'tourmaster') . 
							' <a href="' . esc_url($ical_url) . '" target="_blank" >' . $ical_url . '</a>',
						'type' => 'description'
					),
					/* 'enable-payment' => array(
						'title' => esc_html__('Enable Payment', 'tourmaster'),
						'type' => 'combobox',
						'options' => array(
							'' => esc_html__('Default', 'tourmaster'),
							'enable' => esc_html__('Enable', 'tourmaster'),
							'disable' => esc_html__('Disable', 'tourmaster'),
						)
					),
					'payment-admin-approval' => array(
						'title' => esc_html__('Needs Admin Approval Before Payment', 'tourmaster'),
						'type' => 'combobox',
						'options' => array(
							'disable' => esc_html__('Disable', 'tourmaster'),
						),
					),
					'date-selection-type' => array(
						'title' =>  esc_html__('Date Selection Type', 'tourmaster'),
						'type' => 'combobox',
						'options' => array(
							'calendar' => esc_html__('Calendar', 'tourmaster'),
							'date-list' => esc_html__('Date List', 'tourmaster')
						),
					),
					'last-minute-booking' => array(
						'title' =>  esc_html__('Last Minute Booking (Hour)', 'tourmaster'),
						'type' => 'text',
						'description' =>  esc_html__('Specify the number of hours prior to the travel time you want to close the booking system.', 'tourmaster'),
					),
					'book-in-advance' => array(
						'title' =>  esc_html__('Book In Advance (Month)', 'tourmaster'),
						'type' => 'text',
						'single' => 'tourmaster-book-in-advance',
						'description' =>  esc_html__('For example, If you fill the number "10" (for ten months) and today is in March 2019, customers will have an ability to book the tour from today until Jan 2020 (ten months from current month). Leave this field blank for unlimited booking in advanced.', 'tourmaster'),
					), */
					'tour-price-text' => array(
						'title' =>  esc_html__('Trip Price', 'tourmaster'),
						'type' => 'text',
						'description' => esc_html__('(Numbers Only)', 'tourmaster'),
						'single' => 'tour-price-text',
					),	
					/* 'duration-text' => array(
						'title' =>  esc_html__('Trip Duration', 'tourmaster'),
						'type' => 'text',
						'description' => esc_html__('Only for displaying as tour information.', 'tourmaster'),
					),
					'multiple-duration' => array(
						'title' =>  esc_html__('Duration (Days)', 'tourmaster'),
						'type' => 'text',
						'description' => esc_html__('Ex. Fill "3" for three days (Only Number is Allowed)', 'tourmaster'),
					),
					'departure-location' => array(
						'title' =>  esc_html__('Departure Location', 'tourmaster'),
						'type' => 'text',
						'description' => esc_html__('Only for displaying as tour information.', 'tourmaster'),
					),
					'return-location' => array(
						'title' =>  esc_html__('Destination Location', 'tourmaster'),
						'type' => 'text',
						'description' => esc_html__('Only for displaying as tour information.', 'tourmaster'),
					),
					'minimum-age' => array(
						'title' =>  esc_html__('Minimum Age', 'tourmaster'),
						'type' => 'text',
						'description' => esc_html__('Only for displaying as tour information.', 'tourmaster') . ' ' . 
										 esc_html__('Ex. "16+"', 'tourmaster')
					), 
					'maximum-people-per-booking' => array(
						'title' =>  esc_html__('Maximum People Per Booking', 'tourmaster'),
						'type' => 'text',
						'single' => 'tourmaster-max-people-per-booking',
					),*/
					'maximum-people' => array(
						'title' =>  esc_html__('Max Party Size', 'tourmaster'),
						'type' => 'text',
						'single' => 'tourmaster-max-people',
					),
					/* 'require-each-traveller-info' => array(
						'title' =>  esc_html__('Require Each Traveller\'s Info', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'enable',
						'description' => esc_html__('This option requires customer to fill name and last name of each traveller.', 'tourmaster')
					),
					'require-traveller-info-title' => array(
						'title' =>  esc_html__('Require Traveller\'s Title (Mr/Mrs)', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'enable'
					),
					'require-traveller-passport' => array(
						'title' =>  esc_html__('Require Traveller\'s Passport', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable'
					), */
				)
			), // 'tour-settings' 

			/* 'date-price' => array(
				'title' => esc_html__('Date & Price', 'tourmaster'),
				'options' => array(
					'column-1-open' => array( 'type' => 'column','column-size' => 30, 'right-divider' => 'fa fa-angle-right', 'bottom-divider' => true ),
					'tour-type' => array(
						'title' => esc_html__('1. Select Tour Type', 'tourmaster'),
						'type' => 'radioimage',
						'options' => array(
							'single' => TOURMASTER_URL . '/images/option/type-one.jpg',
							'multiple' => TOURMASTER_URL . '/images/option/type-multiple.jpg'
						),
						'wrapper-class' => 'tourmaster-center-option'
					),
					'column-1-close' => array( 'type' => 'column-close' ),

					'column-2-open' => array( 'type' => 'column','column-size' => 30, 'bottom-divider' => true ),
					'tour-timing-method' => array(
						'title' => esc_html__('2. Select Timing Method', 'tourmaster'),
						'type' => 'radioimage',
						'options' => array(
							'single' => TOURMASTER_URL . '/images/option/timing-one.jpg',
							'recurring' => TOURMASTER_URL . '/images/option/timing-recurring.jpg'
						),
						'wrapper-class' => 'tourmaster-center-option'
					),
					'column-2-close' => array( 'type' => 'column-close', 'clear' => true ),

					'date-price' => array(
						'title' => esc_html__('Add Date & Price', 'tourmaster'),
						'type' => 'custom',
						'item-type' => 'tabs',
						'options' => array(
							'date' => array(
								'title' => esc_html__('Date', 'tourmaster'),
								'type' => 'datepicker',
								'wrapper_class' => 'tourmaster-small-title'
							),
							'day' => array(
								'title' => esc_html__('Day', 'tourmaster'),
								'type' => 'checkboxes',
								'options' => array(
									'monday' => esc_html__('Mon', 'tourmaster'),
									'tuesday' => esc_html__('Tue', 'tourmaster'),
									'wednesday' => esc_html__('Wed', 'tourmaster'),
									'thursday' => esc_html__('Thu', 'tourmaster'),
									'friday' => esc_html__('Fri', 'tourmaster'),
									'saturday' => esc_html__('Sat', 'tourmaster'),
									'sunday' => esc_html__('Sun', 'tourmaster'),
									'select-all' => esc_html__('Select All', 'tourmaster'),
									'deselect-all' => esc_html__('Deselect All', 'tourmaster'),
								)
							),
							'month' => array(
								'title' => esc_html__('Month', 'tourmaster'),
								'type' => 'checkboxes',
								'options' => array(
									'1' => esc_html__('Jan', 'tourmaster'),
									'2' => esc_html__('Feb', 'tourmaster'),
									'3' => esc_html__('Mar', 'tourmaster'),
									'4' => esc_html__('Apr', 'tourmaster'),
									'5' => esc_html__('May', 'tourmaster'),
									'6' => esc_html__('Jun', 'tourmaster'),
									'7' => esc_html__('Jul', 'tourmaster'),
									'8' => esc_html__('Aug', 'tourmaster'),
									'9' => esc_html__('Sep', 'tourmaster'),
									'10' => esc_html__('Oct', 'tourmaster'),
									'11' => esc_html__('Nov', 'tourmaster'),
									'12' => esc_html__('Dec', 'tourmaster'),
									'select-all' => esc_html__('Select All', 'tourmaster'),
									'deselect-all' => esc_html__('Deselect All', 'tourmaster'),
								)
							),
							'year' => array(
								'title' => esc_html__('Year', 'tourmaster'),
								'type' => 'checkboxes',
								'options' => array(
									// '2016' => '2016',
									// '2017' => '2017',
									// '2018' => '2018',
									'2019' => '2019',
									'2020' => '2020',
									'2021' => '2021',
									'2022' => '2022',
									'2023' => '2023',
									'2024' => '2024',
									'2025' => '2025',
									'2026' => '2026',
								)
							),

							'extra-date-description' => array(
								'description' => esc_html__('Fill the date in yyyy-mm-dd format and separated the date using comma. Eg. 2020-12-25,2020-12-26,2020-12-27', 'tourmaster'),
								'type' => 'description'
							),
							'extra-date' => array(
								'title' => esc_html__('INCLUDE EXTRA DATES USING DATE FORMAT', 'tourmaster'),
								'type' => 'textarea',
								'wrapper_class' => 'tourmaster-full-size',
								'title_color' => '#67b1a1'
							),
							'exclude-extra-date' => array(
								'title' => esc_html__('EXCLUDE EXTRA DATES USING DATE FORMAT', 'tourmaster'),
								'type' => 'textarea',
								'wrapper_class' => 'tourmaster-full-size',
								'title_color' => '#be7272'
							),

							'pricing-title' => array(
								'title' => esc_html__('PRICING', 'tourmaster'),
								'type' => 'title',
								'wrapper_class' => 'tourmaster-middle-with-divider'
							),
							'pricing-method' => array(
								'title' => esc_html__('Pricing Method', 'tourmaster'),
								'type' => 'radioimage',
								'options' => array(
									'fixed' => TOURMASTER_URL . '/images/option/fixed-price.jpg',
									'variable' => TOURMASTER_URL . '/images/option/variable-price.jpg',
									'group' => TOURMASTER_URL . '/images/option/group-price.jpg'
								),
								'description' => esc_html__('* Variable pricing will differentiate the price of adult, children, student and infant.', 'tourmaster'),
							),
							'pricing-room-base' => array(
								'title' => esc_html__('Enable Room Base', 'tourmaster'),
								'type' => 'radioimage',
								'options' => array(
									'enable' => TOURMASTER_URL . '/images/option/room-base-enable.jpg',
									'disable' => TOURMASTER_URL . '/images/option/room-base-disable.jpg'
								),
								'condition' => array( 'pricing-method' => array( 'fixed', 'variable' ) ),
								'description' => esc_html__('* Calculate tour price based on the hotel\'s room. For example, 2 Adults 2 Rooms will be more expensive than 2 Adults 1 Room.', 'tourmaster'),
							),
							'base-price-description' => array(
								'description' => wp_kses(
									__('When you choose <strong>Room Base Pricing</strong>. There will be 2 parts of the price. The final price will be the summary of these two.', 'tourmaster'),
									array( 'strong' => array() )
								),
								'type' => 'description',
								'condition' => array( 'pricing-method' => array( 'fixed', 'variable' ) ) 
							),
							'package' => array(
								'title' => esc_html__('Add Package', 'tourmaster'),
								'type' => 'tabs',
								'settings' => array(
									'tab-title' => esc_html__('Default Package', 'tourmaster')
								),
								'options' => array(
									'default-package' => array(
										'title' => esc_html__('Default Package', 'tourmaster'),
										'type' => 'checkbox',
										'default' => 'disable',
										'description' => esc_html__('Enable to pre-selected this package on page load. Only the first package that enable this option is effected.', 'tourmaster')
									),
									'group-slug' => array(
										'title' => esc_html__('Package Group Alias', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Fill the same string on each package to group the people from 2 package together ( for Max people option ).', 'tourmaster')
									),
									'title' => array(
										'title' => esc_html__('Package Title', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Each package title has to be in different name.', 'tourmaster')
									),
									'caption' => array(
										'title' => esc_html__('Package Caption', 'tourmaster'),
										'type' => 'text',
									),
									'start-time' => array(
										'title' =>  esc_html__('Start Time', 'tourmaster'),
										'type' => 'time'
									),

									'base-price-title' => array(
										'title' => esc_html__('BASE PRICE', 'tourmaster'),
										'type' => 'title'
									),
									'person-price' => array(
										'title' => esc_html__('Price Per Person', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number).', 'tourmaster'),
									),
									'enable-supplement-pricing' => array(
										'title' => esc_html__('Enable Supplement Pricing', 'tourmaster'),
										'type' => 'checkbox',
										'default' => 'disable',
									),
									'single-supplement-price' => array(
										'title' => esc_html__('Single Supplement Price', 'tourmaster'),
										'type' => 'text',
									),
									'triple-supplement-price' => array(
										'title' => esc_html__('Triple Supplement Price', 'tourmaster'),
										'type' => 'text',
									),
									'adult-price' => array(
										'title' => esc_html__('Adult', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'male-price' => array(
										'title' => esc_html__('Male', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'female-price' => array(
										'title' => esc_html__('Female', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'children-price' => array(
										'title' => esc_html__('Child', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'student-price' => array(
										'title' => esc_html__('Student', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'infant-price' => array(
										'title' => esc_html__('Infant', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'group-price' => array(
										'title' => esc_html__('Price per group', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Fill only number.', 'tourmaster'),
									),
									'max-group' => array(
										'title' => esc_html__('Max group', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('How many group you can accept in this tour? (Fill only number).', 'tourmaster'),
									),
									'max-group-people' => array(
										'title' => esc_html__('Max people for each group', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('People amount in each group (Fill only number).', 'tourmaster'),
									),
									'same-gender' => array(
										'title' => esc_html__('Same Gender Required', 'tourmaster'),
										'type' => 'checkbox',
										'default' => 'disable',
										'description' => esc_html__('This feature will allow only one gender in the this package. Ex. If female book first, the rest has to be female as well. However, mix gender will be allowed if women and men book at the same time by the same customer.', 'tourmaster')
									),

									'room-base-price-title' => array(
										'title' => esc_html__('ROOM BASED PRICE', 'tourmaster'),
										'type' => 'title',
									),
									'initial-price' => array(
										'title' => esc_html__('Initial Price', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('This price based on 2 adults', 'tourmaster'),
									),
									'single-discount' => array(
										'title' => esc_html__('Single Discount', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('This discount will be used for deducting the price of Initial Price. Ex, If you set Initial Price as $100 and Single Discount as $30. If there’re two guests in this room, they will pay for $100. However, if there\'s only one guest in this room, he/she will pay for only $70 instead of $100. This option is an alternative for single supplement.', 'tourmaster'),
									),
									'additional-person' => array(
										'title' => esc_html__('Additional Person', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number).', 'tourmaster'),
									),
									'additional-adult' => array(
										'title' => esc_html__('Additional Adult', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'additional-male' => array(
										'title' => esc_html__('Additional Male', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'additional-female' => array(
										'title' => esc_html__('Additional Female', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'additional-children' => array(
										'title' => esc_html__('Additional Child', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'additional-student' => array(
										'title' => esc_html__('Additional Student', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'additional-infant' => array(
										'title' => esc_html__('Additional Infant', 'tourmaster'),
										'type' => 'text',
										'description' => esc_html__('Price per person (Fill only number). * Leave this field blank to not apply.', 'tourmaster'),
									),
									'minimum-people-per-booking' => array(
										'title' => esc_html__('Minimum People Per Booking', 'tourmaster'),
										'type' => 'text',
									),
									'max-room' => array(
										'title' => esc_html__('Max Room', 'tourmaster'),
										'type' => 'text',
									),
									'max-people-per-room' => array(
										'title' => esc_html__('Max People Per Room', 'tourmaster'),
										'type' => 'text',
									),
									'max-people' => array(
										'title' => esc_html__('Maximum People', 'tourmaster'),
										'type' => 'text'
									),
								),
								'condition' => array( 'pricing-method' => 'css-condition', 'pricing-room-base' => 'css-condition' ),
							), 
							'select-package-text' => array(
								'title' => esc_html__('Select a Package Text', 'tourmaster'),
								'type' => 'text',
								'description' => esc_html__('Leave blank for default', 'tourmaster')
							)
						),
						'settings' => array(
							'tab-title' => esc_html__('Date', 'tourmaster') . '<i class="fa fa-edit" ></i>',
							'allow-duplicate' => '<i class="fa fa-copy" ></i>' . esc_html__('Duplicate', 'tourmaster'),
						),
						'condition' => array( 'tour-type' => 'css-condition', 'tour-timing-method' => 'css-condition' ),
						'wrapper-class' => 'tourmaster-with-bottom-divider'
					),
					
					'group-discount-title' => array(
						'title' => esc_html__('Group Discount', 'tourmaster'),
						'type' => 'title',
						'wrapper-class' => 'tourmaster-main-title'
					),
					'group-discount-category' => array(
						'title' => esc_html__('Group Discount Category Counting', 'tourmaster'),
						'type' => 'multi-combobox',
						'options' => array(
							'adult' => esc_html__('Adult', 'tourmaster'),
							'male' => esc_html__('Male', 'tourmaster'),
							'female' => esc_html__('Female', 'tourmaster'),
							'children' => esc_html__('Children', 'tourmaster'),
							'student' => esc_html__('Student', 'tourmaster'),
							'infant' => esc_html__('Infant', 'tourmaster'),
						),
						'description' => esc_html__('Leave this field blank to select all traveller types. Use "ctrl" to select multiple or deselect the option.', 'tourmaster') . 
							'<br><br>' . esc_html__('This option will let you choose which group to be counted for discount. Ex. if you choose to use only Adult to be counted and choose 3 Travellers Number to get discount. When select 2 adult + 1 child, this condition will not be met. However, if select 3 adults + 1 child, this condition met. Note that this option apply to Variable Price only.', 'tourmaster')
					),
					'group-discount-apply' => array(
						'title' => esc_html__('Group Discount Apply To', 'tourmaster'),
						'type' => 'multi-combobox',
						'options' => array(
							'adult' => esc_html__('Adult', 'tourmaster'),
							'male' => esc_html__('Male', 'tourmaster'),
							'female' => esc_html__('Female', 'tourmaster'),
							'children' => esc_html__('Children', 'tourmaster'),
							'student' => esc_html__('Student', 'tourmaster'),
							'infant' => esc_html__('Infant', 'tourmaster'),
						),
						'description' => esc_html__('You can choose  which category to get discount when discount condition met.', 'tourmaster')
					),
					'group-discount-per-person' => array(
						'title' => esc_html__('Group Discount Based On Person', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'description' => esc_html__('This option will be automatically set to "Enable" if the "Group Discount Apply To" option is selected.', 'tourmaster') . 
							'<br><br>' . esc_html__('If you turn this option on, the discount will apply on per person basis and if you\'re using \'Room Base\' pricing, it will only apply to \'Base Price\' and won\'t apply to \'Room Based Price\'. Please also note that with this option, the discount won\'t be applied to "Tour Service" as well. However, if you turn this option off, the discount will be applied to everything and will be shown as discount at the end of price breakdown.', 'tourmaster')
					),
					'group-discount' => array(
						'title' => esc_html__('Add Group Discount', 'tourmaster'),
						'type' => 'custom',
						'item-type' => 'group-discount',
						'options' => array(
							'traveller-number' => array(
								'title' => esc_html__('Travellers number', 'tourmaster'),
								'type' => 'text'
							),
							'discount' => array(
								'title' => esc_html__('Discount', 'tourmaster'),
								'type' => 'text'
							),
							'description' => array(
								'type' => 'description',
								'description' => esc_html__('* Fill only number for fixed amount, ex. \'10\' for $10. Fill % at the end if using as percentage, ex. \'10%\'' , 'tourmaster')
							)
						),
						'description' => esc_html__('For example, if you create 2 discount boxes, and for the first box, you set up 5 travellers with 15% discount and for another box, 10 travellers with 25% discount. When customers book for 5,6,7,8,9 travellers, they will get 15% off. However, if they book for 10, 11, 12 ( and so on ) travellers, they will get 25% off.', 'tourmaster')
					) 
				),
			), */ // 'date-price'

			/* 'urgency-message' => array(
				'title' => esc_html__('Urgency Message', 'tourmaster'),
				'options' => array(
					'enable-urgency-message' => array(
						'title' =>  esc_html__('Enable Urgency Message :', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'description' => esc_html__('By enabling this option, the urgent message will be shown in the front-end of the single tour. Ex. "20 travellers are considering this tour right now!"', 'tourmaster') . '<br>' . 
							esc_html__('** Urgency message will be disappeared for 1 day after you close it.', 'tourmaster')
					),
					'real-urgency-message' => array(
						'title' =>  esc_html__('Use Real Data :', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'enable',
						'description' => esc_html__('Real data will record each user for 1 hour.', 'tourmaster')
					),
					'urgency-message-number-from' => array(
						'title' =>  esc_html__('Number From :', 'tourmaster'),
						'type' => 'text',
						'default' => '5',
						'condition' => array( 'real-urgency-message' => 'disable' ),
						'description' => esc_html__('The system will randomly pick the number between "from" and "to" fields.', 'tourmaster')
					),
					'urgency-message-number-to' => array(
						'title' =>  esc_html__('Number To :', 'tourmaster'),
						'type' => 'text',
						'default' => '10',
						'condition' => array( 'real-urgency-message' => 'disable' )
					),
				)
			), // urgency message
 */
			/* 'group-message' => array(
				'title' => esc_html__('Reminder & Message', 'tourmaster'),
				'options' => array(
					'carbon-copy-mail' => array(
						'title' =>  esc_html__('Carbon Copy Email (CC)', 'tourmaster'),
						'type' => 'text',
						'single' => 'tourmaster-tour-cc-mail',
						'description' => esc_html__('Fill the email here to send a copy of an Admin Email for transaction related to this tour.', 'tourmaster')
					),
					'payment-notification-title' => array(
						'title' =>  esc_html__('Payment Notification', 'tourmaster'),
						'type' => 'title',
					),
					'enable-payment-notification' => array(
						'title' =>  esc_html__('Enable Payment Notification', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'single' => 'tourmaster-payment-notification',
						'description' => esc_html__('By, enabling this option, the system will automatically send a payment notification to customer\'s email.', 'tourmaster')
					),
					'payment-notification-days-before-travel' => array(
						'title' =>  esc_html__('Days Before Travel (Haven\'t Paid)', 'tourmaster'),
						'type' => 'text',
						'condition' => array( 'enable-payment-notification' => 'enable' ),
						'description' => esc_html__('Send reminder message XX days before the travel date. This will remind customers if customers haven\'t paid for anything yet.', 'tourmaster')
					),
					'deposit-payment-notification-days-before-travel' => array(
						'title' =>  esc_html__('Days Before Travel (Deposit Paid)', 'tourmaster'),
						'type' => 'text',
						'condition' => array( 'enable-payment-notification' => 'enable' ),
						'description' => esc_html__('Send reminder message XX days before the travel date. This will remind customers if customers have paid the deposit but haven\' paid the rest amount yet. It will remind customers to pay the rest. If you allow to pay at arrival, you may skip this feature so ones who paid the deposit won\'t get the reminder message.', 'tourmaster')
					),
					'payment-notification-mail-subject' => array(
						'title' =>  esc_html__('Email Subject', 'tourmaster'),
						'type' => 'text',
						'condition' => array( 'enable-payment-notification' => 'enable' ),
					),
					'payment-notification-mail-message' => array(
						'title' =>  esc_html__('Email Message', 'tourmaster'),
						'type' => 'textarea',
						'condition' => array( 'enable-payment-notification' => 'enable' ),
					),
					'enable-payment-notification-message-admin-copy' => array(
						'title' =>  esc_html__('Send a copy to admin', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'condition' => array( 'enable-payment-notification' => 'enable' ),
					),

					'reminder-message-title' => array(
						'title' =>  esc_html__('Reminder Message', 'tourmaster'),
						'type' => 'title',
					),
					'enable-reminder-message' => array(
						'title' =>  esc_html__('Enable Reminder Message', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'single' => 'tourmaster-reminder-message',
						'description' => esc_html__('By, enabling this option, the system will automatically send a reminder message to customer\'s email.', 'tourmaster')
					),
					'reminder-message-days-before-travel' => array(
						'title' =>  esc_html__('Reminder Message Days Before Travel', 'tourmaster'),
						'type' => 'text',
						'condition' => array( 'enable-reminder-message' => 'enable' ),
						'description' => esc_html__('Only number is allowed here.', 'tourmaster')
					),
					'reminder-message-mail-subject' => array(
						'title' =>  esc_html__('Email Subject', 'tourmaster'),
						'type' => 'text',
						'condition' => array( 'enable-reminder-message' => 'enable' ),
					),
					'reminder-message-mail-message' => array(
						'title' =>  esc_html__('Email Message', 'tourmaster'),
						'type' => 'textarea',
						'condition' => array( 'enable-reminder-message' => 'enable' ),
					),
					'enable-reminder-message-admin-copy' => array(
						'title' =>  esc_html__('Send a copy to admin', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'condition' => array( 'enable-reminder-message' => 'enable' ),
					),

					'group-message-title' => array(
						'title' =>  esc_html__('Group Message', 'tourmaster'),
						'type' => 'title',
						'wrapper-class' => 'tourmaster-top-margin-wrapper'
					),
					'group-message-date' => array(
						'title' =>  esc_html__('Group Message Date', 'tourmaster'),
						'type' => 'datepicker',
						'description' => esc_html__('* To specify the exact group of customer that you want to send the message to.', 'tourmaster')
					),
					'group-message-mail-subject' => array(
						'title' =>  esc_html__('Email Subject', 'tourmaster'),
						'type' => 'text',
					),
					'group-message-mail-message' => array(
						'title' =>  esc_html__('Email Message', 'tourmaster'),
						'type' => 'textarea',
					),
					'enable-group-message-admin-copy' => array(
						'title' =>  esc_html__('Send a copy to admin', 'tourmaster'),
						'type' => 'checkbox',
						'default' => 'disable',
						'description' => esc_html__('Enable this to send the copy of the mail which cusmoter receieve to admin e-mail.', 'tourmaster')
					),
					'group-message-submit' => array(
						'button-title' =>  esc_html__('Send Email', 'tourmaster'),
						'type' => 'button',
						'data-type' => 'ajax',
						'data-action' => 'tourmaster_submit_group_message',
						'data-fields' => array( 'group-message-date', 'group-message-mail-subject', 'group-message-mail-message', 'enable-group-message-admin-copy', 'group-message-tour-id' ) 
					),
				)
			), */

		)) // tourmaster_tour_options
	));
	tourmaster_user_content_block_start();

	$test->create_page_option_meta_box_2($trip_post);

	tourmaster_add_pb_element_content_navigation();

	tourmaster_user_content_block_end();

	
	$arrayDates = get_post_meta($trip_id, 'tour-available');
	$arrayDates = $arrayDates[0]; 

	?>

<style>

/* The Modal (background) */
.modal {
	display: none; /* Hidden by default */
	position: fixed; /* Stay in place */
	z-index: 1; /* Sit on top */
	padding-top: 120px; /* Location of the box */
	left: 0;
	top: 0;
	width: 100%; /* Full width */
	height: 100%; /* Full height */
	overflow: auto; /* Enable scroll if needed */
	background-color: rgb(0,0,0); /* Fallback color */
	background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
	background-color: #fefefe;
	margin: auto;
	padding: 20px;
	border: 1px solid #888;
	width: 80%;
	max-width: 600px;
}

/* The Close Button */
.close {
	color: #aaaaaa;
	float: right;
	font-size: 28px;
	font-weight: bold;
	clear: both;
}

.close:hover,
.close:focus {
	color: #000;
	text-decoration: none;
	cursor: pointer;
}
.selected {
	background-color: #F6A32A;
	color: #FFFFFF;
	cursor: pointer;
}
.disabled {
	color: #aaaaaa;
	cursor: text;
}
label.chk {
	display: block;
	text-align: left;
	font-weight: normal;
}
table tr th {
	background-color: #F6A32A;
}
table tr td {
	font-weight: 700;
}
</style>


<div id="m-2">

	<h1>February</h1>
	<button onclick="siguienteMes(2)">
		Next
	</button>
<?php

$mes = date("m"); 
$año = date ("Y");
$semana = array ("Mon","Tue", "Wed", "Thu","Fri","Sat","Sun");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='7'>",date("M, Y"),"</th>";
echo "</tr>";
echo "<tr>";
echo "<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
//// MARCO EL DÍA 1º DEL MES:  ////////////////////
echo "<tr>";
for ($i=0;$i<=6;$i++){
    if (date("D",mktime(0,0,0,$mes,1,$año))==$semana[$i]){
        echo "<td>", date("d", mktime(0,0,0,$mes,1,$año)) ,"   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>"; 
            if ( date("D",mktime(0,0,0,$mes,1,$año))=="Sun" ){
                echo "</tr>","<tr>";
                break;
            }else{
                break;
            }
        break;        
    }else{
        echo  "<td>", "</td>"  ;
    }
}

/////////marco los días subsiguientes////////////////////
for ($j=2;$j<=date("t");$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {

			/* echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>";  */
		
		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>"; 

		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>", "</tr>", "<tr>" ;
				
			}

		}

    }else{

		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
				
			}
		}
        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";
?>
	
</div>
<div id="m-3" style="display: none;">

<?php
$mes = date("m") + 1; 
$año = date ("Y");
if ($mes == 12) {
	$mes = 1;
	$año = $año + 1;
}
$nueva_fecha = $año . "-" . $mes . "-01";
?>
	<button onclick="anteriorMes(3)">
		Previous
	</button>
	<h1><?php echo date("M, Y", strtotime($nueva_fecha)); ?></h1>
	<button onclick="siguienteMes(3)">
		Next
	</button>
<?php
$semana = array ("Mon","Tue", "Wed", "Thu","Fri","Sat","Sun");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='7'>",date("M, Y", strtotime($nueva_fecha)),"</th>";
echo "</tr>";
echo "<tr>";
echo "<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
//// MARCO EL DÍA 1º DEL MES:  ////////////////////
echo "<tr>";
for ($i=0;$i<=6;$i++){
    if (date("D",mktime(0,0,0,$mes,1,$año))==$semana[$i]){
        echo "<td>", date("d", mktime(0,0,0,$mes,1,$año)) ,"   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>"; 
            if ( date("D",mktime(0,0,0,$mes,1,$año))=="Sun" ){
                echo "</tr>","<tr>";
                break;
            }else{
                break;
            }
        break;        
    }else{
        echo  "<td>", "</td>"  ;
    }
}
$nueva_fecha = $año . "-" . $mes . "-01";
/////////marco los días subsiguientes////////////////////
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {

			/* echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>";  */
		
		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>"; 

		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>", "</tr>", "<tr>" ;
				
			}

		}

    }else{

		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
				
			}
		}
        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";
?>
	
</div>
<div id="m-4" style="display: none;">
	
	<?php
$mes = date("m") + 2; 
$año = date ("Y");
$nueva_fecha = $año . "-" . $mes . "-01";
	?>

	<button onclick="anteriorMes(4)">
		Previous
	</button>
	<h1><?php echo date("M, Y", strtotime($nueva_fecha));  ?></h1>
	<button onclick="siguienteMes(4)">
		Next
	</button>
	<?php
$semana = array ("Mon","Tue", "Wed", "Thu","Fri","Sat","Sun");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='7'>",date("M, Y", strtotime($nueva_fecha)),"</th>";
echo "</tr>";
echo "<tr>";
echo "<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
//// MARCO EL DÍA 1º DEL MES:  ////////////////////
echo "<tr>";
for ($i=0;$i<=6;$i++){
    if (date("D",mktime(0,0,0,$mes,1,$año))==$semana[$i]){
        echo "<td>", date("d", mktime(0,0,0,$mes,1,$año)) ,"   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>"; 
            if ( date("D",mktime(0,0,0,$mes,1,$año))=="Sun" ){
                echo "</tr>","<tr>";
                break;
            }else{
                break;
            }
        break;        
    }else{
        echo  "<td>", "</td>"  ;
    }
}
$nueva_fecha = $año . "-" . $mes . "-01";
/////////marco los días subsiguientes////////////////////
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {

			/* echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>";  */
		
		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>"; 

		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>", "</tr>", "<tr>" ;
				
			}

		}

    }else{

		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
				
			}
		}
        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";
?>
</div>
<div id="m-5" style="display: none;">
	
	<?php
$mes = date("m") + 3;
$año = date ("Y");
$nueva_fecha = $año . "-" . $mes . "-01";
	?>

	<button onclick="anteriorMes(5)">
		Previous
	</button>
	<h1><?php echo date("M, Y", strtotime($nueva_fecha));  ?></h1>
	<button onclick="siguienteMes(5)">
		Next
	</button>
	<?php
$semana = array ("Mon","Tue", "Wed", "Thu","Fri","Sat","Sun");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='7'>",date("M, Y", strtotime($nueva_fecha)),"</th>";
echo "</tr>";
echo "<tr>";
echo "<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
//// MARCO EL DÍA 1º DEL MES:  ////////////////////
echo "<tr>";
for ($i=0;$i<=6;$i++){
    if (date("D",mktime(0,0,0,$mes,1,$año))==$semana[$i]){
        echo "<td>", date("d", mktime(0,0,0,$mes,1,$año)) ,"   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>"; 
            if ( date("D",mktime(0,0,0,$mes,1,$año))=="Sun" ){
                echo "</tr>","<tr>";
                break;
            }else{
                break;
            }
        break;        
    }else{
        echo  "<td>", "</td>"  ;
    }
}
$nueva_fecha = $año . "-" . $mes . "-01";
/////////marco los días subsiguientes////////////////////
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {

			/* echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>";  */
		
		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>"; 

		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>", "</tr>", "<tr>" ;
				
			}

		}

    }else{

		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
				
			}
		}
        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";
?>
</div>
<div id="m-6" style="display: none;">
	
	<?php
$mes = date("m") + 4;
$año = date ("Y");
$nueva_fecha = $año . "-" . $mes . "-01";
	?>

	<button onclick="anteriorMes(6)">
		Previous
	</button>
	<h1><?php echo date("M, Y", strtotime($nueva_fecha));  ?></h1>
	<button onclick="siguienteMes(6)">
		Next
	</button>
	<?php
$semana = array ("Mon","Tue", "Wed", "Thu","Fri","Sat","Sun");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='7'>",date("M, Y", strtotime($nueva_fecha)),"</th>";
echo "</tr>";
echo "<tr>";
echo "<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
//// MARCO EL DÍA 1º DEL MES:  ////////////////////
echo "<tr>";
for ($i=0;$i<=6;$i++){
    if (date("D",mktime(0,0,0,$mes,1,$año))==$semana[$i]){
        echo "<td>", date("d", mktime(0,0,0,$mes,1,$año)) ,"   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>"; 
            if ( date("D",mktime(0,0,0,$mes,1,$año))=="Sun" ){
                echo "</tr>","<tr>";
                break;
            }else{
                break;
            }
        break;        
    }else{
        echo  "<td>", "</td>"  ;
    }
}
$nueva_fecha = $año . "-" . $mes . "-01";
/////////marco los días subsiguientes////////////////////
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {

			/* echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>";  */
		
		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>"; 

		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>", "</tr>", "<tr>" ;
				
			}

		}

    }else{

		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
				
			}
		}
        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";
?>
</div>
<div id="m-7" style="display: none;">
	
	<?php
$mes = date("m") + 5;
$año = date ("Y");
$nueva_fecha = $año . "-" . $mes . "-01";
	?>

	<button onclick="anteriorMes(7)">
		Previous
	</button>
	<h1><?php echo date("M, Y", strtotime($nueva_fecha));  ?></h1>
	
	<?php
$semana = array ("Mon","Tue", "Wed", "Thu","Fri","Sat","Sun");
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th colspan='7'>",date("M, Y", strtotime($nueva_fecha)),"</th>";
echo "</tr>";
echo "<tr>";
echo "<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
//// MARCO EL DÍA 1º DEL MES:  ////////////////////
echo "<tr>";
for ($i=0;$i<=6;$i++){
    if (date("D",mktime(0,0,0,$mes,1,$año))==$semana[$i]){
        echo "<td>", date("d", mktime(0,0,0,$mes,1,$año)) ,"   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
		<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>"; 
            if ( date("D",mktime(0,0,0,$mes,1,$año))=="Sun" ){
                echo "</tr>","<tr>";
                break;
            }else{
                break;
            }
        break;        
    }else{
        echo  "<td>", "</td>"  ;
    }
}
$nueva_fecha = $año . "-" . $mes . "-01";
/////////marco los días subsiguientes////////////////////
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {

			/* echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>";  */
		
		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), "   <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>   </td>", "</tr>", "<tr>"; 

		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>", "</tr>", "<tr>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>", "</tr>", "<tr>" ;
				
			}

		}

    }else{

		if (empty($arrayDates)) {
			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
			<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
		} else {

			foreach ($arrayDates as $key => $value) {
				if ($key == date("Y-m-d", mktime(0,0,0,$mes,$j,$año))) {
					$flag = 1; 
					$fechaEvaluar = $value;
					$stringFechaEvaluar = $key;
					break;
				}  else {
					$flag = 0; 
				}
			}
	
			if ($flag == 1) {
		
				$ind = array_search("1", $fechaEvaluar);	
				switch ($ind) {
					case '1':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '2':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '3':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					case '4':
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input checked type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
					
					default:
						echo      "<td style='background: #ddd'>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
						<label class=\"chk\"><input  type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label> </td>" ;
						break;
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "  <label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 1, ", $trip_id ,")\"> All Day</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 2, ", $trip_id ,")\"> Guide Morning</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 3, ", $trip_id ,")\"> Guide Afternoon</label> 		
				<label class=\"chk\"><input type=\"checkbox\" onclick=\"seleccionado('",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"', 4, ", $trip_id ,")\"> Guide Evening</label>  </td>" ;
				
			}
		}
        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";
?>
</div>
	<?php


	



	

	echo '</div>'; // tourmaster-user-content-inner 

	?>

	<script>
		function seleccionado(fecha, tipo, tour){
			console.log("fecha seleccionada: " + fecha);
			console.log("Tipo: " + tipo);
			console.log("tour: " + tour);
			var datos = new FormData();
			datos.append('fecha', fecha);
			datos.append('tipo', tipo);
			datos.append('post', tour);
			fetch("https://theoutdoortrip.com/calendar-add/",{
			// fetch("https://theoutdoortrip.stg.elaniin.dev/calendar-add/",{
				method: "POST",
				body: datos
			});
		}

		function siguienteMes(idActual){
			document.getElementById("m-" + idActual).style.display = "none";
			document.getElementById("m-" + (idActual + 1 ) ).style.display = "block";
		}
		function anteriorMes(idActual){
			document.getElementById("m-" + idActual).style.display = "none";
			document.getElementById("m-" + (idActual - 1 ) ).style.display = "block";
		}
	</script>