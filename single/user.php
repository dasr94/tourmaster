<?php
	/**
	 * The template for displaying single tour posttype
	 */

include(TOURMASTER_LOCAL . '/single/user/user-update.php');

get_header();

	echo '<div class="tourmaster-template-wrapper tourmaster-template-wrapper-user" >';

	// user navigation
	echo '<div class="tourmaster-user-navigation" >';
	include('user/user-navigation.php');

	$navigation_bottom_text = tourmaster_get_option('general', 'user-navigation-bottom-text', '');
	if( !empty($navigation_bottom_text) ){
		echo '<div class="tourmaster-user-navigation-bottom-text" >';
		echo tourmaster_content_filter($navigation_bottom_text);
		echo '</div>';
	}
	echo '</div>';

	// tab content
	echo '<div class="tourmaster-user-content" >';
	echo '<div class="tourmaster-user-mobile-navigation tourmaster-form-field" >';
	include('user/user-mobile-navigation.php');
	echo '</div>';
	
	$page_type = empty($_GET['page_type'])? 'dashboard': $_GET['page_type'];
	if( !empty($_GET['sub_page']) ){
		$page_type .= '-' . $_GET['sub_page'];
	}

	$template = TOURMASTER_LOCAL . '/single/user/' . $page_type . '.php';
	$template = apply_filters('tourmaster_user_content_template', $template, $page_type);
	if( file_exists($template) ){
		include($template);
	}else{
		tourmaster_user_content_block_start(); 
		echo esc_html__('Sorry, we couldn\'t find the page you\'re looking for.', 'tourmaster');
		tourmaster_user_content_block_end();
	}
	echo '</div>';

	echo '</div>'; // tourmaster-template-wrapper

get_footer(); 

?>