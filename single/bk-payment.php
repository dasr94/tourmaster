<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/**
 * The template for displaying single tour posttype
 */


/* 2021-01-05
30734 */

if (!empty($_POST)) {
	$booking_cookie = json_encode($_POST);
	setcookie('tourmaster-booking-detail', $booking_cookie, 0, '/', COOKIE_DOMAIN, is_ssl(), false);
	wp_redirect(add_query_arg(array()));
	$entro = 1;
	$aux = $booking_cookie;
} else {


	$entro = 0;
	if (!empty($_GET['tid'])) {
		$result = tourmaster_get_booking_data(array(
			'id' => $_GET['tid'],
			'user_id' => get_current_user_id(),
			'order_status' => array(
				'condition' => '!=',
				'value' => 'cancel'
			)
		), array('single' => true));

		$entro = "2";
		$aux = "2";

		if (!empty($result)) {
			// $user_id = get_current_user_id();
			// $img = get_avatar_url($user_id );
			$booking_detail = json_decode($result->booking_detail, true);
			$booking_detail2 = json_decode($result->booking_detail, true);
			$booking_detail['tid'] = $_GET['tid'];
			$booking_detail['step'] = (empty($_GET['step']) ? 3 : intval($_GET['step']));
			// var_dump($booking_detail);
			if (!empty($_GET['payment_method']) && $_GET['payment_method'] == 'paypal') {
				$booking_detail['payment_method'] = 'paypal';
			}
			if (!empty($_GET['payment_method']) && $_GET['payment_method'] == 'hipayprofessional') {
				$booking_detail['payment_method'] = 'hipayprofessional';
			}

			$transaction_id = $_GET['tid'];
			$entro = $booking_detail;

			if ($booking_detail['step'] != 4) {
				setcookie('tourmaster-booking-detail', json_encode($booking_detail), 0, '/', COOKIE_DOMAIN, is_ssl(), false);
				wp_redirect(remove_query_arg(array('tid', 'step')));
			}
		}

	}
}

get_header();






echo '<div class="tourmaster-page-wrapper" id="tourmaster-page-wrapper" >';
if (empty($booking_detail)) {
	if (!empty($_COOKIE['tourmaster-booking-detail'])) {
		$booking_detail = json_decode(wp_unslash($_COOKIE['tourmaster-booking-detail']), true);
		$booking_detail = stripslashes_deep($booking_detail);
	} else {
		$booking_detail = array();
	}
}
$booking_step = empty($booking_detail['step']) ? 2 : intval($booking_detail['step']);

if (!empty($booking_detail['tour-id']) && !empty($booking_detail['tour-date'])) {
	$tour_option = tourmaster_get_post_meta($booking_detail['tour-id'], 'tourmaster-tour-option');
	$date_price = tourmaster_get_tour_date_price($tour_option, $booking_detail['tour-id'], $booking_detail['tour-date']);
	$date_price = tourmaster_get_tour_date_price_package($date_price, $booking_detail);
} else {
	$tour_option = '';
	$date_price = '';
}

global $wpdb;
$id_tour = $booking_detail['tour-id'];
$user = $_REQUEST['user'];
$tabla = $wpdb->base_prefix."posts";
$query = "SELECT post_author FROM $tabla where  ID = '$id_tour' ";
$res = $wpdb->get_row($query);
$guia = $res->post_author;

$tabla = $wpdb->base_prefix."usermeta";
$query = "SELECT meta_value FROM $tabla where  user_id = '$guia' and meta_key = 'guide-company' ";
$res = $wpdb->get_row($query);
$guide_name = $res->meta_value;


$guide_name = str_replace('-', ' ', $guide_name);
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
$img_guide = get_avatar_url($guide_id );
$guide_info = get_user_meta($guide_id, 'guide-info', true);

$precio_arr = get_post_meta($id_tour, 'tourmaster-tour-option', true);
$precio = $precio_arr['tour-price-text'];
if($booking_detail['tour-people'] == ""){
	$people = 1;
} else {
	$people = $booking_detail['tour-people'];
}

// echo $precio * $people;

$header_img_id = get_user_meta($guide_id, 'image-guide', true);
$header_img = wp_get_attachment_url($header_img_id);
?>





<div class="gdlr-core-pbf-wrapper " style="margin: 0px 0px 0px 0px;padding: 115px 0px 115px 0px;">
	<div class="gdlr-core-pbf-background-wrap">
		<div class="gdlr-core-pbf-background" style="background-image: url(&quot;<?php echo $header_img; ?>&quot;); background-size: cover; background-position: top center;" data-parallax-speed="0.8"></div>
	</div>
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-column gdlr-core-column-first first-c">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
						<div class="gdlr-core-pbf-element">
							<?php $img_guide = str_replace("-96x96","", $img_guide) ?>
							<?php echo '<img class="img-guide-c" src="' . $img_guide . '" alt="" width="600" height="600" title="' . $guide_name . '">' ?>
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
									<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 43px ;letter-spacing: 5px ;color: #ffffff ;"><?php echo  $guide_name; ?><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
								</div>
								<!-- <div class="gdlr-core-title-item-title-wrap">
										<p class="gdlr-core-skin-title location">City Name, State Name</p>
									</div> -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php



