<?php
class Adlogic_Alerts_Widget extends WP_Widget {
	function init() {
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		if ($Adlogic_Job_Board->check_setup() == true) {
			add_action( 'widgets_init', create_function( '', 'return register_widget( "Adlogic_Alerts_Widget" );' ) );
		}
	}

	function __construct() {
		parent::__construct('adlogic_alerts_widget', $name = 'Adlogic Job Alerts', array( 'description' => 'A widget to display the Adlogic Job Alerts form on your sidebar.' ) );
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$title = __( 'Job Alerts', 'text_domain' );
		$subscribe_text = 'Subscribe';
		$classifications_text = 'Classification';
		$sub_classifications_text = 'Sub-classification';
		$locations_text = 'Location';
		$sub_locations_text = 'Sub-Location';
		$work_type_text = 'Work Type';
		$keyword_text = 'Keywords';
		$first_name_text = 'First Name';
		$surname_text = 'Surname';
		$email_address_text = 'Email';
		$phone_number_text = 'Phone';
		$alert_frequency_text = 'Alert Frequency';
		$default_salary_type = 'annual';
		$dropdown_type['classification'] = 'single';
		$dropdown_type['location'] = 'single';
		$dropdown_type['worktype'] = 'single';
		$hide_empty['classification'] = true;
		$hide_empty['location'] = true;
		$hide_empty['work_type']	= true;
		$disabled_fields = array();			
		if ( $instance ) {
				$title = esc_attr( $instance[ 'title' ] );
				$disabled_fields = $instance[ 'hidden_fields' ];
				if (isset($instance[ 'subscribe_text' ])) {
					$subscribe_text = $instance[ 'subscribe_text' ];
				} else {
					$subscribe_text = 'Subscribe';
				}
				if (isset($instance[ 'classifications_text' ])) {
					$classifications_text = $instance[ 'classifications_text' ];
				} else {
					$classifications_text = 'Classification';
				}
				if (isset($instance[ 'sub_classifications_text' ])) {
					$sub_classifications_text = $instance[ 'sub_classifications_text' ];
				} else {
					$sub_classifications_text = 'Sub-classification';
				}
				if (isset($instance[ 'locations_text' ])) {
					$locations_text = $instance[ 'locations_text' ];
				} else {
					$locations_text = 'Location';
				}
				if (isset($instance[ 'sub_locations_text' ])) {
					$sub_locations_text = $instance[ 'sub_locations_text' ];
				} else {
					$sub_locations_text = 'Sub-location';
				}
				if (isset($instance[ 'work_type_text' ])) {
					$work_type_text = $instance[ 'work_type_text' ];
				} else {
					$work_type_text = 'Work Type';
				}
				if (isset($instance[ 'keyword_text' ])) {
					$keyword_text = $instance[ 'keyword_text' ];
				} else {
					$keyword_text = 'Keywords';
				}
				if (isset($instance[ 'first_name_text' ])) {
					$first_name_text = $instance[ 'first_name_text' ];
				} else {
					$first_name_text = 'First Name';
				}
				if (isset($instance[ 'surname_text' ])) {
					$surname_text = $instance[ 'surname_text' ];
				} else {
					$surname_text = 'Surname';
				}
				if (isset($instance[ 'email_address_text' ])) {
					$email_address_text = $instance[ 'email_address_text' ];
				} else {
					$email_address_text = 'Email';
				}
				if (isset($instance[ 'phone_number_text' ])) {
					$phone_number_text = $instance[ 'phone_number_text' ];
				} else {
					$phone_number_text = 'Phone';
				}
				if (isset($instance[ 'alert_frequency_text' ])) {
					$alert_frequency_text = $instance[ 'alert_frequency_text' ];
				} else {
					$alert_frequency_text = 'Alert Frequency';
				}

				if (isset($instance['dropdown_type'])) {
					$dropdown_type = $instance['dropdown_type'];
				} else {
					$dropdown_type['classification'] = 'single';
					$dropdown_type['location'] = 'single';
					$dropdown_type['worktype'] = 'single';
				}

				if (isset($instance['top_level_only'])) {
					$top_level_only = $instance['top_level_only'];
				}
				$hide_empty	= $instance[ 'hide_empty' ];
			}

			$widget_unique_id = uniqid('ajb_widget');
		?>
		<p>
			<label for="<?php print $this->get_field_id('title'); ?>"><strong><?php _e('Title:'); ?></strong></label> 
			<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print $title; ?>" />
		</p>
		<p>
			<label><strong><?php _e('Job Alert Field Labels:'); ?></strong></label> 
		</p>
		<p>
			<label for="<?php print $this->get_field_id('first_name_text'); ?>"><?php _e('First Name:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('first_name_text'); ?>" name="<?php print $this->get_field_name('first_name_text'); ?>" type="text" value="<?php print $first_name_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('surname_text'); ?>"><?php _e('Surname:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('surname_text'); ?>" name="<?php print $this->get_field_name('surname_text'); ?>" type="text" value="<?php print $surname_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('email_address_text'); ?>"><?php _e('Email Address:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('email_address_text'); ?>" name="<?php print $this->get_field_name('email_address_text'); ?>" type="text" value="<?php print $email_address_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('phone_number_text'); ?>"><?php _e('Telephone Number:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('phone_number_text'); ?>" name="<?php print $this->get_field_name('phone_number_text'); ?>" type="text" value="<?php print $phone_number_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('classifications_text'); ?>"><?php _e('Classifications:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('classifications_text'); ?>" name="<?php print $this->get_field_name('classifications_text'); ?>" type="text" value="<?php print $classifications_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('sub_classifications_text'); ?>"><?php _e('Sub-classifications:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('sub_classifications_text'); ?>" name="<?php print $this->get_field_name('sub_classifications_text'); ?>" type="text" value="<?php print $sub_classifications_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('locations_text'); ?>"><?php _e('Locations:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('locations_text'); ?>" name="<?php print $this->get_field_name('locations_text'); ?>" type="text" value="<?php print $locations_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('locations_text'); ?>"><?php _e('Sub-Locations:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('sub_locations_text'); ?>" name="<?php print $this->get_field_name('sub_locations_text'); ?>" type="text" value="<?php print $sub_locations_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('work_type_text'); ?>"><?php _e('Work Types:'); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php print $this->get_field_id('work_type_text'); ?>" name="<?php print $this->get_field_name('work_type_text'); ?>" type="text" value="<?php print $work_type_text; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('alert_frequency_text'); ?>"><?php _e('Alert Frequency:'); ?></label>
		</p>
			<p>
			<input class="widefat" id="<?php print $this->get_field_id('alert_frequency_text'); ?>" name="<?php print $this->get_field_name('alert_frequency_text'); ?>" type="text" value="<?php print $alert_frequency_text; ?>" />
		</p>
		
		<p>
			<label><strong><?php _e('Disabled Search Fields: <br/>(<em>default values are in brackets</em>)'); ?></strong></label>
		</p>
		<p>
			<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_surname" name="<?php print $this->get_field_name('hidden_fields'); ?>[surname]" type="checkbox" <?php print ( isset($disabled_fields['surname']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_surname"> <?php _e('Hide Surname (<em>blank</em>)'); ?></label><br />
			<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_phone" name="<?php print $this->get_field_name('hidden_fields'); ?>[phone]" type="checkbox" <?php print ( isset($disabled_fields['phone']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_phone"> <?php _e('Hide Phone (<em>blank</em>)'); ?></label><br />			
			<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_classification" name="<?php print $this->get_field_name('hidden_fields'); ?>[classification]" type="checkbox" <?php print ( isset($disabled_fields['classification']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_classification"> <?php _e('Hide Classifications (<em>all</em>)'); ?></label><br />
			<?php if (!Adlogic_Job_Board::shouldUseNewAPI()) { ?>
			<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_location" name="<?php print $this->get_field_name('hidden_fields'); ?>[location]" type="checkbox" <?php print ( isset($disabled_fields['location']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_location"> <?php _e('Hide Locations (<em>all</em>)'); ?></label><br /> 
			<?php } else { ?> <input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_location" name="<?php print $this->get_field_name('hidden_fields'); ?>[location]" type="checkbox" checked="checked"  /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_location"> <?php _e('Hide Locations (<em>all</em>)'); ?></label><br /> 
			<?php } ?>
			<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_work_type" name="<?php print $this->get_field_name('hidden_fields'); ?>[work_type]" type="checkbox" <?php print ( isset($disabled_fields['work_type']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_work_type"> <?php _e('Hide Work Types (<em>all</em>)'); ?></label><br />
			<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_alert_frequency" name="<?php print $this->get_field_name('hidden_fields'); ?>[alert_frequency]" type="checkbox" <?php print ( isset($disabled_fields['alert_frequency']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_alert_frequency"> <?php _e('Hide Alert Frequency (<em>daily</em>)'); ?></label><br />
		</p>
		<p>
			<label><strong><?php _e('Dropdown Types:'); ?></strong></label><br/>
			<em>Classification:</em><br/>
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_classification_single" name="<?php print $this->get_field_name('dropdown_type'); ?>[classification]" type="radio" value="single" <?php print ( ($dropdown_type['classification'] == 'single') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_classification_single"> <?php _e('Single Dropdown'); ?></label><br />
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_classification_double" name="<?php print $this->get_field_name('dropdown_type'); ?>[classification]" type="radio" value="double" <?php print ( ($dropdown_type['classification'] == 'double') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_classification_double"> <?php _e('Double Dropdown'); ?></label><br/>
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_classification_multiple" name="<?php print $this->get_field_name('dropdown_type'); ?>[classification]" type="radio" value="multiple" <?php print ( ($dropdown_type['classification'] == 'multiple') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_classification_multiple"> <?php _e('Multiple Choice'); ?></label><br/>
			<em>Location:</em><br/>
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_location_single" name="<?php print $this->get_field_name('dropdown_type'); ?>[location]" type="radio" value="single" <?php print ( ($dropdown_type['location'] == 'single') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_location_single"> <?php _e('Single Dropdown'); ?></label><br />
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_location_double" name="<?php print $this->get_field_name('dropdown_type'); ?>[location]" type="radio" value="double" <?php print ( ($dropdown_type['location'] == 'double') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_location_double"> <?php _e('Double Dropdown'); ?></label><br/>
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_location_multiple" name="<?php print $this->get_field_name('dropdown_type'); ?>[location]" type="radio" value="multiple" <?php print ( ($dropdown_type['location'] == 'multiple') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_location_multiple"> <?php _e('Multiple Choice'); ?></label><br/>
			<em>Work Type:</em><br/>
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_single" name="<?php print $this->get_field_name('dropdown_type'); ?>[worktype]" type="radio" value="single" <?php print ( ($dropdown_type['worktype'] == 'single') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_single"> <?php _e('Single Dropdown'); ?></label><br />
			<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_multiple" name="<?php print $this->get_field_name('dropdown_type'); ?>[worktype]" type="radio" value="multiple" <?php print ( ($dropdown_type['worktype'] == 'multiple') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_multiple"> <?php _e('Multiple Choice'); ?></label><br/>
		</p>
		<p>
			<label><strong><?php _e('Button Text:'); ?></strong></label>
		</p>
		<p>
			<label for="<?php print $this->get_field_id('subscribe_text'); ?>"><?php _e('Subscribe:'); ?></label> 
			<input class="widefat" id="<?php print $this->get_field_id('subscribe_text'); ?>" name="<?php print $this->get_field_name('subscribe_text'); ?>" type="text" value="<?php print $subscribe_text; ?>" />
		</p>
		<p class="<?php print $widget_unique_id; ?> toggle-arrow">Advanced Settings</p>
			<div class="<?php print $widget_unique_id; ?> advanced-settings" style="display: none">
				<p>
					<label><strong><?php _e('Show Only Top Level:'); ?></strong></label><br />
				</p>
				<p>
					<input class="checkbox" id="<?php print $this->get_field_id('top_level_only'); ?>_classification" name="<?php print $this->get_field_name('top_level_only'); ?>[classification]" type="checkbox" <?php print ( isset($top_level_only['classification']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('top_level_only'); ?>_classification"> <?php _e('Classifications'); ?></label><br />
					<input class="checkbox" id="<?php print $this->get_field_id('top_level_only'); ?>_location" name="<?php print $this->get_field_name('top_level_only'); ?>[location]" type="checkbox" <?php print ( isset($top_level_only['location']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('top_level_only'); ?>_location"> <?php _e('Locations'); ?></label><br />
				</p>
                                <p>
					<label><strong><?php _e('Hide Empty:'); ?></strong></label><br />
				</p>
				<p>
					<input class="checkbox" id="<?php print $this->get_field_id('hide_empty'); ?>_classification" name="<?php print $this->get_field_name('hide_empty'); ?>[classification]" type="checkbox" <?php print ( isset($hide_empty['classification']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hide_empty'); ?>_classification"> <?php _e('Hide Empty Classifications'); ?></label><br />
				</p>
				<p>
				<input class="checkbox" id="<?php print $this->get_field_id('hide_empty'); ?>_location" name="<?php print $this->get_field_name('hide_empty'); ?>[location]" type="checkbox" <?php print ( isset($hide_empty['location']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hide_empty'); ?>_location"> <?php _e('Hide Empty Locations'); ?></label><br />
				</p>
				<p>
				<input class="checkbox" id="<?php print $this->get_field_id('hide_empty'); ?>_work_type" name="<?php print $this->get_field_name('hide_empty'); ?>[work_type]" type="checkbox" <?php print ( isset($hide_empty['work_type']) ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('hide_empty'); ?>_work_type"> <?php _e('Hide Empty Work Types'); ?></label><br />
				</p>
			</div>
			<script type="text/javascript">
				(function($) {
					$(document).ready(function () {
						$('.<?php print $widget_unique_id; ?>.toggle-arrow').click(function() {
							$('.<?php print $widget_unique_id; ?>.advanced-settings').slideToggle(300);
							$(this).toggleClass('toggle-arrow-active');
						});
					});
				})(jQuery); 
			</script>
		<?php 
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']				= strip_tags($new_instance['title']);
		$instance['hidden_fields']		= (isset($new_instance['hidden_fields'])?$new_instance['hidden_fields']:array());
		$instance['top_level_only']		= (isset($new_instance['top_level_only'])?$new_instance['top_level_only']:array());
		$instance['hide_empty']			= (isset($new_instance['hide_empty'])?$new_instance['hide_empty']:array());
		$instance = array_merge($instance, $new_instance);
		return $instance;
	}

	function widget( $args, $instance ) {

		// Enqueue Javascript
		wp_enqueue_script( 'jquery-adlogic-jobAlertsWidget' );

		// Enqueue Stylesheet
		wp_enqueue_style( 'adlogic-job-alerts-widget' );

		// outputs the content of the widget
		if ( $instance ) {
			if (!empty($args['before_title']) && !empty($args['after_title'])) {
				$title = $args['before_title'] . esc_attr( $instance[ 'title' ] ) . $args['after_title'];
			} else {
				$title = '<h4>' . esc_attr( $instance[ 'title' ] ) . '</h4>';
			}

			if (isset($instance[ 'first_name_text' ])) {
				$first_name_text = esc_attr( $instance[ 'first_name_text' ] );
			} else {
				$first_name_text = 'First Name';
			}

			if (isset($instance[ 'surname_text' ])) {
				$surname_text = esc_attr( $instance[ 'surname_text' ] );
			} else {
				$surname_text = 'Surname';
			}

			if (isset($instance[ 'email_address_text' ])) {
				$email_address_text = esc_attr( $instance[ 'email_address_text' ] );
			} else {
				$email_address_text = 'Email Address';
			}

			if (isset($instance[ 'phone_number_text' ])) {
				$phone_number_text = esc_attr($instance[ 'phone_number_text' ]);
			} else {
				$phone_number_text = 'Phone';
			}

			if (isset($instance[ 'view_all_jobs_text' ])) {
				$view_all_jobs_text = esc_attr( $instance[ 'view_all_jobs_text' ] );
			} else {
				$view_all_jobs_text = 'View All Jobs';
			}
			
			if (isset($instance[ 'subscribe_text' ])) {
				$subscribe_text = esc_attr( $instance[ 'subscribe_text' ] );
			} else {
				$subscribe_text = 'Subscribe';
			}

			if (isset($instance[ 'classifications_text' ])) {
				$classifications_text = esc_attr( $instance[ 'classifications_text' ] );
			} else {
				$classifications_text = 'Classification';
			}

			if (isset($instance[ 'sub_classifications_text' ])) {
				$sub_classifications_text = esc_attr( $instance[ 'sub_classifications_text' ] );
			} else {
				$sub_classifications_text = 'Sub-classification';
			}

			if (isset($instance[ 'locations_text' ])) {
				$locations_text = esc_attr( $instance[ 'locations_text' ] );
			} else {
				$locations_text = 'Location';
			}

			if (isset($instance[ 'sub_locations_text' ])) {
				$sub_locations_text = esc_attr( $instance[ 'sub_locations_text' ] );
			} else {
				$sub_locations_text = 'Sub-location';
			}

			if (isset($instance[ 'work_type_text' ])) {
				$work_type_text = esc_attr( $instance[ 'work_type_text' ] );
			} else {
				$work_type_text = 'Work Type';
			}

			if (isset($instance[ 'alert_frequency_text' ])) {
				$alert_frequency_text = esc_attr($instance[ 'alert_frequency_text' ]);
			} else {
				$alert_frequency_text = 'Alert Frequency';
			}

			if (isset($instance['dropdown_type'])) {
				$dropdown_type_locations = $instance['dropdown_type']['location'];
				$dropdown_type_classifications = $instance['dropdown_type']['classification'];
				$dropdown_type_worktype = $instance['dropdown_type']['worktype'];
			} else {
				$dropdown_type_locations = 'single';
				$dropdown_type_classifications = 'single';
				$dropdown_type_worktype = 'single';
			}

			if (!isset($args['widget_id']) || empty($args['widget_id'])) {
				if (isset($args['id']) && !empty($args['id'])) {
					$args['widget_id'] = $args['id'];
				} else {
					 throw new InvalidArgumentException('No ID for hot jobs');
				}
			}
			$Pluralizer = new Pluralizer();
		?>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					$('#<?php print $args['widget_id']; ?>').adlogicJobAlertsWidget({
						//confirmUrl : '<?php //print get_permalink( $instance[ 'confirm_page_id' ] );?>',
						widgetId : '<?php print $args['widget_id']; ?>',
						translations : {
							'locations' : '<?php print ucwords($Pluralizer->pluralize($locations_text)); ?>',
							'sub_locations' : '<?php print ucwords($Pluralizer->pluralize($sub_locations_text)); ?>',
							'classifications' : '<?php print ucwords($Pluralizer->pluralize($classifications_text)); ?>',
							'sub_classifications' : '<?php print ucwords($Pluralizer->pluralize($sub_classifications_text)); ?>',
							'worktype' : '<?php print ucwords($Pluralizer->pluralize($work_type_text)); ?>'
						}
						<?php if (isset($instance['dropdown_type'])) : ?>
						, dropdownType: {
							'locations': '<?php print $dropdown_type_locations; ?>',
							'classifications': '<?php print $dropdown_type_classifications; ?>',
							'worktypes': '<?php print $dropdown_type_worktype; ?>'
						}
						<?php endif; ?>
						,
                                                hideEmpty : {
								'locations' : <?php print ((isset($instance['hide_empty']['location'])) ? 'true' : 'false'); ?>,
								'classifications' : <?php print ((isset($instance['hide_empty']['classification'])) ? 'true' : 'false'); ?>,
								'worktypes' : <?php print ((isset($instance['hide_empty']['work_type'])) ? 'true' : 'false'); ?>,
						},
                                                topLevelOnly : {
							'locations' : <?php print ((isset($instance['top_level_only']['location'])) ? 'true' : 'false'); ?>,
							'classifications' : <?php print ((isset($instance['top_level_only']['classification'])) ? 'true' : 'false'); ?>,
							'worktypes' : <?php print ((isset($instance['top_level_only']['work_type'])) ? 'true' : 'false'); ?>
						}
					});
				});
			})(jQuery);
		</script>
		<?php if( !empty($args['before_widget']) ) { print $args['before_widget']; }?>
		<div id="<?php print $args['widget_id']; ?>" class="ajb-alerts-widget">
			<?php print $title;?>
			<form>
				<p class="ajb-first-name-holder ajb-alert-field">
					<label><?php print $first_name_text; ?><span class="required">*</span></label>
					<input class="ajb-first-name" type="text" id="<?php print $args['widget_id']; ?>-first_name" name="ajb-first_name" />
				</p>
				<?php if (!isset($instance['hidden_fields']['surname'])):?>
				<p class="ajb-surname-holder ajb-alert-field">
					<label for="<?php print $args['widget_id']; ?>-surname"><?php print $surname_text; ?></label>
					<input class="ajb-surname" type="text" id="<?php print $args['widget_id']; ?>-surname" name="ajb-surname" />
				</p>
				<?php endif; ?>
				<p class="ajb-email-address-holder ajb-alert-field">
					<label for="<?php print $args['widget_id']; ?>-email_address"><?php print $email_address_text; ?><span class="required">*</span></label>
					<input class="ajb-email-address" type="text" id="<?php print $args['widget_id']; ?>-email_address" name="ajb-email_address" />
				</p>
				<?php if (!isset($instance['hidden_fields']['phone'])):?>
				<p class="ajb-phone-holder ajb-alert-field">
					<label for="<?php print $args['widget_id']; ?>-phone_number"><?php print $phone_number_text; ?></label>
					<input class="ajb-phone-number" type="text" id="<?php print $args['widget_id']; ?>-phone_number" name="ajb-phone_number" />
				</p>
				<?php endif; ?>
				<?php if (!isset($instance['hidden_fields']['classification'])):?>
					<?php if ((isset($instance['dropdown_type']['classification'])) && ($instance['dropdown_type']['classification'] == 'double')):?>
						<p class="ajb-classifications-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $classifications_text; ?></label>
							<select class="ajb-classifications" name="classification_id" id="<?php print $args['widget_id']; ?>-classification_select_id"></select>
							<input type="hidden" id="<?php print $args['widget_id']; ?>-classification_id"/>
						</p>
						<p class="ajb-subclassifications-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $sub_classifications_text; ?></label>
							<select class="ajb-classifications" name="sub_classification_id" id="<?php print $args['widget_id']; ?>-sub_classification_select_id"></select>
						</p>
					<?php elseif ((isset($instance['dropdown_type']['classification'])) && ($instance['dropdown_type']['classification'] == 'multiple')):?>
					<p class="ajb-classifications-holder ajb-alert-field">
						<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $classifications_text; ?> <em>(hold ctrl key to select multiple options)</em></label>
						<select class="ajb-classifications multi-select" name="classification_id[]" id="<?php print $args['widget_id']; ?>-classification_id" multiple="multiple"></select>
					</p>
					<?php else:?>
						<p  class="ajb-classifications-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $classifications_text; ?></label>
							<select class="ajb-classifications" name="classification_id" id="<?php print $args['widget_id']; ?>-classification_id"></select>
						</p>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (!isset($instance['hidden_fields']['location'])&&!Adlogic_job_board::shouldUseNewAPI()):?>
					<?php if ((isset($instance['dropdown_type']['location'])) && ($instance['dropdown_type']['location'] == 'double')):?>
						<p class="ajb-locations-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
							<select class="ajb-locations" name="location_id" id="<?php print $args['widget_id']; ?>-location_select_id"></select>
							<input type="hidden" id="<?php print $args['widget_id']; ?>-location_id"/>
						</p>
						<p class="ajb-sublocations-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $sub_locations_text; ?></label>
							<select class="ajb-locations" name="sub_location_id" id="<?php print $args['widget_id']; ?>-sub_location_select_id"></select>
						</p>
					<?php elseif ((isset($instance['dropdown_type']['location'])) && ($instance['dropdown_type']['location'] == 'multiple')):?>
						<p class="ajb-locations-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?> <em>(hold ctrl key to select multiple options)</em></label>
							<select class="ajb-locations multi-select" name="location_id[]" id="<?php print $args['widget_id']; ?>-location_id" multiple="multiple"></select>
						</p>
					<?php else:?>
						<p class="ajb-locations-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
							<select class="ajb-locations" name="location_id" id="<?php print $args['widget_id']; ?>-location_id"></select>
						</p>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (!isset($instance['hidden_fields']['work_type'])):?>
					<?php if ((isset($instance['dropdown_type']['worktype'])) && ($instance['dropdown_type']['worktype'] == 'multiple')):?>
						<p class="ajb-worktypes-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-worktype_id"><?php print $work_type_text; ?>  <em>(hold ctrl key to select multiple options)</em></label>
							<select class="ajb-worktypes multi-select" name="worktype_id[]" id="<?php print $args['widget_id']; ?>-worktype_id" multiple="multiple"></select>
						</p>
					<?php else: ?>
						<p class="ajb-worktypes-holder ajb-alert-field">
							<label for="<?php print $args['widget_id']; ?>-worktype_id"><?php print $work_type_text; ?></label>
							<select class="ajb-worktypes" name="worktype_id" id="<?php print $args['widget_id']; ?>-worktype_id"></select>
						</p>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (!isset($instance['hidden_fields']['alert_frequency'])):?>
				<p class="ajb-alert-frequency-holder ajb-alert-field">
					<label for="<?php print $args['widget_id']; ?>-alert_frequency"><?php print $alert_frequency_text; ?></label>
					<select class="ajb-alert-frequency" name="alert_frequency" id="<?php print $args['widget_id']; ?>-alert_frequency">
						<option value="1">Daily</option>
						<option value="7">Weekly</option>
						<option value="31">Monthly</option>
					</select>
				</p>
				<?php else:?>
				<input type="hidden" value="1" id="<?php print $args['widget_id']; ?>-alert_frequency" />
				<?php endif; ?>
				<div class="ajb-button-holder">
					<input type="button" value="<?php print $subscribe_text;?>" class="ajb-subscribe-job-alerts" />
				</div>
			</form>
		</div>
		<?php
			if( !empty($args['after_widget']) ) {
				print $args['after_widget'];
			}
		}
	}
}
?>