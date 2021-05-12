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

			$args_p_2 = array(
				'post_status' => 'publish',
				'post_type' => 'tour',
				'posts_per_page' => 1000				
			);

			$posts_g_2 = new WP_Query($args_p_2);

			if ($posts_g_2->have_posts()) { 
				foreach($posts_g_2->posts as $post){
					$titlo_post = 'half';
					$titulo = $post->post_title;
					$tit=strtoupper($titulo); 
					$fil=strtoupper($titlo_post);
					if ((strpos($tit,$fil) !== false)) {
						echo '<div class="Entradas">'.$titulo.'</div>';
					}
					// $data_price = get_post_meta($post->ID, 'tourmaster-tour-option', true);
					// array_push($prices, (empty($data_price['tour-price-text'])) ? 0 : intval($data_price['tour-price-text'])  ) ;
					// $feature_img = get_the_post_thumbnail_url($post->ID,'full');
					echo "<br>";
					// echo "NOMBRE DEL POST: " . $post->post_title;
					// var_dump($post); 
				}  
			 }

			 echo '</div>'; 
			 echo '</div>'; // FIN DE CARDS
			 echo '</div>';




			 echo '</div>';

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