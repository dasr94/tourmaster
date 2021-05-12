<?php
get_header();

$page_type = $_GET['guide'];
if( $page_type != '' ){
	$guide_name = str_replace('-', ' ', $_GET['guide']);
	$guide_name = stripslashes($guide_name);
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
	<style> 
		#search-box {
			display: flex; 
			flex-flow: row nowrap; 
			background: #E0E0E0; 
			justify-content: space-between; 
			align-items: center;
		}
		#search-box .item {
			width: 20%;
		}
		#banner-main {
			height: 720px;
		}
		.trip_item {
			height: 230px !important;
		}
		.custom-warp-g-t2 {
			padding-left: 10px !important;
		}
		#bnr-top {
			background-image: url('<?php echo $header_img; ?>'); 
			background-size: cover; 
			background-position: top center;
		}
		#mobile-banner {
			display: none;
		}
		@media only screen and (max-width: 700px) { 
			#label-custom {
				font-size: 10px;
			}
			#bnr-top {
				background-size: contain; 
				background-position: bottom center;
				background-repeat: no-repeat;
			}
			.trip_item {
				height: auto !important;
			}
			.trip_item .custom-warp-g-t3, .trip_item .custom-warp-g-t2 {
				width: 100% !important;
				border: 0 !important;
				padding: 0 !important;
			}
			#card-trip-item-2 {
				width: 100% !important;
				padding-left: 0 !important; 
			}
			#banner-main {
				height: 500px;
				display: none;
			}
			
			.item-btn {
				width: 100% !important;
				margin: 0 auto;
				text-align: center;
				margin-bottom: 10px;
			}
			#search-box .item {
				width: 49%;
			}
			#search-box {
				display: flex; 
				flex-flow: row wrap; 
			}
			.item-btn .form_send {
				float: none !important;
				margin: 0 auto;
				text-align: center;
				color: white;
			}
			.contact_body {
				padding-right: 10px;
				padding-left: 10px;
			}
			#mobile-banner {
				display: flex;
				flex-direction: column;
			}
			
		}
	</style>
	<div class="gdlr-core-pbf-wrapper " style="margin: 0px 0px 0px 0px;padding: 0px 0px 0px 0px;">
	<form action="https://theoutdoortrip.com/tours/"  method="GET">
		<div id="search-box">
				<div class="item">
					<div class="contact_body">
						<div class="form_input" style="padding-bottom: 20px;">
							<label for="destination" id="label-custom">Destination or Guide Name</label>
							<input type="text" name="destination" id="destination" placeholder="Enter Destination or Guide Name">
						</div>
					</div>
				</div>
				<div class="item">
					<div class="contact_body">
						<div class="form_input" style="padding-bottom: 20px;">
							<label for="trip_date" id="label-custom">Trip Date</label>
							<input type="date" name="trip_date" id="trip_date" placeholder="Trip Date">
						</div>
					</div>
				</div>
				<div class="item">
					<div class="contact_body">
						<div class="form_input" style="padding-bottom: 20px;">
							<label for="type" id="label-custom">Type</label>
							<select name="type" id="type" style="padding-right: 14px; padding-left: 14px; width: 100%; background: #FFFFFF; height: 45px; border: 1px solid #E0E0E0;box-sizing: border-box;">
								<option value="Fishing">Fishing</option>
								<option value="Hunting">Hunting</option>
							</select>
						</div>
					</div>
				</div>
				<div class="item">
					<div class="contact_body">
						<div class="form_input" style="padding-bottom: 20px;">
							<label for="species" id="label-custom">Species</label>
							<select name="species" id="species"  style="padding-right: 14px; padding-left: 14px; width: 100%; background: #FFFFFF; height: 45px; border: 1px solid #E0E0E0;box-sizing: border-box;">
								<option value="" selected disabled>Choose Your Species</option>
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
					</div>
				</div>
				<div class="item-btn item">
					<div class="form_input" style="padding-right: 10px;">
						<button class="form_send">Search</button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="gdlr-core-pbf-wrapper " id="banner-main" style="margin: 0px 0px 0px 0px;padding: 0px 0px 115px 0px;">


		<div class="gdlr-core-pbf-background-wrap">
			<div class="gdlr-core-pbf-background" id="bnr-top" data-parallax-speed="0.8"></div>
		</div>
		
		<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container" style="padding-left: 0; padding-top: 10px;">
				<div class="gdlr-core-pbf-column gdlr-core-column-first first-c">
					<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
						<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
							<div class="gdlr-core-pbf-element" style="background-color: rgba(130,130,130, 0.75); padding-left: 15px;">

									<div class="gdlr-core-title-item-title-wrap">
										<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 43px ;letter-spacing: 5px ;color: #ffffff ; text-transform: capitalize;"><?php echo $guide_name ?><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
									</div>
									<div class="gdlr-core-title-item-title-wrap">
										<p class="gdlr-core-skin-title location" style="font-weight: 500;"><?php echo $state; echo  ($city == "") ? "" : " - " . $city; ?></p>
									</div>

								<!-- <?php $img = str_replace("-96x96","", $img) ?>
								<?php echo '<img class="img-guide-c" src="'.$img.'" alt="" width="600" height="600" title="'.$guide_name.'">'?> -->
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
									<!-- <div class="gdlr-core-title-item-title-wrap">
										<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 43px ;letter-spacing: 5px ;color: #ffffff ;"><?php echo $guide_name ?><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
									</div>
									<div class="gdlr-core-title-item-title-wrap">
										<p class="gdlr-core-skin-title location" style="font-weight: 500;"><?php echo $state; echo  ($city == "") ? "" : " - " . $city; ?></p>
									</div> -->
									
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- <a href="#trips" data-anchor="#trips" style="position: absolute; right: 20px; bottom: 20px; padding: 20px; color: white; background:#F6A32A;">Book Now</button> -->
				<a href="#trips" data-anchor="#trips" style="position: absolute; right: 20px; bottom: 20px; padding: 15px 33px; color:#fff; background:#F6A32A; font-family: 'Poppins', sans-serif; font-weight: bold; font-size: 13px;">Book Now</a>
			</div>
		</div>
	</div>
	<div id="mobile-banner">
		<div class="item" style="background: white; text-align: center; color: white; padding-top: 20px;">
			<h5 style="text-transform: capitalize;"><?php echo $guide_name ?></h5>
			<h6><?php echo $state; echo  ($city == "") ? "" : " - " . $city; ?></h6>
		</div>
		<div class="item">
			<img style="width: 100%;" src="<?php echo $header_img; ?>" alt="">
		</div>
		<div class="item" style="background:#F6A32A; text-align: center; padding-top: 15px; padding-bottom: 15px;">
			<a style="color:#fff;  font-family: 'Poppins', sans-serif; font-weight: bold; font-size: 13px;" href="#trips">Book Now</a>
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
													$featured_img_id = get_user_meta($guide_id, 'image-custom-guide', true);
													$featured_img = wp_get_attachment_url($featured_img_id);
													if (!empty($pos1) || $pos1 == ""){
														echo '<img src="'.$featured_img.'" style="width: 500px;">';
													} else {
														echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.substr($video, $pos1 + 3).'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
													}
													
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
										<div class="gdlr-core-gallery-item gdlr-core-item-pdb clearfix  gdlr-core-gallery-item-style-slider gdlr-core-item-pdlr " style="display: flex; flex-direction: row; flex-wrap: wrap;">
											<?php
												$sld = json_decode($slider);
												if(empty($sld)){
												} else {
													$i = 0;
													foreach ($sld as $ky => $value) {
														if($i < 6) {

															echo "<div style='flex: 3; flex-basis: 32%; padding: 5px;'>";
															$imgBig = str_replace("-150x150", "", $value->thumbnail);
															echo '<div class="gdlr-core-gallery-list gdlr-core-media-image">';
															echo '<a class="gdlr-core-ilightbox gdlr-core-js " href="'.$imgBig.'" data-ilightbox-group="gdlr-core-img-group-1" data-type="image">';
															echo '<img src="'.$value->thumbnail.'" alt="" title="Largemouth-Bass-358×260" draggable="false">';
															echo '<span class="gdlr-core-image-overlay ">
																	<i class="gdlr-core-image-overlay-icon  gdlr-core-size-22 fa fa-search"></i>
																	</span>
																	</a>';
															echo '</div>';
															echo "</div>";
														} else {

															echo "<div style='flex: 3; flex-basis: 32%; padding: 5px; display: none;'>";
															$imgBig = str_replace("-150x150", "", $value->thumbnail);
															echo '<div class="gdlr-core-gallery-list gdlr-core-media-image">';
															echo '<a class="gdlr-core-ilightbox gdlr-core-js " href="'.$imgBig.'" data-ilightbox-group="gdlr-core-img-group-1" data-type="image">';
															echo '<img src="'.$value->thumbnail.'" alt="" title="Largemouth-Bass-358×260" draggable="false">';
															echo '<span class="gdlr-core-image-overlay ">
																	<i class="gdlr-core-image-overlay-icon  gdlr-core-size-22 fa fa-search"></i>
																	</span>
																	</a>';
															echo '</div>';
															echo "</div>";

														}
														$i++;
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
															<input type="text" name="first_name" id="first_name" placeholder="Enter Your First Name">
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<label for="first_name">Your Last Name</label>
															<input type="text" name="last_name" id="last_name" placeholder="Enter Your Last Name">
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<label for="first_name">Your Email Address</label>
															<input type="text" name="email" id="email" placeholder="Enter Your Email Address">
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<label for="first_name">Your Message</label>
															<textarea type="text" name="message" id="message" placeholder="Ready to book, or have a question? Type your message here..."></textarea>
														</div>
													</div>
													<div class="contact_body">
														<div class="form_input">
															<button class="form_send" onclick="enviarCorreo()">Send</button>
														</div>
														<span id="confirmation" style="color: #219653; display: none;">Mensaje enviado</span>
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
			
			<div class="gdlr-core-pbf-wrapper " style="padding: 0px 0px 30px 0px;" data-skin="Blue Icon" id="trips">
				<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
					<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container ">
						<div class="gdlr-core-pbf-column gdlr-core-column-10">
							<div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="padding: 0px 40px 0px 0px;">
							<h1 style="color:#ffffff">hola</h1>	
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
													<style>
														table tr td.selected {
															background: #F6A32A !important;
															color: #FFFFFF !important;
															cursor: pointer;
														}
														table {
															border-spacing: 0;	
														}
														table tr td.disabled {
															background: #ffffff;
															color: #BDBDBD;
															border: 1px solid #FAFAFA;
														}
														table tr td {
															background: #ffffff;
															color: #BDBDBD;
															border: 1px solid #DFDFDF;
															width: 50px;
														}
														table tr td.available {
															background: #ffffff;
															color: #000000;
															border: 1px solid #FAFAFA;
															cursor: pointer;
														}
														table tr th {
															background: #ffffff;
															color: #000000;
														}
													
													</style>
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
															$contenido = substr($post->post_content, 0, 190) . "...";
														} else {
															$contenido = $post->post_content;
														}
														
														echo '<div class="tourmaster-item-list tourmaster-tour-full tourmaster-item-mglr clearfix tourmaster-tour-frame trip_item" style="height: 230px" id="card-trip-item">
														
															<div class="tourmaster-tour-full-inner trips_item_content" style="padding: 10px !important">

																<div class="tourmaster-tour-content-wrap custom-warp-g-t3 clearfix gdlr-core-skin-e-background" style="width:74%; display: inline-block; padding: 0; height: 100%;" id="card-trip-item-1">
																	<div class="">
																		<h3 class="tourmaster-tour-title gdlr-core-skin-title" style="text-transform: uppercase; margin-bottom: 0px;">
																			<a href="#">'.$post->post_title.'</a>
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
																<div class="tourmaster-tour-content-wrap custom-warp-g-t2 clearfix gdlr-core-skin-e-background" style="border-left: 1px solid #828282; width: 25% " id="card-trip-item-2">
																	<div class="tourmaster-center-tour-content" style="padding-top: 21.5px; padding-bottom: 21.5px;">
																		<div class="tourmaster-tour-price-wrap " >
																			<span class="tourmaster-tour-price" style="text-align: center; display: flex;">
																				
																				<span class="tourmaster-tail" style="font-size: 20px; text-align: center; width: 100%;">From $'.$price.'</span>
																			</span>
																		</div>
																		<div class="tourmaster-tour-price-wrap ">
																		<form method="POST" id="'.$post->ID.'" action="https://theoutdoortrip.com/?tourmaster-payment">
																		<input type="hidden" name="tour-id" value="'.$post->ID.'" id="tour-id-'.$post->ID.'">
																		<input type="hidden" name="tour-start" id="tour-start-'.$post->ID.'" value="">
																		<input type="hidden" name="tour-end" id="tour-end-'.$post->ID.'" value="">
																		<input type="hidden" name="tour-date" id="tour-date-'.$post->ID.'" value="">
																		<input type="hidden" name="tour-people" id="tour-people-'.$post->ID.'" value="">
																		<input type="button" value="BOOK NOW" onclick="levantarModal('.$post->ID.')" class="tourmaster-tour-view-more" style="display:block; width: 100%; background-color: #F6A32A; font-size: 20px;">
																		</form>
																			
																		</div>
																	</div>
																</div>
															</div>
														</div>';


														$arrayDates = get_post_meta($post->ID, 'tour-available');
														$arrayDates = $arrayDates[0];

														echo '
														<style>
															.modal-'.$post->ID.' {
																display: none;
																position: fixed;
																z-index: 1;
																padding-top: 120px;
																left: 0;
																top: 0;
																width: 100%;
																height: 100%;
																overflow: auto;
																background-color: rgb(0,0,0);
																background-color: rgba(0,0,0,0.4);
															}
															.modal-content-'.$post->ID.' {
																background-color: #fefefe;
																margin: auto;
																padding: 20px;
																border: 1px solid #888;
																width: 80%;
																max-width: 600px;
															}
															.close-'.$post->ID.' {
																color: #aaaaaa;
																float: right;
																font-size: 28px;
																font-weight: bold;
																clear: both;
																cursor: pointer;
															}
														</style>
														<script>
															
														</script>
														<div id="myModal-'.$post->ID.'" class="modal-'.$post->ID.'">
															<div class="modal-content-'.$post->ID.'">
																<div style="display: flex; flex-flow: row nowrap; justify-content: flex-end">
																	<span class="close-'.$post->ID.'" onclick="cerrarModal('.$post->ID.')" >&times;</span>
																</div>
														';
														$arrayDates = get_post_meta($post->ID, 'tour-available');
														$arrayDates = $arrayDates[0]; 

														echo '<div id="m-'.$post->ID.'-2">
														<div style="display: flex; flex-direction: row; justify-content: space-between;">
														<div> <h6>'. date("M") .'</h6> </div>
														<div> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="nextMonth('.$post->ID.',2)">></span> </div>
														</div>';
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
        echo "<td onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" >", date("d", mktime(0,0,0,$mes,1,$año))  , "</td>"; 
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
        // echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), " </td>", "</tr>", "<tr>"; 


		if (!empty($arrayDates)) {

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
				
				if (date("d") < $j) {
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
					}
				} else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;	
		}

    }else{

		if (!empty($arrayDates)) {

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
				
				if (date("d") < $j) {
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
					}
				} else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;	
			
		}


		


        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";



														echo '</div>'; // AQQUI TERMINA FEBRERO
														echo '<div id="m-'.$post->ID.'-3" style="display: none;">
														<div style="display: flex; flex-direction: row; justify-content: space-between;">';
														
														$mes = date("m") + 1; 
														$año = date ("Y");
														if ($mes == 12) {
															$mes = 1;
															$año = $año + 1;
														}
														$nueva_fecha = $año . "-" . $mes . "-01";
														echo '
														<div> <h6>'. date("M, Y", strtotime($nueva_fecha)) .'</h6> </div>
														<div> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="prevMonth('.$post->ID.',3)"><</span> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="nextMonth('.$post->ID.',3)">></span> </div>
														</div>';

														$arrayDates = get_post_meta($post->ID, 'tour-available');
														$arrayDates = $arrayDates[0]; 

														

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
        echo "<td onclick=\"setDay('",date("d", mktime(0,0,0,$mes,1,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"','",$post->ID,"', 'All Day')\" >", date("d", mktime(0,0,0,$mes,1,$año))  , "</td>"; 
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
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {
        // echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), " </td>", "</tr>", "<tr>"; 


		if (!empty($arrayDates)) {

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
				
				if (date("d") < $j) {
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
					}
				} else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;	
		}

    }else{

		if (!empty($arrayDates)) {

			foreach ($arrayDates as $key => $value) {
				// echo "key: " . $key . " fecha: " . date("Y-m-d", mktime(0,0,0,$mes,$j,$año)) . "<br>";

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
				
				/* if (date("d") < $j) { */
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
					}
				/* } else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
				} */
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;	
			
		}


		


        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";

														echo '</div>'; // AQUI TERMINA MARZO
														echo '<div id="m-'.$post->ID.'-4" style="display: none;">
														<div style="display: flex; flex-direction: row; justify-content: space-between;">';
														$mes = date("m") + 2; 
														$año = date ("Y");
														if ($mes == 12) {
															$mes = 1;
															$año = $año + 1;
														}
														$nueva_fecha = $año . "-" . $mes . "-01";
														echo '
														<div> <h6>'. date("M, Y", strtotime($nueva_fecha)) .'</h6> </div>
														<div> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="prevMonth('.$post->ID.',4)"><</span> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="nextMonth('.$post->ID.',4)">></span> </div>
														</div>';

														
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
																echo "<td onclick=\"setDay('",date("d", mktime(0,0,0,$mes,1,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"','",$post->ID,"', 'All Day')\" >", date("d", mktime(0,0,0,$mes,1,$año))  , "</td>"; 
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
														for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
															if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {
																// echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), " </td>", "</tr>", "<tr>"; 
														
														
																if (!empty($arrayDates)) {
														
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
																		
																		if (date("d") < $j) {
																			switch ($ind) {
																				case '1':
																					echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
																					break;
																				case '2':
																					echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
																					break;
																				case '3':
																					echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
																					break;
																				case '4':
																					echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
																					break;
																				
																				default:
																					echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
																					break;
																			}
																		} else {
																			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
																		}
																		
																	}  else {
																		echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
																	}
																} else {
																	echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;	
																}
														
															}else{
														
																if (!empty($arrayDates)) {
														
																	foreach ($arrayDates as $key => $value) {
																		// echo "key: " . $key . " fecha: " . date("Y-m-d", mktime(0,0,0,$mes,$j,$año)) . "<br>";
														
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
																		
																		/* if (date("d") < $j) { */
																			switch ($ind) {
																				case '1':
																					echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
																					break;
																				case '2':
																					echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
																					break;
																				case '3':
																					echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
																					break;
																				case '4':
																					echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
																					break;
																				
																				default:
																					echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
																					break;
																			}
																		/* } else {
																			echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
																		} */
																		
																	}  else {
																		echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
																	}
																} else {
																	echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;	
																	
																}
														
														
																
														
														
																
															}
														}
														echo "</tr>";
														echo "</tbody>";
														echo "</table>";


														echo '</div>'; // AQUI TERMINA ABIL
														echo '<div id="m-'.$post->ID.'-5" style="display: none;">
														<div style="display: flex; flex-direction: row; justify-content: space-between;">';
														$mes = date("m") + 3; 
														$año = date ("Y");
														if ($mes == 12) {
															$mes = 1;
															$año = $año + 1;
														}
														$nueva_fecha = $año . "-" . $mes . "-01";
														echo '
														<div> <h6>'.date("M, Y", strtotime($nueva_fecha)).'</h6> </div>
														<div>  <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="prevMonth('.$post->ID.',5)"><</span> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="nextMonth('.$post->ID.',5)">></span>  </div>
														</div>';

														$arrayDates = get_post_meta($post->ID, 'tour-available');
														$arrayDates = $arrayDates[0]; 


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
        echo "<td onclick=\"setDay('",date("d", mktime(0,0,0,$mes,1,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"','",$post->ID,"', 'All Day')\" >", date("d", mktime(0,0,0,$mes,1,$año))  , "</td>"; 
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
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {
        // echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), " </td>", "</tr>", "<tr>"; 


		if (!empty($arrayDates)) {

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
				
				if (date("d") < $j) {
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
					}
				} else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;	
		}

    }else{

		if (!empty($arrayDates)) {

			foreach ($arrayDates as $key => $value) {
				// echo "key: " . $key . " fecha: " . date("Y-m-d", mktime(0,0,0,$mes,$j,$año)) . "<br>";

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
				
				/* if (date("d") < $j) { */
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
					}
				/* } else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
				} */
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;	
			
		}


		


        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";

														echo '</div>'; // AQUI TERMINA MAYO
														echo '<div id="m-'.$post->ID.'-6" style="display: none;">
														<div style="display: flex; flex-direction: row; justify-content: space-between;">';
														$mes = date("m") + 4; 
														$año = date ("Y");
														if ($mes == 12) {
															$mes = 1;
															$año = $año + 1;
														}
														$nueva_fecha = $año . "-" . $mes . "-01";
														echo '
														<div> <h6>'.date("M, Y", strtotime($nueva_fecha)).'</h6> </div>
														<div> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="prevMonth('.$post->ID.',6)"><</span> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="nextMonth('.$post->ID.',6)">></span> </div>
														</div>';

														$arrayDates = get_post_meta($post->ID, 'tour-available');
														$arrayDates = $arrayDates[0]; 


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
        echo "<td onclick=\"setDay('",date("d", mktime(0,0,0,$mes,1,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"','",$post->ID,"', 'All Day')\" >", date("d", mktime(0,0,0,$mes,1,$año))  , "</td>"; 
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
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {
        // echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), " </td>", "</tr>", "<tr>"; 


		if (!empty($arrayDates)) {

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
				
				if (date("d") < $j) {
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
					}
				} else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;	
		}

    }else{

		if (!empty($arrayDates)) {

			foreach ($arrayDates as $key => $value) {
				// echo "key: " . $key . " fecha: " . date("Y-m-d", mktime(0,0,0,$mes,$j,$año)) . "<br>";

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
				
				/* if (date("d") < $j) { */
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
					}
				/* } else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
				} */
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;	
			
		}


		


        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";

														echo '</div>'; // AQUI TERMINA JUNIO
														echo '<div id="m-'.$post->ID.'-7" style="display: none;">
														<div style="display: flex; flex-direction: row; justify-content: space-between;">';
														$mes = date("m") + 5; 
														$año = date ("Y");
														if ($mes == 12) {
															$mes = 1;
															$año = $año + 1;
														}
														$nueva_fecha = $año . "-" . $mes . "-01";
														echo '
														<div> <h6>'.date("M, Y", strtotime($nueva_fecha)).'</h6> </div>
														<div> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="prevMonth('.$post->ID.',7)"><</span> </div>
														</div>';

														$arrayDates = get_post_meta($post->ID, 'tour-available');
														$arrayDates = $arrayDates[0]; 


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
        echo "<td onclick=\"setDay('",date("d", mktime(0,0,0,$mes,1,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,1,$año)),"','",$post->ID,"', 'All Day')\" >", date("d", mktime(0,0,0,$mes,1,$año))  , "</td>"; 
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
for ($j=2;$j<=date("t", strtotime($nueva_fecha));$j++){
    if ( date("D",mktime(0,0,0,$mes,$j,$año))=="Sun" )  {
        // echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)), " </td>", "</tr>", "<tr>"; 


		if (!empty($arrayDates)) {

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
				
				if (date("d") < $j) {
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;
							break;
					}
				} else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
				}
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>", "</tr>", "<tr>" ;	
		}

    }else{

		if (!empty($arrayDates)) {

			foreach ($arrayDates as $key => $value) {
				// echo "key: " . $key . " fecha: " . date("Y-m-d", mktime(0,0,0,$mes,$j,$año)) . "<br>";

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
				
				/* if (date("d") < $j) { */
					switch ($ind) {
						case '1':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'All Day')\" > ",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '2':
							echo "<td class='available' id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Morning')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '3':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Afternoon')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						case '4':
							echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"'  onclick=\"setDay('",date("d", mktime(0,0,0,$mes,$j,$año)),"','",date("Y-m-d", mktime(0,0,0,$mes,$j,$año)),"','",$post->ID,"', 'Evening')\" >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
						
						default:
							echo "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
							break;
					}
				/* } else {
					echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
				} */
				
			}  else {
				echo      "<td>",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;			
			}
		} else {
			echo "<td >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;	
			
		}


		


        
    }
}
echo "</tr>";
echo "</tbody>";
echo "</table>";

														echo '</div>'; // AQUI TERMINA JULIO

														echo '<script> function nextMonth(post, idActual){
															document.getElementById("m-" + post + "-" + idActual).style.display = "none";
															document.getElementById("m-" + post + "-" + (idActual + 1 ) ).style.display = "block";
														} function prevMonth(post, idActual){
															document.getElementById("m-" + post + "-" + idActual).style.display = "none";
															document.getElementById("m-" + post + "-" + (idActual - 1 ) ).style.display = "block";
														} </script>';

														echo '
														<form method="POST" action="https://theoutdoortrip.com/?tourmaster-payment">
														<!-- <form method="POST" action="http://localhost/bookdev/?tourmaster-payment"> -->
															<input type="hidden" name="tour-id" value="" id="md-tour-id-'.$post->ID.'">			
															<input type="hidden" name="tour-date" value="2021-01-21" id="md-tour-start-'.$post->ID.'">			
															<input type="submit" value="book now" style="display:block; width: 250px; background-color: #F6A32A; font-size: 20px; margin: 0 auto; padding: 15px 0;">
														</form>
													';


														echo '
															</div>
														</div>
														';
															
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
	echo '</div></div></div></div>';
}
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
	.week {
		display: flex;
	}

	.day {
		flex-grow: 1;
		flex-shrink: 1;
		flex-basis: 0;
	}

	.week:first-of-type .day:first-of-type {
		margin-left: 42.85714%;
	}

	.week:last-of-type .day:last-of-type {
		margin-right: 14.285%;
	}
	.month {
		max-width: 560px;
		margin: 20px auto;
	}

	.week {
		height: 40px;
	}

	.day {
		color: black;
		padding: 10px;
		background-color: transparentize(white, 0.30);
		box-shadow: 
			-1px -1px #DFDFDF, 
			inset -1px -1px 0 0 #DFDFDF;
		text-align: center;
		cursor: pointer;
	}
	
	.disabled {
		color: #aaaaaa;
		cursor: text;
	}
</style>

<!-- The Modal -->
<div id="myModal" class="modal">
	<!-- Modal content -->
	<div class="modal-content">
		<div style="display: flex; flex-flow: row nowrap; justify-content: flex-end">
			<span class="close">&times;</span>
		</div>
		<div style="display: flex; flex-flow: row nowrap; justify-content: space-between">
			<div class="item">
				<span id="actual-month" data-mnth="" style="font-size: 32px;"></span>
			</div>
			<div class="item">
				<button onclick="anteriorMes()" id="btn-mnth-before" style="padding: 0; font-size: 32px; color:black; background-color: #fff; border: none;"><</button> 
				<button onclick="siguienteMes()" id="btn-mnth-next" style="padding: 0; font-size: 32px; color:black; background-color: #fff; border: none;">></button>
			</div>
		</div>
		<div style="display: flex; flex-flow: row nowrap; justify-content: space-between">
			<div class="item">
				<span id="actual-year" style="font-size: 32px;"></span>
			</div>
			<div class="item">
				<button onclick="anteriorYear()" id="btn-year-before" style="padding: 0; font-size: 32px; color:black; background-color: #fff; border: none;"><</button> 
				<button onclick="siguienteYear()" id="btn-year-next" style="padding: 0; font-size: 32px; color:black; background-color: #fff; border: none;">></button>
			</div>
		</div>

		<!-- <div class="years">
			<p id="next-year"><</p>
			<p id="year">2021</p>
			<p id="next-year">></p>
		</div> -->

		<div style="display: flex; flex-flow: row wrap; ">
		</div>
		<div class="month">
		<script>
		var fechaCalendar = new Date();
		var dyc = fechaCalendar.getDate();
		for (let index = 1; index <= 31; index++) {
			if (index == 1) {
				document.write('<div class="week">');
			}
			if (index == 5 || index == 12 || index == 19 || index == 26 ) {
				document.write('</div> <div class="week">');
			}
			
			if (index <= dyc) {
				document.write('<div class="day disabled" id="d-'+index+'">'+index+'</div>')
			} else {

				document.write('<div class="day" onclick="setDay('+index+')" id="d-'+index+'">'+index+'</div>')
			}
			// document.write("hola");		
			if (index == 31) {
				document.write('</div>');
			}	
		}
		</script>
		</div>

		<?php
			// $fecha = new DateTime();
		?>

		

		<form method="POST" action="https://theoutdoortrip.com/?tourmaster-payment">
		<!-- <form method="POST" action="http://localhost/bookdev/?tourmaster-payment"> -->
			<input type="hidden" name="tour-id" value="" id="md-tour-id">			
			<input type="hidden" name="tour-date" value="2021-01-21" id="md-tour-start">			
			<input type="submit" value="book now" style="display:block; width: 100%; background-color: #F6A32A; font-size: 20px;">
		</form>
	</div>
</div>

<?php
get_footer();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
	// console.log($("#date-start").val
	// console.log($("#date-end").val());
	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];
	var modal = document.getElementById("myModal");
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
		$("#myModal").hide();
	}
	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
		if (event.target == modal) {
			$("#myModal").show();
		}
	}
	let months = {0:"January", 1:"February", 2:"March", 3:"April", 4:"May", 5:"June", 6:"July", 7:"August", 8:"September", 9:"October", 10:"November", 11:"December"}
	let months2 = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
	var fecha = new Date();
	var n = fecha.getMonth();
	var year = fecha.getFullYear();
	var dy = fecha.getDate();
	$("#md-tour-start").val(year + "-" + (n + 1) + "-" + dy);
	$("#actual-year").html(year);
	console.log("el mes es: " + months[n]);
	$("#actual-month").html(months[n]);
	$("#actual-month").data("mnth",months[n]);
	if ( n == 0) {
		$("#btn-mnth-before").css("color", "gray")
	}
	function setDay(dia, fecha, tour){
		console.log("dia: " + dia);
		console.log("fecha: " + fecha);
		console.log("tour: " + tour);
		for (let index = 0; index <= 31; index++) {
			if (index == dia) {
				$("#d-"+ index).addClass("selected");
			} else {
				$("#d-"+ index).removeClass("selected");
			}
		}
		var month_span = $("#actual-month").html();
		var year_span = $("#actual-year").html();
		var num_year_span = months2.indexOf(month_span);
		$("#md-tour-start-" + tour).val(fecha);
		$("#md-tour-id-" + tour).val(tour);
	}
	function anteriorMes(){
		
		var actual = $("#actual-month").data("mnth");
		console.log("mes actual: " + actual);
		var pos = months2.indexOf(actual);
		console.log("posicione mes actual: " + pos);
		$("#actual-month").html(months[pos - 1]);
		$("#actual-month").data("mnth",months[pos - 1]);
		if ( (pos -1) == 0) {
			$("#btn-mnth-before").css("color", "#BDBDBD");
			$("#btn-mnth-next").css("color", "black");
		} else {
			$("#btn-mnth-before").css("color", "black")
			$("#btn-mnth-next").css("color", "black");
		}
	}
	function siguienteMes(){
		var actual = $("#actual-month").data("mnth");
		console.log("mes actual: " + actual);
		var pos = months2.indexOf(actual);
		console.log("posicione mes actual: " + pos);
		$("#actual-month").html(months[pos + 1]);
		$("#actual-month").data("mnth",months[pos + 1]);
		if ( (pos + 1) == 11) {
			$("#btn-mnth-next").css("color", "#BDBDBD");
			$("#btn-mnth-before").css("color", "black");
		} else {
			$("#btn-mnth-next").css("color", "black");
			$("#btn-mnth-before").css("color", "black");
		}
	}
	function anteriorYear(){
		var actual_year = $("#actual-year").html();
		console.log("el año actual es: " + actual_year);
		var int_actual_year = parseInt(actual_year);
		$("#actual-year").html(int_actual_year - 1);
	}
	function siguienteYear(){
		var actual_year = $("#actual-year").html();
		console.log("el año actual es: " + actual_year);
		var int_actual_year = parseInt(actual_year);
		$("#actual-year").html(int_actual_year + 1);
	}
	function pasarDatos(id){

		$("#tour-start-" + id).val($("#date-start").val());
		$("#tour-date-" + id).val($("#date-start").val());
		$("#tour-end-" + id).val($("#date-end").val());
		$("#tour-people-" + id).val($("#trip-guest").val());
		console.log($("#tour-start-" + id).val());
		console.log($("#tour-end-" + id).val());
		console.log("se hizo form");
		$("#md-tour-id").val($("#tour-id-" + id).val())
		// $("#" + id).submit();
		$("#myModal").show();
		return false;
	}
	function levantarModal(id){
		$("#myModal-" + id).show();
	}
	function cerrarModal(id){
		$("#myModal-" + id).hide();
	}

	function enviarCorreo(){
		

		var ajax_url = "https://theoutdoortrip.com/paypage-copy/";
		// var ajax_url = "https://theoutdoortrip.stg.elaniin.dev/paypage-copy/";
		var data = new FormData();
		var first_name = $("#firs-name").val();
		var last_name = $("#firs-name").val();
		var mail = $("#mail").val();
		var message = $("#message").val();
		var mail_to = "<?php echo $guide[0]->user_email; ?>";
		// var mail_to = "daniel.sanchez@elaniin.com";
		var name = first_name + last_name;
		data.append("name", name);
		data.append("mail", mail);
		data.append("mail_to", mail_to);
		data.append("message", message);
		$.ajax({
			type: 'POST',
			url: ajax_url,
			data: data,
			cache: false,
			processData: false,
			contentType: false,
			// dataType: 'json',
			beforeSend: function(){
				console.log("enviando mail...");
			},
			success: function(){
				console.log("Listo");
				S("#confirmation").show()
			}
		});
	}
</script>
<?php

?>