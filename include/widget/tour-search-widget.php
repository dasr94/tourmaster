<?php

	add_action('widgets_init', 'tourmaster_tour_search_widget');
	if( !function_exists('tourmaster_tour_search_widget') ){
		function tourmaster_tour_search_widget() {
			register_widget( 'Tourmaster_Tour_Search_Widget' );
		}
	}

	if( !class_exists('Tourmaster_Tour_Search_Widget') ){
		class Tourmaster_Tour_Search_Widget extends WP_Widget{

			// Initialize the widget
			function __construct() {

				parent::__construct(
					'tourmaster-tour-search-widget', 
					esc_html__('Tour Search Widget ( Goodlayers )', 'tourmaster'), 
					array('description' => esc_html__('A widget that show tour search box', 'tourmaster'))
				);  

			}

			// Output of the widget
			function widget( $args, $instance ) {
	
				$title = empty($instance['title'])? '': apply_filters('widget_title', $instance['title']);
				$fields = empty($instance['fields'])? array(): explode(',', $instance['fields']);
					
				// Opening of widget
				echo $args['before_widget'];
				
				// Open of title tag
				if( !empty($title) ){ 
					echo $args['before_title'] . $title . $args['after_title']; 
				}
					
				// Widget Content
				echo tourmaster_pb_element_tour_search::get_content(array(
					'fields' => $fields,
					'style' => 'full',
					'no-pdlr' => true,
					'with-frame' => 'disable'
				));
						
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
						'fields' => array(
							'type' => 'multi-combobox',
							'id' => $this->get_field_id('fields'),
							'name' => $this->get_field_name('fields'),
							'title' => esc_html__('Select Fields', 'tourmaster'),
							'options' => array(
								'keywords' => esc_html__('Keywords', 'tourmaster'),
								'location' => esc_html__('Location', 'tourmaster'),
								'duration' => esc_html__('Duration', 'tourmaster'),
								'date' => esc_html__('Date', 'tourmaster'),
								'min-price' => esc_html__('Min Price', 'tourmaster'),
								'max-price' => esc_html__('Max Price', 'tourmaster'),
							),
							'value' => (isset($instance['fields'])? $instance['fields']: '')
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