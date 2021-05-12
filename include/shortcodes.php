<?php
	/*	
	*	Tourmaster Custom Shortcodes
	*	---------------------------------------------------------------------
	*/

	// quick search
	// [tourmaster_quick_search type="with-border" ]
	add_shortcode('tourmaster_quick_search', 'tourmaster_quick_search');
	if( !function_exists('tourmaster_quick_search') ){
		function tourmaster_quick_search( $atts, $content = null ){
			$action_url = tourmaster_get_template_url('search');

			ob_start();
?>
<form class="tourmaster-quick-search-shortcode tourmaster-form-field <?php
	if( !empty($atts['type']) && $atts['type'] == 'with-border' ){
		echo 'tourmaster-with-border ';
	}
?>" action="<?php echo esc_attr($action_url); ?>" type="GET" >
	<input name="tour-search" type="text" value="" placeholder="<?php echo esc_html__('Quick Search', 'tourmaster'); ?>" />
	<input type="submit" value="<?php echo esc_html__('Search', 'tourmaster'); ?>" />					
</form> 
<?php
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;
		}
	}