// payment head
/* if (!empty($booking_detail['tour-id'])) {

	$feature_image = get_post_thumbnail_id($booking_detail['tour-id']);
	echo '<div class="tourmaster-payment-head ' . (empty($feature_image) ? 'tourmaster-wihtout-background' : 'tourmaster-with-background') . '" ';
	if (!empty($feature_image)) {
		echo tourmaster_esc_style(array('background-image' => $feature_image));
	}

	echo ' >';
	
		echo '<div class="traveltour-header-transparent-substitute" ></div>';
		echo '<div class="tourmaster-payment-head-overlay-opacity" ></div>';
		echo '<div class="tourmaster-payment-head-overlay" ></div>';
		echo '<div class="tourmaster-payment-head-top-overlay" ></div>';
		echo '<div class="tourmaster-payment-title-container tourmaster-container" >';
		echo '<h1 class="tourmaster-payment-title tourmaster-item-pdlr">' . get_the_title($booking_detail['tour-id']) . '</h1>';
		echo '</div>'; // tourmaster-payment-title-container
		

	$step_count = 1;
	$payment_steps = array(
		esc_html__('Select Tour', 'tourmaster'),
		esc_html__('Contact Details', 'tourmaster'),
		esc_html__('Payment', 'tourmaster'),
		esc_html__('Complete', 'tourmaster'),
	);
	echo '<div class="tourmaster-payment-step-wrap" id="tourmaster-payment-step-wrap" >';
	echo '<div class="tourmaster-payment-step-overlay" ></div>';
	echo '<div class="tourmaster-payment-step-container tourmaster-container" >';
	echo '<div class="tourmaster-payment-step-inner tourmaster-item-pdlr clearfix" >';
	foreach ($payment_steps as $payment_step) {
		echo '<div class="tourmaster-payment-step-item ';
		if ($step_count == 1) {
			echo 'tourmaster-checked ';
		} else if ($booking_step == $step_count) {
			echo 'tourmaster-current ';
		} else if ($booking_step > $step_count) {
			echo 'tourmaster-enable ';
		}
		echo '" data-step="' . esc_attr($step_count) . '" >';
		echo '<span class="tourmaster-payment-step-item-icon" >';
		echo '<i class="fa fa-check" ></i>';
		echo '<span class="tourmaster-text" >' . $step_count . '</span>';
		echo '</span>';
		echo '<span class="tourmaster-payment-step-item-title" >' . $payment_step . '</span>';
		echo '</div>';

		$step_count++;
	}
	echo '</div>'; // tourmaster-payment-step-inner
	echo '</div>'; // tourmaster-payment-step-container
	echo '</div>'; // tourmaster-payment-step-wrap
	echo '</div>'; // tourmaster-payment-head
} else {
	echo '<div class="traveltour-header-transparent-substitute" ></div>';
} */

// DATOS DE CIUDAD
$state = get_post_meta($booking_detail['tour-id'], 'state');
$city = get_post_meta($booking_detail['tour-id'], 'city');
/* ======Banner con datos====== */

?>


<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon" style="border: none;">
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js " style="border: none;">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video" style="padding-bottom: 5px; border: none;">
			<div class="steps" style="display: flex; justify-content: space-between; align-items: center; flex-direction: row;">
				<div class="item">
					<p>Choose Your Trip</p>
				</div>
				<div class="item">
					<p>Enter your Details</p>
				</div>
				<div class="item">
					<p>Final Steps</p>
				</div>
			</div>
			<div class="steps" style="display: flex; justify-content: space-between; align-items: center; flex-direction: row;">

				<div class="item-1">
					<button style="padding: 0; background-color: #F6A32A; border-radius: 50%; padding: 10px">
						<i class=" gdlr-core-icon-item-icon fa fa-check" style="background-color: #F6A32A ; color: #fff; font-size: 20px ;min-width: 20px ;min-height: 20px ;"></i>
					</button>
				</div>
				<div class="item-2">
					<hr style="content: ''; height: 2px; background-color:#F6A32A; color:#F6A32A; border: none; width: calc((1180px / 2) - 70px);">
				</div>
				<div class="item-3">
					<button style="padding: 0; background-color: #F6A32A; border-radius: 50%; padding: 10px">
						<i class=" gdlr-core-icon-item-icon fa fa-circle" style="background-color: #F6A32A ; color: #fff; font-size: 20px ;min-width: 20px ;min-height: 20px ;"></i>
					</button>
				</div>
				<div class="item-4">
					<hr style="content: ''; height: 2px; background-color:#E0E0E0; color:#E0E0E0; border: none; width: calc((1180px / 2)  - 70px);">
				</div>
				<div class="item-5">
					<button style="padding: 0; background-color: #E0E0E0; border-radius: 50%; padding: 10px">
						<i class=" gdlr-core-icon-item-icon fa fa-circle" style="background-color: #E0E0E0 ; color: #E0E0E0; font-size: 20px ;min-width: 20px ;min-height: 20px ;"></i>
					</button>
				</div>
				

			</div>
		</div>
	</div>
</div>



<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon">
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video" style="background-color: #F2F2F2; padding-bottom: 5px;">
			<div class="gdlr-core-pbf-column gdlr-core-column-36 gdlr-core-column-first" style="background-color: #F2F2F2; ">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px;">

							<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 10px ; padding-top: 20px ;">
								<div class="gdlr-core-title-item-title-wrap">
									<h1 class="gdlr-core-title-item-title gdlr-core-skin-title"><?php echo get_the_title($booking_detail['tour-id']) ?><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h1>
								</div>
								<span class="gdlr-core-title-item-caption gdlr-core-info-font gdlr-core-skin-caption" style="margin-bottom: 0px ;">/ <?php echo $state[0]; echo  ($city[0] == "") ? "" : " - " . $city[0];
								?> </span>
							</div>

						</div>
					</div>
				</div>
			</div>
			<div class="gdlr-core-pbf-column gdlr-core-column-12 " style="background-color: #F2F2F2;">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px;">
							<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr" style="padding-bottom: 10px ;">
								<span class="gdlr-core-title-item-caption gdlr-core-info-font gdlr-core-skin-caption" style="margin-bottom: 0px ;">Package Price</span>
								<div class="gdlr-core-title-item-title-wrap">
									<h1 class="gdlr-core-title-item-title gdlr-core-skin-title">$<?php $price = tourmaster_get_price($booking_detail, true);  echo $precio * $people; ?><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h1>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="gdlr-core-pbf-column gdlr-core-column-12 " style="background-color: #F2F2F2;">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">

						<!-- <div class="gdlr-core-pbf-element gdlr-core-center-align" style="padding-top: 20px ;">
							<div class="gdlr-core-icon-item gdlr-core-item-pdlr gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 0px ;"><i class=" gdlr-core-icon-item-icon fa fa-star" style="color: #828282 ;font-size: 110px ;min-width: 110px ;min-height: 110px ;"></i></div>
							<span class="gdlr-core-title-item-caption gdlr-core-info-font gdlr-core-skin-caption" style="margin-bottom: 0px ;">Add to favorites</span>
						</div> -->

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- METER AQUI -->

