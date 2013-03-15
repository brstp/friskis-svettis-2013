<?php
//Adding custom widgets
add_action( 'widgets_init', 'customWidgets' ); 

function customWidgets() {
	register_widget( 'widgetFacebookGroup' );
}

class WidgetFacebookGroup extends WP_Widget {
	
	function widgetFacebookGroup() {
		$widget_ops = array( 'classname' => 'facebookGroup', 'description' => __('Displays a Facebook group ', 'facebookGroup') );
		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'facebookGroup-widget' );
		$this->WP_Widget( 'facebookGroup-widget', __('Facebook', 'facebookGroup'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
			
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['name'];
		$show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo $before_title . $title . $after_title;

		//Display the name 
		if ( $name )
			printf( '<p>' . __('Hey their Sailor! My name is %1$s.', 'facebookGroup') . '</p>', $name );

		if ( $show_info )
			printf( $name );

		echo $after_widget;
	}
} 
?>