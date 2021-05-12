<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/**
 * The template for displaying single tour posttype
 */

// $booking_detail['tour-id'] = '30734';
// $booking_detail['tour-date'] = '2020-01-20';
// $id_tour = $booking_detail['tour-id'];
// $booking_detail['tour-people'] = get_post_meta($id_tour, 'tourmaster-max-people', true);
// $date_selected = date_create($booking_detail['tour-date']);

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

// DATOS DE CIUDAD
$state = get_post_meta($booking_detail['tour-id'], 'state');
$city = get_post_meta($booking_detail['tour-id'], 'city');
/* ======Banner con datos====== */

$booking_detail['tour-people'] = get_post_meta($id_tour, 'tourmaster-max-people', true);
$date_selected = date_create($booking_detail['tour-date']);
$people = 1;
?>
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
	width: 50%;
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

table.custom-table {
		background-color: #F2F2F2;
	}
	table.custom-table tr th {
		background-color: #BDBDBD;
	}
	table.custom-table tr td {
		font-weight: 400;
		padding: 5px;
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
	@media only screen and (max-width: 999px) { 
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
		}
		
	}
</style>


<div class="gdlr-core-pbf-wrapper " style="margin: 0px 0px 0px 0px;padding: 0px 0px 115px 0px;">


<form action="https://theoutdoortrip.com/tours/"  method="GET">
		<div id="search-box">
				<div class="item">
					<div class="contact_body">
						<div class="form_input" style="padding-bottom: 20px;">
							<label for="destination">Destination or Guide Name</label>
							<input type="text" name="destination" id="destination" placeholder="Enter Destination or Guide Name">
						</div>
					</div>
				</div>
				<div class="item">
					<div class="contact_body">
						<div class="form_input" style="padding-bottom: 20px;">
							<label for="trip_date">Trip Date</label>
							<input type="date" name="trip_date" id="trip_date" placeholder="Trip Date">
						</div>
					</div>
				</div>
				<div class="item">
					<div class="contact_body">
						<div class="form_input" style="padding-bottom: 20px;">
							<label for="type">Type</label>
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
							<label for="species">Species</label>
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



		<div class="gdlr-core-pbf-background-wrap">
			<!-- <div class="gdlr-core-pbf-background" style="background-image: url(&quot;https://theoutdoortrip.stg.elaniin.dev/wp-content/uploads/2020/11/AdobeStock_220302498_Preview-1.jpg&quot;); background-size: cover; background-position: center center;" data-parallax-speed="0.8"></div> -->
			<div class="gdlr-core-pbf-background" style="background-image: url(&quot;<?php echo $header_img; ?>&quot;); background-size: cover; background-position: top center;" data-parallax-speed="0.8"></div>
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
										<p class="gdlr-core-skin-title location" style="font-weight: 500;"><?php echo $state[0]; echo  ($city[0] == "") ? "" : " - " . $city[0]; ?></p>
									</div>

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
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- <div class="gdlr-core-pbf-wrapper " style="margin: 0px 0px 0px 0px;padding: 115px 0px 115px 0px;">
	<div class="gdlr-core-pbf-background-wrap">
		<div class="gdlr-core-pbf-background" style="background-image: url(&quot;<?php echo $header_img; ?>&quot;); background-size: cover; background-position: top center;" data-parallax-speed="0.8"></div>
	</div>
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-column gdlr-core-column-first first-c">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js">
						<div class="gdlr-core-pbf-element">
								<div class="gdlr-core-title-item-title-wrap">
									<h2 class="gdlr-core-title-item-title gdlr-core-skin-title" style="font-size: 43px ;letter-spacing: 5px ;color: #ffffff ;"><?php echo  $guide_name; ?><span class="gdlr-core-title-item-title-divider gdlr-core-skin-divider"></span></h2>
									<h4 class="gdlr-core-title-item-caption gdlr-core-info-font gdlr-core-skin-caption" style="color: #fff;">/ <?php echo $state[0]; echo  ($city[0] == "") ? "" : " - " . $city[0];
								?> </h4>
								</div>
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
										<p class="gdlr-core-skin-title location">City Name, State Name</p>
									</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> -->




