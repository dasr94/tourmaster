<?php
get_header();
	
	$shadow_size = tourmaster_get_option('general', 'search-page-tour-frame-shadow-size', '');
	$settings = array(
		'pagination' => 'page',
		'tour-style' => tourmaster_get_option('general', 'search-page-tour-style', 'full'),
		'grid-style' => tourmaster_get_option('general', 'search-page-tour-grid-style', 'style-1'),
		'column-size' => tourmaster_get_option('general', 'search-page-column-size', '20'),
		'thumbnail-size' => tourmaster_get_option('general', 'search-page-thumbnail-size', 'full'),
		'tour-info' => tourmaster_get_option('general', 'search-page-tour-info', array()),
		'excerpt' => tourmaster_get_option('general', 'search-page-excerpt', 'specify-number'),
		'excerpt-number' => tourmaster_get_option('general', 'search-page-excerpt-number', '55'),
		'tour-rating' => tourmaster_get_option('general', 'search-page-tour-rating', 'enable'),
		'custom-pagination' => true,
		'frame-shadow-size' => empty($shadow_size)? '': array('x' => 0, 'y' => 0, 'size' => $shadow_size),
		'frame-shadow-color' => tourmaster_get_option('general', 'search-page-tour-frame-shadow-color', ''),
		'frame-shadow-opacity' => tourmaster_get_option('general', 'search-page-tour-frame-shadow-opacity', ''),
	);
	$settings['paged'] = (get_query_var('paged'))? get_query_var('paged') : get_query_var('page');
	$settings['paged'] = empty($settings['paged'])? 1: $settings['paged'];

	if( $settings['grid-style'] == 'style-2' ){
		$settings['tour-border-radius'] = '3px';
	} 

	// archive query
	global $wp_query;
	$settings['query'] = $wp_query;

	// start the content
	echo '<div class="tourmaster-template-wrapper" >';
	echo '<div class="tourmaster-container" >';
	
	// sidebar content
	$sidebar_type = tourmaster_get_option('general', 'search-sidebar', 'none');
	echo '<div class="' . tourmaster_get_sidebar_wrap_class($sidebar_type) . '" >';
	echo '<div class="' . tourmaster_get_sidebar_class(array('sidebar-type'=>$sidebar_type, 'section'=>'center')) . '" >';
	echo '<div class="tourmaster-page-content" >';
	
	$term_description = term_description();
	$archive_description = tourmaster_get_option('general', 'archive-description', 'enable');
	if( $archive_description == 'enable' && !empty($term_description) ){
		echo '<div class="tourmaster-taxonomy-description tourmaster-item-pdlr" >' . tourmaster_text_filter($term_description) . '</div>';
	}

	echo tourmaster_pb_element_tour::get_content($settings);

	echo '</div>'; // tourmaster-page-content
	echo '</div>'; // tourmaster-get-sidebar-class

	// sidebar left
	if( $sidebar_type == 'left' || $sidebar_type == 'both' ){
		$sidebar_left = tourmaster_get_option('general', 'search-sidebar-left');
		echo tourmaster_get_sidebar($sidebar_type, 'left', $sidebar_left);
	}

	// sidebar right
	if( $sidebar_type == 'right' || $sidebar_type == 'both' ){
		$sidebar_right = tourmaster_get_option('general', 'search-sidebar-right');
		echo tourmaster_get_sidebar($sidebar_type, 'right', $sidebar_right);
	}

	echo '</div>'; // tourmaster-get-sidebar-wrap-class	

	echo '</div>'; // tourmaster-container
	echo '</div>'; // tourmaster-template-wrapper

get_footer(); 

?>