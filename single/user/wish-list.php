<?php
	echo '<div class="tourmaster-user-content-inner tourmaster-user-content-inner-wish-list" >';
	tourmaster_get_user_breadcrumb();

	// wish list block
	tourmaster_user_content_block_start();
	echo '<table class="tourmaster-wish-list-table tourmaster-table" >';

	tourmaster_get_table_head(array(
		esc_html__('Tour Name', 'tourmaster'),
		esc_html__('Action', 'tourmaster'),
	));

	$wish_list = get_user_meta($current_user->ID, 'tourmaster-wish-list', true);
	$wish_list = empty($wish_list)? array(): $wish_list;
	if( !empty($_POST['remove-from-wish-list']) ){
		$wish_list = array_diff($wish_list, array($_POST['remove-from-wish-list']));
		update_user_meta($current_user->ID, 'tourmaster-wish-list', $wish_list);
	}
	
	foreach( $wish_list as $tour_id ){
		$tour_link  = '<div class="tourmaster-wish-list-item" >';
		$thumbnail_id = get_post_thumbnail_id($tour_id);
		if( !empty($thumbnail_id) ){
			$tour_link .= '<div class="tourmaster-wish-list-thumbnail tourmaster-media-image" >' . tourmaster_get_image($thumbnail_id, 'thumbnail') . '</div>';
		}

		$tour_link .= '<div class="tourmaster-wish-list-item-content">';
		$tour_link .= '<a class="tourmaster-wish-list-item-title" href="' . get_permalink($tour_id) . '" target="_blank" >';
		$tour_link .= get_the_title($tour_id);
		$tour_link .= '</a>';

		$post_meta = get_post_meta($tour_id, 'tourmaster-tour-option', true);
		if( !empty($post_meta['duration-text']) ){
			$tour_link .= '<div class="tourmaster-wish-list-item-info" >';
			$tour_link .= '<i class="icon_clock_alt" ></i>';
			$tour_link .= tourmaster_text_filter($post_meta['duration-text']);
			$tour_link .= '</div>';
		}
		$tour_link .= '</div>'; // tourmaster-wish-list-item-content
		$tour_link .= '</div>'; // tourmaster-wish-list-item

		$remove_btn  = '<form class="tourmaster-wish-list-remove-item" method="POST" action="" onClick="this.submit();" >';
		$remove_btn .= '<i class="fa fa-trash-o" ></i>' . esc_html__('Remove', 'tourmaster');
		$remove_btn .= '<input type="hidden" name="remove-from-wish-list" value="' . esc_attr($tour_id) . '" />';
		$remove_btn .= '</form>';

		tourmaster_get_table_content(array($tour_link, $remove_btn));
	}

	echo '</table>';
	tourmaster_user_content_block_end();

	echo '</div>'; // tourmaster-user-content-inner