<div class="gdlr-core-pbf-wrapper " style="padding: 40px 0px 30px 0px;" data-skin="Blue Icon" style="border: none;" id="steps-esconder"> 
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
						<i class=" gdlr-core-icon-item-icon fa fa-check" style="background-color: #F6A32A ; color: #fff; font-size: 20px ;min-width: 20px ;min-height: 20px ;"></i>
					</button>
				</div>
				<div class="item-4">
					<hr style="content: ''; height: 2px; background-color:#F6A32A; color:#E0E0E0; border: none; width: calc((1180px / 2)  - 70px);">
				</div>
				<div class="item-5">
					<button style="padding: 0; background-color: #E0E0E0; border-radius: 50%; padding: 10px">
						<i class=" gdlr-core-icon-item-icon fa fa-circle" style="background-color: #E0E0E0 ; color: #FFFFFF; font-size: 20px ;min-width: 20px ;min-height: 20px ;"></i>
					</button>
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
			<div class="gdlr-core-pbf-column gdlr-core-column-100 gdlr-core-column-first">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px; padding: 10px;">


								<table class="custom-table">
									<tr>
										<th colspan="2" style="color: black; font-weight: 700">Your Information</th>
									</tr>
									<tr>
										<td colspan="2"><p id="mensaje_error" style="color: #EB5757; display: none;">You need to complete all fields</p></td>
									</tr>
									<tr>
										<td>
											<div class="form-group" style="width: 90%; margin: 0 auto;">
												<label for="user_first_frm">First name</label>
												<input type="text" name="first_name_frm" id="user_first_frm" class="input"  value="" style="width: 100%;">
											</div>
										</td>
										<td>
											<div class="form-group" style="width: 90%; margin: 0 auto;">
												<label for="user_last_frm">Last name</label>
												<input type="text" name="last_name_frm" id="user_last_frm" class="input" value="" style="width: 100%;">
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="form-group" style="width: 90%; margin: 0 auto; margin-bottom: 10px;">
												<label for="user_email_frm">Email Address</label>
												<input type="text" name="email_address_frm" id="user_email_frm" class="input" value="" style="width: 100%;">
											</div>
										</td>
										<td>
											<div class="form-group" style="width: 90%; margin: 0 auto;  margin-bottom: 10px;">
												<label for="user_number_frm">Mobile Number</label>
												<input type="text" name="mobile_number_frm" id="user_number_frm" class="input" value="" style="width: 100%;">
											</div>
										</td>
									</tr>
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
										<td class="table-left">Party Size: </td>
										<td class="table-right"> 
											<select name="tour-people" id="tour-people" style="padding: 10px;">
												<?php
												for ($i=1; $i <= $booking_detail['tour-people']; $i++) { 
													echo '<option value="'.$i.'">'.$i.'</option>';
												}
												?> 

											</select> 
										</td>
									</tr>
									<tr>
										<td class="table-left">Arrival Date: </td>
										<td class="table-right"> <?php echo date_format($date_selected, "D, d M-Y"); ?> </td>
									</tr>
									<tr>
										<td class="table-left">Departure Date: </td>
										<td class="table-right"> <?php echo date_format($date_selected, "D, d M-Y"); ?> </td>
									</tr>
									
									<tr>
										<td> </td>
										<td> </td>
									</tr>
									<tr>
										<td class="table-left">Trip Price: </td>
										<td class="table-right"> $ <?php echo ($precio); ?> </td>
									</tr>
									<tr id="url-return" style="display: none;">
										<td> <a href="https://theoutdoortrip.com/">Return to www.theoutdoortrip.com</a> </td>
									</tr>
									<tr id="hide-1">
										<th colspan="2" style="color: black; font-weight: 700">
											Payments Due
										</th>
									</tr>

									<tr>
										<td class="table-left"> 
											<select name="tour-price-option" id="tour-price-option" style="padding: 10px;">
												<option value="<?php echo $precio; ?>">Pay Full Amount</option>
												<option value="<?php echo ($precio * 0.30); ?>">Pay Deposit</option>
											</select> 
											<input type="hidden" id="total-hid-total" value="<?php echo $precio; ?>">
											<input type="hidden" id="total-hid-deposit" value="<?php echo ($precio * 0.30); ?>">
										</td>
										<td class="table-right"> $<span id="will-pay"><?php echo $precio; ?></span> </td>
									</tr>

									<tr id="hide-3">
										<td class="table-left">Booking Fee: </td>
										<td class="table-right"> $20 </td>
									</tr>

									<tr id="hide-4">
										<td class="table-left">Processing Fee: </td>
										<td class="table-right"> <span id="calculate-fee"> $<?php echo round( ( ($precio) + 20) * 0.039, 2); ?> </span> </td>
									</tr>

									<tr id="hide-4">
										<td class="table-left">Total Due Now: </td>
										<td class="table-right"> <span id="total-due-now"> $<?php echo round( ( ($precio) + 20) * 1.039, 2); ?> </span> </td>
									</tr>
									<tr id="hide-4">
										<td class="table-left" style="color: #EB5757;">Remaining Balance Due:: </td>
										<td class="table-right" style="color: #EB5757;"> $<span id="remaining-balance"> 0 </span> </td>
									</tr>



									<!-- <tr id="hide-2">
										<td class="table-left">Deposit: </td>
										<td class="table-right"> $ <?php echo ($precio) * 0.30; ?> </td>
									</tr>
									<tr id="hide-2">
										<td class="table-left">Balance: </td>
										<td class="table-right"> $ <?php echo ($precio - ($precio * 0.30)); ?> </td>
									</tr> -->
									
									
									
									
									
									
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
											<button style="border: 3px solid #F6A32A; color: #F6A32A; padding: 10px 20px; margin-bottom: 50px; font-weight: 700; background-color:#F2F2F2">
												Cancel
											</button>	
											<button style="border: 3px solid #F6A32A; background: #F6A32A; color: #fff; padding: 10px 20px; margin-bottom: 50px; font-weight: 700;" id="myBtn">
												Pay Now
											</button>	
											<br>
											<a href="#" style="color: #4285F4;">Terms of Cancellation</a>
										</td>
									</tr>
									
								</table>

						</div>
					</div>
				</div>
			</div>


			<div class="gdlr-core-pbf-column gdlr-core-column-36">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js ">
						<div class="gdlr-core-pbf-element" style="margin-top: 5px; padding: 10px;">