<?php
function generarCodigo($longitud) {
	$key = '';
	$pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
	$max = strlen($pattern)-1;
	for($i=0;$i < $longitud;$i++) $key .= $pattern[mt_rand(0,$max)];
	return $key;
}
$codigo = $booking_detail['tour-id'] . generarCodigo(3);

?>

<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;"  data-skin="Blue Icon" id="part-2">
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video" style="padding-left: 0; padding-right: 0;">
			<div class="gdlr-core-pbf-column gdlr-core-column-36 gdlr-core-column-first" style="background-color: #F2F2F2;">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px; padding: 10px;">
							<h3>Book Your Trip</h3>
							<style>
								div.form-group {
									margin-bottom: 10px;
								}
								div.form-group, div.form-group label, div.form-group input {
									display: block;
								}
								div.form-group input {
									width: 50%;
									padding: 10px;
								}
								div.form-group input[type=submit]{
									background-color: #F6A32A;
								}
							</style>

							<?php

							if( is_user_logged_in() ){
								$current_user = wp_get_current_user();

								echo '
								<div class="form-group">
									<label for="user_first_frm">First name</label>
									<input type="text" name="first_name_frm" id="user_first_frm" class="input"  value="'.$current_user->user_firstname.'" size="20">
								</div>
								<div class="form-group">
									<label for="user_last_frm">last name</label>
									<input type="text" name="last_name_frm" id="user_last_frm" class="input" value="'.$current_user->user_lasttname.'" size="20">
								</div>
								<div class="form-group">
									<label for="user_email_frm">Email Address</label>
									<input type="text" name="email_address_frm" id="user_email_frm" class="input" value="'.$current_user->user_email.'" size="20">
								</div>
								<div class="form-group">
									<label for="user_number_frm">Mobile Number</label>
									<input type="text" name="mobile_number_frm" id="user_number_frm" class="input" value="">
								</div>
								';
							} else {

								echo '<p>  <button style="color: #F6A32A; background-color: transparent; padding: 0;" id="btn-sign-in"><i class=" gdlr-core-icon-item-icon fa fa-address-book" style="color: #F6A32A ;font-size: 15px ;min-width: 15px ;min-height: 15px ;"></i>  Sign in</button> to book faster and view your favorite trips <p>
								';

								custom_registration_function();
								echo '
								<div id="login-code-container"  style="display: none;">
									<form name="loginform" id="loginform" action="/wp-login.php" method="post">

										<div class="form-group">
											<label for="user_login">Nombre de usuario o dirección de correo</label>
											<input type="text" name="log" id="user_login" class="input" value="" size="20">
										</div>

										<div class="form-group">
											<label for="user_pass">Contraseña</label>
											<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
										</div>

										<div class="form-group">
											<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="Acceder">
											<input type="hidden" name="redirect_to" value="/">
										</div>

										
									</form>


									<a href="/wp-login.php?action=logout&amp;redirect_to=%2F">Desconectar</a>

								</div> ';
							}
							?>

						</div>
					</div>
				</div>
			</div>
			
			<div class="gdlr-core-pbf-column gdlr-core-column-24 " style="padding-left: 10px;">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="background-color: #F2F2F2;">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">

						<div id="info-payment" class="fixed-layout" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #C4C4C4; display: none;">

							<div class="pay-info" style="display: flex; align-content: center; justify-content: center; height: 100%; width: 100%; flex-direction: column; padding: 10px;">
								<h3 style="text-align: center; background-color: #F6A32A; color: #ffffff; padding: 20px 10px; margin-bottom: 0;">Payment information</h3>
								<p style="background-color: #ffffff; padding: 10px; ">20% deposit charged upfront by The Outdoor Trip to guarantee your reservation. The remaining balance is to be paid directly to the outfitter on or prior to your trip date in one of the following payment methods: Cash, PayPal or Stripe.</p>
							</div>

						</div>

						<h3 class="test-tourmaster" style="text-align: center; background-color: #F6A32A; color: #ffffff; padding: 20px 10px; font-size: 100%; width: 100%" >YOUR TRIP DETAILS</h3>
						<div class="flex" style="display: flex; flex-direction: row; align-items: flex-start; margin: 0 auto; padding-top: 30px; justify-content: space-between">
							<div class="item-1" style="text-align: left;">
								<!-- <p style="font-weight: 500; margin: 0px 0px 0px 10px; font-size: 18px">Hunting Season:</p> -->
								<p style="font-weight: 500; margin: 0px 0px 0px 10px; font-size: 18px">Trip:</p>
								<p style="font-weight: 500; margin: 0px 0px 0px 10px; font-size: 18px">Date:</p>
								<p style="font-weight: 500; margin: 0px 0px 0px 10px; font-size: 18px">For:</p>
								<p style="font-weight: 500; margin: 0px 0px 0px 10px; font-size: 18px">Time:</p>
								<p style="font-weight: 500; margin: 0px 0px 0px 10px; font-size: 18px; border-top: 1px solid #BDBDBD">Trip Price:</p>
								<p style="font-weight: 500; margin: 0px 0px 0px 10px; font-size: 18px; border-top: 1px solid #BDBDBD">Total Price:</p>
							</div>
							<div class="item-2" style="text-align: right;">
								<?php
									// $tour_option = tourmaster_get_post_meta($booking_detail['tour-id'], 'tourmaster-tour-option');
									// var_dump($booking_detail) ;
									$hunting_season = get_post_meta($booking_detail['tour-id'], 'tourmaster-tour-date');
									// var_dump($hunting_season);
									// echo $hunting_season[0];
									$abble_days = explode(",", $hunting_season[0]);
									// echo count($abble_days);
									$date_selected = date_create($booking_detail['tour-date']);
								?>
								<!-- <p style="font-weight: 500; margin: 0px 10px 0px 0px; font-size: 18px"><?php echo $abble_days[0] . " - " . $abble_days[count($abble_days) - 1];  ?></p> -->
								<p style="font-weight: 500; margin: 0px 10px 0px 0px; font-size: 18px"><?php echo count($abble_days) . " Days"; ?></p>
								<p style="font-weight: 500; margin: 0px 10px 0px 0px; font-size: 18px"><?php echo ($booking_detail['tour-date']) ? date_format($date_selected, "D - d, M") : "" ; ?></p>
								<p style="font-weight: 500; margin: 0px 10px 0px 0px; font-size: 18px"><?php echo $booking_detail['tour-people']; ?></p>
								<p style="font-weight: 500; margin: 0px 10px 0px 0px; font-size: 18px"><?php echo "7 PM" ?></p>
								<p style="font-weight: 500; margin: 0px 10px 0px 0px; font-size: 18px">$<?php echo $precio; ?></p>
								<p style="font-weight: 500; margin: 0px 10px 0px 0px; font-size: 18px">$<?php echo $precio * $people; ?></p>

							</div>
						</div>
						<!-- <h3 style="text-align: center; color: #F6A32A; padding: 10px 10px; font-size: 18px; font-weight: 500; border-top: 1px solid #BDBDBD" >Change your selection</h3> -->

					
						</div>
					</div>
				</div>
			</div>

			<div class="gdlr-core-pbf-column gdlr-core-column-36">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px; padding: 10px;">

							<p style="border: 2px solid #F6A32A; padding: 15px 10px;"> <span style="font-weight: 800;">Free Cancellation.</span> You can cancel or change your booking for before <?php 
							$fecha_booking = date($booking_detail['tour-date']);
							echo date("M, d", strtotime($fecha_booking . " + 5 days"));
							
							?> </p>


