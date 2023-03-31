<?php
class Adlogic_HotJobs_Widget extends WP_Widget {
	function init() {
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		if ($Adlogic_Job_Board->check_setup() == true) {
			add_action( 'widgets_init', create_function( '', 'return register_widget( "Adlogic_HotJobs_Widget" );' ) );
		}
	}

	function __construct() {
		parent::__construct('adlogic_hot_jobs_widget', $name = 'Adlogic Hot Jobs', array( 'description' => 'A widget to display Hot/Featured Jobs on your sidebar.' ) );
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
			$searchSettings = get_option('adlogic_search_settings');

			if ( $instance ) {
				$title = esc_attr( $instance[ 'title' ] );
			} else {
				$title = __( 'Hot Jobs', 'text_domain' );
			}
	
			if (isset($instance['total_hot_jobs'])) {
				$total_hot_jobs = $instance['total_hot_jobs'];
			} else {
				$total_hot_jobs = 12;
			}
			if (isset($instance['hot_jobs_per_page'])) {
				$hot_jobs_per_page = $instance['hot_jobs_per_page'];
			} else {
				$hot_jobs_per_page = 6;
			}
			if (isset($instance['job_details_page_id'])) {
				$job_details_page_id = $instance['job_details_page_id'];
			} else {
				$job_details_page_id = 0;
			}

			if (isset($instance['scroll_direction'])) {
				$scroll_direction = $instance['scroll_direction'];
			} else {
				$scroll_direction = 'horizontal';
			}

			if (isset($instance['listing_type'])) {
				$listing_type = $instance['listing_type'];
			} else {
				$listing_type = 'ExtOrBoth';
			}

			if (isset($instance['template'])) {
				$template = $instance['template'];
			} else {
				$template = '';
			}
			
			if (isset($instance['search_params'])) {
				$search_params = $instance['search_params'];
			} else {
				$search_params = '';
			}
                        if (isset($instance['scroll_speed'])) {
				$scroll_speed = $instance['scroll_speed']; 
			} else {
				$scroll_speed = 3000;
			}
			$easy_scrolling = (isset($instance['easy_scrolling']) ? $instance['easy_scrolling'] : false);
			
			$widget_unique_id = uniqid('ajb_widget');
		?>
		<p>
			<label for="<?php print $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print $title; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('total_hot_jobs'); ?>"><?php _e('Total Jobs:'); ?></label> 
			<input size="2" id="<?php print $this->get_field_id('total_hot_jobs'); ?>" name="<?php print $this->get_field_name('total_hot_jobs'); ?>" type="text" value="<?php print $total_hot_jobs; ?>" />
		</p>
		<p>
			<label for="<?php print $this->get_field_id('hot_jobs_per_page'); ?>"><?php _e('Jobs per slide:'); ?></label> 
			<input size="2" id="<?php print $this->get_field_id('hot_jobs_per_page'); ?>" name="<?php print $this->get_field_name('hot_jobs_per_page'); ?>" type="text" value="<?php print $hot_jobs_per_page; ?>" />
		</p>
		<?php if ((isset($searchSettings['adlogic_search_intranet_setting'])) && ( $searchSettings['adlogic_search_intranet_setting'] == 'true')) : ?>
		<p>
			<label for="<?php print $this->get_field_id('listing_type_text'); ?>"><?php _e('Listing Type:'); ?></label>
		</p>
		<p>
			<input class="radio" id="<?php print $this->get_field_id('listing_type'); ?>_external" name="<?php print $this->get_field_name('listing_type'); ?>" type="radio" value="ExtOrBoth" <?php print ( ($listing_type == 'ExtOrBoth') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('listing_type'); ?>_external"> <?php _e('External'); ?></label><br />
			<input class="radio" id="<?php print $this->get_field_id('listing_type'); ?>_internal" name="<?php print $this->get_field_name('listing_type'); ?>" type="radio" value="IntOrBoth" <?php print ( ($listing_type == 'IntOrBoth') ? 'checked="checked"': '' ); ?> /><label for="<?php print $this->get_field_id('listing_type'); ?>_internal"> <?php _e('Internal'); ?></label><br />
		</p>
		<?php endif; ?>
		<p>
			<label><strong><?php _e('Job Details Page:'); ?></strong></label><br />
			<?php wp_dropdown_pages(array('selected' => $job_details_page_id, 'name' => $this->get_field_name('job_details_page_id'), 'show_option_none' => '- Please Select -')); ?>
		</p>
		<p>
			<label><strong><?php _e('Scroll Direction:'); ?></strong></label>
			<select id="<?php print $this->get_field_id('scroll_direction'); ?>" name="<?php print $this->get_field_name('scroll_direction')?>">
				<option value="horizontal" <?php print ($scroll_direction == 'horizontal' ? 'selected' : '');?>>Horizontal</option>
				<option value="vertical" <?php print ($scroll_direction == 'vertical' ? 'selected' : '');?>>Vertical</option>
				<option value="fade" <?php print ($scroll_direction == 'fade' ? 'selected' : '');?>>Fade</option>
				<option value="none" <?php print ($scroll_direction == 'none' ? 'selected' : '');?>>None (Disable)</option>
			</select>
		</p>
                <p>
		<input class="checkbox" id="<?php print $this->get_field_id('easy_scrolling'); ?>_easy_scrolling" name="<?php print $this->get_field_name('easy_scrolling'); ?>[easy_scrolling]" type="checkbox" <?php print ( isset($easy_scrolling) && $easy_scrolling != false ? 'checked="checked"': '' ); ?> />
		<label for="<?php print $this->get_field_id('easy_scrolling'); ?>_easy_scrolling"> <?php _e('Smooth page transitions'); ?></label><br />

		</p>
		<p>
			<label><strong><?php _e('Template:'); ?></strong></label>
			<select id="<?php print $this->get_field_id('template'); ?>" name="<?php print $this->get_field_name('template')?>">
				<option value="" <?php print ($template == '' ? 'selected' : '');?>>None</option>
				<?php 
					if (is_dir(AJB_PLUGIN_PATH . '/templates/hot_jobs/')) {
						$hDir = opendir(AJB_PLUGIN_PATH . '/templates/hot_jobs/');
						$sysTemplateCount = 0;

						if ($sysTemplateCount == 0) {
							print '<optgroup label="System Templates">';
						}

						while (false !== ($fName = readdir($hDir))) {
							if ($fName != "." && $fName != ".." && substr($fName, -5, 5) == '.html') {
								print '<option value="system/' . substr($fName, 0, -5) . '"' . ($template == 'system/' . substr($fName, 0, -5) ? 'selected' : '') . '>' . ucwords(strtolower(substr($fName, 0, -5)))  . '</option>';
							}
						}
						if ($sysTemplateCount >0) {
							print '</optgroup>';
						}
						closedir($hDir);
					}
				?>
				<?php 
					if (is_dir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/hot_jobs/')) {
						$hDir = opendir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/hot_jobs/');
						$themeTemplateCount = 0;

						if ($themeTemplateCount == 0) {
							print '<optgroup label="Theme Templates">';
						}

						while (false !== ($fName = readdir($hDir))) {
							if ($fName != "." && $fName != ".." && substr($fName, -5, 5) == '.html') {
								print '<option value="theme/' . substr($fName, 0, -5) . '"' . ($template == 'theme/' . substr($fName, 0, -5) ? 'selected' : '') . '>' . ucwords(strtolower(substr($fName, 0, -5)))  . '</option>';
							}
						}
						if ($themeTemplateCount >0) {
							print '</optgroup>';
						}
						closedir($hDir);
					}
				?>
			</select>
		</p>
		<p class="<?php print $widget_unique_id; ?> toggle-arrow">Advanced Settings</p>
		<div class="<?php print $widget_unique_id; ?> advanced-settings" style="display: none">
			<p>
				<label><strong><?php _e('Use search parameters instead of hot jobs (leave blank to disable):'); ?></strong></label><br />
			</p>
			<p>
				<input class="widefat" id="<?php print $this->get_field_id('search_params'); ?>" name="<?php print $this->get_field_name('search_params'); ?>" type="text" value="<?php print $search_params; ?>" />
			</p>
                        <p>
				<label><strong><?php _e('Speed between page transitions'); ?></strong></label><br />
			</p>
			<p>
				<input class="widefat" id="<?php print $this->get_field_id('scroll_speed'); ?>" name="<?php print $this->get_field_name('scroll_speed'); ?>" type="text" value="<?php print ((isset($scroll_speed) ? $scroll_speed : '3000')); ?>" />
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
		$instance							= $old_instance;
		$instance['title']					= strip_tags($new_instance['title']);
		$instance['total_hot_jobs']			= $new_instance['total_hot_jobs'];
		$instance['hot_jobs_per_page']		= $new_instance['hot_jobs_per_page'];
		$instance['job_details_page_id']	= $new_instance['job_details_page_id'];
		$instance['scroll_direction']		= $new_instance['scroll_direction'];
		$instance['template']				= $new_instance['template'];
		$instance['easy_scrolling']			= (isset($new_instance['easy_scrolling']) ? $new_instance['easy_scrolling'] : false);
		$instace['scroll_speed']			= $new_instance['scroll_speed'];
		$instance							= array_merge($instance, $new_instance);
		return $instance;
	}

	function widget( $args, $instance ) {
		global $wp_rewrite;

		$searchSettings = get_option('adlogic_search_settings');

		// Enqueue Javascript
		wp_enqueue_script( 'jquery-adlogic-hotJobsWidget' );

		// Enqueue Stylesheet
		wp_enqueue_style( 'adlogic-hotjobs-widget' );

		if ( $instance ) {
			if (!empty($args['before_title']) && !empty($args['after_title'])) {
				$title = $args['before_title'] . esc_attr( $instance[ 'title' ] ) . $args['after_title'];
			} else {
				$title = esc_attr( $instance[ 'title' ] );
			}

			if (isset($instance[ 'total_hot_jobs' ])) {
				$total_hot_jobs = intval(esc_attr($instance[ 'total_hot_jobs' ]));
			} else {
				$total_hot_jobs = 12;
			}
			if (isset($instance[ 'hot_jobs_per_page' ])) {
				$hot_jobs_per_page = intval(esc_attr($instance[ 'hot_jobs_per_page' ]));
			} else {
				$hot_jobs_per_page = 3;
			}
			if (isset($instance['scroll_direction'])) {
				if($instance['scroll_direction'] != "none") {
					wp_enqueue_script( 'jquery-bxSlider' );
				}
				$scroll_direction = $instance['scroll_direction'];
			} else {
				$scroll_direction = 'horizontal';
			}
			if (isset($instance['job_details_page_id'])) {
				$job_details_page_id = $instance['job_details_page_id'];
			} else {
				$job_details_page_id = 0;
			}
			
			if ((isset($searchSettings['adlogic_search_intranet_setting'])) && ( $searchSettings['adlogic_search_intranet_setting'] == 'true')) {
				if (isset($instance['listing_type'])) {
					$listing_type = $instance['listing_type'];
				} else {
					$listing_type = 'ExtOrBoth';
				}
			}

			$template_text = "";
			if (isset($instance['template'])) {
				$template = $instance['template'];
				$templateArray = explode('/', $template);
				switch ($templateArray[0]) {
					case 'system':
						if (is_file(AJB_PLUGIN_PATH . '/templates/hot_jobs/' . $templateArray[1] . '.html')) {
							$template_text = file_get_contents(AJB_PLUGIN_PATH . '/templates/hot_jobs/' . $templateArray[1] . '.html');
						} else {
							$template_text = 'Error: could not load hot jobs template';
						}
						break;
					case 'theme':
						if (get_stylesheet_directory() . '/css/adlogic-job-board/templates/hot_jobs/' . $templateArray[1] . '.html') {
							$template_text = file_get_contents(get_stylesheet_directory() . '/css/adlogic-job-board/templates/hot_jobs/' . $templateArray[1] . '.html');
						} else {
							$template_text = 'Error: could not load hob jobs template';
						}
						break;
				}
			} else {
				$template = '';
				$template_text = '';
			}

			$searchString = '';
			if (isset($instance['search_params']) && !empty($instance['search_params'])) {
				$queryBlocks = explode(';', $instance['search_params']);
				$searchString = '';
				foreach ($queryBlocks as $queryBlock) {
					$queryBlock = explode(':', trim($queryBlock), 2);
					if (is_array($queryBlock) && count($queryBlock) > 1) {
						list($searchParam, $searchValue) = $queryBlock;
						// loop through valid parameter list and set values
						$searchString .= '&' . $searchParam . '=' . $searchValue;
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
                        if (isset($instance['scroll_speed'])) {
				$scroll_speed = $instance['scroll_speed']; 
			} else {
				$scroll_speed = 3000;
			}

			$easy_scrolling = $instance[ 'easy_scrolling' ];
			
			?>
			<script type="text/javascript">
					jQuery(document).ready(function($) {
						$('#<?php print $args['widget_id']; ?> .ajb-hotjobs').adlogicHotjobs({
							perPage: <?php print $hot_jobs_per_page; ?>,
							detailsPage: '<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/': '&/');?>',
							scrollDirection: '<?php print $scroll_direction; ?>',
							ajaxServer: adlogicJobSearch.ajaxurl + '?action=<?php print (empty($searchString) ? 'searchHotJobs' : 'searchJobs') . '&widget_id=' . $args['widget_id'] . (isset($listing_type) ? '&internalExternal=' . $listing_type : '') . (!empty($searchString) ? $searchString : ''); ?>&to=<?php print $total_hot_jobs; ?>',
							template: '<?php print base64_encode($template_text); ?>',
                            easyScrolling: <?php print ((isset($easy_scrolling) && $easy_scrolling != false ? 'true' : 'false')); ?>,
							scrollSpeed: <?php print $scroll_speed; ?>,
							totalHotJobs: <?php print $total_hot_jobs; ?>
						});
					});
			</script>
			<?php if( !empty($args['before_widget']) ) { print $args['before_widget']; }?>
			<div id="<?php print $args['widget_id']; ?>" class="ajb-hotjobs-widget">
				<?php print $title;?>
				<div class="ajb-hotjobs">
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