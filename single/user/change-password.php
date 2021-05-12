<?php
	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-change-password" >';
	tourmaster_get_user_breadcrumb();

	// updated data
	global $tourmaster_updated_status;
	if( !empty($tourmaster_updated_status) ){
		
		// print error message
		if( is_wp_error($tourmaster_updated_status) ){
			$error_messages = '';
			foreach( $tourmaster_updated_status->get_error_messages() as $messages ){
				$error_messages .= empty($error_messages)? '': '<br />';
				$error_messages .= $messages;
			}
			tourmaster_user_update_notification($error_messages, false);

		// print success status
		}else{
			tourmaster_user_update_notification(esc_html__('Your password has been successfully changed.', 'tourmaster'));
		}
	}

	// edit profile page content
	$password_fields = array(
		'old-password' => array(
			'title' => esc_html__('Old Password', 'tourmaster'),
			'type' => 'password',
			'required' => true
		),
		'new-password' => array(
			'title' => esc_html__('New Password', 'tourmaster'),
			'type' => 'password',
			'required' => true
		),
		'confirm-password' => array(
			'title' => esc_html__('Confirm Password', 'tourmaster'),
			'type' => 'password',
			'required' => true
		),
	);

	echo '<form class="tourmaster-edit-profile-wrap tourmaster-form-field" method="POST" >';

	foreach( $password_fields as $slug => $password_field ){
		$password_field['slug'] = $slug;
		tourmaster_get_form_field($password_field, 'profile');
	}

	echo '<input type="submit" class="tourmaster-edit-profile-submit tourmaster-button" value="' . esc_html__('Update Password', 'tourmaster') . '" />';

	wp_nonce_field('tourmaster-change-password', 'security');
	echo '</form>'; // tourmaster-edit-profile-wrap

	echo '</div>'; // tourmaster-user-content-inner

?>