<style>

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}
.modal-2 {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(196,196,196,0.85); /* Black w/ opacity */
}
.modal-3 {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(196,196,196,0.85); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
	background-color: #f2f2f2;
	margin: auto;
	padding: 10px;
	border: 1px solid #888;
	width: 95%;
}
.modal-content-2 {
	background-color: #f2f2f2;
	margin: auto;
	padding: 10px;
	border: 1px solid #888;
	width: 50%;
}
.modal-content-3 {
	background-color: #f2f2f2;
	margin: auto;
	padding: 10px;
	border: 1px solid #888;
	width: 50%;
}

/* The Close Button */
.close {
	position: absolute;
	right: 10px;
	top: 10px;
	color: #aaaaaa;
	float: right;
	font-size: 28px;
	font-weight: bold;
}
.close-2 {
	position: absolute;
	right: 10px;
	top: 10px;
	color: #aaaaaa;
	float: right;
	font-size: 28px;
	font-weight: bold;
}
.close-3 {
	position: absolute;
	right: 10px;
	top: 10px;
	color: #aaaaaa;
	float: right;
	font-size: 28px;
	font-weight: bold;
}

.close:hover,
.close:focus {
	color: #000;
	text-decoration: none;
	cursor: pointer;
}
.close-2:hover,
.close-2:focus {
	color: #000;
	text-decoration: none;
	cursor: pointer;
}
.close-3:hover,
.close-3:focus {
	color: #000;
	text-decoration: none;
	cursor: pointer;
}
</style>

<button id="myBtn"  style="background-color: #F6A32A; color: #ffffff; font-size: 32px; font-weight: 700; padding: 10px 20px; ">Continue >></button>
<button id="btn-continue"  style="background-color: #F6A32A; color: #ffffff; font-size: 32px; font-weight: 700; padding: 10px 20px; display: none;">Continue >></button>
<div id="myModal" class="modal" >

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
	<div class="flex" style="display: flex; flex-direction: row; align-items: flex-start; width: 100%; margin: 0 auto; padding-top: 10px;">
		<div class="item-1">
			<?php echo '<img src="' . $img_guide . '" alt="" width="150" height="150" title="' . $guide_name . '">' ?>
		</div>
		<div class="item-2">
			<p style="padding-left: 15px;">Say Hello to <?php echo $guide_name;  ?> </p>
			<p style="padding-left: 15px;"> Share some details with your outfitter, such as who’s coming with you, what type of big game you’d like to hunt, or any special request you may have. This will help them prepare for the trip.
			</p>
		</div>
	</div>
	<div class="flex" style="display: flex; flex-direction: row; align-items: flex-start; width: 100%; margin: 0 auto; padding-top: 10px;">
		<textarea onkeyup="countChars(this);" name="" id="" style="width: 100%; height: 130px;"></textarea>
		<!-- <input type="text" style="width: 100%; height: 130px;"> -->
	</div>
	<div class="flex" style="display: flex; flex-direction: row; align-items: flex-end; width: 99%; margin: 0 auto;  justify-content: space-between; margin-top: -30px;">
		<div class="item-1" style="z-index: 1;">
			<p style="color: #BDBDBD; margin-bottom: 0px;"> <span id="charNum">0</span> /100 words</p>
		</div>
		<div class="item-2" style="z-index: 1;">
			
			<button style="background-color: #F6A32A; color: #ffffff; padding: 5px; font-size: 10px;" id="send-btn"> <i class=" gdlr-core-icon-item-icon fa fa-paper-plane" style="color: #ffffff ;font-size: 10px ;min-width: 10px ;min-height: 10px ;"></i> Send</button>
		</div>
	</div>
  </div>

</div>

							<!-- <a href="#" id="myBtn" style="background-color: #F6A32A; color: #ffffff; font-size: 32px; font-weight: 700; padding: 10px 20px; ">Continue >></a> -->

						</div>
					</div>
				</div>
			</div>

			<div class="gdlr-core-pbf-column gdlr-core-column-24">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px; padding: 10px;">

							<input type="checkbox" id="terms" name="term-and-service" />
							<label for="terms">* I agree with <a href="%s" target="_blank">Terms of Service</a> and <a href="%s" target="_blank">Privacy Statement</a>.</label>


						</div>
					</div>
				</div>
			</div>




		</div>
	</div>
