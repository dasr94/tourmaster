// AQUI INICIA TODO

		if($_GET['species_search'] && !empty($_GET['species_search'])) {
			$tax = $_GET['species_search'];
		} else {
			$tax = "";
		}
		if($_GET['guide_name'] && !empty($_GET['guide_name'])) {
			$guide_nombre = '*' . $_GET['guide_name'] . '*';
			$titlo_post = $_GET['guide_name'];
		} else {
			$guide_nombre = "";
		}

		$args = array(
			'user-type' => 'user-guide',
			'search' => $guide_nombre,
			'exclude' => array(1)
		);

		$users = get_users($args);
		if (!empty($users)){
			// ENCONTRO USUARIOS Y LOS IMPRIMIRA
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
				if ($posts_g->have_posts()) {	
					$prices = array();
					foreach($posts_g->posts as $post){
						$data_price = get_post_meta($post->ID, 'tourmaster-tour-option', true);
						array_push($prices, (empty($data_price['tour-price-text'])) ? 0 : intval($data_price['tour-price-text'])  ) ;
						$feature_img = get_the_post_thumbnail_url($post->ID,'full');
						// echo "<br>";
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

		} else {
			// NO ENCONTRO USUARIOS VA A BUSCAR POST
			$args = array(
				'user-type' => 'user-guide',
				'exclude' => array(1)
			);
			$users = get_users($args);
			$hay = FALSE;
			foreach ($users as $usr) {
				$usr_id = $usr->ID;
				// echo "USUARIO " . $usr_id;
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
				if ($posts_g->have_posts()) {	
					foreach($posts_g->posts as $post){
						$titulo = $post->post_title;
						$tit=strtoupper($titulo); 
						$fil=strtoupper($titlo_post);
						if ( strpos($tit,$fil) !== false ) {
							// echo " EL TITULO ES: " . $titulo;
							$hay = TRUE;
							break;
						}
					}  
				}
			}


			if($hay){

				foreach ($users as $usr) {
					$usr_id = $usr->ID;
					// echo "USUARIO " . $usr_id;
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
					$hayUsuario = FALSE;
					if ($posts_g->have_posts()) {	
						$prices = array();
						foreach($posts_g->posts as $post){
							$data_price = get_post_meta($post->ID, 'tourmaster-tour-option', true);
							array_push($prices, (empty($data_price['tour-price-text'])) ? 0 : intval($data_price['tour-price-text'])  ) ;
							$feature_img = get_the_post_thumbnail_url($post->ID,'full');
							$titulo = $post->post_title;
							$tit=strtoupper($titulo); 
							$fil=strtoupper($titlo_post);
							if ( strpos($tit,$fil) !== false ) {
								// echo " EL TITULO ES: " . $titulo;
								$hayUsuario = TRUE;
								break;
							}
						}  
						sort($prices, 1);
						if($hayUsuario){

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

						} else {

						}
						
					}
				}

			} else {
				// NOT FOUND
				echo '</div>';
				echo '</div>';
				echo '<div class=" custom-content-2 tourmaster-single-search-not-found-wrap tourmaster-item-pdlr" >';
				echo '<div class="tourmaster-single-search-not-found-inner" >';
				echo '<div class="tourmaster-single-search-not-found" >';
				echo '<h3 class="tourmaster-single-search-not-found-title" >' . esc_html__('Not Found', 'tourmaster') . '</h3>';
				echo '<div class="tourmaster-single-search-not-found-caption" >' . esc_html__('Nothing matched your search criteria. Please try again with different keywords', 'tourmaster') . '</div>';
				echo '</div>'; // tourmaster-single-search-not-found
				// TERMINA NOT FOUND
			}

		}







       //OTRA COSA
       
       
       // ENCONTRO USUARIOS Y LOS IMPRIMIRA
		$i = 0;
		foreach ($users as $usr) {
			array_push($arrayEnviar, $usr->ID);

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
			
			if ($posts_g->have_posts()) {	
				$prices = array();
				foreach($posts_g->posts as $post){
					$data_price = get_post_meta($post->ID, 'tourmaster-tour-option', true);
					array_push($prices, (empty($data_price['tour-price-text'])) ? 0 : intval($data_price['tour-price-text'])  ) ;
					$feature_img = get_the_post_thumbnail_url($post->ID,'full');
				}  
				sort($prices, 1);
				// AQUI IRIA EL CONTENIDO
				$arrayEnviar[$i]['ID'] = $usr->ID;
				$arrayEnviar[$i]['latitude'] = $usr->latitude;
				$arrayEnviar[$i]['longitude'] = $usr->longitude;
				$arrayEnviar[$i]['img_url'] = $header_img;
				$arrayEnviar[$i]['guide_url'] = 'https://theoutdoortrip.stg.elaniin.dev/guides/?guide=' . $guide_name;
				$arrayEnviar[$i]['guide_name'] = $guide_name;
				$arrayEnviar[$i]['amount'] = 0;
				$arrayEnviar[$i]['price'] = $prices[0];
				unset($prices);
			}
			$i++
		}