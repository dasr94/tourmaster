<?php
	/**
	 * A widget that show recent posts ( Specified by category ).
	 */

	add_action('widgets_init', 'tourmaster_tour_widget');
	if( !function_exists('tourmaster_tour_widget') ){
		function tourmaster_tour_widget() {
			register_widget( 'Tourmaster_Tour_Widget' );
		}
	}

	if( !class_exists('Tourmaster_Tour_Widget') ){
		class Tourmaster_Tour_Widget extends WP_Widget{

			// Initialize the widget
			function __construct() {

				parent::__construct(
					'tourmaster-tour-widget', 
					esc_html__('Tour Widget ( Goodlayers )', 'tourmaster'), 
					array('description' => esc_html__('A widget that show latest tour', 'tourmaster'))
				);  

			}

			// Output of the widget
			function widget( $args, $instance ) {
	
				$title = empty($instance['title'])? '': apply_filters('widget_title', $instance['title']);
				$category = empty($instance['category'])? '': $instance['category'];
				$num_fetch = empty($instance['num-fetch'])? '': $instance['num-fetch'];
					
				// Opening of widget
				echo $args['before_widget'];
				
				// Open of title tag
				if( !empty($title) ){ 
					echo $args['before_title'] . $title . $args['after_title']; 
				}
					
				// Widget Content
				$query_args = array(
					'post_type' => 'tour', 
					'suppress_filters' => false,
					'orderby' => 'post_date',
					'order' => 'desc',
					'paged' => 1,
					'ignore_sticky_posts' => 1,
					'posts_per_page' => $num_fetch,
					'tour_category' => $category,
					'post__not_in' => array(get_the_ID())
				);
				$query = new WP_Query( $query_args );
				
				
				if($query->have_posts()){
					$tour_style = new tourmaster_tour_style();

					tourmaster_setup_admin_postdata();
					echo '<div class="tourmaster-recent-tour-widget tourmaster-tour-item">';
					while($query->have_posts()){ $query->the_post();
						echo $tour_style->tour_widget(array(
							'thumbnail-size' => 'thumbnail',
						));
					}
					echo '</div>'; // tourmaster-recent-tour-widget
					wp_reset_postdata();
					tourmaster_reset_admin_postdata();
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
						'category' => array(
							'type' => 'combobox',
							'id' => $this->get_field_id('category'),
							'name' => $this->get_field_name('category'),
							'title' => esc_html__('Category', 'tourmaster'),
							'options' => tourmaster_get_term_list('tour_category', '', true),
							'value' => (isset($instance['category'])? $instance['category']: '')
						),
						'num-fetch' => array(
							'type' => 'text',
							'id' => $this->get_field_id('num-fetch'),
							'name' => $this->get_field_name('num-fetch'),
							'title' => esc_html__('Display Number', 'tourmaster'), 
							'value' => (isset($instance['num-fetch'])? $instance['num-fetch']: '3')
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