</div>



<!-- METER AQUI -->

<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon" id="part-1">
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video" style="padding-left: 0; padding-right: 0;">
			<div class="gdlr-core-pbf-column gdlr-core-column-36 gdlr-core-column-first" style="background-color: #F2F2F2;">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px;">
							<?php
							/* ======PHOTOS====== */


			$id_slider = $booking_detail['tour-id'];

			echo ' <div class="gdlr-core-pbf-wrapper " style="padding: 20px 0px 30px 0px;" data-skin="Blue Icon" id="photos">
            <div class="gdlr-core-pbf-background-wrap"></div>
			<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
			<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-element">
			<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr"
			style="padding-bottom: 35px ;">
			<div class="gdlr-core-title-item-title-wrap">
			
			</div>
			</div>
			</div>
			
			';



			$sliders = get_post_meta($id_slider, 'header-slider');
			if($sliders[0] == ""){

				echo '<div class="gdlr-core-pbf-element">
			<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr"
			style="padding-bottom: 35px ;">
			<div class="gdlr-core-title-item-title-wrap">
			<h6 class="gdlr-core-title-item-title gdlr-core-skin-title"
			style="font-size: 24px ;font-weight: 600 ;letter-spacing: 0px ;text-transform: none ;">
			<span class="gdlr-core-title-item-left-icon" style="font-size: 18px ;"><i
			class="icon_images"></i></span>No photos...<span
			class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h6>
			</div>
			</div>
			</div>
				';

			} else {

				echo '
				<div class="gdlr-core-pbf-element">
				<div class="gdlr-core-gallery-item gdlr-core-item-pdb clearfix  gdlr-core-gallery-item-style-slider gdlr-core-item-pdlr ">
				<div class="gdlr-core-flexslider flexslider gdlr-core-js-2 " data-type="slider" data-effect="default" data-nav="bullet">
				<div class="flex-viewport" style="overflow: hidden; position: relative;">
				<ul class="slides" style="width: 1200%; margin-left: -571px;">
				';

				foreach($sliders[0] as $img){
					$img_cut = str_replace("-150x150","-300x300",$img['thumbnail']);
					echo '<li class="clone" aria-hidden="true"
					style="width: 571px; margin-right: 0px; float: left; display: block;">
					<div class="gdlr-core-gallery-list gdlr-core-media-image"><a
					class="gdlr-core-ilightbox gdlr-core-js "
					href="'.$img_cut.'"
					data-caption="Map" data-ilightbox-group="gdlr-core-img-group-1"
					data-type="image"><img
					src="'.$img_cut.'"
					alt="" width="450" height="400" title="Traveller"
					draggable="false"><span class="gdlr-core-image-overlay "><i
					class="gdlr-core-image-overlay-icon  gdlr-core-size-22 fa fa-search"></i></span></a>
					</div>
					</li>';
				}

				echo '
				</ul>
				</div>
				</div> ';
				echo '<div style="text-align: center; margin: 0 auto; display: inline-block; width: 100%; padding-top: 20px;">';
				foreach($sliders[0] as $img){
					$img_cut = str_replace("-150x150","-300x300",$img['thumbnail']);
					echo "<img style='display: inline-block; width: 100px; padding: 0px 5px;' src='" . $img_cut . "'>   ";
				}
				echo '</div>';
				echo '
				
				</div>
				</div>
				';
			}

			echo '
			</div>
            </div>
			</div>';


							/* ======/PHOTOS====== */
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="gdlr-core-pbf-column gdlr-core-column-24 " style="padding-left: 10px; height: 732px; min-height: 732px;">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="background-color: #F2F2F2; height: 732px; min-height: 732px;">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js " style="background-color: #F2F2F2; height: 732px; min-height: 732px;">
						<div class="gdlr-core-pbf-column-content-margin gdlr-core-js " style="background-color: #F2F2F2; height: 729px; min-height: 732px;">

						<div class="flex" style="display: flex; flex-direction: row; align-items: flex-start; width: 90%; margin: 0 auto; padding-top: 10px;">
							<div class="item-1">
								<?php echo '<img src="' . $img_guide . '" alt="" width="150" height="150" title="' . $guide_name . '">' ?>
							</div>
							<div class="item-2">
								<p style="font-weight: 500; margin: 20px 0px 0px 10px"><?php echo $guide_name ?></p>
								<p style="font-weight: 500; margin: 0px 0px 0px 10px"><?php echo "United States" ?></p>
								<?php
									$star_yellow = '<div class="gdlr-core-icon-item  gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 0px ; display: inline-block; padding-left: 20px;" ><i class=" gdlr-core-icon-item-icon fa fa-star" style="color: #FFC916 ;font-size: 10px ;min-width: 10px ;min-height: 10px ;"></i></div>
									';
									$star_gray = '<div class="gdlr-core-icon-item  gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 0px ; display: inline-block; padding-left: 20px;" ><i class=" gdlr-core-icon-item-icon fa fa-star" style="color: #828 ;font-size: 10px ;min-width: 10px ;min-height: 10px ;"></i></div>
									';
									$array_reviews = [1.1,4.5,5.6];
									$prom = 0;
									foreach($array_reviews as $k){
										$prom = $k + $prom;
									}
									$review_point = ($prom / count($array_reviews));
									// echo round( $review_point , 2) . "</br>";									
									$review_point_substr = substr($review_point, 0, 1);	
									$review_point_substr = 11;							
									switch ($review_point_substr){
										case 1:
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											break;
										case 2:
											echo $star_yellow;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											break;
										case 3:
											echo $star_yellow;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											break;
										case 4;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											break;
										case 5;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_gray;
											echo $star_gray;
											echo $star_gray;
											break;
										case 6;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_gray;
											echo $star_gray;
											break;
										case 7;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_gray;
											echo $star_gray;
											break;
										case 8;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_gray;
											break;
										case 9;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_gray;
											break;
										case 10;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											echo $star_yellow;
											break;
										case 11;
											break;



									}

									// echo '<div class="gdlr-core-icon-item  gdlr-core-item-pdb gdlr-core-center-align" style="padding-bottom: 0px ; display: inline-block; padding-left: 20px;" > ' . round($review_point, 2) . ' </div>';
									?>
									<p style="font-weight: 500; margin: 0px 0px 0px 10px"><?php echo "Response Rate: 98 %" ?></p>
									<p style="font-weight: 500; margin: 0px 0px 0px 10px"><?php echo "Response Time: Within A Week" ?></p>

							</div>
						</div>

						
							<p style=" margin: 0 auto; padding: 20px; overflow-y: scroll; height: 450px;"><?php echo $guide_info ?></p>
							<h1 class="test-tourmaster" style="text-align: center; background-color: #F6A32A; color: #ffffff; padding: 20px 10px; position: absolute; bottom: 0px; margin-bottom: 0; font-size: 100%; width: 100%;" >CONTACT THE OUTFITTER</h1>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
