<?php

if($_POST['op'] == '1'){ 
	$data = get_users( [ 'role__in' => [ 'author' ] ] );

	$response = array();
	$counter = 0;
	foreach ( $data as $user ) {
		$response[$counter]['name'] = get_user_meta($user->ID, 'guide-company', true);
		$response[$counter]['id'] = $user->ID;
		$counter+=1;
	}
	echo json_encode($response);
	die;
}


get_header();

	echo '<input type="hidden" id="check-fixed"/>'; 

	function get_combobox( $name, $options, $value = '' ){
					
		$ret  = '<div class="tourmaster-combobox-wrap" >';
		$ret .= '<select name="' . esc_attr($name) . '" >';
		foreach( $options as $option_slug => $option_title ){
			$ret .= '<option value="' . esc_attr($option_slug) . '" ';
			$ret .= (!empty($_GET[$name]) && $_GET[$name] == $option_slug)? ' selected': '';
			$ret .= ' >' . esc_html($option_title) . '</option>';
		}
		$ret .= '</select>';
		$ret .= '</div>';

		return $ret;
	}
	$search_settings = array(
		'fields' => tourmaster_get_option('general', 'tour-search-fields', '')
	);
	function category_field($search_settings){
		$fields = empty($search_settings['fields'])? array(): $search_settings['fields'];
		if( !is_array($fields) ){
			$fields = array_map('trim', explode(',', $fields));
		}
		if( empty($fields) || in_array('tour_category', $fields) ){
			$tour_category = array(
				'' => ($placeholder)? esc_html__('All', 'tourmaster'): esc_html__('All', 'tourmaster')
			) + tourmaster_get_term_list('tour_category');
			$ret .= '<div id="div-cat" class="tourmaster-tour-search-field tourmaster-tour-search-field-tour-category" ' . tourmaster_esc_style(array(
				'padding-right' => empty($settings['space-between-input'])? '': $settings['space-between-input'],
				'margin-bottom' => empty($settings['space-between-input'])? '': $settings['space-between-input']
			)) . ' >';
			$ret .= ($input_label)? '<label>' . esc_html__('All', 'tourmaster') . '</label>': '';
			$ret .= get_combobox('tax-tour_category', $tour_category);
			$ret .= '</div>';
		}
		return $ret;
	}

	$shadow_size = tourmaster_get_option('general', 'tour-search-item-frame-shadow-size', '');
	$settings = array(
		'pagination' => 'page',
		'grid-style' => tourmaster_get_option('general', 'tour-search-order-filterer-grid-style-type', 'style-1'),
		'column-size' => tourmaster_get_option('general', 'tour-search-order-filterer-grid-style-column', '30'),
		'tour-info' => tourmaster_get_option('general', 'tour-search-item-info', array()),
		'excerpt' => tourmaster_get_option('general', 'tour-search-item-excerpt', 'specify-number'),
		'excerpt-number' => tourmaster_get_option('general', 'tour-search-item-excerpt-number', '55'),
		'tour-rating' => tourmaster_get_option('general', 'tour-search-item-rating', 'enable'),
		'num-fetch' => tourmaster_get_option('general', 'tour-search-item-num-fetch', '9'),
		'custom-pagination' => true,
		'frame-shadow-size' => empty($shadow_size)? '': array('x' => 0, 'y' => 0, 'size' => $shadow_size),
		'frame-shadow-color' => tourmaster_get_option('general', 'tour-search-item-frame-shadow-color', ''),
		'frame-shadow-opacity' => tourmaster_get_option('general', 'tour-search-item-frame-shadow-opacity', ''),
	);
	$settings['paged'] = (get_query_var('paged'))? get_query_var('paged') : get_query_var('page');
	$settings['paged'] = empty($settings['paged'])? 1: $settings['paged'];

	if( $settings['grid-style'] == 'style-2' ){
		$settings['tour-border-radius'] = '3px';
	} 

	if(!empty($_GET['guide'])){
		$author = get_users(array('meta_key' => 'guide-company', 'meta_value' => $_GET['guide']));
		$id = $author[0]->ID;
		$args = array(
			'post_status' => 'publish',
			'post_type' => 'tour',
			'posts_per_page' => $settings['num-fetch'],
			'paged' => $settings['paged'],
			'author' => $id
		);
	} else {
		$args = array(
			'post_status' => 'publish',
			'post_type' => 'tour',
			'posts_per_page' => $settings['num-fetch'],
			'paged' => $settings['paged'],
		);	
	}
	// search query
	

	// keywords
	if( !empty($_GET['tour-search']) ){
		$args['s'] = $_GET['tour-search'];
	}

	// category
	$args['tax_query'] = array(
		'relation' => 'AND'
	);
	$category = empty($_GET['tax-tour_category'])? '': $_GET['tax-tour_category'];
	if( !empty($category) ){
		$args['tax_query'][] = array(
			array('terms'=>$category, 'taxonomy'=>'tour_category', 'field'=>'slug')
		);
	}

	$species = empty($_GET['species'])? '': $_GET['species'];
	$specie = strtolower($species);
	if( !empty($specie) ){
		$args['tax_query'][] = array(
			array('terms'=>$_GET['species'], 'taxonomy'=>'tour-activity', 'field'=>'slug')
		);
	}

	// taxonomy
	$tax_fields = array( 'tour_tag' => esc_html__('Tag', 'tourmaster') );
	$tax_fields = $tax_fields + tourmaster_get_custom_tax_list();
	foreach( $tax_fields as $tax_field => $tax_title ){
		if( !empty($_GET['tax-' . $tax_field]) ){
			$args['tax_query'][] = array(
				array('terms'=>$_GET['tax-' . $tax_field], 'taxonomy'=>$tax_field, 'field'=>'slug')
			);
		}
	}
	$meta_query = array(
		'relation' => 'AND'
	);

	

	if( !empty($_GET['guest']) ){
		$meta_query[] = array(
			'key'     => 'tourmaster-max-people',
			'value'   => $_GET['guest'],
		);
	}

	if( !empty($_GET['location']) ){	
		if ($_GET['location'] == "USA" || $_GET['location'] == "United States" || $_GET['location'] == "All") {
			$meta_query[] = array(
					'key'     => 'country',
					'value'   => 'United States',
			);
		} else {
			$meta_query[] = array(
				'relation' => 'OR',
				array(
					'key'     => 'state',
					'value'   => $_GET['location'],
					'compare' => 'IN',
				),
				array(
					'key'     => 'country',
					'value'   => $_GET['location'],
					'compare' => 'IN',
				),
				array(
					'key'     => 'address',
					'value'   => $_GET['location'],
					'compare' => 'IN',
				),
				array(
					'key'     => 'city',
					'value'   => $_GET['location'],
					'compare' => 'IN',
				)
			);
		}
		
	}

	// duration
	if( !empty($_GET['duration']) ){
		if( $_GET['duration'] == '1' ){
			$meta_query[] = array(
				'key'     => 'tourmaster-tour-duration',
				'value'   => '1',
				'compare' => '=',
				'type'    => 'NUMERIC'
			);
		}else if( $_GET['duration'] == '2' ){
			$meta_query[] = array(
				'key'     => 'tourmaster-tour-duration',
				'value'   => array(2, 4),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC'
			);
		}else if( $_GET['duration'] == '5' ){
			$meta_query[] = array(
				'key'     => 'tourmaster-tour-duration',
				'value'   => array(5, 7),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC'
			);
		}else if( $_GET['duration'] == '7' ){
			$meta_query[] = array(
				'key'     => 'tourmaster-tour-duration',
				'value'   => '7',
				'compare' => '>',
				'type'    => 'NUMERIC'
			);
		}
	}

	// date
	if( !empty($_GET['date-start']) ){
		$meta_query[] = array(
			'key'     => 'tourmaster-tour-date',
			'value'   => $_GET['date-start'],
			'compare' => '>=',
		);
	}	

	if( !empty($_GET['date-end']) ){
		$meta_query[] = array(
			'key'     => 'tourmaster-tour-date',
			'value'   => $_GET['date-end'],
			'compare' => '<=',
		);
	}	

	// date
	if( !empty($_GET['month']) ){
		$meta_query[] = array(
			'key'     => 'tourmaster-tour-date',
			'value'   => $_GET['month'],
			'compare' => 'LIKE',
		);
	}	

	// min price 
	if( !empty($_GET['min-price']) ){
		$meta_query[] = array(
			'key'     => 'tourmaster-tour-price',
			'value'   => $_GET['min-price'],
			'compare' => '>=',
			'type'    => 'NUMERIC'
		);
	}

	// max price 
	if( !empty($_GET['max-price']) ){
		$meta_query[] = array(
			'key'     => 'tourmaster-tour-price',
			'value'   => $_GET['max-price'],
			'compare' => '<=',
			'type'    => 'NUMERIC'
		);
	}

	// max price 
	if( !empty($_GET['rating']) ){
		$meta_query[] = array(
			'key'     => 'tourmaster-tour-rating-score',
			'value'   => $_GET['rating'],
			'compare' => '>=',
			'type'    => 'NUMERIC'
		);
	}

	if( !empty($meta_query) ){
		$args['meta_query'] = $meta_query;
	}

	$settings['query'] = new WP_Query($args);
	
	global $tourmaster_found_posts;
	$tourmaster_found_posts = $settings['query']->found_posts;


	// start the content
	$search_style = tourmaster_get_option('general', 'search-page-style', 'style-1');
	if( $search_style == 'style-2' ){
		$settings['filter-icon'] = 'svg';
	}

	echo '<div class="tourmaster-template-wrapper custom-search-tm tourmaster-search-' . esc_attr($search_style) . '" >';
	echo '<div class="tourmaster-container" >';

	// sidebar content
	$sidebar_type = 'none';
	echo '<div class="' . tourmaster_get_sidebar_wrap_class($sidebar_type) . '" >';
	echo '<div class="' . tourmaster_get_sidebar_class(array('sidebar-type'=>$sidebar_type, 'section'=>'center')) . '" >';
	$data_place = stripslashes($_GET['data-place']);
	echo '<div id="search-form-fixed" class="tourmaster-tour-search-item custom-search-bar clearfix tourmaster-style-column tourmaster-column-count-8 tourmaster-item-pdlr tourmaster-input-style-no-border" style="padding-top: 10px;padding-bottom: 0px;">
	<div class="tourmaster-tour-search-wrap ">
	<form class="tourmaster-form-field  tourmaster-medium" action="https://theoutdoortrip.com/tours/" method="GET">'.
	/*'<div id="div-loc" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px;">
	<input type="text" placeholder="Location" id="trip-location" name="location" value="'. $_GET['location']. '"/>
	</div>
	'.category_field($search_settings).'	*/
	'<div id="div-guide" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px;">
	<label for="guide_name">Guide Name</label>
	<input autocomplete="off" type="text" value="' . (empty($_GET['guide_name'])? '': esc_attr(($_GET['guide_name']))) . '" placeholder="Search by guide" max="6" id="guide_name" name="guide_name"/>
	</div>
	<div id="div-start" class="tourmaster-tour-search-field tourmaster-tour-search-field-date" style="padding-right: 10px;margin-bottom: 10px;">
	<div class="tourmaster-datepicker-wrap" >
	<label>Trip Date</label>
	<input readonly class="tourmaster-datepicker" type="text" 
	value="' . (empty($_GET['start_date_search'])? '': esc_attr($_GET['start_date_search'])) . '" placeholder="' . esc_html__('Start Date', 'tourmaster') . '" data-date-format="' . esc_attr(tourmaster_get_option('general', 'datepicker-date-format', 'dd mm yy')) . '" /><input class="tourmaster-datepicker-alt" name="start_date_search" type="hidden" value="' . (empty($_GET['start_date_search'])? '': esc_attr($_GET['date-start'])) . '" />
	</div>
	</div>
	<div id="div-spec" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px;">
	<label id="species_search">Species</label>
	<select id="species_search" name="species_search">
	<option value="86">Catfish</option>
	<option value="278">Crappie</option>
	<option value="266">Flounder</option>
	<option value="279">Grouper</option>
	<option value="280 Bass">Largemouth Bass</option>
	<option value="281">Musky</option>
	<option value="282">Pike</option>
	<option value="283">Redfish</option>
	<option value="284">Sand Bass</option>
	<option value="285">Smallmouth Bass</option>
	<option value="286">Snapper</option>
	<option value="287">Snook</option>
	<option value="288">Speckled Trout</option>
	<option value="289">Spoonbill</option>
	<option value="290">Striper</option>
	<option value="291">Sturgeon</option>
	<option value="292">Trout</option>
	<option value="293">Tuna</option>
	<option value="294">Walleye</option>
	<option value="295">White Bass</option>
	</select>
	</div>
	'.
	'<div id="div-guest" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px;">
	<label for="cat">Type</label>
	<select id="cat" name="category_search">
		<option name="fishing">Fishing</option>
		<option name="hunting">Hunting</option>
	</select>
	</div>'.
	'<div id="div-submit" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px;">
	<input class="tourmaster-tour-search-submit" type="submit" value="Search">
	</div>
	<input type="hidden" id="trip-data-location" name="data-place" value="'.esc_attr($data_place).'"/>
	</form></div></div>';
	echo '<div class="tourmaster-page-content custom-content" style="background-color: #ffffff;">';
	
	// search filter
	$enable_search_filter = tourmaster_get_option('general', 'enable-tour-search-filter', 'disable');
	if( $enable_search_filter != 'disable' ){
		$search_settings = array(
			'fields' => tourmaster_get_option('general', 'tour-search-fields', ''),
			'enable-rating-field' => tourmaster_get_option('general', 'tour-search-rating-field', ''),
			'filters' => tourmaster_get_option('general', 'tour-search-filters', ''),
			'filter-state' => tourmaster_get_option('general', 'tour-search-filter-state', 'disable'),
			'style' => 'full',
			'item-style' => $search_style,
			'with-frame' => 'enable'
		);
		echo '<div class="tourmaster-tour-search-item-wrap" >';
		echo tourmaster_pb_element_tour_search::get_content($search_settings);
		echo '</div>';
	}

	// content
	if( $settings['query']->have_posts() ){	

		$settings['enable-order-filterer'] = 'disable'; 
		$settings['order-filterer-grid-style'] = tourmaster_get_option('general', 'tour-search-order-filterer-grid-style', ''); 
		$settings['order-filterer-grid-style-thumbnail'] = tourmaster_get_option('general', 'tour-search-order-filterer-grid-style-thumbnail', ''); 
		$settings['order-filterer-grid-style-column'] = $settings['column-size']; 
		
		$settings['order-filterer-list-style'] = tourmaster_get_option('general', 'tour-search-item-style', '');
		$settings['order-filterer-list-style-thumbnail'] = tourmaster_get_option('general', 'tour-search-item-thumbnail', '');

		$default_style = tourmaster_get_option('general', 'tour-search-default-style', 'list');
		if( $default_style == 'list' ){
			$settings['tour-style'] = $settings['order-filterer-list-style'];
			$settings['thumbnail-size'] = $settings['order-filterer-list-style-thumbnail'];
		}else if( $default_style == 'grid' ){
			$settings['tour-style'] = $settings['order-filterer-grid-style'];
			$settings['thumbnail-size'] =$settings['order-filterer-grid-style-thumbnail'];
		}

		$settings['s'] = empty($args['s'])? '': $args['s'];
		$settings['tax_query'] = $args['tax_query'];
		$settings['meta_query'] = $meta_query;

		$data = $settings['query'];
		
		foreach ( $data->posts as $post ) {
			$data_price = get_post_meta($post->ID, 'tourmaster-tour-option', true);
			$feature_img = get_the_post_thumbnail_url($post->ID,'full');
			$url = str_replace(' ', '-', get_user_meta($post->post_author, 'guide-company', true));
			$post->latitude = get_post_meta($post->ID, 'latitude', true);
			$post->longitude = get_post_meta($post->ID, 'longitude', true);
			$post->price =   (empty($data_price['tour-price-text'])) ?  "" : $data_price['tour-price-text']  ;		
			$post->img_url = $feature_img;
			$post->amount = 0;
			// $post->guide_url = 'https://theoutdoortrip.stg.elaniin.dev/guides/?guide='.$url;
			$post->guide_url = 'https://theoutdoortrip.com/guides/?guide='.$url;
			$post->guide_name = $url;
		}
		//custom map-section
		echo '<div class="tourmaster-tour-search-item-wrap custom-map" >';
		// echo '<div id="map" style="width: 100%; height: 1438px;"></div>';
		echo '<div id="map" style="width: 100%; height: 1000px;"></div>';
		echo '</div>';

		echo '<div class="tourmaster-tour-search-content-wrap custom-content-2" >';
		echo '<p type="hidden" class="tourmaster-search-option-value" style="margin-bottom: 10px !important" name="search-result" value="' . esc_attr(json_encode($data->posts)) . '" value2="' . esc_attr(json_encode($data->posts)) . '"></p>';
		/*if( $search_style == 'style-2' ){
			global $tourmaster_found_posts;
			echo '<h4 class="tourmaster-tour-search-content-head tourmaster-item-mglr" >';
			echo '<span class="total-results">'.$tourmaster_found_posts.'</span>';
			echo sprintf(esc_html__(' Results Found', 'tourmaster'), $tourmaster_found_posts);
			echo '</h4>';
		} */
		echo '<div id="data-res">';
		// echo tourmaster_pb_element_tour::get_content($settings);
		echo '<div class="tourmaster-tour-item clearfix  tourmaster-tour-item-style-grid tourmaster-tour-item-column-2">';
		echo '<div class="tourmaster-tour-item-holder gdlr-core-js-2 clearfix">';
		// AQUI INICIA TODO

		if($_GET['species_search'] && !empty($_GET['species_search'])) {
			$tax = $_GET['species_search'];
		} else {
			$tax = "";
		}
		if($_GET['guide_name'] && !empty($_GET['guide_name'])) {
			$guide_nombre = '*' . $_GET['guide_name'] . '*';
		} else {
			$guide_nombre = "";
		}

		$args = array(
			'user-type' => 'user-guide',
			'search' => $guide_nombre
		);
		// $args['user-type'] = 'user-guide';
		// $args['search'] = $guide_nombre;
		$users = get_users($args);
		if (!empty($users)){
			
			foreach ($users as $usr) {
				$usr_id = $usr->ID;
				$hoy = date('Y-m-d');
				$hoy = '2020-01-01';
				$args_p = array(
					'post_status' => 'publish',
					'post_type' => 'tour',
					'posts_per_page' => 1000,
					'author' => $usr_id,
					'meta_query' => array(
						array(
							'key' => 'tour-activity',
							'value' => $tax,
							'compare' => 'LIKE'
						)
					)
				);
	
				$posts_g = new WP_Query($args_p);
				
				$header_img_id = get_user_meta($usr_id, 'image-guide', true);
				$header_img = wp_get_attachment_url($header_img_id);
				$guide_info = get_user_meta($usr_id, 'guide-company', true);
				$guide_name = str_replace(' ', '-', strtolower($guide_info));
				if($usr_id == 1){ } else {
					if ($posts_g->have_posts()) {	
						$prices = array();
						foreach($posts_g->posts as $post){
							$data_price = get_post_meta($post->ID, 'tourmaster-tour-option', true);
							array_push($prices, (empty($data_price['tour-price-text'])) ? 0 : intval($data_price['tour-price-text'])  ) ;
							$feature_img = get_the_post_thumbnail_url($post->ID,'full');
							echo "<br>";
						}  
						sort($prices, 1);
			?>
			<div class="card-guide" style="height: 238px; width: 100%; display: block; background-color: #f2f2f2;" id="card-guide-<?php echo $usr_id; ?>">
				<!-- <a href="http://localhost/bookdev/guides/?guide=<?php echo $guide_name; ?>"> -->
				<!-- <a href="https://theoutdoortrip.stg.elaniin.dev/guides/?guide=<?php echo $guide_name; ?>"> -->
				<a href="https://theoutdoortrip.com/guides/?guide=<?php echo $guide_name; ?>">
				<div class="img-back" style="width: 100%; height: 149px; background-color: #fff; background-image: url('<?php echo $header_img; ?>'); background-size: cover; background-position: top center;" ></div>
				<!-- <img src="<?php echo $header_img; ?>"  style="width: 100%; height: 149px;"> -->
				<div style="display: flex; flex-direction: row; flex-wrap:nowrap; height: 89px; border: 0.515834px solid #BDBDBD;">
					<!-- <div class="item-1" >
						<img style="padding: 10px;" src="<?php echo get_avatar_url($usr_id) ?>" alt="">
					</div> -->
					<div class="item 2" style="text-align: center; padding: 5px; width: 100%;">
						<h3 style="text-decoration: underline; font-size: 100%; font-weight:600; margin: 0; color: #000;"><?php echo $guide_info; ?></h3>
						<p style="text-decoration: underline; font-weight:400; margin: 0;  color: #000;"> Trips From: </p>
						<h1 style="font-weight: 800; margin: 0; font-size: 32px;">$<?php echo $prices[0] ?></h1>
					</div>
				</div>
				</a>
			</div>
			<?php
						unset($prices);
					}
				}
			}

			echo '</div>'; // FIN contenedor DE CARDS
			echo '</div>'; // FIN DE CARDS
			echo '</div>';
			echo '</div>';

		} else {

			echo '<div class="tourmaster-tour-search-item-wrap custom-map" >';
			// echo '<div id="map" style="width: 100%; height: 500px;"></div>';
			// echo '</div>';
			echo '<div class=" custom-content-2 tourmaster-single-search-not-found-wrap tourmaster-item-pdlr" >';
			echo '<div class="tourmaster-single-search-not-found-inner" >';
			echo '<div class="tourmaster-single-search-not-found" >';
			echo '<h3 class="tourmaster-single-search-not-found-title" >' . esc_html__('Not Found', 'tourmaster') . '</h3>';
			echo '<div class="tourmaster-single-search-not-found-caption" >' . esc_html__('Nothing matched your search criteria. Please try again with different keywords', 'tourmaster') . '</div>';
			echo '</div>'; // tourmaster-single-search-not-found


		}
		

		
		
		
	}else{
		echo '<div class="tourmaster-tour-search-item-wrap custom-map" >';
		echo '<div id="map" style="width: 100%; height: 500px;"></div>';
		echo '</div>';
		echo '<div class=" custom-content-2 tourmaster-single-search-not-found-wrap tourmaster-item-pdlr" >';
		echo '<div class="tourmaster-single-search-not-found-inner" >';
		echo '<div class="tourmaster-single-search-not-found" >';
		echo '<h3 class="tourmaster-single-search-not-found-title" >' . esc_html__('Not Found', 'tourmaster') . '</h3>';
		echo '<div class="tourmaster-single-search-not-found-caption" >' . esc_html__('Nothing matched your search criteria. Please try again with different keywords', 'tourmaster') . '</div>';
		echo '</div>'; // tourmaster-single-search-not-found

		if( $enable_search_filter == 'disable' ){
			echo tourmaster_pb_element_tour_search::get_content(array(
				'fields' => tourmaster_get_option('general', 'search-not-found-fields', array()),
				'style' => tourmaster_get_option('general', 'search-not-found-style', 'column'),
				'with-frame' => 'disable',
				'padding-bottom' => '0px',
				'no-pdlr' => true
			));		
		}

		echo '</div>'; // tourmaster-single-search-not-found-inner
		echo '</div>'; // tourmaster-single-search-not-found-wrap
	}

	echo '</div>'; // tourmaster-page-content
	
	echo '</div>'; // tourmaster-get-sidebar-class
	echo '</div>'; // tourmaster-get-sidebar-wrap-class	
	
	echo '</div>'; // tourmaster-container
	echo '</div>'; // tourmaster-template-wrapper
	
	
	get_footer(); 
	echo '<script>function redireccion(id){
		console.log(id);
		document.getElementById("tour-30597").submit();
	}</script>';
?>