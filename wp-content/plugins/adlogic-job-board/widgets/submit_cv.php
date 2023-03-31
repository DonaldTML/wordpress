<?php
class Adlogic_Submit_CV_Widget extends WP_Widget {
	function init() {
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		if ($Adlogic_Job_Board->check_setup() == true) {
			// Register the new widget
			add_action( 'widgets_init', create_function( '', 'return register_widget( "Adlogic_Submit_CV_Widget" );' ) );
		}
	}

	function __construct() {
		parent::__construct('adlogic_submit_cv_widget', $name = 'Adlogic Submit CV', array( 'description' => 'A widget to allow potential candidates to Submit their CV.' ));
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
			$title = "";
			$content = "";
			$show_terms = false;
			$terms_page_id = 0;
			$submit_cv_url = "";
			if ( $instance ) {
				$title = esc_attr( $instance[ 'title' ] );

				if (isset($instance['content'])) {
					$content = $instance['content'];
				}

				if (isset($instance['show_terms'])) {
					$show_terms = $instance['show_terms'];
				}

				if (isset($instance['terms_page_id'])) {
					$terms_page_id = $instance['terms_page_id'];
				}

				if (isset($instance['submit_cv_url'])) {
					$submit_cv_url = $instance['submit_cv_url'];
				}
			}
			else {
				$title = __( 'Submit Your CV', 'text_domain' );
			}
		?>
		<p>
			<label for="<?php print $this->get_field_id('title'); ?>"><strong><?php _e('Title:'); ?></strong></label> 
			<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print $title; ?>" />
		</p>
		<p>
			<label><strong><?php _e('Widget Content:'); ?></strong></label>
			<textarea name="<?php print $this->get_field_name('content'); ?>" id="<?php print $this->get_field_id('title'); ?>" cols="20" rows="5" class="widefat"><?php print $content; ?></textarea>
		</p>
		<p>
			<label><strong><?php _e('Show Terms and Conditions:'); ?></strong></label>
		</p>
		<p>
			<input class="checkbox" id="<?php print $this->get_field_id('show_terms'); ?>" name="<?php print $this->get_field_name('show_terms'); ?>" type="checkbox" <?php print ( $show_terms == true ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('show_terms'); ?>"> <?php _e('Enable'); ?></label><br />
		</p>
		<p>
			<label><strong><?php _e('Terms and Conditions Page:'); ?></strong></label> 
		</p>
		<p>
			<?php wp_dropdown_pages(array('selected' => $terms_page_id, 'name' => $this->get_field_name('terms_page_id'), 'show_option_none' => '- Please Select -')); ?>
		</p>
		<p>
			<label for="<?php print $this->get_field_id('submit_cv_url'); ?>"><strong><?php _e('Submit CV URL:'); ?></strong></label> 
			<input class="widefat" id="<?php print $this->get_field_id('submit_cv_url'); ?>" name="<?php print $this->get_field_name('submit_cv_url'); ?>" type="text" value="<?php print $submit_cv_url; ?>" />
		</p>
		<?php 
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ($new_instance['show_terms'] == 'on') {
			$instance['show_terms'] = true;
			$new_instance['show_terms'] = true;
		} else {
			$instance['show_terms'] = false;
			$new_instance['show_terms'] = false;
		}
		$instance = array_merge($instance, $new_instance);
		return $instance;
	}

	function widget( $args, $instance ) {

		// Enqueue Stylesheet
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_style( 'adlogic-submit-cv-widget' );

		// outputs the content of the widget
		if ( $instance ) {
			if (!empty($args['before_title']) && !empty($args['after_title'])) {
				$title = $args['before_title'] . esc_attr( $instance[ 'title' ] ) . $args['after_title'];
			} else {
				$title = '<h4>' . esc_attr( $instance[ 'title' ] ) . '</h4>';
			}

			if (isset($instance['content'])) {
				$content = $instance['content'];
			} else {
				$content = '';
			}

			if (isset($instance['show_terms'])) {
				$show_terms = $instance['show_terms'];
			} else {
				$show_terms = false;
			}

			if (isset($instance['terms_page_id'])) {
				$terms_page_id = $instance['terms_page_id'];
			} else {
				$terms_page_id = '';
			}

			if (isset($instance['submit_cv_url'])) {
				$submit_cv_url = $instance['submit_cv_url'];
			} else {
				$submit_cv_url = 'about:blank';
			}

			if (isset($instance['submit_cv_button'])) {
				$submit_cv_button = $instance['submit_cv_button'];
			} else {
				$submit_cv_button = 'Submit CV';
			}

			$uniqueId = uniqid('submit_cv_dialog_');
			
			if (!isset($args['widget_id']) || empty($args['widget_id'])) {
				if (isset($args['id']) && !empty($args['id'])) {
					$args['widget_id'] = $args['id'];
				} else {
					 throw new InvalidArgumentException('No ID for hot jobs');
				}
			}
		?>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					submit_cv_dialog = $('#<?php print $args['widget_id']; ?> .<?php print $uniqueId; ?>').dialog({
						autoOpen: false,
						width: 800
					});
					
					$('#<?php print $args['widget_id']; ?> .ajb-submit-cv-button').click(function() {
						if ($('.<?php print $uniqueId; ?> .ajb-submit-cv-terms').length) {
							$('.<?php print $uniqueId; ?> iframe.ajb-submit-cv-iframe').hide();
							$('.<?php print $uniqueId; ?> .ajb-submit-cv-terms').show();
							$('.<?php print $uniqueId; ?> iframe.ajb-submit-cv-iframe').attr('src', 'about:blank');
							submit_cv_dialog.dialog(
								'option', 'buttons', {
									'Accept': function() {
										$('.<?php print $uniqueId; ?> .ajb-submit-cv-terms').hide();
										$('.<?php print $uniqueId; ?> iframe.ajb-submit-cv-iframe').fadeIn('slow');
										submit_cv_dialog.dialog('option', 'buttons', {'Close':function() { $(this).dialog('close'); }});
									}, 
									'Decline': function() { 
										$(this).dialog('close');
									} 
								}
							);
						} else {
							submit_cv_dialog.dialog('option', 'buttons', {'Close':function() { $(this).dialog('close'); }});
							$('.<?php print $uniqueId; ?> iframe.ajb-submit-cv-iframe').show();
						}
						$('.<?php print $uniqueId; ?> iframe.ajb-submit-cv-iframe').attr('src', '<?php print $submit_cv_url?>');
						submit_cv_dialog.dialog('open');
					});
				});
			})(jQuery);
		</script>
		<?php if( !empty($args['before_widget']) ) { print $args['before_widget']; }?>
		<div id="<?php print $args['widget_id']; ?>" class="ajb-submit-cv-widget">
			<?php print $title;?>
			<p><?php print $content; ?></p>
			<div class="ajb-submit-cv-buttons">
				<input type="button" class="ajb-submit-cv-button" value="<?php print $submit_cv_button; ?>">
			</div>
			<div class="submit_cv_dialog <?php print $uniqueId; ?>" style="display: none;" title="<?php print esc_attr( $instance[ 'title' ] ); ?>">
				<?php if ((!empty($terms_page_id)) && ($show_terms == true)) : ?>
				<div class="ajb-submit-cv-terms">
					<?php
						query_posts(
							array( 
								'page_id' => $terms_page_id, 
							)
						);
						while (have_posts()) : the_post();
					?>
					<h4 class="ajb-submit-cv-terms-title"><?php the_title(); ?></h4>
					<?php the_content(); ?>
					<?php endwhile; wp_reset_query(); ?>
				</div>
				<?php endif; ?>
				<iframe src="<?php print $submit_cv_url?>" class="ajb-submit-cv-iframe">
				</iframe>
			</div>
		</div>
		<?php
			if( !empty($args['after_widget']) ) {
				print $args['after_widget'];
			}
		}
	}
}
?>