// if not logging in print the login before proceed form
if( !is_user_logged_in() ){
	$guest_booking = tourmaster_get_option('general', 'enable-guest-booking', 'enable');
	$guest_booking = ($guest_booking == 'enable')? true: false;
	echo tourmaster_lightbox_content(array(
		'id' => 'proceed-without-login',
		'title' => esc_html__('Proceed Booking', 'tourmaster'),
		'content' => tourmaster_get_login_form2(false, array(
			'continue-as-guest'=>$guest_booking,
			'redirect'=>'payment'
		))
	));
}

?>


<div id="date-modal" class="modal-3">
	<div class="modal-content-3">
		<!-- <span class="close-2">&times;</span> -->
		<!-- <h3>tour id: <?php echo $_POST['tour-id']; echo $booking_detail['tour-id']; ?></h3>
		<h3>date: <?php echo $_POST['tour-date'] . $booking_detail['tour-date']; ?></h3>
		<h3>date: <?php echo $_POST['tour-date'] . $booking_detail['tour-people']; ?></h3> -->

<div class="tourmaster-tour-booking-bar-inner">
	<form class="tourmaster-single-tour-booking-fields tourmaster-update-header-price tourmaster-form-field tourmaster-with-border" method="post" action="https://theoutdoortrip.stg.elaniin.dev/?tourmaster-payment" id="tourmaster-single-tour-booking-fields" data-ajax-url="http://localhost/bookdev/wp-admin/admin-ajax.php">
		<input type="hidden" name="tour-id" value="<?php echo $booking_detail['tour-id']; ?>">
		<input type="hidden" name="tour-valid" value="1">
		<div class="tourmaster-tour-booking-date clearfix" data-step="1">
			<i class="fa fa-calendar"></i>
			<div class="tourmaster-tour-booking-date-input">
				<div class="tourmaster-datepicker-wrap">
					<!-- <input type="text" class="tourmaster-datepicker hasDatepicker" readonly="" value="2021-01-06" data-date-format="d M yy" data-tour-range="5"  id="dp1609973079552"> -->
					<input type="date" style="width: 100%; padding: 5px;" name="tour-date" class="tourmaster-datepicker-alt" value="2021-01-06">
				</div>
			</div>
		</div>
		<div class="tourmaster-tour-booking-people clearfix" data-step="4">
			<div class="tourmaster-tour-booking-next-sign">
				<span></span>
			</div>
			<i class="fa fa-users"></i>
			<div class="tourmaster-tour-booking-people-input">
				<input type="text" name="tour-people">
			</div>
		</div>
		<div class="tourmaster-tour-booking-submit" data-step="5">
			<div class="tourmaster-tour-booking-next-sign">
				<span></span>
			</div>
			<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512.007 512.007" style="enable-background:new 0 0 512.007 512.007;" xml:space="preserve">
		<path d="M397.413,199.303c-2.944-4.576-8-7.296-13.408-7.296h-112v-176c0-7.552-5.28-14.08-12.672-15.648
			c-7.52-1.6-14.88,2.272-17.952,9.152l-128,288c-2.208,4.928-1.728,10.688,1.216,15.2c2.944,4.544,8,7.296,13.408,7.296h112v176
			c0,7.552,5.28,14.08,12.672,15.648c1.12,0.224,2.24,0.352,3.328,0.352c6.208,0,12-3.616,14.624-9.504l128-288
			C400.805,209.543,400.389,203.847,397.413,199.303z" fill="#4692e7"></path>
</svg>

<i class="fa fa-check-circle"></i>
<div class="tourmaster-tour-booking-submit-input">
	<input class="tourmaster-button" type="submit" style="background-color: #F6A32A;" value="Proceed Booking">
</div>
</div>
</form>
</div>

	</div>
</div>







<style>
	table.custom-table {
		background-color: #F2F2F2;
	}
	table.custom-table tr th {
		background-color: #BDBDBD;
	}
	table.custom-table tr td {
		font-weight: 400;
	}
	table.custom-table tr:nth-child(even) {
		background-color: #F2F2F2;
	}
	table.custom-table tr:nth-child(odd) {
		background-color: #F2F2F2;
	}
	.table-left {
		text-align: left;
		padding-left: 10px;
	}
	.table-right {
		text-align: right;
		padding-right: 10px;
	}
</style>

<div  id="table" style="display: none;">
	
