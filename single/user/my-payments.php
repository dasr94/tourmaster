<?php 

function callAPI($method, $url, $data){
    $curl = curl_init();
    switch ($method){
       case "POST":
          curl_setopt($curl, CURLOPT_POST, 1);
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
          break;
       case "PUT":
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
          if ($data)
             curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
          break;
       default:
          if ($data)
             $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die(curl_error($curl));}
    curl_close($curl);
    return $result;
 }
echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-my-booking" >';
tourmaster_get_user_breadcrumb();

tourmaster_user_content_block_start(array(
    'title' => esc_html__('My Information', 'tourmaster')/*,
    'title-link-text' => esc_html__('Edit Profile', 'tourmaster'),
    'title-link' => tourmaster_get_template_url('user', array('page_type'=>'edit-profile'))*/
));

$current_user = wp_get_current_user();
$email = $current_user->user_email;
$username = $current_user->user_login;

$stripe_act_id = get_user_meta($current_user->ID, 'stripe_connect_id', true);

echo '<div class="tourmaster-my-profile-wrapper" style="margin-top: 15px !important">';
echo '<div class="tourmaster-my-profile-info-wrap clearfix" >';

if ($stripe_act_id != '' || $stripe_act_id != null) {
   $get_data = callAPI('GET', 'https://theoutdoortrip.com/wp-json/tot/v1/stripe/get_acct/'.$stripe_act_id, false);
   $response = json_decode($get_data);
   echo '<div class="tourmaster-my-profile-info tourmaster-my-profile-info-full_name tourmaster-even clearfix"><span class="tourmaster-head">Name</span><span class="tourmaster-tail">'.$response->business_profile->name.'</span></div>';
   echo '<div class="tourmaster-my-profile-info tourmaster-my-profile-info-full_name tourmaster-odd clearfix"><span class="tourmaster-head">Description</span><span class="tourmaster-tail">'.$response->business_profile->product_description.'</span></div>';
   echo '<div class="tourmaster-my-profile-info tourmaster-my-profile-info-full_name tourmaster-even clearfix"><span class="tourmaster-head">Email</span><span class="tourmaster-tail">'.$response->email.'</span></div>';
   echo '<div class="tourmaster-my-profile-info tourmaster-my-profile-info-full_name tourmaster-odd clearfix"><span class="tourmaster-head">Phone</span><span class="tourmaster-tail">'.$response->business_profile->support_phone.'</span></div>';
   echo '<div class="tourmaster-my-profile-info tourmaster-my-profile-info-full_name tourmaster-even clearfix"><span class="tourmaster-head">Web Url</span><span class="tourmaster-tail">'.$response->business_profile->url.'</span></div>';
} else {
   //$get_data = callAPI('GET', 'https://theoutdoortrip.stg.elaniin.dev/wp-json/tot/v1/get/oauth/?email='.$email.'&username='.$username, false);
   //$response = json_decode($get_data, true);
   echo '<div>There is no Stripe account associated yet. A Stripe account will be required to receive online payments.</div>';
   echo '</br>';
   echo '<div>Please click the button below and follow the process</div>';
   echo '<a href="https://connect.stripe.com/express/oauth/authorize?client_id=ca_AbYvq1YpGg7rRjY9T7ICBtBWj6z87bCb&state='.$username.'&suggested_capabilities[]=transfers&stripe_user[email]='.$email.'" class="stripe-connect" ><span>Connect with stripe<span></a>';
}

echo '</div>';
echo '</div>';

echo '</table>';

tourmaster_user_content_block_end();

if ($stripe_act_id != '' || $stripe_act_id != null) {

tourmaster_user_content_block_start(array(
   'title' => esc_html__('My Payments', 'tourmaster')/*,
   'title-link-text' => esc_html__('Edit Profile', 'tourmaster'),
   'title-link' => tourmaster_get_template_url('user', array('page_type'=>'edit-profile'))*/
));


echo '<table class="tourmaster-table" >';
tourmaster_get_table_head(array(
   esc_html__('Order', 'tourmaster'),
   esc_html__('Transaction ID', 'tourmaster'),
   esc_html__('Amount', 'tourmaster'),
   esc_html__('Date', 'tourmaster'),
));

$get_data = callAPI('GET', 'https://theoutdoortrip.com/wp-json/tot/v1/stripe/get_all_tr/'.$stripe_act_id, false);
$orders = json_decode($get_data);

foreach($orders as $order){

   $result = tourmaster_get_booking_data(array(
      'id' => $order->order_trip_id
   ), array('single' => true));
   
   $order_det  = '<div class="tourmaster-head" >#' . $result->id . '<span class="tourmaster-travel-date" > - ' . tourmaster_date_format($result->travel_date) . '</span>' . '</div>';
   $order_det .= '<div class="tourmaster-content" ><a href="' . add_query_arg(array('page_type'=>'my-orders','single'=>$result->id), remove_query_arg(array('order_id', 'from_date', 'to_date', 'action', 'id', 'export'))) . '" >';
   $order_det .= get_the_title($result->tour_id);
   $order_det .= '</a></div>';

   $tr_id = $order->pi_id;

   $tr_amount = $order->amount/100 - $order->fee/100;

   $date = gmdate("Y-m-d", $order->payment_date);

   tourmaster_get_table_content(array(
      $order_det,
      $tr_id,
      '<span class="tourmaster-my-booking-price" style="font-weight: 600" >' . tourmaster_money_format($tr_amount) . '</span>',
      $date
   ));
}
echo '</table>';

tourmaster_user_content_block_end();
}

echo '</div>';

?>