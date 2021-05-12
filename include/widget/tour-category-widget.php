<?php
	/**
	 * A widget that show recent posts ( Specified by category ).
	 */

	add_action('widgets_init', 'tourmaster_widget_tour_category');
	if( !function_exists('tourmaster_widget_tour_category') ){
		function tourmaster_widget_tour_category() {
			register_widget( 'Tourmaster_Widget_Tour_Category' );
		}
	}

	if( !class_exists('Tourmaster_Widget_Tour_Category') ){
		class Tourmaster_Widget_Tour_Category extends WP_Widget{

			// Initialize the widget
			function __construct() {

				parent::__construct(
					'tourmaster-widget-tour-category', 
					esc_html__('Tour Category Widget ( Goodlayers )', 'tourmaster'), 
					array('description' => esc_html__('A widget that show list of tour categories', 'tourmaster'))
				);  

			}

			// Output of the widget
			function widget( $args, $instance ) {
	
				$title = empty($instance['title'])? '': apply_filters('widget_title', $instance['title']);
				$num_fetch = empty($instance['num-fetch'])? '': $instance['num-fetch'];
				$style = empty($instance['style'])? 'widget': $instance['style'];
				$thumbnail_size = empty($instance['thumbnail-size'])? '': $instance['thumbnail-size'];
				$column_size = empty($instance['column-size'])? '': $instance['column-size'];
				$taxonomy = empty($instance['taxonomy'])? 'tour_category': $instance['taxonomy'];
					
				// Opening of widget
				echo $args['before_widget'];
				
				// Open of title tag
				if( !empty($title) ){ 
					echo $args['before_title'] . $title . $args['after_title']; 
				}

				// query
				$query_args = array(
					'taxonomy' => $taxonomy,
					'orderby' => 'name',
					'order' => 'asc',
					'number' => $num_fetch,
					'hide_empty' => false
				);

				$categories = get_terms($query_args);
	
				if( !empty($categories) && !is_wp_error($categories) ){
					echo '<div class="tourmaster-widget-tour-category">';
					if( $style == 'widget' ){
						echo tourmaster_pb_element_tour_category::get_category_widget($categories, array(
							'column-size' => $column_size,
							'thumbnail-size' => $thumbnail_size
						), $taxonomy);
					}else if( $style == 'list' ){
						echo '<ul class="tourmaster-widget-tour-category-list" >';
						foreach( $categories as $category ){
							echo '<li>';
							echo '<a class="tourmaster-tour-category-head-link" href="' . esc_url(get_term_link($category->term_id, $taxonomy)) . '" >';
							echo $category->name;
							echo '</a>';
							echo '</li>';
						}
						echo '</ul>';
					}
					echo '</div>'; // tourmaster-widget-tour-category
				}
						
				// Closing of widget
				echo $args['after_widget'];

			}

			// Widget Form
			function form( $instance ) {

				if( class_exists('tourmaster_widget_util') ){
					tourmaster_widget_util::get_option(array(
						'title' => array(
							'type' => 'text',
							'id' => $this->get_field_id('title'),
							'name' => $this->get_field_name('title'),
							'title' => esc_html__('Title', 'tourmaster'),
							'value' => (isset($instance['title'])? $instance['title']: '')
						),
						'taxonomy' => array(
							'type' => 'combobox',
							'options' => array(
								'tour_category' => esc_html__('Tour Category', 'tourmaster'),
								'tour_tag' => esc_html__('Tour Tag', 'tourmaster'),
							) + tourmaster_get_custom_tax_list(),
							'id' => $this->get_field_id('taxonomy'),
							'name' => $this->get_field_name('taxonomy'),
							'title' => esc_html__('Taxonomy', 'tourmaster'),
							'value' => (isset($instance['taxonomy'])? $instance['taxonomy']: '')
						),
						'num-fetch' => array(
							'type' => 'text',
							'id' => $this->get_field_id('num-fetch'),
							'name' => $this->get_field_name('num-fetch'),
							'title' => esc_html__('Display Number', 'tourmaster'), 
							'value' => (isset($instance['num-fetch'])? $instance['num-fetch']: '3')
						),
						'column-size' => array(
							'type' => 'combobox',
							'options' => array( 60=>1, 30=>2, 20=>3, 15=>4, 12=>5 ),
							'default' => 20,
							'id' => $this->get_field_id('column-size'),
							'name' => $this->get_field_name('column-size'),
							'title' => esc_html__('Column Size', 'tourmaster'), 
							'value' => (isset($instance['column-size'])? $instance['column-size']: 'thumbnail')
						),
						'thumbnail-size' => array(
							'type' => 'combobox',
							'options' => 'thumbnail-size',
							'id' => $this->get_field_id('thumbnail-size'),
							'name' => $this->get_field_name('thumbnail-size'),
							'title' => esc_html__('Thumbnail Size', 'tourmaster'), 
							'value' => (isset($instance['thumbnail-size'])? $instance['thumbnail-size']: 'thumbnail')
						),
						'style' => array(
							'type' => 'combobox',
							'options' => array(
								'widget' => esc_html__('Widget', 'tourmaster'),
								'list' => esc_html__('List', 'tourmaster'),
							),
							'id' => $this->get_field_id('style'),
							'name' => $this->get_field_name('style'),
							'title' => esc_html__('Style', 'tourmaster'), 
							'value' => (isset($instance['style'])? $instance['style']: 'widget')
						),
					));
				}

			}
			
			// Update the widget
			function update( $new_instance, $old_instance ) {

				if( class_exists('tourmaster_widget_util') ){
					return tourmaster_widget_util::get_option_update($new_instance);
				}

				return $new_instance;
			}	
		} // class
	} // class_exists
?>