<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;"  data-skin="Blue Icon" id="part-2">
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container guide_video" style="padding-left: 0; padding-right: 0;">
			<div class="gdlr-core-pbf-column gdlr-core-column-100 gdlr-core-column-first" style="background-color: #F2F2F2;">

				<table class="custom-table">
					<tr>
						<th colspan="2" style="color: black; font-weight: 700">
							Make a Payment
						</th>
					</tr>
					<tr style="display: none;" id="thx-message">
						<td colspan="2" class="table-left"> <b>Thank you for your payment for the following booking. </b> <br>Note that you will receive a separate email notification confirming your payment. </td>
					</tr>
					<tr>
						<td class="table-left">Booking Reference: </td>
						<td class="table-right"> <?php echo $codigo; ?> </td>
					</tr>
					<tr>
						<td class="table-left">Outfitter Name:</td>
						<td class="table-right"> <?php echo $guide_name; ?> </td>
					</tr>
					<tr>
						<td class="table-left">Customer: </td>
						<td class="table-right"> </span> <?php echo $current_user->user_firstname ?> </td>
					</tr>
					<tr>
						<td class="table-left">Party Size: </td>
						<td class="table-right"> <?php echo $booking_detail['tour-people']; ?> </td>
					</tr>
					<tr>
						<td class="table-left">Arrival Date: </td>
						<td class="table-right"> <?php echo date_format($date_selected, "m-d-Y"); ?> </td>
					</tr>
					<tr>
						<td class="table-left">Departure Date: </td>
						<td class="table-right"> <?php echo date_format($date_selected, "m-d-Y"); ?> </td>
					</tr>
					<tr>
						<td class="table-left">Check-in Time: </td>
						<td class="table-right"> </td>
					</tr>
					<tr>
						<td> </td>
						<td> </td>
					</tr>
					<tr>
						<td class="table-left">Total Package Amount: </td>
						<td class="table-right"> $ <?php echo $precio * $people; ?> </td>
					</tr>
					<tr id="url-return" style="display: none;">
						<td> <a href="https://theoutdoortrip.stg.elaniin.dev/">Return to www.theoutdoortrip.com</a> </td>
					</tr>
					<tr id="hide-1">
						<th colspan="2" style="color: black; font-weight: 700">
							Payments Due
						</th>
					</tr>
					
					<tr id="hide-2">
						<td class="table-left">20% Down Payment: </td>
						<td class="table-right"> $ <?php echo ($precio * $people) * 0.20; ?> </td>
					</tr>
					<tr id="hide-3">
						<td class="table-left">booking fee: </td>
						<td class="table-right"> $20 </td>
					</tr>
					<tr id="hide-4">
						<td class="table-left">Processing fee: </td>
						<td class="table-right"> $<?php echo (($precio * $people) * 0.20 + 20) * 0.039; ?> </td>
					</tr>
					<tr id="hide-5">
						<td class="table-left">Amount to Pay: </td>
						<td class="table-right"> $ <?php echo (($precio * $people) * 0.20 + 20) * 1.039; ?> </td>
					</tr>
					<tr id="hide-6">
						<th colspan="2" style="color: black; font-weight: 700">
							Secure Online Credit/Debit Card Payment Using Stripe
						</th>
					</tr>
					<tr id="hide-7">
						<td colspan="2" style="padding-top: 50px;"> 
							<span style="background: black; color: #fff; padding: 10px 20px;">
								Powered by <b>Stripe</b>
							</span>	
						</td>
					</tr>
					<tr id="hide-8">
						<td colspan="2" style="padding-bottom: 50px;"> 
							<button style="background: #F6A32A; color: #fff; padding: 10px 20px; margin-bottom: 50px; font-weight: 700;" id="btn-open-stripe">
								Pay Now
							</button>	
						</td>
					</tr>
					
				</table>

				
<div id="stripe-modal" class="modal-2">
	<div class="modal-content-2">
		<!-- <span class="close-2">&times;</span> -->
		<h6 style="color: #ffffff; background-color: #0A2540; text-align: center; margin: -10px;">Stripe</h6>
		<style>
			::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
			color: #E0E0E0 !important;
			opacity: 1; /* Firefox */
			}

			:-ms-input-placeholder { /* Internet Explorer 10-11 */
			color: #E0E0E0;
			}

			::-ms-input-placeholder { /* Microsoft Edge */
			color: #E0E0E0;
			}
			form.stripe-pay-form {
				display: flex;
				flex-direction: column;
				padding-top: 25px;
				width: 80%;
				margin: 0 auto;
				padding-bottom: 25px;
			}
			form.stripe-pay-form input {
				border: #E0E0E0 1px solid;
				padding: 10px;
				margin: 0;
			}
			form.stripe-pay-form input[type=submit] {
				color: #ffffff; 
				background-color: #0A2540; 
				text-align: center;
			}
		</style>
		<form method="POST" class="stripe-pay-form" id="frm-payment">
			<label for="email">Email</label>
			<input type="text" placeholder="yourname@email.com" name="email" value="<?php echo $current_user->user_email; ?>">
			<br>
			<br>
			<br>
			<label for="email">Shipping Address</label>
			<input type="text" placeholder="Name" name="nombre" value="<?php echo $current_user->user_firstname; ?>">
			<input type="text" placeholder="Address line 1" name="adr-line1">
			<input type="text" placeholder="Address line 2" name="adr-line2">
			<input type="text" placeholder="City" name="city">
			<input type="text" placeholder="State" name="state">
			<input type="text" placeholder="Country" name="country">
			<br>
			<br>
			<br>
			<label for="email">Card Information</label>
			<input name="numbers" maxlength="16" type="text" placeholder="1234 1234 1234 1234">
			<div style="display: flex; flex-direction: row;">
			<input name="date" maxlength="5" type="text" placeholder="MM/YY" style="width: 50%">
			<input name="cvc" maxlength="3" type="text" placeholder="CVC" style="width: 50%">
			</div>
			<br>
			<label for="email">Name on card</label>
			<input name="nameCard" type="text" placeholder="name">
			<br>
			<br>
			<br>
			<label for="email">Country or Region</label>
			<input type="text" name="country" placeholder="Country">
			<input type="text" name="zip" placeholder="Zip">
			<br>
			<input type="hidden" name="total" value="<?php echo $precio * $people; ?>">
			<input type="hidden" name="tour-id" value="<?php echo $booking_detail['tour-id']; ?>">
			<input type="hidden" name="tour-date" value="<?php echo $booking_detail['tour-date']; ?>">
			<input type="hidden" name="tour-people" value="<?php echo $booking_detail['tour-people']; ?>">
			<input type="hidden" name="codigo" value="<?php echo $codigo ?>">
			<?php
				$auth_id = get_post_field( 'post_author', $booking_detail['tour-id'] );
				$stripe_act_id = get_user_meta($auth_id, 'stripe_connect_id', true);
			?>
			<input type="hidden" name="guide-stripe" value="<?php echo $stripe_act_id; ?>">
			<input type="hidden" name="user-id" value="<?php echo get_current_user_id(); ?>">
			<input type="hidden" name="option" value="payment-order">

			<input type="submit" value="Pay $<?php echo $precio * $people; ?>">


		</form>
	</div>
