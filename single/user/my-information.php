<style>
.tourmaster-body .tourmaster-form-field textarea, .traveltour-body input, .traveltour-body input {
	border: 1px solid #E0E0E0 !important;
}
</style>
<?php
	$profile_fields = tourmaster_get_profile_guide_fields();

	tourmaster_user_content_block_start();


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
	echo '<div class="tourmaster-page-option-content">';
	echo '<input type="hidden" class="tourmaster-page-option-value" name="tourmaster-tour-option" value="">';
	echo '<form class="tourmaster-edit-profile-wrap tourmaster-form-field" method="POST" enctype="multipart/form-data" >';
    
	foreach( $profile_fields as $slug => $profile_field ){
		$profile_field['slug'] = $slug; 
		tourmaster_get_form_field($profile_field, 'profile');
	}

	echo '<input type="submit" class="tourmaster-edit-profile-submit tourmaster-button" value="' . esc_html__('Update information   ', 'tourmaster') . '" />';
	echo '<input type="hidden" name="tourmaster-edit-profile" value="1" />';
	echo '</form>'; // tourmaster-edit-profile-wrap
	echo '</div>';

	echo '</div>'; // tourmaster-user-content-inner

	tourmaster_user_content_block_end();
?>