<div id="myModal" class="modal" >
	<div class="modal-content">
		<span class="close">&times;</span>
		<!-- <span class="close-2">&times;</span> -->
		<h6 style="color: #ffffff; background-color: #0A2540; text-align: center; margin: -10px;">Stripe</h6>
		<form method="POST" class="stripe-pay-form" id="frm-payment">
			<br>
			<label for="email">Address</label>
			<input type="text" placeholder="Name" name="nombre" value="">
			<input type="text" placeholder="Address line 1" name="adr-line1">
			<input type="text" placeholder="Address line 2" name="adr-line2">
			<input type="text" placeholder="City" name="city">
			<input type="text" placeholder="State" name="state">
			<input type="text" placeholder="Country" name="country">
			<br>
			<br>
			<br>
			<label for="email">Card Information</label>
			<input name="numbers" id="numbers" maxlength="16" type="text" placeholder="1234 1234 1234 1234">
			<div style="display: flex; flex-direction: row;">
			<input name="month" id="months" maxlength="2" type="text" placeholder="MM" style="width: 50%">
			<input name="year" id="year" maxlength="2" type="text" placeholder="YY" style="width: 50%">
			</div>
			<input name="cvc" maxlength="3" type="text" placeholder="CVC">
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
			<input type="hidden" name="total" value="<?php echo (($precio * $people) * 0.30 + 20) * 1.039; ?>">
			<input type="hidden" name="total_trip" id="total_trip" value="<?php echo $precio ?>">
			<input type="hidden" name="tour-id" id="tour-id" value="<?php echo $booking_detail['tour-id']; ?>">
			<input type="hidden" name="tour-date" id="tour-date" value="<?php echo $booking_detail['tour-date']; ?>">
			<input type="hidden" name="codigo" id="codigo_booking" value="<?php echo $codigo ?>">
			<?php
				$auth_id = get_post_field( 'post_author', $booking_detail['tour-id'] );
				$stripe_act_id = get_user_meta($auth_id, 'stripe_connect_id', true);
				$guide = get_userdata($auth_id);
				$title = get_post($booking_detail['tour-id']);
				$title_post = $title->post_title;
			?>
			<input type="hidden" name="guide-stripe" value="<?php echo $stripe_act_id; ?>">
			<input type="hidden" name="user-id" id="user-id" value="<?php echo $guia; ?>">
			<input type="hidden" name="user-id-mail" id="user-id-mail" value="<?php echo $guide->user_email;  ?>">
			<input type="hidden" name="user-id-title" id="user-id-title" value="<?php echo $title_post; ?>">
			<input type="hidden" name="option" value="payment-order">
			<input type="submit" id="btn-pay" value="Pay $<?php echo round( ( ($precio) + 20) * 1.039, 2); ?>">
		</form>
		
	</div>

</div>
<div id="modal2" class="modal-2">
	<div class="modal-content-2" style="padding: 20px;">
		<span class="close-2">&times;</span>
		<h1 style="text-align: center;">Thank you for booking your trip!</h1>
		<h6 style="text-align: center;">Your payment has been completed.</h6>
		<!-- <a href="https://theoutdoortrip.stg.elaniin.dev/">Return to Homepage</a> -->
		<a href="https://theoutdoortrip.com/">Return to Homepage</a>
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
var modal = document.getElementById("myModal");
var btn = document.getElementById("myBtn");
var span = document.getElementsByClassName("close")[0];
var label_box = document.getElementById("mensaje_error");

btn.onclick = function() {
	var email = document.getElementById("user_email_frm").value;
	var first_name = document.getElementById("user_first_frm").value;
	var last_name = document.getElementById("user_last_frm").value;
	var phone = document.getElementById("user_number_frm").value;
	if(email == "" || first_name == "" || last_name == "" || phone == ""){
		window.scroll({
			top: 550,
			behavior: 'smooth'
		});
		label_box.style.display = "block";
		console.log(email);
		console.log(first_name);
		console.log(last_name);
		console.log(phone);
	} else {

		modal.style.display = "block";
	}
}
span.onclick = function() {
	modal.style.display = "none";
}
window.onclick = function(event) {
	if (event.target == modal) {
		modal.style.display = "none";
	}
	if (event.target == document.getElementById("date-modal")) {
		document.getElementById("date-modal").style.display = "none";
	}
}
</script>
</div>

<?php
get_footer();
do_action('include_goodlayers_payment_script');
?>