</div>






				

			</div>
		</div>
	</div>
</div>



</div>
<?php
if($booking_detail['tour-date'] == ""){
	echo '
	<script>
	
	document.getElementById("date-modal").style.display = "block";
	console.log("no hay fecha");
	</script>
	';
	} else {
		echo "
		<script>
			console.log('no hay fecha en else');
		</script>
		";
	
	}
?>

<script>
	console.log("el id del usuario es: " + <?php echo get_current_user_id(); ?>);
var modal = document.getElementById("myModal");
var btn = document.getElementById("myBtn");
var send_btn = document.getElementById("send-btn");
var btn_stripe = document.getElementById("btn-open-stripe");
var btnContinue = document.getElementById("btn-continue");
var span = document.getElementsByClassName("close")[0];
var span2 = document.getElementsByClassName("close")[1];
var info_payment = document.getElementById("info-payment");
var btn_continue = document.getElementById("btn-continue");

var modal2 = document.getElementById("stripe-modal");

var div_table = document.querySelector("#table");
var div_part1 = document.querySelector("#part-1");
var div_part2 = document.querySelector("#part-2");
btn.onclick = function() {
	modal.style.display = "block";
	info_payment.style.display = "block";
	btn_continue.style.display = "block";
	btn.style.display = "none";
}
send_btn.onclick = function(){
	modal.style.display = "none";
}
btnContinue.onclick = function() {
	div_part1.style.display = "none";
	div_part2.style.display = "none";
	div_table.style.display = "block";
}
btn_stripe.onclick = function(){
	modal2.style.display = "block";
}
span.onclick = function() {
  	modal.style.display = "none";
}
/* span2.onclick = function() {
  	modal2.style.display = "none";
} */
window.onclick = function(event) {
	if (event.target == modal) {
		modal.style.display = "none";
	}
	if (event.target == modal2) {
		modal2.style.display = "none";
	}
	if (event.target == document.getElementById("date-modal")) {
		document.getElementById("date-modal").style.display = "none";
	}
}

function countChars(obj){
    var maxLength = 100;
    var strLength = obj.value.length;
    
    if(strLength > maxLength){
        document.getElementById("charNum").innerHTML = '<span style="color: red;">'+strLength+' out of '+maxLength+' characters</span>';
    }else{
        document.getElementById("charNum").innerHTML = strLength;
    }
}
</script>



<!-- PRUEBA -->
<!-- <div id="frm-prueba" <?php echo 'data-ajax-url="' . esc_url(TOURMASTER_CUSTOM) . '" ';   echo 'data-booking-detail="' . esc_attr(json_encode($booking_detail)) . '" ';      ?> >
<p>Hola <?php echo $booking_detail['tour-id'] ?> </p>
</div>
	<button id="btn_click">Enviar 2</button>
<button onclick="prueba_click();">Enviar</button>

<form method="POST" id="frm-payment">

<p>Card Information</p>
<input type="text" name="number" placeholder="1234 1234 1234 1234">
<input type="text" name="date" placeholder="MM/YY">
<input type="text" name="cvc" placeholder="CVC">

<p>Name on card</p>
<input type="text" name="name" placeholder="Name">

<input type="hidden" name="tour-id" value="<?php echo $booking_detail['tour-id'] ?>">
<input type="hidden" name="option" value="payment-order">

<input type="submit">
</form>
 -->


</div>

<?php




/* ======/Banner con datos====== */









// echo '<div class="tourmaster-template-wrapper" id="tourmaster-payment-template-wrapper" ';
// echo 'data-ajax-url="' . esc_url(TOURMASTER_AJAX_URL) . '" ';
// echo 'data-booking-detail="' . esc_attr(json_encode($booking_detail)) . '" >';
// echo '<div class="tourmaster-container" >';
// echo '<div class="tourmaster-page-content tourmaster-item-pdlr clearfix" >';

$content = tourmaster_get_payment_page($booking_detail, true);

/* tourmaster booking bar */
// echo '<div class="tourmaster-tour-booking-bar-wrap" id="tourmaster-tour-booking-bar-wrap" style="margin-top: 0;" >';
// echo '<div class="tourmaster-tour-booking-bar-outer" >';
// echo '<div class="tourmaster-tour-booking-bar-inner" id="tourmaster-tour-booking-bar-inner" >';
// echo $content['sidebar'];
// echo "<h1>HOLA</h1>";}
// echo '</div>'; // tourmaster-tour-booking-bar-inner
// echo '</div>'; // tourmaster-tour-booking-bar-outer
// sidebar widget
$sidebar_name = tourmaster_get_option('general', 'payment-page-sidebar', 'none');
if ($sidebar_name != 'none' && is_active_sidebar($sidebar_name)) {
	$sidebar_class = apply_filters('gdlr_core_sidebar_class', '');

	// echo '<div class="tourmaster-tour-booking-bar-widget ' . esc_attr($sidebar_class) . '" >';
	dynamic_sidebar($sidebar_name);
	// echo '</div>';
}
// echo '</div>'; // tourmaster-tour-booking-bar-wrap

// echo '<div class="tourmaster-tour-payment-content" id="tourmaster-tour-payment-content" >';
// echo $content['content'];
// echo '</div>'; // tourmaster-tour-payment-content

// echo '</div>'; // tourmaster-page-content
// echo '</div>'; // tourmaster-container
// echo '</div>'; // tourmaster-template-wrapper	

// echo '</div>'; // tourmaster-page-wrapper
get_footer();

do_action('include_goodlayers_payment_script');


?>