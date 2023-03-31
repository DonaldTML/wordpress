<?php
/**
 * @package Adlogic_Job_Board
 * @version 1.0.0
 */

	class Adlogic_Consultant_Details_Widget extends WP_Widget {
		function init() {
			if (Adlogic_Job_Board::check_setup() == true) {
				// Register the new widget
				add_action( 'widgets_init', create_function( '', 'return register_widget( "Adlogic_Consultant_Details_Widget" );' ) );
			}
		}

		function __construct() {
			parent::__construct('adlogic_consultant_widget', $name = 'Adlogic Consultant Details', array( 'description' => 'A widget to display the consultant\'s contact details.' ) );
		}

		/** @see WP_Widget::form */
		function form( $instance ) {

				if(isset($instance['show_image'])) {
					$show_image = $instance['show_image'];
				} else {
					$show_image = 0;
				}

				if(isset($instance['show_phone'])) {
					$show_phone = $instance['show_phone'];
				} else {
					$show_phone = 0;
				}

				if(isset($instance['show_email'])) {
					$show_email = $instance['show_email'];
				} else {
					$show_email = 0;
				}
				
				$widget_unique_id = uniqid('ajb_widget');
			?>
			<div class="<?php print $widget_unique_id; ?>">
			
			<div>
				<label><strong>Configuration:</strong></label><br />
				
			<p>
				<label for="<?php print $this->get_field_id('show_image'); ?>"><?php _e('Show Consultant Image?'); ?></label><br />
				<select id="<?php print $this->get_field_id('show_image'); ?>" name="<?php print $this->get_field_name('show_image')?>">
					<option value="yes" <?php print ($show_image == 'yes' ? 'selected' : ''); ?>>Yes</option>
					<option value="no"  <?php print ($show_image == 'no' ? 'selected' : ''); ?>>No</option>
				</select>
			</p>

			<p>
				<label for="<?php print $this->get_field_id('show_phone'); ?>"><?php _e('Show Consultant Phone Number?'); ?></label><br />
				<select id="<?php print $this->get_field_id('show_phone'); ?>" name="<?php print $this->get_field_name('show_phone')?>">
					<option value="yes" <?php print ($show_phone == 'yes' ? 'selected' : ''); ?>>Yes</option>
					<option value="no"  <?php print ($show_phone == 'no' ? 'selected' : ''); ?>>No</option>
				</select>
			</p>
			
			<p>
				<label for="<?php print $this->get_field_id('show_email'); ?>"><?php _e('Show Consultant Email?'); ?></label><br />
				<select id="<?php print $this->get_field_id('show_email'); ?>" name="<?php print $this->get_field_name('show_email')?>">
					<option value="yes" <?php print ($show_email == 'yes' ? 'selected' : ''); ?>>Yes</option>
					<option value="no"  <?php print ($show_email == 'no' ? 'selected' : ''); ?>>No</option>
				</select>
			</p>

			</div>
			
			
			</div>			
			<?php 
		}
	
		/** @see WP_Widget::update */
		function update( $new_instance, $old_instance ) {
			// Set new values
			$instance							= $old_instance;
			$instance['show_image']				= $new_instance['show_image'];
			$instance['show_phone']				= $new_instance['show_phone'];
			$instance['show_email']				= $new_instance['show_email'];


	
			$instance = array_merge($instance, $new_instance);
			return $instance;
		}
	
		function widget( $args, $instance ) {
			global $wp_rewrite;
			
			if($instance) {
						
				if(isset($instance['show_image'])) {
					$show_image = $instance['show_image'];
				} else {
					$show_image = 0;
				}

				if(isset($instance['show_phone'])) {
					$show_phone = $instance['show_phone'];
				} else {
					$show_phone = 0;
				}

				if(isset($instance['show_email'])) {
					$show_email = $instance['show_email'];
				} else {
					$show_email = 0;
				}
			}

			// Get the details for the ad, we can grab the consultant information aswell.
			Adlogic_Job_Details_Shortcodes::get_ad_details();

			$oJobPosting = Adlogic_Job_Details_Shortcodes::$jobDetails;
				
			$consultantPhone = $oJobPosting->Enquiry->Phone;
			$consultantEmail = $oJobPosting->Enquiry->ConsultantEmail;
			$consultantName	= $oJobPosting->Enquiry->Name;
			$consultantCompany = $oJobPosting->PlacedBy->Office;


			?>
				<div class="consultant-profile">
					<?php 

					if($show_image == 'yes') {

						if($oJobPosting->Enquiry->Image == '') {
							// This switch statement is a placeholder.

							switch($consultantName) {
								case 'Madison Lee':
									?>
									<div class="consultant-image"></div>
									<?php
								break;

								case '3564 Candidate Registration':
									?>
									<div class="consultant-image" style="background: url(http://static.freepik.com/free-photo/business--person--men--male_3213563.jpg);"></div>
									<?php
								break;
								default:
							
							?>
								<div class="consultant-image"></div>

							<?php 
							}
						} else {
							?>
								<div class="consultant-image" style="background: url(<?php print $oJobPosting->Enquiry->Image; ?>);"></div>
							<?php
						}
					} 
					?>
					<h2 class="consultant-name">
						<span class="contact-consultant">Contact</span>
						<?php print $consultantName; ?>
					</h2>
					<div class="connect-with-consultant">
						<ul class="contact-methods">
							<?php
							if($show_email == 'yes') { ?>

								<li class="consultant-email">
									<a href="mailto:<?php print $consultantEmail; ?>?subject=Ad: <?php print str_replace('&', 'and', $oJobPosting->JobTitle); ?> at <?php print get_bloginfo('url'); ?>
									&body=Hi <?php print $consultantName; ?>,"><?php print $consultantEmail; ?></a>
								</li>
							<?php
							}

							if($show_phone == 'yes') { ?>
								<li class="consultant-phone">
									<a href="tel:<?php print $consultantPhone; ?>"><?php print $consultantPhone; ?></a>
								</li>
							<?php
							}
							/* Add later for social stuff.
							<li class="consultant-linkedin">
								<a href="<?php print $consultantLinkedIn; ?>"><?php print 'Find me on LinkedIn!'; ?></a>
							</li>
							*/
							?>
						</ul>

					</div>
				</div>
				<?php 

			
		}
	}
	
	

?>