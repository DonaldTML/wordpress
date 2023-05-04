<?php

class Sidebar_Dialog_Widget extends WP_Widget {
	function init() {
		add_action( 'widgets_init', create_function( '', 'return register_widget( "Sidebar_Dialog_Widget" );' ) );
		add_action('wp_enqueue_scripts', array('Sidebar_Dialog_Widget', 'register_scripts'));
	}

	function __construct() {
		parent::__construct('sidebar_dialog_widget', $name = 'Sidebar Dialog Widget', array( 'description' => 'A widget that creates a dialog popup for another sidebar.' ) );
	}

	function register_scripts() {
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js' );
		wp_register_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		} else {
			$title = __( 'default', 'text_domain' );
		}

		if ( $instance['html_class'] ) {
			$html_class = esc_attr( $instance[ 'html_class' ] );
		} else {
			$html_class = __( '', 'text_domain' );
		}
		
		if ( $instance['company_name'] ) {
			$company_name = esc_attr( $instance[ 'company_name' ] );
		} else {
			$company_name = __( '', 'text_domain' );
		}

		if (isset($instance['content'])) {
			$content = $instance['content'];
		}

		if (isset($instance['button_text'])) {
			$button_text = $instance['button_text'];
		} else {
			$button_text = 'Submit';
		}

		if (isset($instance['sidebar_id'])) {
			$sidebar_id = $instance['sidebar_id'];
		} else {
			$sidebar_id = 0;
		}

		$sidebar_select_html = '<select id="' . $this->get_field_name('sidebar_id') . '" name="' . $this->get_field_name('sidebar_id') . '">';
		$sidebar_select_html .= '<option value="">- Please Select -</option>';

		foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
			if ($sidebar['id'] != 'wp_inactive_widgets') {
				$sidebar_select_html .= '<option value="' . $sidebar['id'] . '"' . ($sidebar['id'] == $sidebar_id ? ' selected="selected"' : '') . '>' . $sidebar['name'] . '</option>';
			}
		}
		$sidebar_select_html .= '</select>';
		?>
		
		<p>
			<label for="<?php print $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print $title; ?>" />
		</p>
		
		<p>
			<label for="<?php print $this->get_field_id('company_name'); ?>"><?php _e('Company Name:'); ?></label> 
			<input class="widefat" id="<?php print $this->get_field_id('company_name'); ?>" name="<?php print $this->get_field_name('company_name'); ?>" type="text" value="<?php print $company_name; ?>" />
		</p>
		
		<p>
			<label><strong><?php _e('Widget Content:'); ?></strong></label>
			<textarea name="<?php print $this->get_field_name('content'); ?>" id="<?php print $this->get_field_id('title'); ?>" cols="20" rows="5" class="widefat"><?php print $content; ?></textarea>
		</p>
		<p>
			<label><strong><?php _e('Sidebar:'); ?></strong></label>
			<?php print $sidebar_select_html; ?>
		</p>
		<p>
			<label for="<?php print $this->get_field_id('html_class'); ?>"><?php _e('Custom Widget CSS Class: (optional)'); ?></label> 
			<input class="widefat" id="<?php print $this->get_field_id('html_class'); ?>" name="<?php print $this->get_field_name('html_class'); ?>" type="text" value="<?php print $html_class; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('button_text'); ?>"><?php _e('Button Text'); ?></label> 
			<input class="widefat" id="<?php print $this->get_field_id('button_text'); ?>" name="<?php print $this->get_field_name('button_text'); ?>" type="text" value="<?php print $button_text; ?>" />
		</p>
		<?php 
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance							= $old_instance;
		$instance['title']					= strip_tags($new_instance['title']);
		$instance['company_name']			= strip_tags($new_instance['company_name']);
		$instance['content']				= $new_instance['content'];
		$instance['sidebar_id']				= $new_instance['sidebar_id'];
		$instance['html_class']				= sanitize_html_class($new_instance['html_class']);
		$instance							= array_merge($instance, $new_instance);
		return $instance;
	}

	function widget( $args, $instance ) {
		if ( $instance ) {
			if (!empty($args['before_title']) && !empty($args['after_title'])) {
				$title = $args['before_title'] . esc_attr( $instance[ 'title' ] ) . $args['after_title'];
			} else {
				$title = esc_attr( $instance[ 'title' ] );
			}

			if ( $instance['html_class'] ) {
				$html_class = esc_attr( $instance[ 'html_class' ] );
			} else {
				$html_class = __( '', 'text_domain' );
			}
			
			if ( $instance['company_name'] ) {
				$company_name = esc_attr( $instance[ 'company_name' ] );
			} else {
				$company_name = __( '', 'text_domain' );
			}

			if (isset($instance['content'])) {
				$content = $instance['content'];
			} else {
				$content = '';
			}

			if (isset($instance['button_text'])) {
				$button_text = $instance['button_text'];
			} else {
				$button_text = 'Submit';
			}

			if (isset($instance['sidebar_id'])) {
				$sidebar_id = $instance['sidebar_id'];
			} else {
				$sidebar_id = 0;
			}
			
			$uniqueId = uniqid('sidebar_dialog_');
		?>
			<?php 
			if( !empty($args['before_widget']) ) { 
				print (!empty($html_class) ? str_replace('class="', 'class="' . $html_class . ' ', $args['before_widget']): $args['before_widget']); 
			}
			?>
			<?php print $title; ?>
			<p><?php print $content; ?></p>
			<div class="sidebar_dialog <?php print $uniqueId; ?>" title="<?php print esc_attr( $instance[ 'title' ] ); ?>">
			
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-body">
					<div class="jobalerts-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h1 style="color: #60a1e1; font-size: 45px; font-weight: 300;padding-top: 35px;"><?php print $company_name; ?></h1>
						<h1>Job Alerts</h1>
					</div>
					<div class="jobalerts-body">
						<p>Keep up to date with latest specialist roles and vacancies in your profession.<p>
						<p>Choose your preference below and you will be notified by email when a new role is advertised online.</p>
						<?php dynamic_sidebar('jobalerts-widget'); ?>
					</div>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <button type="button" id="modalSubscribe" class="btn btn-primary">Subscribe</button>
			      </div>
			    </div>
			  </div>
			</div>
			
			</div>
			<div class="button" style="display: none;">
				<a id="openJobAlerts" data-toggle="modal" data-target="#myModal"><?php print $button_text; ?></a>
			</div>
			<script type="text/javascript">
				jQuery("#modalSubscribe").click(function() {
					jQuery(".ajb-subscribe-job-alerts").trigger('click');
				});
				jQuery(".openJobAlerts").click(function() {
					$("#openJobAlerts").trigger('click');
				});
			</script>
			
		<?php
			if( !empty($args['after_widget']) ) {
				print $args['after_widget'];
			}
		}
	}
}

Sidebar_Dialog_Widget::init();
?>