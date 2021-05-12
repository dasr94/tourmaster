<?php

	$nav_list = tourmaster_get_user_nav_list();
	$nav_active = empty($_GET['page_type'])? '': $_GET['page_type'];
	
	echo '<div class="tourmaster-combobox-wrap" >';
	echo '<select onchange="location=this.options[this.selectedIndex].value;" >';
	foreach( $nav_list as $nav_slug => $nav ){
		if( !empty($nav['type']) && $nav['type'] == 'title' ){
			echo '<option disabled >-- ' . $nav['title'] . ' --</option>';
		}else{

			// get the navigation link
			if( !empty($nav['link']) ){
				$nav_link = $nav['link'];
			}else{
				$nav_link = tourmaster_get_template_url('user', array('page_type'=>$nav_slug));
			}

			echo '<option value="' . esc_url($nav_link) . '" ';
			if( empty($nav_active) || $nav_active == $nav_slug ){
				$nav_active = $nav_slug;
				echo 'selected'; 
			}
			echo ' >' .  esc_html($nav['title']) . '</option>';		
		}
	}
	echo '</select>';
	echo '</div>';
?>