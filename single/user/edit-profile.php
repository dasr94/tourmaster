<?php
	$profile_fields = tourmaster_get_profile_fields();

	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-edit-profile" >';
	tourmaster_get_user_breadcrumb();

	// update data
	if( isset($_POST['tourmaster-edit-profile']) ){
		$verify = tourmaster_validate_profile_field($profile_fields);

		// if( !empty($_FILES['profile-image']['size']) && $_FILES['profile-image']['size'] > 150000 ){
		//     $verify = new WP_Error('file-size-limit', __("Please upload smaller file size", "tourmaster" ));
		// }

		if( is_wp_error($verify) ){
			$error_messages = '';
			foreach( $verify->get_error_messages() as $messages ){
				$error_messages .= empty($error_messages)? '': '<br />';
				$error_messages .= $messages;
			}
			tourmaster_user_update_notification($error_messages, false);
		}else{
			tourmaster_update_profile_avatar();			
			tourmaster_update_profile_field($profile_fields);
			tourmaster_user_update_notification(esc_html__('Your profile has been successfully changed.', 'tourmaster'));
		}
	}

	// edit profile page content
	$avatar = get_the_author_meta('tourmaster-user-avatar', $current_user->data->ID);
	echo '<form class="tourmaster-edit-profile-wrap tourmaster-form-field" method="POST" enctype="multipart/form-data" >';
	echo '<div class="tourmaster-edit-profile-avatar" >';
	if( !empty($avatar['thumbnail']) ){
		echo '<img src="' . esc_url($avatar['thumbnail']) . '" alt="profile-image" />';
	}else if( !empty($avatar['file_url']) ){
		echo '<img src="' . esc_url($avatar['file_url']) . '" alt="profile-image" />';
	}else{
		echo get_avatar($current_user->data->ID, 85);
	}
	echo '<label>';
	echo '<a class="tourmaster-button" >' . esc_html__('Change Profile Picture', 'tourmaster') . '</a>';
	echo '<input type="file" name="profile-image" />';
	echo '</label>';
	// echo '<a class="tourmaster-button" href="https://gravatar.com" target="_blank" >' . esc_html__('Change Profile Picture', 'tourmaster') . '</a>';
	echo '</div>';

	foreach( $profile_fields as $slug => $profile_field ){
		$profile_field['slug'] = $slug;
		tourmaster_get_form_field($profile_field, 'profile');
	}




	echo '<input type="submit" class="tourmaster-edit-profile-submit tourmaster-button" value="' . esc_html__('Update Profile', 'tourmaster') . '" />';
	echo '<input type="hidden" name="tourmaster-edit-profile" value="1" />';
	echo '</form>'; // tourmaster-edit-profile-wrap

	echo '</div>'; // tourmaster-user-content-inner
?>