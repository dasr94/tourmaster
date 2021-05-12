<?php

	global $current_user;
	$current_user = wp_get_current_user();
	$userlogin = get_user_meta($current_user->ID, 'user-type', true);

	if($userlogin == "user-traveler"){
		$nav_list = tourmaster_get_user_nav_list();
	} else {
		$nav_list = tourmaster_get_user_guide_nav_list();
	}

	$nav_active = empty($_GET['page_type'])? '': $_GET['page_type'];
	
	foreach( $nav_list as $nav_slug => $nav ){
		if( !empty($nav['type']) && $nav['type'] == 'title' ){
			echo '<h3 class="tourmaster-user-navigation-head" >' . $nav['title'] . '</h3>';
		}else{

			// assign active class
			$nav_class = 'tourmaster-user-navigation-item-' . $nav_slug;

			if( empty($nav_active) || $nav_active == $nav_slug ){
				$nav_active = $nav_slug;
				$nav_class = ' tourmaster-active'; 
			}

			// get the navigation link
			if( !empty($nav['link']) ){
				$nav_link = $nav['link'];
			}else{
				$nav_link = tourmaster_get_template_url('user', array('page_type'=>$nav_slug));
			}

			echo '<div class="tourmaster-user-navigation-item ' . esc_attr($nav_class) . '" >';
			echo '<a href="' . esc_url($nav_link) . '" >';
			if( !empty($nav['icon']) ){
				echo '<i class="tourmaster-user-navigation-item-icon ' . esc_attr($nav['icon']) . '" ></i>';
			}
			echo $nav['title'];
			echo '</a>';
			echo '</div>';		
		}
		
		
	}




?>