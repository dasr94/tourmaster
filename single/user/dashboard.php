<?php
	/* dashboard page content */
	global $current_user;

	///////////////////////
	// my profile section
	///////////////////////
	tourmaster_user_content_block_start(array(
		'title' => esc_html__('My Profile', 'tourmaster'),
		'title-link-text' => esc_html__('Edit Profile', 'tourmaster'),
		'title-link' => tourmaster_get_template_url('user', array('page_type'=>'edit-profile'))
	));

	$profile_list = array(
		'full_name' => esc_html__('Name', 'tourmaster'),
		'gender' => esc_html__('Gender', 'tourmaster'),
		'birth_date' => esc_html__('Birth Date', 'tourmaster'),
		'country' => esc_html__('Country', 'tourmaster'),
		'email' => esc_html__('Email', 'tourmaster'),
		'phone' => esc_html__('Phone', 'tourmaster'),
		'contact_address' => esc_html__('Contact Address', 'tourmaster'),
	);
	echo '<div class="tourmaster-my-profile-wrapper" >';
	echo '<div class="tourmaster-my-profile-avatar" >';
	$avatar = get_the_author_meta('tourmaster-user-avatar', $current_user->data->ID);
	if( !empty($avatar['thumbnail']) ){
		echo '<img src="' . esc_url($avatar['thumbnail']) . '" alt="profile-image" />';
	}else if( !empty($avatar['file_url']) ){
		echo '<img src="' . esc_url($avatar['file_url']) . '" alt="profile-image" />';
	}else{
		echo get_avatar($current_user->data->ID, 85);
	}
	echo '</div>';

	$even_column = true;
	echo '<div class="tourmaster-my-profile-info-wrap clearfix" >';
	foreach( $profile_list as $meta_field => $field_title ){
		$extra_class  = 'tourmaster-my-profile-info-' . $meta_field;
		$extra_class .= ($even_column)? ' tourmaster-even': ' tourmaster-odd';
		

		echo '<div class="tourmaster-my-profile-info ' . esc_attr($extra_class) . ' clearfix" >';
		echo '<span class="tourmaster-head" >' . $field_title . '</span>';
		echo '<span class="tourmaster-tail" >';
		if( $meta_field == 'birth_date' ){
			$user_meta = tourmaster_get_user_meta($current_user->data->ID, $meta_field, '-');
			if( $user_meta == '-' ){
				echo $user_meta;
			}else{
				echo tourmaster_date_format($user_meta);
			}
		}else if( $meta_field == 'gender' ){
			$user_meta = tourmaster_get_user_meta($current_user->data->ID, $meta_field, '-');
			if( $user_meta == 'male' ){
				echo esc_html__('Male', 'tourmaster');
			}else if( $user_meta == 'female' ){
				echo esc_html__('Female', 'tourmaster');
			}
		}else{
			echo tourmaster_get_user_meta($current_user->data->ID, $meta_field, '-');
		}

		echo '</span>';
		echo '</div>';

		$even_column = !$even_column;
	}
	echo '</div>'; // tourmaster-my-profile-info-wrap

	echo '</div>'; // tourmaster-my-profile-wrapper
	tourmaster_user_content_block_end();

	global $wpdb;
	$usr_id = $current_user->data->ID;
	$results = $wpdb->get_results( 'SELECT * FROM wp_tourmaster_order WHERE user_id = ' . $usr_id, OBJECT );
	// var_dump($results);
	$dates = array();
	foreach($results as $rs){
		array_push($dates, $rs->travel_date);
	}
	var_dump($dates);

	echo '<div class="tourmaster-user-content-block" >';
	echo '<div class="tourmaster-user-content-title-wrap"><h3 class="tourmaster-user-content-title">Calendary</h3><a class="tourmaster-user-content-title-link" href="https://theoutdoortrip.com/dashboard/?page_type=my-orders">See Orders</a></div>';
	echo '<h6>Calendary</h6>';


	echo '<div id="m-2">
	<div style="display: flex; flex-direction: row; justify-content: space-between;">
	<div> <h6>'. date("M") .'</h6> </div>
	<div> <span style="font-size: 24px; font-weight: 700; color: #000;" onclick="nextMonth(2)">></span> </div>
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
echo "<td>", date("d", mktime(0,0,0,$mes,1,$año))  , "</td>"; 
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
echo "<td class='available'  id='d-", date("d", mktime(0,0,0,$mes,$j,$año)) ,"' >",   date("d", mktime(0,0,0,$mes,$j,$año)) ,  "</td>" ;
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




	echo '</div>';

	///////////////////////
	// my booking section
	///////////////////////
 
	$current_user = wp_get_current_user();
	$current_id = $current_user->ID;

	$role_end = get_user_meta($current_id, "user-type", true);

	if ($role_end == 'user-traveler') {
		// query 
		$conditions = array('user_id' => $current_user->data->ID, 'order_status'=> array('condition'=>'!=', 'value'=>'cancel'));
		$results = tourmaster_get_booking_data($conditions, array('paged'=>1, 'num-fetch'=>5));

		if( !empty($results) ){	
			$statuses = array(
				'all' => esc_html__('All', 'tourmaster'),
				'pending' => esc_html__('Pending', 'tourmaster'),
				'approved' => esc_html__('Approved', 'tourmaster'),
				'receipt-submitted' => esc_html__('Receipt Submitted', 'tourmaster'),
				'online-paid' => esc_html__('Online Paid', 'tourmaster'),
				'deposit-paid' => esc_html__('Deposit Paid', 'tourmaster'),
				'departed' => esc_html__('Departed', 'tourmaster'),
				'rejected' => esc_html__('Rejected', 'tourmaster'),
				'wait-for-approval' => esc_html__('Wait For Approval', 'tourmaster'),
			);

			tourmaster_user_content_block_start(array(
				'title' => esc_html__('Current Booking', 'tourmaster'),
				'title-link-text' => esc_html__('View All Bookings', 'tourmaster'),
				'title-link' => tourmaster_get_template_url('user', array('page_type'=>'my-booking'))
			));

			echo '<table class="tourmaster-my-booking-table tourmaster-table" >';
			tourmaster_get_table_head(array(
				esc_html__('Tour Name', 'tourmaster'),
				esc_html__('Travel Date', 'tourmaster'),
				esc_html__('Total', 'tourmaster'),
				esc_html__('Payment Status', 'tourmaster'),
			));
			foreach( $results as $result ){

				$single_booking_url = add_query_arg(array(
					'page_type' => 'my-booking',
					'sub_page' => 'single',
					'id' => $result->id,
					'tour_id' => $result->tour_id
				));
				$title = '<a class="tourmaster-my-booking-title" href="' . esc_url($single_booking_url) . '" >' . get_the_title($result->tour_id) . '</a>';

				$status  = '<span class="tourmaster-my-booking-status tourmaster-booking-status tourmaster-status-' . esc_attr($result->order_status) . '" >';
				if( $result->order_status == 'approved' ){
					$status .= '<i class="fa fa-check" ></i>';
				}else if( $result->order_status == 'departed' ){
					$status .= '<i class="fa fa-check-circle-o" ></i>';
				}else if( $result->order_status == 'rejected' ){
					$status .= '<i class="fa fa-remove" ></i>';
				}		
				$status .= $statuses[$result->order_status];
				$status .= '</span>';
				if( in_array($result->order_status, array('pending', 'receipt-submitted', 'rejected')) ){
					$status .= '<a class="tourmaster-my-booking-action fa fa-dollar" href="' . esc_url($single_booking_url) . '" ></a>';
				}
				if( in_array($result->order_status, array('pending', 'receipt-submitted', 'rejected')) ){
					$status .= '<a class="tourmaster-my-booking-action fa fa-remove" href="' . add_query_arg(array('action'=>'remove', 'id'=>$result->id)) . '" ></a>';
				}

				tourmaster_get_table_content(array(
					$title,
					tourmaster_date_format($result->travel_date),
					'<span class="tourmaster-my-booking-price" >' . tourmaster_money_format($result->total_price) . '</span>',
					$status
				));
			}
			echo '</table>';

			tourmaster_user_content_block_end();
		}
	} else {
		
	}
	

	///////////////////////
	// review section
	///////////////////////
	$conditions = array(
		'user_id' => $current_user->data->ID,
		'order_status' => 'departed'
	);
	$results = tourmaster_get_booking_data($conditions, array('paged'=>1, 'num-fetch'=>5, 'with-review' => true));

	if( !empty($results) ){
		tourmaster_user_content_block_start(array(
			'title' => esc_html__('Reviews', 'tourmaster'),
			'title-link-text' => esc_html__('View All Reviews', 'tourmaster'),
			'title-link' => tourmaster_get_template_url('user', array('page_type'=>'reviews'))
		));

		echo '<table class="tourmaster-user-review-table tourmaster-table" >';
		tourmaster_get_table_head(array(
			esc_html__('Tour Name', 'tourmaster'),
			esc_html__('Status', 'tourmaster'),
			esc_html__('Action', 'tourmaster'),
		));		
		foreach( $results as $result ){
			$title = get_the_title($result->tour_id);

			if( $result->review_score == '' ){
				$status  = '<span class="tourmaster-user-review-status tourmaster-status-pending" >';	
				$status .= esc_html__('Pending', 'tourmaster');
				$status .= '</span>';

				$action  = '<span class="tourmaster-user-review-action" data-tmlb="submit-review" >' . esc_html__('Submit Review', 'tourmaster') . '</span>';
				$action .= tourmaster_lightbox_content(array(
					'id' => 'submit-review',
					'title' => esc_html__('Submit Your Review', 'tourmaster'),
					'content' => tourmaster_get_review_form( $result )
				));
			}else{
				$status  = '<span class="tourmaster-user-review-status tourmaster-status-submitted" >';	
				$status .= esc_html__('Submitted', 'tourmaster');
				$status .= '</span>';

				$action  = '<span class="tourmaster-user-review-action" data-tmlb="view-review" >' . esc_html__('View Review', 'tourmaster') . '</span>';		
				$action .= tourmaster_lightbox_content(array(
					'id' => 'view-review',
					'title' => esc_html__('Your Review', 'tourmaster'),
					'content' => tourmaster_get_submitted_review( $result )
				));
			}

			tourmaster_get_table_content(array($title, $status, $action));
		}
		echo '</table>';

		tourmaster_user_content_block_end();
	}

?>