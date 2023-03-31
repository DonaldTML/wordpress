<?php

/**
 * @package Adlogic_Job_Board
 * @version 1.0.0
 */

class Adlogic_Search_Widget extends WP_Widget
{
	function init()
	{
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		if ($Adlogic_Job_Board->check_setup() == true) {
			// Register the new widget
			add_action('widgets_init', create_function('', 'return register_widget( "Adlogic_Search_Widget" );'));
		}
	}

	function __construct()
	{
		parent::__construct('adlogic_search_widget', $name = 'Adlogic Job Search', array('description' => 'A widget to display the Adlogic Job Search form on your sidebar.'));
	}

	/** @see WP_Widget::form */
	function form($instance)
	{
		$searchSettings = get_option('adlogic_search_settings');

		$title = __('Job Search', 'text_domain');
		$search_text = 'Search';
		$view_all_jobs_text = 'View All Jobs';
		$classifications_text = 'Classification';
		$sub_classifications_text = 'Sub-classification';
		$locations_text = 'Location';
		$sub_locations_text = 'Sub-Location';
		$sub_sub_locations_text = 'Sub-Sub-Location';
		$work_type_text = 'Work Type';
		$keyword_text = 'Keywords';
		$salary_text = 'Salary Range';
		$listing_type_text = 'Listing Type';
		$default_listing_type = 'ExtOrBoth';
		$cost_center_text = 'Cost Centre';
		$default_salary_type = 'annual';
		$hourly_rate_min = '0';
		$hourly_rate_max = '200';
		$hourly_rate_step = '1';
		$annual_package_min = '0';
		$annual_package_max = '200';
		$annual_package_step = '5';
		$dropdown_type = array();
		$dropdown_type['classification'] = 'single';
		$dropdown_type['location'] = 'single';
		$dropdown_type['worktype'] = 'single';
		$dropdown_type['costcenter'] = 'single';
		$orgunit_id = '';
		$costcenter_id = '';
		//Empty Options disabled
		$hide_empty['classification'] = true;
		$hide_empty['location'] = true;
		$hide_empty['work_type'] = true;
		$disabled_fields['costcenter'] = true;
		$disabled_fields['salary'] = true;
		$location_depth = '(cities)';
		$field_order = 'classification,location,worktype,costcenter,listingtype,keyword,salary';
		if ($instance) {
			$title				= esc_attr($instance['title']);
			$disabled_fields	= $instance['hidden_fields'];
			$show_count			= $instance['show_count'];
			$hide_empty			= $instance['hide_empty'];
			if (isset($instance['dropdown_type'])) {
				$dropdown_type = $instance['dropdown_type'];
			} else {
				$dropdown_type['classification'] = 'single';
				$dropdown_type['location'] = 'single';
				$dropdown_type['worktype'] = 'single';
				$dropdown_type['costcenter'] = 'single';
			}

			if (isset($instance['search_text'])) {
				$search_text = $instance['search_text'];
			} else {
				$search_text = 'Search';
			}
			if (isset($instance['view_all_jobs_text'])) {
				$view_all_jobs_text = $instance['view_all_jobs_text'];
			} else {
				$view_all_jobs_text = 'View All Jobs';
			}

			if (isset($instance['classifications_text'])) {
				$classifications_text = $instance['classifications_text'];
			} else {
				$classifications_text = 'Classification';
			}
			if (isset($instance['sub_classifications_text'])) {
				$sub_classifications_text = $instance['sub_classifications_text'];
			} else {
				$sub_classifications_text = 'Sub-classification';
			}
			if (isset($instance['locations_text'])) {
				$locations_text = $instance['locations_text'];
			} else {
				$locations_text = 'Location';
			}
			if (isset($instance['sub_locations_text'])) {
				$sub_locations_text = $instance['sub_locations_text'];
			} else {
				$sub_locations_text = 'Sub-location';
			}
			if (Adlogic_job_board::shouldUseNewAPI()) {
				if (isset($instance['sub_sub_locations_text'])) {
					$sub_sub_locations_text = $instance['sub_sub_locations_text'];
				} else {
					$sub_sub_locations_text = 'Suburb';
				}
			}
			if (isset($instance['work_type_text'])) {
				$work_type_text = $instance['work_type_text'];
			} else {
				$work_type_text = 'Work Type';
			}
			if (isset($instance['cost_center_text'])) {
				$cost_center_text = $instance['cost_center_text'];
			} else {
				$cost_center_text = 'Cost Centre';
				/**
				 * :FIXME: Hacky way to determine whether cost centre should be disabled in existing widget configurations or
				 * not by default as existing widget configurations before version 2.2.3
				 * @since 2.2.3
				 */
				$disabled_fields['costcenter'] = true;
				$disabled_fields['salary'] = true;
				$dropdown_type['costcenter'] = 'single';
			}
			if (isset($instance['keyword_text'])) {
				$keyword_text = $instance['keyword_text'];
			} else {
				$keyword_text = 'Keywords';
			}
			if (isset($instance['salary_text'])) {
				$salary_text = $instance['salary_text'];
			} else {
				$salary_text = 'Salary Range';
			}
			if (isset($instance['default_salary_type'])) {
				$default_salary_type = $instance['default_salary_type'];
			} else {
				$default_salary_type = 'annual';
			}

			if (isset($instance['hourly_rate_min'])) {
				$hourly_rate_min = $instance['hourly_rate_min'];
			} else {
				$hourly_rate_min = '0';
			}

			if (isset($instance['hourly_rate_max'])) {
				$hourly_rate_max = $instance['hourly_rate_max'];
			} else {
				$hourly_rate_max = '200';
			}

			if (isset($instance['hourly_rate_step'])) {
				$hourly_rate_step = $instance['hourly_rate_step'];
			} else {
				$hourly_rate_step = '1';
			}

			if (isset($instance['annual_package_min'])) {
				$annual_package_min = $instance['annual_package_min'];
			} else {
				$annual_package_min = '0';
			}

			if (isset($instance['annual_package_max'])) {
				$annual_package_max = $instance['annual_package_max'];
			} else {
				$annual_package_max = '200';
			}

			if (isset($instance['annual_package_step'])) {
				$annual_package_step = $instance['annual_package_step'];
			} else {
				$annual_package_step = '5';
			}

			// Intranet Options
			if (isset($instance['listing_type_text'])) {
				$listing_type_text = $instance['listing_type_text'];
			} else {
				$listing_type_text = 'Listing Type';
			}

			if (isset($instance['default_listing_type'])) {
				$default_listing_type = $instance['default_listing_type'];
			}

			if (isset($instance['top_level_only'])) {
				$top_level_only = $instance['top_level_only'];
			}

			if (isset($instance['orgunit_id'])) {
				$orgunit_id = $instance['orgunit_id'];
			} else {
				$orgunit_id = '';
			}

			if (isset($instance['costcenter_id'])) {
				$costcenter_id = $instance['costcenter_id'];
			} else {
				$costcenter_id = '';
			}

			if (isset($instance['field_order'])) {
				$field_order = $instance['field_order'];
			} else {
				$field_order = 'classification,location,worktype,costcenter,listingtype,keyword,salary';
			}

			if (isset($instance['show_third_level'])) {
				$show_third_level = $instance['show_third_level'];
			}
			if (isset($instance['geolocation'])) {
				if (isset($instance['geolocation']['location_depth'])) {
					$location_depth = $instance['geolocation']['location_depth'];
				}
			}
		}
		if (isset($instance['search_page_id'])) {
			$search_page_id = $instance['search_page_id'];
		} else {
			$search_page_id = 0;
		}

		$widget_unique_id = uniqid('ajb_widget');
		?>
		<div class="<?php print $widget_unique_id; ?>">
			<p>
				<label for="<?php print $this->get_field_id('title'); ?>"><strong><?php _e('Title:'); ?></strong></label>
				<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print $title; ?>" />
			</p>
			<div>
				<label><strong>Search Fields:</strong></label><br />
				<p><em>Drag and drop fields to re-order</em></p>
				<input id="<?php print $this->get_field_id('field_order'); ?>" name="<?php print $this->get_field_name('field_order'); ?>" type="hidden" value="<?php print $field_order; ?>" />
				<ul class="ajb-field-order">
					<?php foreach (explode(',', $field_order) as $field_name) :
								switch ($field_name) {
									case 'classification':
										?>
								<li class="classification">
									<div class="re-order-arrow">Classification</div>
									<p>
										<label for="<?php print $this->get_field_id('classifications_text'); ?>"><?php _e('Label:'); ?></label>
									</p>
									<p>
										<input class="widefat" id="<?php print $this->get_field_id('classifications_text'); ?>" name="<?php print $this->get_field_name('classifications_text'); ?>" type="text" value="<?php print $classifications_text; ?>" />
									</p>
									<p>
										<label for="<?php print $this->get_field_id('sub_classifications_text'); ?>"><?php _e('Sub-Label:'); ?></label>
									</p>
									<p>
										<input class="widefat" id="<?php print $this->get_field_id('sub_classifications_text'); ?>" name="<?php print $this->get_field_name('sub_classifications_text'); ?>" type="text" value="<?php print $sub_classifications_text; ?>" />
									</p>
									<p>
										<em>Dropdown Type:</em><br />
										<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_classification_single" name="<?php print $this->get_field_name('dropdown_type'); ?>[classification]" type="radio" value="single" <?php print(($dropdown_type['classification'] == 'single') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_classification_single"> <?php _e('Single Dropdown'); ?></label><br />
										<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_classification_double" name="<?php print $this->get_field_name('dropdown_type'); ?>[classification]" type="radio" value="double" <?php print(($dropdown_type['classification'] == 'double') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_classification_double"> <?php _e('Double Dropdown'); ?></label><br />
										<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_classification_multiple" name="<?php print $this->get_field_name('dropdown_type'); ?>[classification]" type="radio" value="multiple" <?php print(($dropdown_type['classification'] == 'multiple') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_classification_multiple"> <?php _e('Multiple Selection'); ?></label><br />
									</p>
									<input class="checkbox" id="<?php print $this->get_field_id('show_count'); ?>_classification" name="<?php print $this->get_field_name('show_count'); ?>[classification]" type="checkbox" <?php print(isset($show_count['classification']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('show_count'); ?>_classification"> <?php _e('Show Job Count'); ?></label><br />
									<input class="checkbox" id="<?php print $this->get_field_id('hide_empty'); ?>_classification" name="<?php print $this->get_field_name('hide_empty'); ?>[classification]" type="checkbox" <?php print(isset($hide_empty['classification']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hide_empty'); ?>_classification"> <?php _e('Hide Empty Classifications'); ?></label><br />
									<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_classification" name="<?php print $this->get_field_name('hidden_fields'); ?>[classification]" type="checkbox" <?php print(isset($disabled_fields['classification']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_classification"> <?php _e('Disable Classifications'); ?></label>
								</li>
							<?php
											break;
										case 'location':
											$style = (Adlogic_Job_Board::shouldUseNewLocationField() ? "style='display:none;'" : "");
											?>
								<li class="location">
									<div class="re-order-arrow">Location</div>
									<p>
										<label for="<?php print $this->get_field_id('locations_text'); ?>"><?php _e('Label:'); ?></label>
									</p>
									<p>
										<input class="widefat" id="<?php print $this->get_field_id('locations_text'); ?>" name="<?php print $this->get_field_name('locations_text'); ?>" type="text" value="<?php print $locations_text; ?>" />
									</p>
									<p <?php print $style; ?>>
										<label for="<?php print $this->get_field_id('locations_text'); ?>"><?php _e('Sub-Label:'); ?></label>
									</p>
									<p <?php print $style; ?>>
										<input class="widefat" id="<?php print $this->get_field_id('sub_locations_text'); ?>" name="<?php print $this->get_field_name('sub_locations_text'); ?>" type="text" value="<?php print $sub_locations_text; ?>" />
									</p>
									<p <?php print $style; ?>>
										<label for="<?php print $this->get_field_id('locations_text'); ?>"><?php _e('Sub-Sub-Label:'); ?></label>
									</p>
									<p <?php print $style; ?>>
										<input class="widefat" id="<?php print $this->get_field_id('sub_sub_locations_text'); ?>" name="<?php print $this->get_field_name('sub_sub_locations_text'); ?>" type="text" value="<?php print $sub_sub_locations_text; ?>" />
									</p>
									<p <?php print $style; ?>>
										<em>Dropdown Type:</em><br />
										<?php if (!Adlogic_Job_Board::shouldUseNewAPI()) { ?>
											<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_location_single" name="<?php print $this->get_field_name('dropdown_type'); ?>[location]" type="radio" value="single" <?php print(($dropdown_type['location'] == 'single') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_location_single"> <?php _e('Single Dropdown'); ?></label><br />
											<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_location_double" name="<?php print $this->get_field_name('dropdown_type'); ?>[location]" type="radio" value="double" <?php print(($dropdown_type['location'] == 'double') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_location_double"> <?php _e('Double Dropdown'); ?></label><br />
											<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_location_multiple" name="<?php print $this->get_field_name('dropdown_type'); ?>[location]" type="radio" value="multiple" <?php print(($dropdown_type['location'] == 'multiple') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_location_multiple"> <?php _e('Multiple Selection'); ?></label><br />
										<?php } else { ?>
											<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_location_double" name="<?php print $this->get_field_name('dropdown_type'); ?>[location]" type="radio" value="double" <?php print(($dropdown_type['location'] == 'double') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_location_double"> <?php _e('Double Dropdown'); ?></label><br />
										<?php } ?>
									</p>
									<?php if (!Adlogic_Job_Board::shouldUseNewAPI()) { ?>
									<input <?php print $style; ?> class="checkbox" id="<?php print $this->get_field_id('show_third_level'); ?>_location" name="<?php print $this->get_field_name('show_third_level'); ?>[location]" type="checkbox" <?php print(isset($show_third_level['location']) ? 'checked="checked"' : ''); ?> /><label <?php print $style; ?> for="<?php print $this->get_field_id('show_third_level'); ?>_location"> <?php _e('Show 3rd Level'); ?></label><br <?php print $style; ?> />
									<input <?php print $style; ?> class="checkbox" id="<?php print $this->get_field_id('show_count'); ?>_location" name="<?php print $this->get_field_name('show_count'); ?>[location]" type="checkbox" <?php print(isset($show_count['location']) ? 'checked="checked"' : ''); ?> /><label <?php print $style; ?> for="<?php print $this->get_field_id('show_count'); ?>_location"> <?php _e('Show Job Count'); ?></label><br <?php print $style; ?> />
									<input <?php print $style; ?> class="checkbox" id="<?php print $this->get_field_id('hide_empty'); ?>_location" name="<?php print $this->get_field_name('hide_empty'); ?>[location]" type="checkbox" <?php print(isset($hide_empty['location']) ? 'checked="checked"' : ''); ?> /><label <?php print $style; ?> for="<?php print $this->get_field_id('hide_empty'); ?>_location"> <?php _e('Hide Empty Locations'); ?></label><br <?php print $style; ?> /> 
									<?php } ?>
									<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_location" name="<?php print $this->get_field_name('hidden_fields'); ?>[location]" type="checkbox" <?php print(isset($disabled_fields['location']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_location"> <?php _e('Disable Locations'); ?></label><br />

									<?php if (Adlogic_Job_Board::shouldUseNewLocationField()) : ?>
										<p style="display:none;">
											<span>Location Depth:</span>
											<br />
											<select id="<?php print $this->get_field_id('geolocation'); ?>_location_depth" name="<?php print $this->get_field_name('geolocation'); ?>[location_depth]">
												<option value="(cities)" <?php print((isset($location_depth) && $location_depth == '(cities)') ? "selected" : "") ?>>Suburb / Locality</option>
												<option value="(regions)" <?php print((isset($location_depth) && $location_depth == '(regions)') ? "selected" : "") ?>>Country</option>
											</select><br />
											<em style="color:#666;margin:5px;font-size:12px;display:block;">Select the highest level that can be searched e.g. "Locality" will NOT return states, provinces or countries, whereas "Country" will return everything allowing users to search for "Australia", etc.</em>
										</p>
									<?php endif; ?>

								</li>
							<?php
											break;
										case 'worktype':
											?>
								<li class="worktype">
									<div class="re-order-arrow">Work Type</div>
									<p>
										<label for="<?php print $this->get_field_id('work_type_text'); ?>"><?php _e('Label:'); ?></label>
									</p>
									<p>
										<input class="widefat" id="<?php print $this->get_field_id('work_type_text'); ?>" name="<?php print $this->get_field_name('work_type_text'); ?>" type="text" value="<?php print $work_type_text; ?>" />
									</p>
									<p>
										<em>Dropdown Type:</em><br />
										<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_single" name="<?php print $this->get_field_name('dropdown_type'); ?>[worktype]" type="radio" value="single" <?php print(($dropdown_type['worktype'] == 'single') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_single"> <?php _e('Single Dropdown'); ?></label><br />
										<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_multiple" name="<?php print $this->get_field_name('dropdown_type'); ?>[worktype]" type="radio" value="multiple" <?php print(($dropdown_type['worktype'] == 'multiple') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_worktype_multiple"> <?php _e('Multiple Selection'); ?></label><br />
									</p>
									<input class="checkbox" id="<?php print $this->get_field_id('show_count'); ?>_work_type" name="<?php print $this->get_field_name('show_count'); ?>[work_type]" type="checkbox" <?php print(isset($show_count['work_type']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('show_count'); ?>_work_type"> <?php _e('Show Job Count'); ?></label><br />
									<input class="checkbox" id="<?php print $this->get_field_id('hide_empty'); ?>_work_type" name="<?php print $this->get_field_name('hide_empty'); ?>[work_type]" type="checkbox" <?php print(isset($hide_empty['work_type']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hide_empty'); ?>_work_type"> <?php _e('Hide Empty Work Types'); ?></label><br />
									<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_work_type" name="<?php print $this->get_field_name('hidden_fields'); ?>[work_type]" type="checkbox" <?php print(isset($disabled_fields['work_type']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_work_type"> <?php _e('Disable Work Types'); ?></label>
								</li>
								<?php
												break;
											case 'listingtype':
												if ((isset($searchSettings['adlogic_search_intranet_setting'])) && ($searchSettings['adlogic_search_intranet_setting'] == 'true')) {
													?>
									<li class="listingtype">
										<div class="re-order-arrow">Listing Type</div>
										<p>
											<label for="<?php print $this->get_field_id('listing_type_text'); ?>"><?php _e('Label:'); ?></label>
										</p>
										<p>
											<input class="widefat" id="<?php print $this->get_field_id('listing_type_text'); ?>" name="<?php print $this->get_field_name('listing_type_text'); ?>" type="text" value="<?php print $listing_type_text; ?>" />
										</p>
										<label><strong><?php _e('Intranet Defaults:'); ?></strong></label>
										<p>
											<label for="<?php print $this->get_field_id('default_listing_type'); ?>"><?php _e('Default Listing Type:'); ?></label><br />
											<input class="radio" id="<?php print $this->get_field_id('default_listing_type'); ?>_external" name="<?php print $this->get_field_name('default_listing_type'); ?>" type="radio" value="ExtOrBoth" <?php print(($default_listing_type == 'ExtOrBoth') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('default_listing_type'); ?>_external"> <?php _e('External'); ?></label><br />
											<input class="radio" id="<?php print $this->get_field_id('default_listing_type'); ?>_internal" name="<?php print $this->get_field_name('default_listing_type'); ?>" type="radio" value="IntOrBoth" <?php print(($default_listing_type == 'IntOrBoth') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('default_listing_type'); ?>_internal"> <?php _e('Internal'); ?></label><br />
										</p>
										<p>
											<em>Note: If listing type search field is disabled below, this setting will be the default search parameter.</em>
										</p>
										<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_listing_type" name="<?php print $this->get_field_name('hidden_fields'); ?>[listing_type]" type="checkbox" <?php print(isset($disabled_fields['listing_type']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_listing_type"> <?php _e('Disable Listing Type'); ?></label><br />

										<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_listing_type_specific" name="<?php print $this->get_field_name('hidden_fields'); ?>[listing_type_specific]" type="checkbox" <?php print(isset($disabled_fields['listing_type_specific']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_listing_type_specific"> <?php _e('Specific Internal / External jobs'); ?></label>
									</li>
								<?php
												} else {
													print '<li class="listingtype" style="display: none;"><div class="re-order-arrow">Listing Type</div></li>';
												}
												break;
											case 'costcenter':
												?>
								<li class="costcenter">
									<div class="re-order-arrow">Cost Centre</div>
									<p>
										<label for="<?php print $this->get_field_id('cost_center_text'); ?>"><?php _e('Label:'); ?></label>
									</p>
									<p>
										<input class="widefat" id="<?php print $this->get_field_id('cost_center_text'); ?>" name="<?php print $this->get_field_name('cost_center_text'); ?>" type="text" value="<?php print $cost_center_text; ?>" />
									</p>
									<p>
										<em>Dropdown Type:</em><br />
										<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_costcenter_single" name="<?php print $this->get_field_name('dropdown_type'); ?>[costcenter]" type="radio" value="single" <?php print(($dropdown_type['costcenter'] == 'single') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_costcenter_single"> <?php _e('Single Dropdown'); ?></label><br />
										<input class="radio" id="<?php print $this->get_field_id('dropdown_type'); ?>_costcenter_multiple" name="<?php print $this->get_field_name('dropdown_type'); ?>[costcenter]" type="radio" value="multiple" <?php print(($dropdown_type['costcenter'] == 'multiple') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('dropdown_type'); ?>_costcenter_multiple"> <?php _e('Multiple Selection'); ?></label><br />
									</p>
									<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_costcenter" name="<?php print $this->get_field_name('hidden_fields'); ?>[costcenter]" type="checkbox" <?php print(isset($disabled_fields['costcenter']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_costcenter"> <?php _e('Disable Cost Centre'); ?></label>
								</li>
							<?php
											break;
										case 'keyword':
											?>
								<li class="keyword">
									<div class="re-order-arrow">Keywords</div>
									<p>
										<label for="<?php print $this->get_field_id('keyword_text'); ?>"><?php _e('Label:'); ?></label>
									</p>
									<p>
										<input class="widefat" id="<?php print $this->get_field_id('keyword_text'); ?>" name="<?php print $this->get_field_name('keyword_text'); ?>" type="text" value="<?php print $keyword_text; ?>" />
									</p>
									<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_keyword" name="<?php print $this->get_field_name('hidden_fields'); ?>[keyword]" type="checkbox" <?php print(isset($disabled_fields['keyword']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_keyword"> <?php _e('Disable Keywords'); ?></label>
								</li>
							<?php
											break;
										case 'salary':
											?>
								<li class="salary">
									<div class="re-order-arrow">Salary Range</div>
									<p>
										<label for="<?php print $this->get_field_id('salary_text'); ?>"><?php _e('Label:'); ?></label>
									</p>
									<p>
										<input class="widefat" id="<?php print $this->get_field_id('salary_text'); ?>" name="<?php print $this->get_field_name('salary_text'); ?>" type="text" value="<?php print $salary_text; ?>" />
									</p>
									<p>
										<label><strong><?php _e('Defaults:'); ?></strong></label>
									</p>
									<p>
										<label for="<?php print $this->get_field_id('default_salary_type'); ?>"><?php _e('Default Salary Type:'); ?></label><br />
										<input class="radio" id="<?php print $this->get_field_id('default_salary_type'); ?>_annual" name="<?php print $this->get_field_name('default_salary_type'); ?>" type="radio" value="annual" <?php print(($default_salary_type == 'annual') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('default_salary_type'); ?>_annual"> <?php _e('Annual Package'); ?></label><br />
										<input class="radio" id="<?php print $this->get_field_id('default_salary_type'); ?>_hourly" name="<?php print $this->get_field_name('default_salary_type'); ?>" type="radio" value="hourly" <?php print(($default_salary_type == 'hourly') ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('default_salary_type'); ?>_hourly"> <?php _e('Hourly Rate'); ?></label>
									</p>
									<p>
										<label><?php _e('Hourly Rate Ranges:'); ?></label><br />
										Min: $<input id="<?php print $this->get_field_id('hourly_rate_min'); ?>" name="<?php print $this->get_field_name('hourly_rate_min'); ?>" type="text" size="2" value="<?php print $hourly_rate_min; ?>" /> per hour<br />
										Max: $<input id="<?php print $this->get_field_id('hourly_rate_max'); ?>" name="<?php print $this->get_field_name('hourly_rate_max'); ?>" type="text" size="2" value="<?php print $hourly_rate_max; ?>" /> per hour<br />
										Increments: $<input id="<?php print $this->get_field_id('hourly_rate_step'); ?>" name="<?php print $this->get_field_name('hourly_rate_step'); ?>" type="text" size="2" value="<?php print $hourly_rate_step; ?>" /> per hour
									</p>
									<p>
										<label><?php _e('Annual Package Ranges:'); ?></label><br />
										Min: $<input id="<?php print $this->get_field_id('annual_package_min'); ?>" name="<?php print $this->get_field_name('annual_package_min'); ?>" type="text" size="2" value="<?php print $annual_package_min; ?>" /> K per annum<br />
										Max: $<input id="<?php print $this->get_field_id('annual_package_max'); ?>" name="<?php print $this->get_field_name('annual_package_max'); ?>" type="text" size="2" value="<?php print $annual_package_max; ?>" /> K per annum<br />
										Increments: $<input id="<?php print $this->get_field_id('annual_package_step'); ?>" name="<?php print $this->get_field_name('annual_package_step'); ?>" type="text" size="2" value="<?php print $annual_package_step; ?>" /> K per annum
									</p>
									<input class="checkbox" id="<?php print $this->get_field_id('hidden_fields'); ?>_salary" name="<?php print $this->get_field_name('hidden_fields'); ?>[salary]" type="checkbox" <?php print(isset($disabled_fields['salary']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('hidden_fields'); ?>_salary"> <?php _e('Disable Salary Range'); ?></label>
								</li>
						<?php
										break;
								}
							endforeach; ?>
				</ul>
			</div>
			<p>
				<label><strong><?php _e('Button Text:'); ?></strong></label>
			</p>
			<p>
				<label for="<?php print $this->get_field_id('search_text'); ?>"><?php _e('Search:'); ?></label>
				<input class="widefat" id="<?php print $this->get_field_id('search_text'); ?>" name="<?php print $this->get_field_name('search_text'); ?>" type="text" value="<?php print $search_text; ?>" />
				<label for="<?php print $this->get_field_id('view_all_jobs_text'); ?>"><?php _e('View All Jobs:'); ?></label>
				<input class="widefat" id="<?php print $this->get_field_id('view_all_jobs_text'); ?>" name="<?php print $this->get_field_name('view_all_jobs_text'); ?>" type="text" value="<?php print $view_all_jobs_text; ?>" />
			</p>
			<p>
				<label><strong><?php _e('Search Page:'); ?></strong></label><br />
				<?php wp_dropdown_pages(array('selected' => $search_page_id, 'name' => $this->get_field_name('search_page_id'), 'show_option_none' => '- Please Select -')); ?>
			</p>
			<p class="toggle-arrow">Advanced Settings</p>
			<div class="advanced-settings" style="display: none">
				<p>
					<label><strong><?php _e('Show Only Top Level:'); ?></strong></label><br />
				</p>
				<p>
					<input class="checkbox" id="<?php print $this->get_field_id('top_level_only'); ?>_classification" name="<?php print $this->get_field_name('top_level_only'); ?>[classification]" type="checkbox" <?php print(isset($top_level_only['classification']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('top_level_only'); ?>_classification"> <?php _e('Classifications'); ?></label><br />
					<input class="checkbox" id="<?php print $this->get_field_id('top_level_only'); ?>_location" name="<?php print $this->get_field_name('top_level_only'); ?>[location]" type="checkbox" <?php print(isset($top_level_only['location']) ? 'checked="checked"' : ''); ?> /><label for="<?php print $this->get_field_id('top_level_only'); ?>_location"> <?php _e('Locations'); ?></label><br />
				</p>
				<p>
					<label><strong><?php _e('Organisation Unit Id:'); ?></strong></label><br />
					<em>Leave blank if unknown</em>
				</p>
				<p>
					<input class="widefat" id="<?php print $this->get_field_id('orgunit_id'); ?>" name="<?php print $this->get_field_name('orgunit_id'); ?>" type="text" value="<?php print $orgunit_id; ?>" />
				</p>
				<p>
					<label><strong><?php _e('Default Cost Centre Id:'); ?></strong></label><br />
					<em>Leave blank if unknown</em>
				</p>
				<p>
					<input class="widefat" id="<?php print $this->get_field_id('costcenter_id'); ?>" name="<?php print $this->get_field_name('costcenter_id'); ?>" type="text" value="<?php print $costcenter_id; ?>" />
				</p>
			</div>
		</div>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					$('.<?php print $widget_unique_id; ?> .toggle-arrow').click(function() {
						$('.<?php print $widget_unique_id; ?> .advanced-settings').slideToggle(300);
						$(this).toggleClass('toggle-arrow-active');
					});

					$('.<?php print $widget_unique_id; ?> .ajb-field-order').sortable({
						update: function(event, ui) {
							$('#<?php print $this->get_field_id('field_order'); ?>').val('');
							$.each($(this).children('li'), function(idx, obj) {
								old_val = $('#<?php print $this->get_field_id('field_order'); ?>').val();
								$('#<?php print $this->get_field_id('field_order'); ?>').val(old_val + $(obj).attr('class') + ',');
							});
							new_val = $('#<?php print $this->get_field_id('field_order'); ?>').val();
							$('#<?php print $this->get_field_id('field_order'); ?>').val(new_val.substring(0, (new_val.length - 1)));
							console.log($('#<?php print $this->get_field_id('field_order'); ?>').val());
						}
					});
				});
			})(jQuery);
		</script>
	<?php
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance)
		{
			// Set new values

			// $instance['hidden_fields']		= (isset($new_instance['hidden_fields'])?$new_instance['hidden_fields']:array());
			// $instance['top_level_only']		= (isset($new_instance['top_level_only'])?$new_instance['top_level_only']:array());
			// $instance['hide_empty']			= (isset($new_instance['hide_empty'])?$new_instance['hide_empty']:array());
			$instance							= $old_instance;
			$instance['title']					= strip_tags($new_instance['title']);
			$instance['hidden_fields']			= (isset($new_instance['hidden_fields']) ? $new_instance['hidden_fields'] : array());
			$instance['top_level_only']			= (isset($new_instance['top_level_only']) ? $new_instance['top_level_only'] : array());
			$instance['show_third_level']		= (isset($new_instance['show_third_level']) ? $new_instance['show_third_level'] : array());
			$instance['hide_empty']				= (isset($new_instance['hide_empty']) ? $new_instance['hide_empty'] : array());
			$instance['show_count']				= (isset($new_instance['show_count']) ? $new_instance['show_count'] : array());
			$instance['search_text']			= $new_instance['search_text'];
			$instance['view_all_jobs_text']		= $new_instance['view_all_jobs_text'];
			$instance['search_page_id']			= $new_instance['search_page_id'];
			$instance['classifications_text']	= $new_instance['classifications_text'];
			$instance['sub_classifications_text']	= $new_instance['sub_classifications_text'];
			$instance['locations_text']			= $new_instance['locations_text'];
			$instance['sub_locations_text']			= $new_instance['sub_locations_text'];
			$instance['sub_sub_locations_text']			= $new_instance['sub_sub_locations_text'];
			$instance['work_type_text']			= $new_instance['work_type_text'];
			$instance['keyword_text']			= $new_instance['keyword_text'];
			$instance['salary_text']			= $new_instance['salary_text'];
			$instance['location_depth']			= (isset($new_instance['geolocation']) && isset($new_instance["geolocation"]["location_depth"]) ? $new_instance['geolocation']["location_depth"] : array());
			$instance = array_merge($instance, $new_instance);
			return $instance;
		}

		function widget($args, $instance)
		{
			global $wp_rewrite;

			// Get search settings
			$apiSettings = get_option('adlogic_api_settings');
			$searchSettings = get_option('adlogic_search_settings');

			// Enqueue Javascript
			wp_enqueue_script('jquery-adlogic-searchWidget');
			// Enqueue Stylesheet
			wp_enqueue_style('adlogic-search-widget');

			// outputs the content of the widget
			if ($instance) {
				if (!empty($args['before_title']) && !empty($args['after_title'])) {
					$title = $args['before_title'] . esc_attr($instance['title']) . $args['after_title'];
				} else {
					$title = esc_attr($instance['title']);
				}

				if (isset($instance['dropdown_type'])) {
					$dropdown_type_locations = $instance['dropdown_type']['location'];
					$dropdown_type_classifications = $instance['dropdown_type']['classification'];
					$dropdown_type_worktypes = $instance['dropdown_type']['worktype'];
					$dropdown_type_costcenters = $instance['dropdown_type']['costcenter'];
				} else {
					$dropdown_type_locations = 'single';
					$dropdown_type_classifications = 'single';
					$dropdown_type_worktypes = 'single';
					$dropdown_type_costcenters = 'single';
				}

				if (isset($instance['view_all_jobs_text'])) {
					$view_all_jobs_text = esc_attr($instance['view_all_jobs_text']);
				} else {
					$view_all_jobs_text = 'View All Jobs';
				}

				if (isset($instance['search_text'])) {
					$search_text = esc_attr($instance['search_text']);
				} else {
					$search_text = 'Search';
				}
				if (isset($instance['classifications_text'])) {
					$classifications_text = esc_attr($instance['classifications_text']);
				} else {
					$classifications_text = 'Classification';
				}

				if (isset($instance['sub_classifications_text'])) {
					$sub_classifications_text = esc_attr($instance['sub_classifications_text']);
				} else {
					$sub_classifications_text = 'Sub-classification';
				}

				if (isset($instance['locations_text'])) {
					$locations_text = esc_attr($instance['locations_text']);
				} else {
					$locations_text = 'Location';
				}

				if (isset($instance['sub_locations_text'])) {
					$sub_locations_text = esc_attr($instance['sub_locations_text']);
				} else {
					$sub_locations_text = 'Sub-location';
				}
				if (isset($instance['sub_sub_locations_text'])) {
					$sub_sub_locations_text = esc_attr($instance['sub_sub_locations_text']);
				} else {
					$sub_sub_locations_text = 'Sub-Sub-location';
				}

				if (isset($instance['work_type_text'])) {
					$work_type_text = esc_attr($instance['work_type_text']);
				} else {
					$work_type_text = 'Work Type';
				}

				if (isset($instance['cost_center_text'])) {
					$cost_center_text = esc_attr($instance['cost_center_text']);
				} else {
					$cost_center_text = 'Cost Centre';
					/**
					 * :FIXME: Hacky way to determine whether cost centre should be disabled in existing widget configurations or
					 * not by default as existing widget configurations before version 2.2.3
					 * @since 2.2.3
					 */
					$instance['hidden_fields']['costcenter'] = true;
					$dropdown_type_costcenters = 'single';
				}

				if (isset($instance['keyword_text'])) {
					$keyword_text = esc_attr($instance['keyword_text']);
				} else {
					$keyword_text = 'Keywords';
				}

				if (isset($instance['salary_text'])) {
					$salary_text = esc_attr($instance['salary_text']);
				} else {
					$salary_text = 'Salary Range';
				}

				if (isset($instance['default_salary_type'])) {
					$default_salary_type = esc_attr($instance['default_salary_type']);
				} else {
					$default_salary_type = 'annual';
				}

				if (isset($instance['hourly_rate_min'])) {
					$hourly_rate_min = esc_attr($instance['hourly_rate_min']);
				} else {
					$hourly_rate_min = '0';
				}

				if (isset($instance['hourly_rate_max'])) {
					$hourly_rate_max = esc_attr($instance['hourly_rate_max']);
				} else {
					$hourly_rate_max = '200';
				}

				if (isset($instance['hourly_rate_step'])) {
					$hourly_rate_step = esc_attr($instance['hourly_rate_step']);
				} else {
					$hourly_rate_step = '1';
				}

				if (isset($instance['annual_package_min'])) {
					$annual_package_min = esc_attr($instance['annual_package_min']);
				} else {
					$annual_package_min = '0';
				}

				if (isset($instance['annual_package_max'])) {
					$annual_package_max = esc_attr($instance['annual_package_max']);
				} else {
					$annual_package_max = '200';
				}

				if (isset($instance['annual_package_step'])) {
					$annual_package_step = esc_attr($instance['annual_package_step']);
				} else {
					$annual_package_step = '5';
				}

				// Intranet Options
				if (isset($instance['listing_type_text'])) {
					$listing_type_text = esc_attr($instance['listing_type_text']);
				} else {
					$listing_type_text = 'Listing Type';
				}

				if (isset($instance['default_listing_type'])) {
					$default_listing_type = esc_attr($instance['default_listing_type']);
				} else {
					$default_listing_type = 'ExtOrBoth';
				}

				if (isset($instance['field_order'])) {
					$field_order = $instance['field_order'];
				} else {
					$field_order = 'classification,location,worktype,costcenter,listingtype,keyword,salary';
				}
				if (isset($instance['geolocation'])) {
					if (isset($instance['geolocation']['location_depth'])) {
						$location_depth = $instance['geolocation']['location_depth'];
					}
				}
			}

			if (!isset($args['widget_id']) || empty($args['widget_id'])) {
				if (isset($args['id']) && !empty($args['id'])) {
					$args['widget_id'] = $args['id'];
				} else {
					throw new InvalidArgumentException('No ID for hot jobs');
				}
			}

			// Load Chosen jQuery Plugin if enabled in settings
			if (
				(isset($searchSettings['adlogic_search_enhanced_dropdowns'])) && ($searchSettings['adlogic_search_enhanced_dropdowns'] == 'true')
			) {
				wp_enqueue_script('jquery-chosen');
				wp_enqueue_style('jquery-chosen');
			}
			$Pluralizer = new Pluralizer();
			?>
		<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					$('#<?php print $args['widget_id']; ?>').adlogicSearchWidget({
						searchUrl: '<?php print get_permalink($instance['search_page_id']) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/'); ?>',
						widgetId: '<?php print $args['widget_id']; ?>',
						translations: {
							'locations': '<?php print ucwords($Pluralizer->pluralize($locations_text)); ?>',
							'sub_locations': '<?php print ucwords($Pluralizer->pluralize($sub_locations_text)); ?>',
							'sub_sub_locations': '<?php print ucwords($Pluralizer->pluralize($sub_sub_locations_text)); ?>',
							'classifications': '<?php print ucwords($Pluralizer->pluralize($classifications_text)); ?>',
							'sub_classifications': '<?php print ucwords($Pluralizer->pluralize($sub_classifications_text)); ?>',
							'worktype': '<?php print ucwords($Pluralizer->pluralize($work_type_text)); ?>',
							'costcenter': '<?php print ucwords($Pluralizer->pluralize($cost_center_text)); ?>'
						},
						<?php if (isset($searchSettings['adlogic_search_enhanced_dropdowns'])) : ?>
							chosenEnabled: <?php print $searchSettings['adlogic_search_enhanced_dropdowns']; ?>,
						<?php endif; ?>
						<?php if (isset($instance['dropdown_type'])) : ?>
							dropdownType: {
								'locations': '<?php print $dropdown_type_locations; ?>',
								'classifications': '<?php print $dropdown_type_classifications; ?>',
								'worktypes': '<?php print $dropdown_type_worktypes; ?>',
								'costcenters': '<?php print $dropdown_type_costcenters; ?>'
							},
						<?php endif; ?>
						salary_range_settings: {
							'default_type': '<?php print $default_salary_type; ?>',
							'hourly_rate_min': '<?php print $hourly_rate_min; ?>',
							'hourly_rate_max': '<?php print $hourly_rate_max; ?>',
							'hourly_rate_step': '<?php print $hourly_rate_step; ?>',
							'annual_package_min': '<?php print $annual_package_min; ?>',
							'annual_package_max': '<?php print $annual_package_max; ?>',
							'annual_package_step': '<?php print $annual_package_step; ?>'
						},
						adCounts: {
							'locations': <?php print((isset($instance['show_count']['location'])) ? 'true' : 'false'); ?>,
							'classifications': <?php print((isset($instance['show_count']['classification'])) ? 'true' : 'false'); ?>,
							'worktypes': <?php print((isset($instance['show_count']['work_type'])) ? 'true' : 'false'); ?>,
							'costcenters': <?php print((isset($instance['show_count']['cost_center'])) ? 'true' : 'false'); ?>
						},
						hideEmpty: {
							'locations': <?php print((isset($instance['hide_empty']['location'])) ? 'true' : 'false'); ?>,
							'classifications': <?php print((isset($instance['hide_empty']['classification'])) ? 'true' : 'false'); ?>,
							'worktypes': <?php print((isset($instance['hide_empty']['work_type'])) ? 'true' : 'false'); ?>,
							'costcenters': <?php print((isset($instance['hide_empty']['cost_center'])) ? 'true' : 'false'); ?>
						},
						topLevelOnly: {
							'locations': <?php print((isset($instance['top_level_only']['location'])) ? 'true' : 'false'); ?>,
							'classifications': <?php print((isset($instance['top_level_only']['classification'])) ? 'true' : 'false'); ?>,
							'worktypes': <?php print((isset($instance['top_level_only']['work_type'])) ? 'true' : 'false'); ?>
						},
						showThirdLevel: {
							'locations': <?php print((isset($instance['show_third_level']['location'])) ? 'true' : 'false'); ?>,
							'classifications': <?php print((isset($instance['show_third_level']['classification'])) ? 'true' : 'false'); ?>,
							'worktypes': <?php print((isset($instance['show_third_level']['work_type'])) ? 'true' : 'false'); ?>
						},
						useNewLocation: <?php print Adlogic_Job_Board::shouldUseNewLocationField(); ?>,
						useNewLocationAPI: <?php print Adlogic_Job_Board::shouldUseNewAPI(); ?>
						<?php if (Adlogic_Job_Board::shouldUseNewLocationField()) : ?>,
							geoLocationOpts: {
								filterType: '<?php print $location_depth; ?>'
							}
						<?php endif; ?>
					});
				});
			})(jQuery);
		</script>

		<?php if (!empty($args['before_widget'])) {
					print $args['before_widget'];
				} ?>
		<div id="<?php print $args['widget_id']; ?>" class="ajb-search-widget">
			<?php print $title; ?>
			<form>
				<?php
						foreach (explode(',', $field_order) as $field_name) :
							switch ($field_name) {
								case 'classification':
									?>
							<?php if (!isset($instance['hidden_fields']['classification'])) : ?>
								<?php if ((isset($instance['dropdown_type']['classification'])) && ($instance['dropdown_type']['classification'] == 'double')) : ?>
									<p class="ajb-classifications-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $classifications_text; ?></label>
										<select class="ajb-classifications" name="classification_id" id="<?php print $args['widget_id']; ?>-classification_select_id"></select>
										<input type="hidden" id="<?php print $args['widget_id']; ?>-classification_id" />
									</p>
									<p class="ajb-classifications-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $sub_classifications_text; ?></label>
										<select class="ajb-classifications" name="sub_classification_id" id="<?php print $args['widget_id']; ?>-sub_classification_select_id"></select>
									</p>
								<?php elseif ((isset($instance['dropdown_type']['classification'])) && ($instance['dropdown_type']['classification'] == 'multiple')) : ?>
									<p class="ajb-classifications-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $classifications_text; ?></label>
										<select class="ajb-classifications multi-select" name="classification_id[]" id="<?php print $args['widget_id']; ?>-classification_id" multiple="multiple"></select>
									</p>
								<?php else : ?>
									<p class="ajb-classifications-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-classification_id"><?php print $classifications_text; ?></label>
										<select class="ajb-classifications" name="classification_id" id="<?php print $args['widget_id']; ?>-classification_id"></select>
									</p>
								<?php endif; ?>
							<?php endif; ?>
						<?php
										break;
									case 'location':
										?>
							<?php if (Adlogic_Job_Board::shouldUseNewLocationField()) : ?>
								<?php if (!isset($instance['hidden_fields']['location'])) : ?>
									<p class="ajb-locations-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
										<input class="ajb-locations" name="location_id" id="<?php print $args['widget_id']; ?>-location_select_id" />
										<input type="hidden" id="<?php print $args['widget_id']; ?>-location_id" />
									</p>
								<?php endif; ?>
							<?php elseif (Adlogic_Job_board::shouldUseNewAPI()) : ?>
								<?php if (!isset($instance['hidden_fields']['location'])) : ?>
									<?php if ((isset($instance['dropdown_type']['location'])) && ($instance['dropdown_type']['location'] == 'double')) : ?>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
											<select class="ajb-locations" name="location_id" id="<?php print $args['widget_id']; ?>-location_select_id"></select>
											<input type="hidden" id="<?php print $args['widget_id']; ?>-location_id" />
										</p>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $sub_locations_text; ?></label>
											<select class="ajb-locations" name="sub_location_id" id="<?php print $args['widget_id']; ?>-sub_location_select_id"></select>
										</p>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $sub_sub_locations_text; ?></label>
											<select class="ajb-locations" name="sub_sub_location_id" id="<?php print $args['widget_id']; ?>-sub_sub_location_select_id"></select>
										</p>
									<?php elseif ((isset($instance['dropdown_type']['location'])) && ($instance['dropdown_type']['location'] == 'multiple')) : ?>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
											<select class="ajb-locations multi-select" name="location_id[]" id="<?php print $args['widget_id']; ?>-location_id" multiple="multiple"></select>
										</p>
									<?php else : ?>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
											<select class="ajb-locations" name="location_id" id="<?php print $args['widget_id']; ?>-location_id"></select>
										</p>
									<?php endif; ?>
								<?php endif; ?>
							<?php else : ?>
								<?php if (!isset($instance['hidden_fields']['location'])) : ?>
									<?php if ((isset($instance['dropdown_type']['location'])) && ($instance['dropdown_type']['location'] == 'double')) : ?>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
											<select class="ajb-locations" name="location_id" id="<?php print $args['widget_id']; ?>-location_select_id"></select>
											<input type="hidden" id="<?php print $args['widget_id']; ?>-location_id" />
										</p>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $sub_locations_text; ?></label>
											<select class="ajb-locations" name="sub_location_id" id="<?php print $args['widget_id']; ?>-sub_location_select_id"></select>
										</p>
									<?php elseif ((isset($instance['dropdown_type']['location'])) && ($instance['dropdown_type']['location'] == 'multiple')) : ?>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
											<select class="ajb-locations multi-select" name="location_id[]" id="<?php print $args['widget_id']; ?>-location_id" multiple="multiple"></select>
										</p>
									<?php else : ?>
										<p class="ajb-locations-holder ajb-search-field">
											<label for="<?php print $args['widget_id']; ?>-location_id"><?php print $locations_text; ?></label>
											<select class="ajb-locations" name="location_id" id="<?php print $args['widget_id']; ?>-location_id"></select>
										</p>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; // New Location End If 
											?>
						<?php
										break;
									case 'worktype':
										?>
							<?php if (!isset($instance['hidden_fields']['work_type'])) : ?>
								<?php if ((isset($instance['dropdown_type']['worktype'])) && ($instance['dropdown_type']['worktype'] == 'multiple')) : ?>
									<p class="ajb-worktypes-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-worktype_id"><?php print $work_type_text; ?></label>
										<select class="ajb-worktypes multi-select" name="worktype_id[]" id="<?php print $args['widget_id']; ?>-worktype_id" multiple="multiple"></select>
									</p>
								<?php else : ?>
									<p class="ajb-worktypes-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-worktype_id"><?php print $work_type_text; ?></label>
										<select class="ajb-worktypes" name="worktype_id" id="<?php print $args['widget_id']; ?>-worktype_id"></select>
									</p>
								<?php endif; ?>
							<?php endif; ?>
						<?php
										break;
									case 'costcenter':
										?>
							<?php if (!isset($instance['hidden_fields']['costcenter'])) : ?>
								<?php if ((isset($instance['dropdown_type']['costcenter'])) && ($instance['dropdown_type']['costcenter'] == 'multiple')) : ?>
									<p class="ajb-costcenters-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-costcenter_id"><?php print $cost_center_text; ?></label>
										<select class="ajb-costcenters multi-select" name="costcenter_id[]" id="<?php print $args['widget_id']; ?>-costcenter_id" multiple="multiple"></select>
									</p>
								<?php else : ?>
									<p class="ajb-costcenters-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-costcenter_id"><?php print $cost_center_text; ?></label>
										<select class="ajb-costcenters" name="costcenter_id" id="<?php print $args['widget_id']; ?>-costcenter_id"></select>
									</p>
								<?php endif; ?>
							<?php endif; ?>
						<?php
										break;
									case 'listingtype':
										?>
							<?php if ((isset($searchSettings['adlogic_search_intranet_setting'])) && ($searchSettings['adlogic_search_intranet_setting'] == 'true')) : ?>
								<?php if (!isset($instance['hidden_fields']['listing_type'])) : ?>
									<p class="ajb-listingtypes-holder ajb-search-field">
										<label for="<?php print $args['widget_id']; ?>-listing_type"><?php print $listing_type_text; ?></label>
										<select class="ajb-listingtypes" name="listing_type" id="<?php print $args['widget_id']; ?>-listing_type">
											<?php if (!isset($instance['hidden_fields']['listing_type_specific'])) : ?>
												<option value="ExtOrBoth" <?php ($default_listing_type == 'ExtOrBoth') ? ' checked="checked"' : '' ?>>External</option>
												<option value="IntOrBoth" <?php ($default_listing_type == 'IntOrBoth') ? ' checked="checked"' : '' ?>>Internal</option>
											<?php else : ?>
												<option value="ExtOrBoth" <?php ($default_listing_type == 'Ext') ? ' checked="checked"' : '' ?>>External</option>
												<option value="Int" <?php ($default_listing_type == 'Int') ? ' checked="checked"' : '' ?>>Internal</option>
											<?php endif; ?>
										</select>
									</p>
								<?php else : ?>
									<input type="hidden" id="<?php print $args['widget_id']; ?>-listing_type" name="listing_type" value="<?php print $default_listing_type; ?>" />
								<?php endif; ?>
							<?php endif; ?>
							<?php break; ?>
						<?php
									case 'keyword': ?>
							<?php if (!isset($instance['hidden_fields']['keyword'])) : ?>
								<p class="ajb-keywords-holder ajb-search-field">
									<label for="<?php print $args['widget_id']; ?>-keywords"><?php print $keyword_text; ?></label>
									<input class="ajb-keywords" name="keywords" type="text" id="<?php print $args['widget_id']; ?>-keywords" />
								</p>
							<?php endif; ?>
						<?php
										break;
									case 'salary':
										?>
							<?php if (!isset($instance['hidden_fields']['salary'])) : ?>
								<p class="ajb-salary-holder ajb-search-field">
									<label><?php print $salary_text; ?></label><br />
									<span id="<?php print $args['widget_id']; ?>-salary-switcher" class="ajb-salary-switcher">
										<span class="ajb-salary-amount"></span> <span class="ajb-salary-type-selector"><span class="ajb-salary-hourly"><a href="javascript:void(0)">hour</a></span> | <span class="ajb-salary-annual"><a href="javascript:void(0)">year</a></span></span>
									</span>
									<div class="ajb-salary-range" id="<?php print $args['widget_id']; ?>-salary-range"></div>
									<input type="hidden" id="<?php print $args['widget_id']; ?>-salary-type" name="ajb-salary-type" />
									<input type="hidden" id="<?php print $args['widget_id']; ?>-salary-max" name="ajb-salary-max" />
									<input type="hidden" id="<?php print $args['widget_id']; ?>-salary-min" name="ajb-salary-min" />
								</p>
							<?php endif; ?>
							<?php break; ?>
					<?php }
						endforeach; ?>
					<?php if ((isset($instance['costcenter_id'])) && (!empty($instance['costcenter_id']))) : ?>
						<input type="hidden" id="<?php print $args['widget_id']; ?>-costcenter_id" name="ajb-costcenter_id" value="<?php print $instance['costcenter_id']; ?>" />
					<?php endif; ?>
					<?php if ((isset($instance['orgunit_id'])) && (!empty($instance['orgunit_id']))) : ?>
						<input type="hidden" id="<?php print $args['widget_id']; ?>-orgunit_id" name="ajb-orgunit_id" value="<?php print $instance['orgunit_id']; ?>" />
					<?php endif; ?>

					<div class="ajb-search-widget-buttons">
						<input type="button" value="<?php print $search_text; ?>" class="ajb-search-for-jobs-button" />
						<input type="button" value="<?php print $view_all_jobs_text; ?>" class="ajb-view-all-jobs-button" />
					</div>
			</form>
		</div>
<?php
		if (!empty($args['after_widget'])) {
			print $args['after_widget'];
		}
	}
}

?>