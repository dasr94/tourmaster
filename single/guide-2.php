<?php
get_header();

$page_type = $_GET['guide'];
if( $page_type != '' ){
	$guide_name = str_replace('-', ' ', $_GET['guide']);
	$args = array(
		'meta_query' => array(
			array(
				'key' => 'guide-company',
				'value' => $guide_name,
				'compare' => '='
			)
		)
	);
	$guide = get_users($args, true);
	$guide_id = $guide[0]->ID;
	$img = get_avatar_url($guide_id );
	$info = get_user_meta($guide_id, 'guide-info', true);
	$state = get_user_meta($guide_id, 'state', true);
	$city = get_user_meta($guide_id, 'city', true);
	$slider = get_user_meta($guide_id, 'header-slider', true);
	$video = get_user_meta($guide_id, 'youtube', true);
	$first_name = get_user_meta($guide_id, 'first_name', true);
	$last_name = get_user_meta($guide_id, 'last_name', true);

	$args_p = array(
		'post_status' => 'publish',
		'post_type' => 'tour',
		'posts_per_page' => 1000,
		'author' => $guide_id
	);

	$meta_query = array(
		'relation' => 'AND'
	);

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

	if( !empty($meta_query) ){
		$args_p['meta_query'] = $meta_query;
	}

	$posts_g = new WP_Query($args_p);

	$header_img_id = get_user_meta($guide_id, 'image-guide', true);
	$header_img = wp_get_attachment_url($header_img_id);

	?>
	<div class="gdlr-core-pbf-wrapper " style="margin: 0px 0px 0px 0px;padding: 115px 0px 115px 0px;">
		<div class="gdlr-core-pbf-background-wrap">
			<!-- <div class="gdlr-core-pbf-background" style="background-image: url(&quot;https://theoutdoortrip.stg.elaniin.dev/wp-content/uploads/2020/11/AdobeStock_220302498_Preview-1.jpg&quot;); background-size: cover; background-position: center center;" data-parallax-speed="0.8"></div> -->
			<div class="gdlr-core-pbf-background" style="background-image: url(&quot;<?php echo $header_img; ?>&quot;); background-size: cover; background-position: top center;" data-parallax-speed="0.8"></div>
		</div>
		<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
				<div class="gdlr-core-pbf-column gdlr-core-column-first first-c">
					<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
						<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
							<div class="gdlr-core-pbf-element">
								<?php $img = str_replace("-96x96","", $img) ?>
								<?php echo '<img class="img-guide-c" src="'.$img.'" alt="" width="600" height="600" title="'.$guide_name.'">'?>
							</div>
						</div>
					</div>
				</div>
				<div class="gdlr-core-pbf-column gdlr-core-column-40">
					<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
						<div class="gdlr-core-pbf-background-wrap"></div>
						<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
							<div class="gdlr-core-pbf-element">
								<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr banner_text">
									<div class="gdlr-core-title-item-title-wrap">
										<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 43px ;letter-spacing: 5px ;color: #ffffff ;"><?php echo $guide_name ?><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
									</div>
									<div class="gdlr-core-title-item-title-wrap">
										<p class="gdlr-core-skin-title location" style="font-weight: 500;"><?php echo $state; echo  ($city == "") ? "" : " - " . $city; ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="tourmaster-single-tour-content-wrap">
		<div class="gdlr-core-page-builder-body">
			<div class="gdlr-core-pbf-wrapper " style="padding: 0px 0px 0px 0px;">
				<div class="gdlr-core-pbf-background-wrap">
				</div>
				<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
					<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-pbf-wrapper-full">
						<div class="gdlr-core-pbf-element">
							<div class="tourmaster-content-navigation-item-wrap clearfix" style="padding-bottom: 0px; height: auto;">
								<div class="tourmaster-content-navigation-item-outer" id="tourmaster-content-navigation-item-outer">
									<div class="tourmaster-content-navigation-item-container tourmaster-container">
										<div class="tourmaster-content-navigation-item tourmaster-item-pdlr">
											<a class="tourmaster-content-navigation-tab tourmaster-active tourmaster-slidebar-active" href="#about" data-anchor="#about">ABOUT</a>
											<a class="tourmaster-content-navigation-tab" href="#photos" data-anchor="#photos">PHOTOS</a>
											<a class="tourmaster-content-navigation-tab" href="#clients" data-anchor="#clients">CLIENTS</a>
											<a class="tourmaster-content-navigation-tab" href="#trips" data-anchor="#trips">OUR TRIPS</a>
											<div class="tourmaster-content-navigation-slider" style="width: 91px; left: 15px; overflow: hidden;"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon" id="photos">
				<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
					<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video">
						<div class="gdlr-core-pbf-column gdlr-core-column-30 gdlr-core-column-first">
							<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
								<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 40px ;">
											<div class="gdlr-core-title-item-title-wrap">
												<!-- <h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 24px ;font-weight: 700 ;letter-spacing: 1px ;">Video<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2> -->
												<?php
													// echo $video;
													$pos1 = strpos($video, "?v=");
													// echo $pos1 . "<br>";
													// echo substr($video, $pos1 + 3);

													echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.substr($video, $pos1 + 3).'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="gdlr-core-pbf-column gdlr-core-column-30">
							<div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="padding: 0px 40px 0px 0px;">
								<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 40px ;">
											<div class="gdlr-core-title-item-title-wrap">
												<!-- <h3 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 24px ;font-weight: 700 ;letter-spacing: 1px ;">Images<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h3> -->
												<div style="display: flex; flex-direction: row;">

												<?php
													$sld = json_decode($slider);
													if(empty($sld)){

													} else {

														foreach ($sld as $ky => $value) {
															echo "<div style='width: 32%; padding: 15px; background-color: #F2F2F2; margin: 5px;'>";
															echo "<img src=".$value->thumbnail.">";
															echo "</div>";
														}
													}
												?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon" id="about">
				<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
					<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video">
						<div class="gdlr-core-pbf-column gdlr-core-column-30 gdlr-core-column-first">
							<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
								<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 10px ;">
											<div class="gdlr-core-title-item-title-wrap">
												<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 24px ;font-weight: 700 ;letter-spacing: 1px ;">About<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
											</div>
										</div>
									</div>
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align" style="padding-bottom: 35px ;">
											<div class="gdlr-core-text-box-item-content">
												<p style="white-space: pre-line;"><?php echo $info ?></p>
											</div>
										</div>
									</div>
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 10px ;">
											<div class="gdlr-core-title-item-title-wrap">
												<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 24px ;font-weight: 700 ;letter-spacing: 1px ;">Owner<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
												<p style="font-weight: 500; padding-top: 10px;"><?php echo $first_name . " " . $last_name; ?></p>
											</div>
										</div>
									</div>
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align" style="padding-bottom: 35px ;">
											<div class="gdlr-core-text-box-item-content">
												<p style="white-space: pre-line;"></p>
											</div>
										</div>
									</div>
									<!-- <div class="gdlr-core-pbf-element">
										<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 10px ;">
											<div class="gdlr-core-title-item-title-wrap">
												<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 24px ;font-weight: 700 ;letter-spacing: 1px ;">Team<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
											</div>
										</div>
									</div> -->
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align" style="padding-bottom: 35px ;">
											<div class="gdlr-core-text-box-item-content">
												<p style="white-space: pre-line;"></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="gdlr-core-pbf-column gdlr-core-column-30">
							<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
								<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 10px ;">
											<div class="gdlr-core-title-item-title-wrap">
												<div class="contact_tot">
													<div class="contact_header">
														<h2>Contact the outfitter</h2>
														
													</div>
													<div class="contact_body">
														<div class="form_input">
															<label for="first_name">Your First Name</label>
															<input type="text" name="first_name" placeholder="Enter Your First Name">
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<label for="first_name">Your Last Name</label>
															<input type="text" name="last_name" placeholder="Enter Your Last Name">
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<label for="first_name">Your Email Address</label>
															<input type="text" name="email" placeholder="Enter Your Email Address">
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<label for="first_name">Your Message</label>
															<textarea type="text" name="message" placeholder="Ready to book, or have a question? Type your message here..."></textarea>
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<button class="form_send">Send</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> 
			
			<div class="gdlr-core-pbf-wrapper " style="padding: 0px 0px 30px 0px;" data-skin="Blue Icon" id="clients">
				<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
					<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video">
						<div class="gdlr-core-pbf-element">
							<div class="gdlr-core-text-box-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-left-align">
								<div class="gdlr-core-text-box-item-content">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="gdlr-core-pbf-wrapper " style="padding: 0px 0px 30px 0px;" data-skin="Blue Icon" id="trips">
				<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
					<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container ">
						<div class="gdlr-core-pbf-column gdlr-core-column-20">
							<div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="padding: 0px 40px 0px 0px;">
								<?php 
								$hoy_dmy = date("d-m-Y");
								echo '<div id="search-form-fixed" style="position: relative; padding-top: 10px;" class="tourmaster-tour-search-item custom-search-bar clearfix tourmaster-style-column tourmaster-column-count-8 tourmaster-item-pdlr tourmaster-input-style-no-border">
								<div class="tourmaster-tour-search-wrap ">
								<form class="tourmaster-form-field  tourmaster-medium" action="https://theoutdoortrip.stg.elaniin.dev/guides/?='.$page_type.'" method="GET">
								<div id="" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px; width: 100%;">
								<input autocomplete="off" type="text" value="' . (empty($_GET['guide'])? '': esc_attr(($_GET['guide']))) . '" placeholder="Search by guide" max="6" id="trip-guide" name="guide" list="data-guides"/>
								<template id="template-guides"></template>
								<datalist id="data-guides"></datalist>
								</div>
								<div id="" class="tourmaster-tour-search-field tourmaster-tour-search-field-date" style="padding-right: 10px;margin-bottom: 10px; width: 100%;">
								<div class="tourmaster-datepicker-wrap" >
								<input type="hidden" value="'.$page_type.'" name="guide"/>
								<input readonly class="tourmaster-datepicker" type="text" 
								value="' . (empty($_GET['date-start'])? date('Y-m-d') : esc_attr($_GET['date-start'])) . '" placeholder="' . esc_html__('Start Date', 'tourmaster') . '" data-date-format="' . esc_attr(tourmaster_get_option('general', 'datepicker-date-format', 'dd mm yy')) . '" /><input class="tourmaster-datepicker-alt" name="date-start" id="date-start" type="hidden" value="' . (empty($_GET['date-start'])? date('Y-m-d') : esc_attr($_GET['date-start'])) . '" />
								</div>
								</div>
								<div id="" class="tourmaster-tour-search-field tourmaster-tour-search-field-date" style="padding-right: 10px;margin-bottom: 10px; width: 100%;">
								<div class="tourmaster-datepicker-wrap" >
								<input readonly class="tourmaster-datepicker" type="text" 
								value="' . (empty($_GET['date-end'])? date("Y-m-d", strtotime($hoy_dmy . "+ 1 days")) : esc_attr($_GET['date-end'])) . '" placeholder="' . esc_html__('End Date', 'tourmaster') . '" data-date-format="' . esc_attr(tourmaster_get_option('general', 'datepicker-date-format', 'dd mm yy')) . '" /><input class="tourmaster-datepicker-alt" name="date-end" id="date-end" type="hidden" value="' . (empty($_GET['date-end'])? date("Y-m-d", strtotime($hoy_dmy . "+ 1 days")) : esc_attr($_GET['date-end'])) . '" />
								</div>
								</div>
								<input type="hidden" id="trip-data-location" name="data-place" value="'.esc_attr($data_place).'"/>
								<div id="" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px; width: 100%;">
								<input autocomplete="off" type="text" value="' . (empty($_GET['species'])? '': esc_attr(($_GET['species']))) . '" placeholder="Search by species" max="6" id="trip-species" name="species" list="data-species"/>
								<template id="template-species">
								<option>Alligator</div>
								<option>Catfish</div>
								<option>Duck</div>
								<option>Quail</div>
								<option>White Tail Dear</div>
								<option>Red Fish</div>
								<option>Wild Boar</div>
								<option>Turkey</div>
								</template>
								<datalist id="data-species">
								</datalist>
								</div>
								<div  id="" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px; width: 100%;">
								<input autocomplete="off" type="number" value="' . (empty($_GET['guest'])? '1': esc_attr(($_GET['guest']))) . '" placeholder="People" id="trip-guest" name="guest" list="data-guest"/>
								</div>
								<div id="" class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px; width: 100%;">
								<input class="tourmaster-tour-search-submit" type="submit" value="Search">
								</div>
								</form></div></div>';
								?>
							</div>
						</div>
						<div class="gdlr-core-pbf-column gdlr-core-column-40">
							<div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="padding: 0px 0px 0px 0px;">
								<div class="gdlr-core-pbf-background-wrap"></div>
								<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
									<div class="gdlr-core-pbf-element">
										<div class="gdlr-core-pbf-sidebar-content-inner">
											<div class="gdlr-core-pbf-element">
												<div class="tourmaster-tour-item clearfix  tourmaster-tour-item-style-full">
													<div class="tourmaster-tour-item-holder gdlr-core-js-2 clearfix" data-layout="fitrows">
														<?php
															
														foreach ( $posts_g->posts as $post ) { 
														$data_price = get_post_meta($post->ID, 'tourmaster-tour-option', true);
														$price = $data_price['tour-price-text'];
														$feature_img = get_the_post_thumbnail_url($post->ID,'full');
														$state = get_post_meta($post->ID, 'state');
														$city_arr = get_post_meta($post->ID, 'city');
														$city = (($city_arr[0] == "") ? "" : " - " . $city_arr[0]);
														$contenido = "";
														if(strlen($post->post_content) > 412) {
															$contenido = substr($post->post_content, 0, 412) . "...";
														} else {
															$contenido = $post->post_content;
														}
														
														echo '<div class="tourmaster-item-list tourmaster-tour-full tourmaster-item-mglr clearfix tourmaster-tour-frame trip_item" style="height: 230px !important">
														
															<div class="tourmaster-tour-full-inner trips_item_content" style="padding: 10px !important">

																<div class="tourmaster-tour-content-wrap custom-warp-g-t3 clearfix gdlr-core-skin-e-background" style="width:74%; display: inline-block; padding: 0; height: 100%;">
																	<div class="">
																		<h3 class="tourmaster-tour-title gdlr-core-skin-title" style="text-transform: uppercase; margin-bottom: 0px;">
																			<a href="'.$post->guid.'">'.$post->post_title.'</a>
																		</h3>
																		<p style="margin: 0; padding: 0; margin-bottom: 5px;">/ ' . $state[0] .  $city . ' </p>
																	</div>
																	
																
															
																	<div class="tourmaster-tour-thumbnail trip-image-g" style="width: 200px !important;  height: 150px !important; display: inline-block;">
																		<a href="javascript:void(0)">
																			<img src="'.$feature_img.'" alt="" width="400" height="513">
																		</a>
																	</div>
																	<div class="tourmaster-tour-content-wrap custom-warp-g-t clearfix gdlr-core-skin-e-background" style="width: 300px !important; display: inline-block;">
																		<div class="">
																			<p>'. $contenido .'</p>
																		</div>
																		<div class="tourmaster-center-tour-content" style="padding-top: 21.5px; padding-bottom: 21.5px;">
																			
																		</div>
																	</div>
																</div>
																<div class="tourmaster-tour-content-wrap custom-warp-g-t2 clearfix gdlr-core-skin-e-background" style="border-left: 1px solid #828282; padding-left: 10px !important; width: 25% !important">
																	<div class="tourmaster-center-tour-content" style="padding-top: 21.5px; padding-bottom: 21.5px;">
																		<div class="tourmaster-tour-price-wrap " >
																			<span class="tourmaster-tour-price" style="text-align: center; display: flex;">
																				
																				<span class="tourmaster-tail" style="font-size: 20px; text-align: center; width: 100%;">From $'.$price.'</span>
																			</span>
																		</div>
																		<div class="tourmaster-tour-price-wrap ">
																		<form method="POST" id="'.$post->ID.'" action="https://theoutdoortrip.stg.elaniin.dev/?tourmaster-payment">
																		<input type="hidden" name="tour-id" value="'.$post->ID.'">
																		<input type="hidden" name="tour-start" id="tour-start-'.$post->ID.'" value="">
																		<input type="hidden" name="tour-end" id="tour-end-'.$post->ID.'" value="">
																		<input type="hidden" name="tour-date" id="tour-date-'.$post->ID.'" value="">
																		<input type="hidden" name="tour-people" id="tour-people-'.$post->ID.'" value="">
																		<input type="button" value="BOOK NOW" onclick="pasarDatos('.$post->ID.')" class="tourmaster-tour-view-more" style="display:block; width: 100%; background-color: #F6A32A; font-size: 20px;">
																		</form>
																			
																		</div>
																	</div>
																</div>
															</div>
														</div>';
															
														}
														?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<script>
	
</script>

	<?php
}else{
	$data = get_users( [ 'role__in' => [ 'author' ] ] );
	echo '<div class="gdlr-core-pbf-wrapper  hero-row-custom" style="padding: 40px 0px 40px 0px;" data-skin="Dark">
		<div class="gdlr-core-pbf-background-wrap">
			<div class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js" style="background-image: url(&quot;https://theoutdoortrip.stg.elaniin.dev/wp-content/uploads/2020/07/fly-fishing-guides.jpg&quot;); background-size: cover; background-position: center center; height: 400px; transform: translate(0px, -9.6px);" data-parallax-speed="0.3">
			</div>
		</div>
		<div class="gdlr-core-pbf-wrapper-content gdlr-core-js" data-gdlr-animation-duration="600ms" data-gdlr-animation-offset="0.8" style="">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-pbf-wrapper-full-no-space">
				<div class="gdlr-core-pbf-element">
					<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-center-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr">
						<div class="gdlr-core-title-item-title-wrap">
							<h3 class="gdlr-core-title-item-title gdlr-core-skin-title" style="color: white; font-size: 75px ;letter-spacing: 0px ;text-transform: none ;">Our Guides</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>';

	echo '<div class="gdlr-core-page-builder-body">
		<div class="gdlr-core-pbf-section">
			<div class="gdlr-core-pbf-section-container gdlr-core-container clearfix">
				<div class="gdlr-core-pbf-element">';
	$counter = 0;
	foreach ( $data as $user ) {
		$str_info = substr(get_user_meta($user->ID, 'guide-info', true),0,350);
		$img = get_avatar_url($user->ID);
		$url = str_replace(' ', '-', get_user_meta($user->ID, 'guide-company', true));
		if ($counter < 12 ) {
		echo '<div class="gdlr-core-portfolio-item gdlr-core-item-pdb clearfix  gdlr-core-portfolio-item-style-medium" style="padding-bottom: 40px ;">
				<div class="gdlr-core-portfolio-item-holder gdlr-core-js-2 clearfix" data-layout="fitrows">
					<div class="gdlr-core-item-list gdlr-core-portfolio-medium  content-custom-w-g  gdlr-core-size-small gdlr-core-style-left gdlr-core-item-pdlr">
						<div class="gdlr-core-portfolio-thumbnail-wrap gdlr-core-portfolio-thumbnail-wrap-custom">
							<div class="gdlr-core-portfolio-thumbnail gdlr-core-media-image  gdlr-core-style-margin-icon">
								<div class="gdlr-core-portfolio-thumbnail-image-wrap  gdlr-core-zoom-on-hover">
									<a class="gdlr-core-ilightbox " href="https://theoutdoortrip.stg.elaniin.dev/guides/?guide='.$url.'">
										<img src="'.$img.'" alt="" width="1920" height="1267">
										
									</a>
								</div>
							</div>
						</div>
						<div class="gdlr-core-portfolio-content-wrap">
							<h3 class="gdlr-core-portfolio-title gdlr-core-skin-title" style="font-size: 22px ;font-weight: 700 ;letter-spacing: 0px ;text-transform: none ;">
								<a href="https://theoutdoortrip.stg.elaniin.dev/guides/?guide='.$url.'">'.get_user_meta($user->ID, 'guide-company', true).'</a>
							</h3>
							<div class="gdlr-core-portfolio-content">'.$str_info.'[…]</div>
						</div>
					</div>
				</div>
			</div>';
	}
						$counter += 1;
}
echo '</div></div></div></div>';

}



				/*'<div class="gdlr-core-pagination  gdlr-core-style-rectangle gdlr-core-left-align gdlr-core-item-pdlr"><a class="prev page-numbers" href="https://theoutdoortrip.stg.elaniin.dev/portfolio-left-small-thumbnail/page/2/"></a>
<a class="page-numbers" href="https://theoutdoortrip.stg.elaniin.dev/portfolio-left-small-thumbnail/page/1/">1</a>
<a class="page-numbers" href="https://theoutdoortrip.stg.elaniin.dev/portfolio-left-small-thumbnail/page/2/">2</a>
<span aria-current="page" class="page-numbers current">3</span>
<a class="page-numbers" href="https://theoutdoortrip.stg.elaniin.dev/portfolio-left-small-thumbnail/page/4/">4</a>
<a class="next page-numbers" href="https://theoutdoortrip.stg.elaniin.dev/portfolio-left-small-thumbnail/page/4/"></a></div></div></div></div></div></div>';
/*
echo '<div class="guides-body">';
echo '<div class="guides-background-wrap" style="background-color: #191919 ;"><p></p></div>';
echo '</div>';

echo '<div class="gdlr-core-pbf-wrapper " style="margin: 0px 0px 0px 0px;padding: 100px 0px 60px 0px;"><div class="gdlr-core-pbf-background-wrap"><div class="gdlr-core-pbf-background gdlr-core-parallax gdlr-core-js" style="background-image: url(&quot;https://theoutdoortrip.stg.elaniin.dev/wp-content/uploads/2016/06/section-bg-3.jpg&quot;); background-size: cover; background-position: center center; height: 262px; transform: translate(0px, -64.8px);" data-parallax-speed="0.8"></div></div><div class="gdlr-core-pbf-wrapper-content gdlr-core-js "><div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container"><div class="gdlr-core-pbf-column gdlr-core-column-20 gdlr-core-column-first"><div class="gdlr-core-pbf-column-content-margin gdlr-core-js "><div class="gdlr-core-pbf-background-wrap"></div><div class="gdlr-core-pbf-column-content clearfix gdlr-core-js " style="max-width: 300px ;"><div class="gdlr-core-pbf-element"><div class="gdlr-core-image-item gdlr-core-item-pdb  gdlr-core-center-align gdlr-core-item-pdlr"><div class="gdlr-core-image-item-wrap gdlr-core-media-image  gdlr-core-image-item-style-circle" style="border-width: 0px;"><a class="gdlr-core-ilightbox gdlr-core-js " href="https://theoutdoortrip.stg.elaniin.dev/wp-content/uploads/2016/05/personnel-1.jpg" data-type="image"><img src="https://theoutdoortrip.stg.elaniin.dev/wp-content/uploads/2016/05/personnel-1-600x600.jpg" alt="" width="600" height="600" title="personnel-1"><span class="gdlr-core-image-overlay "><i class="gdlr-core-image-overlay-icon  gdlr-core-size-22 fa fa-search"></i></span></a></div></div></div></div></div></div><div class="gdlr-core-pbf-column gdlr-core-column-40"><div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="padding: 55px 0px 0px 0px;"><div class="gdlr-core-pbf-background-wrap"></div><div class="gdlr-core-pbf-column-content clearfix gdlr-core-js "><div class="gdlr-core-pbf-element"><div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr"><div class="gdlr-core-title-item-title-wrap"><h3 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 54px ;letter-spacing: 5px ;color: #ffffff ;">Jeanette Kingston<span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h3></div><span class="gdlr-core-title-item-caption gdlr-core-info-font gdlr-core-skin-caption" style="font-size: 20px ;color: #ffffff ;">Chief Executive Officer</span></div></div><div class="gdlr-core-pbf-element"><div class="gdlr-core-social-network-item gdlr-core-item-pdb  gdlr-core-left-align gdlr-core-item-pdlr"><a href="mailto:#" target="_blank" class="gdlr-core-social-network-icon" title="email" style="font-size: 16px ;color: #ffffff ;"><i class="fa fa-envelope"></i></a><a href="#" target="_blank" class="gdlr-core-social-network-icon" title="facebook" style="font-size: 16px ;color: #ffffff ;"><i class="fa fa-facebook"></i></a><a href="#" target="_blank" class="gdlr-core-social-network-icon" title="google-plus" style="font-size: 16px ;color: #ffffff ;"><i class="fa fa-google-plus"></i></a><a href="#" target="_blank" class="gdlr-core-social-network-icon" title="skype" style="font-size: 16px ;color: #ffffff ;"><i class="fa fa-skype"></i></a><a href="#" target="_blank" class="gdlr-core-social-network-icon" title="twitter" style="font-size: 16px ;color: #ffffff ;"><i class="fa fa-twitter"></i></a></div></div></div></div></div></div></div></div>';
*/
get_footer();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
	// console.log($("#date-start").val
	// console.log($("#date-end").val());
	function pasarDatos(id){
		$("#tour-start-" + id).val($("#date-start").val());
		$("#tour-date-" + id).val($("#date-start").val());
		$("#tour-end-" + id).val($("#date-end").val());
		$("#tour-people-" + id).val($("#trip-guest").val());
		console.log($("#tour-start-" + id).val());
		console.log($("#tour-end-" + id).val());
		console.log("se hizo form");
		$("#" + id).submit();
		return false;
	}
</script>
<?php

?>