<?php
class Adlogic_Search_Shortcodes
{

	static $cached_resultset;
	static $search_params;
	static $unique_search_id;
	static $queryVars;

	function init()
	{
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		if ($Adlogic_Job_Board->check_setup() == true) {
			// Initialise Shortcodes
			add_shortcode('adlogic_search_results', array('Adlogic_Search_Shortcodes', 'search_results'));
			add_shortcode('adlogic_search_pagination', array('Adlogic_Search_Shortcodes', 'search_pagination'));
			add_shortcode('adlogic_search_breadcrumbs', array('Adlogic_Search_Shortcodes', 'search_breadcrumbs'));

			add_shortcode('adlogic_filtered_search_results', array('Adlogic_Search_Shortcodes', 'filtered_search_results'));
			add_shortcode('adlogic_filtered_search_pagination', array('Adlogic_Search_Shortcodes', 'filtered_search_pagination'));
			// Add Hook to add meta if necessary
			if (!is_admin()) {
				add_action('the_posts', array('Adlogic_Search_Shortcodes', 'check_shortcode'));
			}
		}

		// Add TinyMCE Editor Buttons for Page Editor
		if (is_admin()) {
			// Search Results
			$Adlogic_Job_Board->add_page_editor_button('ajbJobSearch', 'jquery.tinyMCEjobSearchButton.js');
			add_action('admin_print_footer_scripts', array(__CLASS__, 'search_editor_code'), 50);
			// Search Pagination
			$Adlogic_Job_Board->add_page_editor_button('ajbSearchPagination', 'jquery.tinyMCEsearchPaginationButton.js');
			add_action('admin_print_footer_scripts', array(__CLASS__, 'pagination_editor_code'), 50);
			// Search Pagination
			$Adlogic_Job_Board->add_page_editor_button('ajbSearchBreadcrumbs', 'jquery.tinyMCEsearchBreadcrumbsButton.js');
			add_action('admin_print_footer_scripts', array(__CLASS__, 'breadcrumbs_editor_code'), 50);
		}
	}
	static function search_editor_code()
	{
		?>
		<div class="ajb-editor-dialogs" id="ajb-job-search" style="display:none;">
			<p class="title">Job Search Shortcode Options</p>
			<div class="ajb-editor-main-options">
				<p>
					<label><strong><?php _e('Template:'); ?></strong></label>
					<select id="ajb-job-search-template" name="ajb-job-search-template">
						<option value="">Default</option>
						<option value="custom">Custom (enter HTML between tags)</option>
						<?php
								if (is_dir(AJB_PLUGIN_PATH . '/templates/search_page/')) {
									$hDir = opendir(AJB_PLUGIN_PATH . '/templates/search_page/');
									$sysTemplateCount = 0;

									if ($sysTemplateCount == 0) {
										print '<optgroup label="System Templates">';
									}

									while (false !== ($fName = readdir($hDir))) {
										if ($fName != "." && $fName != ".." && substr($fName, -5, 5) == '.html') {
											print '<option value="' . substr($fName, 0, -5) . '"' . '>' . ucwords(strtolower(substr($fName, 0, -5)))  . '</option>';
										}
									}
									if ($sysTemplateCount > 0) {
										print '</optgroup>';
									}
									closedir($hDir);
								}
								?>
						<?php
								if (is_dir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/search_page/')) {
									$hDir = opendir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/search_page/');
									$themeTemplateCount = 0;

									if ($themeTemplateCount == 0) {
										print '<optgroup label="Theme Templates">';
									}

									while (false !== ($fName = readdir($hDir))) {
										if ($fName != "." && $fName != ".." && substr($fName, -5, 5) == '.html') {
											print '<option value="' . substr($fName, 0, -5) . '"' . '>' . ucwords(strtolower(substr($fName, 0, -5)))  . '</option>';
										}
									}
									if ($themeTemplateCount > 0) {
										print '</optgroup>';
									}
									closedir($hDir);
								}
								?>
					</select>
				</p>
			</div>
			<div class="submitbox">
				<div class="ajb-dialog-cancel">
					<a class="submitdelete deletion" href="javascript:void(0);"><?php _e('Cancel'); ?></a>
				</div>
				<div id="ajb-job-search-insert" class="ajb-dialog-submit">
					<input type="button" tabindex="100" value="<?php esc_attr_e('Add Shortcode'); ?>" class="button-primary" id="ajb-job-search-shortcode" name="ajb-job-search-shortcode">
				</div>
			</div>
		</div>
	<?php

		}

		static function pagination_editor_code()
		{
			print '<div id="ajb-search-pagination" style="display:none;">Configuration options:</div>';
		}

		static function breadcrumbs_editor_code()
		{
			?>
		<div class="ajb-editor-dialogs" id="ajb-search-breadcrumbs" style="display:none;">
			<p class="title">Search Breadcrumbs Shortcode Options</p>
			<div class="ajb-editor-main-options">
				<p>
					<label><strong><?php _e('Template:'); ?></strong></label>
					<select id="ajb-search-breadcrumbs-template" name="ajb-search-breadcrumbs-template">
						<option value="">Default</option>
						<option value="custom">Custom (enter HTML between tags)</option>
						<?php
								if (is_dir(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/')) {
									$hDir = opendir(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/');
									$sysTemplateCount = 0;

									if ($sysTemplateCount == 0) {
										print '<optgroup label="System Templates">';
									}

									while (false !== ($fName = readdir($hDir))) {
										if ($fName != "." && $fName != ".." && substr($fName, -5, 5) == '.html') {
											print '<option value="' . substr($fName, 0, -5) . '"' . '>' . ucwords(strtolower(substr($fName, 0, -5)))  . '</option>';
										}
									}
									if ($sysTemplateCount > 0) {
										print '</optgroup>';
									}
									closedir($hDir);
								}
								?>
						<?php
								if (is_dir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/search_breadcrumbs/')) {
									$hDir = opendir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/search_breadcrumbs/');
									$themeTemplateCount = 0;

									if ($themeTemplateCount == 0) {
										print '<optgroup label="Theme Templates">';
									}

									while (false !== ($fName = readdir($hDir))) {
										if ($fName != "." && $fName != ".." && substr($fName, -5, 5) == '.html') {
											print '<option value="' . substr($fName, 0, -5) . '"' . '>' . ucwords(strtolower(substr($fName, 0, -5)))  . '</option>';
										}
									}
									if ($themeTemplateCount > 0) {
										print '</optgroup>';
									}
									closedir($hDir);
								}
								?>
					</select>
				</p>
			</div>
			<div class="submitbox">
				<div class="ajb-dialog-cancel">
					<a class="submitdelete deletion" href="javascript:void(0);"><?php _e('Cancel'); ?></a>
				</div>
				<div id="ajb-search-breadcrumbs-insert" class="ajb-dialog-submit">
					<input type="button" tabindex="100" value="<?php esc_attr_e('Add Shortcode'); ?>" class="button-primary" id="ajb-search-breadcrumbs-shortcode" name="ajb-search-breadcrumbs-shortcode">
				</div>
			</div>
		</div>
	<?php
		}

		static function check_shortcode($posts)
		{
			global $shortcode_tags;
			if (!empty($posts)) {
				$pattern = get_shortcode_regex();

				foreach ($posts as $post) {
					preg_match_all("/$pattern/s", $post->post_content, $matches);

					/**
					 * $matches[2] matches the shortcode name
					 * @see do_shortcode_tag()
					 */
					foreach ($matches[2] as $match) {
						if ($match == 'adlogic_search_results') {
							remove_action('wp_head', 'rel_canonical');
							break 2;
						}
					}
				}
			}
			return $posts;
		}

		static function search_results($atts, $content = '')
		{
			// Enqueue Javascript
			wp_enqueue_script('jquery-adlogic-searchPage');
			// Enqueue Stylesheet
			wp_enqueue_style('adlogic-search-page');

			$searchSettings = get_option('adlogic_search_settings');
			$apiSettings = get_option('adlogic_api_settings');
			$itemsPerPage = $searchSettings['adlogic_search_results_per_page'];

			if (isset($atts['search_id']) && !empty($atts['search_id'])) {
				self::$unique_search_id = $uniqueId = $atts['search_id'];
			} else {
				self::$unique_search_id = $uniqueId = uniqid('adlogicJobSearch_');
			}

			if (isset($atts['query'])) {
				$query = $atts['query'];
				self::$cached_resultset = null;
			} else {
				$query = null;
			}

			if ((isset($atts['type'])) && ($atts['type'] == 'archive')) {
				$oJobSearchResults = self::process_query('archive', $query);
			} else if ((isset($atts['type'])) && ($atts['type'] == 'intranet')) {
				$oJobSearchResults = self::process_query('intranet', $query);
			} else if ((isset($atts['type'])) && ($atts['type'] == 'global')) {
				$oJobSearchResults = self::process_query('global', $query);
			} else {
				$oJobSearchResults = self::process_query(null, $query);
			}

			if (isset($atts['date_format'])) {
				$date_format = $atts['date_format'];
			} else {
				$date_format = null;
			}

			ob_start();
			?>
		<script type="text/javascript">
		
			jQuery(document).ready(function($) {
				$('#<?php print $uniqueId; ?>').adlogicJobSearch({
					<?php if ((isset($atts['type'])) && ($atts['type'] == 'archive')) { ?>
						ajaxServer: adlogicJobSearch.ajaxurl + '?action=searchArchiveAds&page_id=<?php print get_the_ID();
																												print((!empty($date_format)) ? '&date_format=' . base64_encode($date_format) : ''); ?>',
					<?php } else if ((isset($atts['type'])) && ($atts['type'] == 'global')) { ?>
						ajaxServer: adlogicJobSearch.ajaxurl + '?action=searchAllRecruiters&page_id=<?php print get_the_ID();
																												print((!empty($date_format)) ? '&date_format=' . base64_encode($date_format) : ''); ?>',
					<?php } else { ?>
						ajaxServer: adlogicJobSearch.ajaxurl + '?action=searchJobs&page_id=<?php print get_the_ID();
																										print((!empty($date_format)) ? '&date_format=' . base64_encode($date_format) : ''); ?>',
					<?php } ?>
					template: $('#<?php print $uniqueId; ?> .search_result_template').html(),
					items_per_page: <?php print $itemsPerPage; ?>,
					embedded_search: <?php print(((isset($atts['embedded'])) && ($atts['embedded'] == 'true')) ? 'true' : 'false'); ?>,
					searchParams: {
						<?php
								$searchParams = '';
								if (isset(self::$queryVars['locId'])) {
									$searchParams .= 'location_id: \'' . self::$queryVars['locId'] . '\',';
								}
								if (isset(self::$queryVars['indId'])) {
									$searchParams .= 'industry_id: \'' . self::$queryVars['indId'] . '\',';
								}
								if (isset(self::$queryVars['wtId'])) {
									$searchParams .= 'work_type_id: \'' . self::$queryVars['wtId'] . '\',';
								}
								if (isset(self::$queryVars['keyword'])) {
									$searchParams .= 'keywords: \'' . self::$queryVars['keyword'] . '\',';
								}
								if (isset(self::$queryVars['salaryType'])) {
									$searchParams .= 'salary_type: \'' . self::$queryVars['salaryType'] . '\',';
								}
								if (isset(self::$queryVars['salaryMax'])) {
									$searchParams .= 'salary_max: \'' . self::$queryVars['salaryMax'] . '\',';
								}
								if (isset(self::$queryVars['salaryMin'])) {
									$searchParams .= 'salary_min: \'' . self::$queryVars['salaryMin'] . '\',';
								}
								if (isset(self::$queryVars['costCenter'])) {
									$searchParams .= 'cost_center_id: \'' . self::$queryVars['costCenter'] . '\',';
								}
								if (isset(self::$queryVars['from'])) {
									$searchParams .= 'from: ' . self::$queryVars['from'] . ',';
								} else {
									$searchParams .= 'from: 1,';
								}
								if (isset(self::$queryVars['to'])) {
									$searchParams .= 'to: ' . self::$queryVars['to'] . ',';
								} else {
									$searchParams .= 'to: ' . $itemsPerPage . ',';
								}
								if ((isset(self::$queryVars['listingType']))) {
									$searchParams .= 'internalExternal: \'' . self::$queryVars['listingType'] . '\',';
								}
								if ((isset(self::$queryVars['orgUnit']))) {
									$searchParams .= 'orgUnit: \'' . self::$queryVars['orgUnit'] . '\',';
								}
								if ((isset(self::$queryVars['geoLocationJson']))) {
									$searchParams .= 'geoLocationJson: \'' . self::$queryVars['geoLocationJson'] . '\',';
								}
								$searchParams .= 'currentPage: ' . ((isset(self::$queryVars['from'])) ? ((self::$queryVars['from'] > 0) ? ((self::$queryVars['from'] - 1) / $itemsPerPage) : 0) : 0) . ',';
								print substr($searchParams, 0, -1);
								?>
					}
				});
			});
			<?php
					// Generate drop down defaults based on search
					print 'var indIdDef = "' . (isset(self::$queryVars['indId']) ? self::$queryVars['indId'] : '') . '";';
					print 'var locIdDef = "' . (isset(self::$queryVars['locId']) ? self::$queryVars['locId'] : '') . '";';
					print 'var geoLocationJsonDef = \'' . (isset(self::$queryVars['geoLocationJson']) ? self::$queryVars['geoLocationJson'] : '') . '\';';
					print 'var wtIdDef = "' . (isset(self::$queryVars['wtId']) ? self::$queryVars['wtId'] : '') . '";';
					print 'var keyDef = "' . (isset(self::$queryVars['keyword']) ? urldecode(self::$queryVars['keyword']) : '') . '";';
					print 'var listingTypeDef = "' . (isset(self::$queryVars['listingType']) ? urldecode(self::$queryVars['listingType']) : '') . '";';
					print 'var orgUnitDef = "' . (isset(self::$queryVars['orgUnit']) ? urldecode(self::$queryVars['orgUnit']) : '') . '";';
					print 'var salaryTypeDef = "' . (isset(self::$queryVars['salaryType']) ? self::$queryVars['salaryType'] : '') . '";';
					print 'var salaryMinDef = "' . (isset(self::$queryVars['salaryMin']) ? self::$queryVars['salaryMin'] : '') . '";';
					print 'var salaryMaxDef = "' . (isset(self::$queryVars['salaryMax']) ? self::$queryVars['salaryMax'] : '') . '";';
					?>
					
		</script>
		<?php
				$search_javascript = ob_get_contents();
				ob_end_clean();

				if (empty($atts)) {
					$atts['template'] = 'base';
				}


				switch ($atts['template']) {
					case 'custom':
						$new_content = self::parse_search($oJobSearchResults, $content, $date_format);
						break;
					case 'base':
					default:
						if (!empty($atts['template'])) {
							if (defined('MULTISITE') && (MULTISITE == true)) {
								if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html')) {
									$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html');
								} else if (is_file(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/search_page/' . $atts['template'] . '.html')) {
									$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/search_page/' . $atts['template'] . '.html');
								} else if (is_file(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html')) {
									$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html');
								} else {
									$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/default.html');
								}
							} else {
								if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html')) {
									$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html');
								} else if (is_file(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html')) {
									$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html');
								} else {
									$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/default.html');
								}
							}
						} else {
							$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/default.html');
						}

						$new_content = self::parse_search($oJobSearchResults, $content, $date_format);
						break;
				}

				return $search_javascript . '<div id="' . $uniqueId . '" class="adlogic_job_results">' . $new_content . '<div class="search_result_template" style="display:none;">' . $content . '</div>' . '</div>';
			}

			static function filtered_search_results($atts, $content = '')
			{
				// Enqueue Javascript
				wp_enqueue_script('jquery-adlogic-searchPage');
				// Enqueue Stylesheet
				wp_enqueue_style('adlogic-search-page');

				$searchSettings = get_option('adlogic_search_settings');
				$apiSettings = get_option('adlogic_api_settings');
				$itemsPerPage = $searchSettings['adlogic_search_results_per_page'];

				if (isset($atts['search_id']) && !empty($atts['search_id'])) {
					self::$unique_search_id = $uniqueId = $atts['search_id'];
				} else {
					self::$unique_search_id = $uniqueId = uniqid('adlogicJobSearch_');
				}
				if (isset($atts['query'])) {
					$query = $atts['query'];
					self::$cached_resultset = null;
				} else {
					$query = null;
				}
				if (isset($atts['childrenrecruiterids'])) {
					$query .= $atts['childrenrecruiterids'];
				}
				if ((isset($atts['type'])) && ($atts['type'] == 'archive')) {
					$oJobSearchResults = self::process_query('archive', $query);
				} else if ((isset($atts['type'])) && ($atts['type'] == 'intranet')) {
					$oJobSearchResults = self::process_query('intranet', $query);
				} else if ((isset($atts['type'])) && ($atts['type'] == 'global')) {
					$oJobSearchResults = self::process_query('global', $query);
				} else {
					$oJobSearchResults = self::process_query(null, $query);
				}

				if (isset($atts['date_format'])) {
					$date_format = $atts['date_format'];
				} else {
					$date_format = null;
				}

				ob_start();

				?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('#<?php print $uniqueId; ?>').adlogicJobSearch({
						<?php if ((isset($atts['type'])) && ($atts['type'] == 'archive')) { ?>
							ajaxServer: adlogicJobSearch.ajaxurl + '?action=searchArchiveAds&page_id=<?php print get_the_ID();
																													print((!empty($date_format)) ? '&date_format=' . base64_encode($date_format) : ''); ?>',
						<?php } else if ((isset($atts['type'])) && ($atts['type'] == 'global')) { ?>
							ajaxServer: adlogicJobSearch.ajaxurl + '?action=searchAllRecruiters&page_id=<?php print get_the_ID();
																													print((!empty($date_format)) ? '&date_format=' . base64_encode($date_format) : ''); ?>',
						<?php } else { ?>
							ajaxServer: adlogicJobSearch.ajaxurl + '?action=searchFilteredJobs&page_id=<?php print get_the_ID();
																													print((!empty($date_format)) ? '&date_format=' . base64_encode($date_format) : ''); ?>',
						<?php } ?>
						template: $('#<?php print $uniqueId; ?> .search_result_template').html(),
						items_per_page: <?php print $itemsPerPage; ?>,
						embedded_search: <?php print(((isset($atts['embedded'])) && ($atts['embedded'] == 'true')) ? 'true' : 'false'); ?>,
						searchParams: {
							<?php

									$searchParams = '';
									if (isset(self::$queryVars['childrenRecruiterIds'])) {
										$searchParams .= 'childrenRecruiterIds: \'' . self::$queryVars['childrenRecruiterIds'] . '\',';
									}
									if (isset(self::$queryVars['locId'])) {
										$searchParams .= 'location_id: \'' . self::$queryVars['locId'] . '\',';
									}
									if (isset(self::$queryVars['indId'])) {
										$searchParams .= 'industry_id: \'' . self::$queryVars['indId'] . '\',';
									}
									if (isset(self::$queryVars['wtId'])) {
										$searchParams .= 'work_type_id: \'' . self::$queryVars['wtId'] . '\',';
									}
									if (isset(self::$queryVars['keyword'])) {
										$searchParams .= 'keywords: \'' . self::$queryVars['keyword'] . '\',';
									}
									if (isset(self::$queryVars['salaryType'])) {
										$searchParams .= 'salary_type: \'' . self::$queryVars['salaryType'] . '\',';
									}
									if (isset(self::$queryVars['salaryMax'])) {
										$searchParams .= 'salary_max: \'' . self::$queryVars['salaryMax'] . '\',';
									}
									if (isset(self::$queryVars['salaryMin'])) {
										$searchParams .= 'salary_min: \'' . self::$queryVars['salaryMin'] . '\',';
									}
									if (isset(self::$queryVars['costCenter'])) {
										$searchParams .= 'cost_center_id: \'' . self::$queryVars['costCenter'] . '\',';
									}
									if (isset(self::$queryVars['from'])) {
										$searchParams .= 'from: ' . self::$queryVars['from'] . ',';
									} else {
										$searchParams .= 'from: 1,';
									}
									if (isset(self::$queryVars['to'])) {
										$searchParams .= 'to: ' . self::$queryVars['to'] . ',';
									} else {
										$searchParams .= 'to: ' . $itemsPerPage . ',';
									}
									if ((isset(self::$queryVars['listingType']))) {
										$searchParams .= 'internalExternal: \'' . self::$queryVars['listingType'] . '\',';
									}
									$searchParams .= 'currentPage: ' . ((isset(self::$queryVars['from'])) ? ((self::$queryVars['from'] > 0) ? ((self::$queryVars['from'] - 1) / $itemsPerPage) : 0) : 0) . ',';
									print substr($searchParams, 0, -1);
									?>
						}
					});
				});
				<?php
						// Generate drop down defaults based on search
						print 'var indIdDef = "' . (isset(self::$queryVars['indId']) ? self::$queryVars['indId'] : '') . '";';
						print 'var locIdDef = "' . (isset(self::$queryVars['locId']) ? self::$queryVars['locId'] : '') . '";';
						print 'var wtIdDef = "' . (isset(self::$queryVars['wtId']) ? self::$queryVars['wtId'] : '') . '";';
						print 'var keyDef = "' . (isset(self::$queryVars['keyword']) ? urldecode(self::$queryVars['keyword']) : '') . '";';
						print 'var listingTypeDef = "' . (isset(self::$queryVars['listingType']) ? urldecode(self::$queryVars['listingType']) : '') . '";';
						print 'var salaryTypeDef = "' . (isset(self::$queryVars['salaryType']) ? self::$queryVars['salaryType'] : '') . '";';
						print 'var salaryMinDef = "' . (isset(self::$queryVars['salaryMin']) ? self::$queryVars['salaryMin'] : '') . '";';
						print 'var salaryMaxDef = "' . (isset(self::$queryVars['salaryMax']) ? self::$queryVars['salaryMax'] : '') . '";';
						print 'var childrenRecruiterIdsDef = "' . (isset(self::$queryVars['childrenRecruiterIds']) ? self::$queryVars['childrenRecruiterIds'] : '') . '";';
						?>
			</script>
			<?php
					$search_javascript = ob_get_contents();
					ob_end_clean();

					if (empty($atts)) {
						$atts['template'] = 'base';
					}


					switch ($atts['template']) {
						case 'custom':
							$new_content = self::parse_search($oJobSearchResults, $content, $date_format);
							break;
						case 'base':
						default:
							if (!empty($atts['template'])) {
								if (defined('MULTISITE') && (MULTISITE == true)) {
									if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html');
									} else if (is_file(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/search_page/' . $atts['template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/search_page/' . $atts['template'] . '.html');
									} else if (is_file(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html')) {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html');
									} else {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/default.html');
									}
								} else {
									if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_page/' . $atts['template'] . '.html');
									} else if (is_file(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html')) {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/' . $atts['template'] . '.html');
									} else {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/default.html');
									}
								}
							} else {
								$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_page/default.html');
							}

							$new_content = self::parse_search($oJobSearchResults, $content, $date_format);
							break;
					}

					return $search_javascript . '<div id="' . $uniqueId . '" class="adlogic_job_results">' . $new_content . '<div class="search_result_template" style="display:none;">' . $content . '</div>' . '</div>';
				}

				static function parse_search($search_results, $content, $date_format = null, $current_page_id = null)
				{

					global $wp_rewrite;

					if ($current_page_id == null) {
						$current_page_id = get_the_ID();
					}

					$parsed_content = '';
					$searchSettings = get_option('adlogic_search_settings');
					$job_details_page_id = $searchSettings['adlogic_job_details_page'];
					$date_format = (!empty($date_format) ? $date_format : (isset($searchSettings['adlogic_search_results_date_format']) ? $searchSettings['adlogic_search_results_date_format'] : 'd/m/Y'));

					/*
		  *	January 12th 2017
		  *	If an account is marked as "inactive" in the system, the webservice returns the string "Not active now." - we now cater for this response as previously the message
		  *	"Unable to connect to server" would appear instead & was very misleading.
		  */
					if ($search_results == 'Not active now.') {
						return '<div class="adlogic_search_not_active">This account is no longer active, please contact our <a href="mailto:support@myrecruitmentplus.com?subject=My%20company%20jobboard%20is%20inactive.">support team</a> if you believe this is an issue.</div>';
					}
					/*
		  *	May 25th 2016
		  *	We will now always pass along an empty JobPosting element if there are no results returned from the API.
		  *	To ensure no errors occur, we'll check to see if the JobPosting element is empty - if it is return the no results found message.
		  */
					if (empty($search_results->JobPostings->JobPosting)) {
						return '<div class="adlogic_search_no_results">' . (!empty($searchSettings['adlogic_search_results_none']) ? $searchSettings['adlogic_search_results_none'] : 'Job search returned no results') . '</div>';
					}

					if (count($search_results->JobPostings->JobPosting) == 0) {
						return '<div class="adlogic_search_no_results">' . (!empty($searchSettings['adlogic_search_results_none']) ? $searchSettings['adlogic_search_results_none'] : 'Job search returned no results') . '</div>';
					}
					$oJobPosting = $search_results->JobPostings->JobPosting;
					$new_content = $content;
					$oJobAttributes = $oJobPosting->attributes();
					
					foreach ($search_results->JobPostings->JobPosting as $oJobPosting) {
						$new_content = $content;
						$oJobAttributes = $oJobPosting->attributes();

						if (!empty($date_format)) {
							$pubDate = date($date_format, strtotime($oJobPosting->pubDate));
						} else {
							$pubDate = $oJobPosting->pubDate;
						}

						
						// Build Bulletpoints
						$sBulletPointHtml = '<ul>';
						foreach ($oJobPosting->standOut->BulletPoints->BulletPoint as $sBulletPoint) {
							$sBulletPoint = (string) $sBulletPoint;
							if (!empty($sBulletPoint)) {
								$sBulletPointHtml .= '<li>' . $sBulletPoint . '</li>';
							}
						}
						$sBulletPointHtml .= '</ul>';

						if ($sBulletPointHtml == '<ul></ul>') {
							$sBulletPointHtml = '';
						}

						// Location Breadcrumbs
						$sLocationArray = array();

						$sLocationList = '<ul>';
						$sLocationIdx = 0;
						$useNewAPI = Adlogic_Job_Board::shouldUseNewAPI();
						if ($useNewAPI) {

							$apiSettings = get_option('adlogic_api_settings');
							require_once(AJB_PLUGIN_PATH . '/lib/classes/newLocation.class.php');
							$oNewLocations = new NewLocation($apiSettings['adlogic_rest_server'], $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id'], $apiSettings['adlogic_rest_api_key']);
							$allLocations = array();
							$allLocations = $oNewLocations->get();
						}
						$useNewLocationField = Adlogic_Job_Board::shouldUseNewLocationField();

						$sLocation = $oJobPosting->locations->location ;
							$oLocationAttributes = $sLocation->attributes();
							$sLocationArray[] = $sLocation->value;
							$sLocationType = "";
							$oJobAttributes = $oJobPosting->attributes();
							
							if ($useNewLocationField) {
								if ($sLocationIdx == 0) {
									$sLocationType = "Country";
								} else if ($sLocationIdx == 1) {
									$sLocationType = "State";
								} else {
									$sLocationType = "Locality";
								}
							} else {
								$sLocationType = "Location";
							}
							if ($useNewAPI) {
								if (!function_exists('searchForIdCust')) {
									function searchForIdCust($id, $array)
									{

										foreach ($array as $key => $val) {
											if ($val["name"] === $id) {
												return true;
											} else if ($val === $id) {
												return true;
											}
										}
										return null;
									}
								}

								if ($sLocationIdx == 0) {

									foreach ($allLocations as $countries) {

										foreach ($countries as $country) {
											$id = searchForIdCust($sLocation->value->__toString(), $country);
											if ($id == true) {
												foreach ($country as $elements) {
													$a = $elements["suburbIds"];
													$oLocationAttributes->id =  implode(",", $a);
													break 3;
												}
											}
										}
									}
								} else if ($sLocationIdx == 1) {

									foreach ($allLocations as $countries) {

										foreach ($countries as $country) {
											foreach ($country as $country1) {
												foreach ($country1["state"] as $state) {
													$id = searchForIdCust($sLocation->value->__toString(), $state);
													if ($id == true) {

														$a = $state["suburbIds"];
														$oLocationAttributes->id =  implode(",", $a);
														break 4;
													}
												}
											}
										}
									}
								}
							}

							if (!empty($sLocation->value)) {
								if ($wp_rewrite->using_permalinks()) {
									$sLocationList .= '<li><a>'.$oJobPosting->locations->location[2]. '</a></li>';
									$sLocationList .= '<li><a>'.$oJobPosting->locations->location[1]. '</a></li>';
									$sLocationList .= '<li><a>'.$oJobPosting->locations->location[0]. '</a></li>';
								} else {
									$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . '&/Location/' . Adlogic_Job_Board::uriSafe($sLocation->value) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation->value . '">' . $sLocation->value . '</a>' . '</li>';
								}
							}
							$sLocationIdx++;
						$sJobLocationBreadCrumbs = $sLocationList . '</ul>';

						if ($sJobLocationBreadCrumbs == '<ul></ul>') {
							$sJobLocationBreadCrumbs = '';
						}
						// Classification Breadcrumbs
						$sPositionList = '<ul>';
						foreach ($oJobPosting->classifications->classification as $sClassification) {
							$oClassificationAttributes = $sClassification->attributes();
							if (!empty($sClassification->value)) {
								if ($wp_rewrite->using_permalinks()) {
									$sPositionList  .= '<li><a href="' . get_permalink($current_page_id) . 'query/Industry/' . Adlogic_Job_Board::uriSafe($sClassification->value) . '/' . $oClassificationAttributes->id . '/"  title="' . $sClassification->value . ' Jobs">' . $sClassification->value . '</a>' . '</li>';
								} else {
									$sPositionList  .= '<li><a href="' . get_permalink($current_page_id) . '&/Industry/' . Adlogic_Job_Board::uriSafe($sClassification->value) . '/' . $oClassificationAttributes->id . '/"  title="' . $sClassification->value . ' Jobs">' . $sClassification->value . '</a>' . '</li>';
								}
							}
						}

						$sJobClassificationBreadCrumbs = $sPositionList . '</ul>';

						if ($sJobClassificationBreadCrumbs == '<ul></ul>') {
							$sJobClassificationBreadCrumbs = '';
						}

						// Work Type link
						$oWorkTypeAttributes = $oJobPosting->workType->attributes();
						if ($wp_rewrite->using_permalinks()) {
							$sWorkTypeLink = '<a href="' . get_permalink($current_page_id) . 'query/WorkType/' . Adlogic_Job_Board::uriSafe($oJobPosting->workType->value) . '/' . $oWorkTypeAttributes->id . '/"  title="' . $oJobPosting->workType->value . ' Jobs">' . $oJobPosting->workType->value . '</a>';
						} else {
							$sWorkTypeLink = '<a href="' . get_permalink($current_page_id) . '&/WorkType/' . Adlogic_Job_Board::uriSafe($oJobPosting->workType->value) . '/' . $oWorkTypeAttributes->id . '/"  title="' . $oJobPosting->workType->value . ' Jobs">' . $oJobPosting->workType->value . '</a>';
						}

						$content_replacements_array = array(
							'{job_id}',
							'{job_link}',
							'{job_title}',
							'{job_description}',
							'{job_bulletpoints}',
							'{job_location_breadcrumbs}',
							'{job_classification_breadcrumbs}',
							'{job_worktype_link}',
							'{job_post_date}',
							'{job_close_date}',
							'{job_standout_logo}',
							'{total_results}',
							'{job_save}',
							'{job_enquiry_application_url}',
							'{job_costcenter}',
							'{job_salary_minimum}',
							'{job_salary_maximum}',
							'{job_salary_rate}',
							'{job_salary_additional_text}',
							'{job_reference}',
							'{job_is_fresh}'
						);
						$slicedLocationArray = array_slice($sLocationArray, 0, 2);
						
						
						$job_data_array = array(
							$oJobAttributes->ad_id,
							get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($slicedLocationArray)) . '/' . $oJobAttributes->ad_id . '/',
							$oJobPosting->JobTitle,
							$oJobPosting->JobDescription,
							$sBulletPointHtml,
							$sJobLocationBreadCrumbs,
							$sJobClassificationBreadCrumbs,
							$sWorkTypeLink,
							date($date_format, strtotime($oJobPosting->pubDate)),
							isset($oJobPosting->closeDate) ? date($date_format, strtotime($oJobPosting->closeDate)) :"" ,
							$oJobPosting->standOut->logoURL,
							count($search_results->JobPostings->JobPosting),
							(Adlogic_Job_Board_Users::isLoginEnabled() ? '<div id="save_job_id_' . $oJobAttributes->ad_id . '" class="ajb-save-job"></div>' : ''),
							(!empty($apiSettings['adlogic_custom_application_page']) ? get_permalink($apiSettings['adlogic_custom_application_page']) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($slicedLocationArray)) . '/' . $oJobAttributes->ad_id . '/' : ''),
							(string) $oJobPosting->costcenter->value,
							$oJobPosting->Salary->salaryMinimum,
							$oJobPosting->Salary->salaryMaximum,
							$oJobPosting->Salary->salaryRate,
							$oJobPosting->Salary->salaryAdditionalText,
							$oJobPosting->reference,
							Adlogic_Search_Shortcodes::isJobFresh($oJobPosting->pubDate)
						);

						// Parse Job Posted Dates where formats are included
						$aDateMatches = array();

						if (preg_match_all('/{job_post_date format="([^"]*)"}/im', $new_content, $aDateMatches) > 0) {
							foreach ($aDateMatches[0] as $i => $dateMatch) {
								$new_content = str_replace($dateMatch, date($aDateMatches[1][$i], strtotime($oJobPosting->pubDate)), $new_content);
							}
						}
						if (isset($oJobPosting->closeDate)){
							if (preg_match_all('/{job_close_date format="([^"]*)"}/im', $new_content, $closeDateMatches) > 0) {
								foreach ($closeDateMatches[0] as $i => $dateMatch) {
									$new_content = str_replace($dateMatch, date($closeDateMatches[1][$i], strtotime($oJobPosting->closeDate)), $new_content);
								}
							}
						} else {
							$new_content = str_replace($dateMatch, '', $new_content);

						}

						// Job Classification Parameter - "count" - limits number of returned classifications
						if (preg_match_all('/{job_classification_breadcrumbs count="([^"]*)"}/im', $new_content, $aClassificationMatches) > 0) {
							foreach ($aClassificationMatches[0] as $i => $classificationMatch) {
								// Getting the count for classifications
								$classificationCount = $aClassificationMatches[1][$i];
								// Setting loop counter
								$counter = 1;
								// Looping Classification Breadcrumbs
								$sPositionList = '<ul>';
								foreach ($oJobPosting->classifications->classification as $sClassification) {
									if ($counter > $classificationCount) {
										break;
									}
									$oClassificationAttributes = $sClassification->attributes();
									if (!empty($sClassification->value)) {
										if ($wp_rewrite->using_permalinks()) {
											$sPositionList  .= '<li><a href="' . get_permalink($current_page_id) . 'query/Industry/' . Adlogic_Job_Board::uriSafe($sClassification->value) . '/' . $oClassificationAttributes->id . '/"  title="' . $sClassification->value . ' Jobs">' . $sClassification->value . '</a>' . '</li>';
										} else {
											$sPositionList  .= '<li><a href="' . get_permalink($current_page_id) . '&/Industry/' . Adlogic_Job_Board::uriSafe($sClassification->value) . '/' . $oClassificationAttributes->id . '/"  title="' . $sClassification->value . ' Jobs">' . $sClassification->value . '</a>' . '</li>';
										}
									}
									$counter++;
								}

								$sJobClassificationBreadCrumbs = $sPositionList . '</ul>';
								if ($sJobClassificationBreadCrumbs == '<ul></ul>') {
									$sJobClassificationBreadCrumbs = '';
								}
								// Replace content tag
								$new_content = str_replace($classificationMatch, $sJobClassificationBreadCrumbs, $new_content);
							}
						}

						// Job Location Parameter - "count" - limits number of returned locations
						if (preg_match_all('/{job_location_breadcrumbs count="([^"]*)"}/im', $new_content, $aLocationMatches) > 0) {
							
							foreach ($aLocationMatches[0] as $i => $locationMatch) {
								// Getting the count for Location
								$locationCount = $aLocationMatches[1][$i];
								// Setting loop counter
								$counter = 1;
								if ($locationCount == '3'){
									$LocationCounterValue = 1;
									$locationCount = 3;
									
									// Location Breadcrumbs
									$sLocationList = '<ul>';
									
									for($staticLocationCounter=$locationCount; $staticLocationCounter>=0; --$staticLocationCounter){
										if ($staticLocationCounter > $locationCount) {
											break;
										}
										$oLocationAttributes = $sLocation->attributes();
										if (!empty($sLocation->value)) {
											if ($wp_rewrite->using_permalinks()) {
												$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . 'query/Location/' . Adlogic_Job_Board::uriSafe($sLocation[$staticLocationCounter]) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation[$staticLocationCounter] . '"> ' .$sLocation[$staticLocationCounter]. ' </a> '.' </li>';
											} else {
												$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . '&/Location/' . Adlogic_Job_Board::uriSafe($sLocation[$staticLocationCounter]) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation[$staticLocationCounter] . '">'.$sLocation[$staticLocationCounter].'</a>' . '</li>';
											}
										}
										$counter++;
									}

								}
								if ($locationCount == '2'){
									$LocationCounterValue = 1;
									$locationCount = 3;
									// Location Breadcrumbs
									$sLocationList = '<ul>';
									
									for($staticLocationCounter=$locationCount; $staticLocationCounter>=1; --$staticLocationCounter){
										if ($staticLocationCounter > $locationCount) {
											break;
										}
										$oLocationAttributes = $sLocation->attributes();
										if (!empty($sLocation->value)) {
											if ($wp_rewrite->using_permalinks()) {
												$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . 'query/Location/' . Adlogic_Job_Board::uriSafe($sLocation[$staticLocationCounter]) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation[$staticLocationCounter] . '"> ' .$sLocation[$staticLocationCounter]. ' </a> '.' </li>';
											} else {
												$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . '&/Location/' . Adlogic_Job_Board::uriSafe($sLocation[$staticLocationCounter]) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation[$staticLocationCounter] . '">'.$sLocation[$staticLocationCounter].'</a>' . '</li>';
											}
										}
										$counter++;
									}

								}
								if ($locationCount == '1'){
									$LocationCounterValue = 1;
									$locationCount = 2;
									
									// Location Breadcrumbs
									$sLocationList = '<ul>';
									for($staticLocationCounter=$locationCount; $staticLocationCounter<=4; $staticLocationCounter++){
										$oLocationAttributes = $sLocation->attributes();
										if (!empty($sLocation->value)) {
											if ($wp_rewrite->using_permalinks()) {
												$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . 'query/Location/' . Adlogic_Job_Board::uriSafe($sLocation[$staticLocationCounter]) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation[$staticLocationCounter] . '"> ' .$sLocation[$staticLocationCounter]. ' </a> '.' </li>';
											} else {
												$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . '&/Location/' . Adlogic_Job_Board::uriSafe($sLocation[$staticLocationCounter]) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation[$staticLocationCounter] . '">'.$sLocation[$staticLocationCounter].'</a>' . '</li>';
											}
										}
										$counter++;
									}
								}
								
								$sJobLocationBreadCrumbs = $sLocationList . '</ul>';
								if ($sJobLocationBreadCrumbs == '<ul></ul>') {
									$sJobLocationBreadCrumbs = '';
								}

								// Replace content tag
								$new_content = str_replace($locationMatch, $sJobLocationBreadCrumbs, $new_content);
							}
						}

						// Perform final content replacement
						$parsed_content .= str_replace($content_replacements_array, $job_data_array, $new_content);
					}
					return $parsed_content;
				}

				static function process_query($query_type = false, $query = null)
				{

					// check  if we've previously cached results
					if ((!empty(self::$cached_resultset))) {
						// Look up Cached Result Sets for matching queries
						if (isset(self::$cached_resultset[md5($query)])) {
							return self::$cached_resultset[md5($query)];
						}
					}

					// Get Settings from Wordpress
					$apiSettings = get_option('adlogic_api_settings');
					$searchSettings = get_option('adlogic_search_settings');
					$itemsPerPage = isset($searchSettings['adlogic_search_results_per_page']) ? $searchSettings['adlogic_search_results_per_page'] : 5;

					// Reset Query Vars
					self::$queryVars = null;

					$searchVars = explode('/', $_SERVER['QUERY_STRING']);
					$varArray = array();
					if ($query != null && empty($searchVars[0])) {
						$queryBlocks = explode(';', $query);
						foreach ($queryBlocks as $queryBlock) {
							$queryBlock = explode(':', trim($queryBlock), 2);
							if (is_array($queryBlock) && count($queryBlock) > 1) {
								list($searchParam, $searchValue) = $queryBlock;
								// loop through valid parameter list and set values
								switch (trim($searchParam)) {
									case 'recruiterId':
										$varArray['childrenRecruiterIds'] = array(trim($searchValue));
										break;
									case 'indId':
										$varArray['Industry'] = array(trim($searchValue));
										break;
									case 'locId':
										$varArray['Location'] = array(trim($searchValue));
										break;
									case 'wtId':
										$varArray['WorkType'] = array(trim($searchValue));
										break;
									case 'salary_type':
										$varArray['SalaryType'] = array(trim($searchValue));
										break;
									case 'salary_min':
										$varArray['SalaryMin'] = array(trim($searchValue));
										break;
									case 'salary_max':
										$varArray['SalaryMax'] = array(trim($searchValue));
										break;
									case 'salary_min':
										$varArray['SalaryMin'] = array(trim($searchValue));
										break;
									case 'costCenterId':
										$varArray['CostCenter'] = array(trim($searchValue));
										break;
									case 'orgUnit':
										$varArray['OrgUnit'] = array(trim($searchValue));
										break;
									case 'keyword':
										$varArray['Keywords'] = array(trim($searchValue));
										break;
									case 'from':
										$varArray['From'] = array(trim($searchValue));
										break;
									case 'to':
										$varArray['To'] = array(trim($searchValue));
										break;
									case 'listingType':
										$varArray['ListingType'] = array(trim($searchValue));
										break;
									case 'Country':
										$varArray['country'] = array(trim($searchValue));
										break;
									case 'State':
										$varArray['state'] = array(trim($searchValue));
										break;
									case 'Locality':
										$varArray['locality'] = array(trim($searchValue));
										break;
									case 'geoLocationJson':
										$varArray['geoLocationJson'] = array(trim($searchValue));
										break;
								}
							}
						}
					} else {
						$currentVar = null;
						$includedChildrenRecruiterIds = false;
						foreach ($searchVars as $searchVar) {
							if ($searchVar != '') {
								// Made url variable comparisons case-insensitive (Thanks Alex Brindal)
								switch (strtolower($searchVar)) {
									case 'industry':
									case 'location':
									case 'worktype':
									case 'salarytype':
									case 'salarymin':
									case 'salarymax':
									case 'page':
									case 'keywords':
									case 'costcenter':
									case 'orgunit':
									case 'listingtype':
									case 'childrenrecruiterids':
									case 'country':
									case 'state':
									case 'locality':
									case 'geolocationjson':
										if (strtolower($searchVar) == "childrenrecruiterids") {
											$includedChildrenRecruiterIds = true;
										}
										$currentVar = $searchVar;
										break;
									default:
										if (!empty($currentVar)) {
											$varArray[$currentVar][] = $searchVar;
										}
										break;
								}
							}
						}
						if (!$includedChildrenRecruiterIds) {
							// Children recruiter ID wasn't passed in the query, lets try and find it from the shortcode
							if ($query != null && !empty($query)) {
								$queryBlocks = explode(';', $query);
								foreach ($queryBlocks as $queryBlock) {
									$queryBlock = explode(':', trim($queryBlock), 2);
									if (is_array($queryBlock) && count($queryBlock) > 1) {
										list($searchParam, $searchValue) = $queryBlock;
										// loop through valid parameter list and set values
										switch (trim($searchParam)) {
											case 'recruiterId':
												$varArray['childrenRecruiterIds'] = array(trim($searchValue));
												break;
										}
									}
								}
							}
						}
					}

					if (!empty($varArray)) {
						foreach ($varArray as $key => $value) {
							// pick last value from array
							$value = array_pop($value);
							switch (strtolower($key)) {
								case 'childrenrecruiterids':
									self::$queryVars['childrenRecruiterIds'] = $value;
									break;
								case 'industry':
									self::$queryVars['indId'] = $value;
									break;
								case 'location':
									self::$queryVars['locId'] = $value;
									break;
								case 'worktype':
									self::$queryVars['wtId'] = $value;
									break;
								case 'salarytype':
									self::$queryVars['salaryType'] = $value;
									break;
								case 'salarymin':
									self::$queryVars['salaryMin'] = $value;
									break;
								case 'salarymax':
									self::$queryVars['salaryMax'] = $value;
									break;
								case 'page':
									self::$queryVars['from'] = ($value - 1) * $itemsPerPage + 1;
									self::$queryVars['to'] = (($value) * $itemsPerPage);
									break;
								case 'keywords':
									self::$queryVars['keyword'] = $value;
									break;
								case 'costcenter':
									self::$queryVars['costCenter'] = $value;
									break;
								case 'orgunit':
									self::$queryVars['orgUnit'] = $value;
									break;
								case 'from':
									self::$queryVars['from'] = $value;
									break;
								case 'to':
									self::$queryVars['to'] = $value;
									break;
								case 'listingtype':
									self::$queryVars['listingType'] = $value;
									break;
								case 'country':
									self::$queryVars['country'] = $value;
									break;
								case 'state':
									self::$queryVars['state'] = $value;
									break;
								case 'locality':
									self::$queryVars['locality'] = $value;
									break;
								case 'geolocationjson':
									self::$queryVars['geoLocationJson'] = $value;
									break;
							}
						}
					}
					self::$search_params = $varArray;

					// Instantiate Soap Client Object
					$oSoapClient = Adlogic_Job_Board::getSoapConnection();

					// Requires Jobs Class
					require_once(AJB_PLUGIN_PATH . '/lib/classes/jobSearch.class.php');
					$oJobSearch = new JobSearch($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

					/* Search Criteria */

					// Search Keyword
					$keyword = urldecode((isset(self::$queryVars['keyword']) && !empty(self::$queryVars['keyword'])) ?  self::$queryVars['keyword'] : '');
					// Salary Type
					$salaryType = ((isset(self::$queryVars['salaryType']) && !empty(self::$queryVars['salaryType'])) ?  self::$queryVars['salaryType'] : empty(self::$queryVars['salaryType']));
					// Minimum Salary
					$salaryMin = ((isset(self::$queryVars['salaryMin']) && !empty(self::$queryVars['salaryMin'])) ?  self::$queryVars['salaryMin'] : null);
					// Maximum Salary
					$salaryMax = ((isset(self::$queryVars['salaryMax']) && !empty(self::$queryVars['salaryMax'])) ?  self::$queryVars['salaryMax'] : null);

					// Cost Center Id
					$costCenterId = ((isset(self::$queryVars['costCenter']) && !empty(self::$queryVars['costCenter'])) ?  self::$queryVars['costCenter'] : null);
					// Organisation Unit
					$orgUnit = ((isset(self::$queryVars['orgUnit']) && !empty(self::$queryVars['orgUnit'])) ?  self::$queryVars['orgUnit'] : null);

					// From Page
					$from = ((isset(self::$queryVars['from']) && !empty(self::$queryVars['from'])) ?  self::$queryVars['from'] : 1);
					// To Page
					$to = ((isset(self::$queryVars['to']) && !empty(self::$queryVars['to'])) ?  self::$queryVars['to'] : $itemsPerPage);

					// The below should only happen if someone has clicked one of the location optionis in the search results template
					// Or they're searching from a different page (location data will be passed in the URL)
					$geoLocationObject = array();
					$countryObject = array();
					$stateObject = array();
					$localityObject = array();
					// geoLocationDTO.setSuburb(jsonobject.getString("name"));
					// geoLocationDTO.setSuburbLat(jsonobject.getString("lat"));
					// geoLocationDTO.setSuburbLng(jsonobject.getString("lng"));
					// geoLocationDTO.setState(jsonobject.getString("name"));
					// geoLocationDTO.setStateCode(jsonobject.getString("code"));
					// geoLocationDTO.setCountry(jsonobject.getString("name"));
					// geoLocationDTO.setCountryCode(jsonobject.getString("code"));

					// 		"[{"+
					// 	"\"name\": \"Blacktown\","+
					// 	"\"lat\": -33.771,"+
					// 	"\"lng\": 150.9063,"+
					// 	"\"type\": \"locality\""+
					// "}, {"+
					// 	"\"name\": \"New South Wales\","+
					// 	"\"code\": \"NSW\","+
					// 	"\"type\": \"administrative_area_level_1\""+
					// "}, {"+
					// 	"\"name\": \"Australia\","+
					// 	"\"code\": \"AU\","+
					// 	"\"type\": \"country\""+
					// "}, {"+
					// 	"\"name\": \"2148\","+
					// 	"\"type\": \"postal_code\""+
					// "}]";
					if (isset(self::$queryVars['country']) && !empty(self::$queryVars['country'])) {
						//[{"id":"Australia---AU","type":"country"},{"name":"New%20South%20Wales---NSW","type":"administrative_area_level_1"},{"name":"Rooty%20Hill---coord=-33.771543,150.84392200000002","type":"locality"}]
						if (is_numeric(self::$queryVars['country'])) {
							// It's numeric if someone has clicked on the location bread crumbs in the search page as we already know the ID.
							$countryObject["id"] = self::$queryVars['country'];
						} else {
							$exp = explode("---", self::$queryVars['country']);
							if (count($exp) == 2) {
								// 0 = Australia
								// 1 = AU
								$countryObject["name"] = $exp[0];
								$countryObject["code"] = $exp[1];
							}
						}
						$countryObject["type"] = "country";
						array_push($geoLocationObject, $countryObject);
					}
					if (isset(self::$queryVars['state']) && !empty(self::$queryVars['state'])) {
						if (is_numeric(self::$queryVars['state'])) {
							$stateObject["id"] = self::$queryVars['state'];
						} else {
							$exp = explode("---", self::$queryVars['state']);
							if (count($exp) == 2) {
								// 0 = New South Wales
								// 1 = NSW
								$stateObject["name"] = urldecode($exp[0]);
								$stateObject["code"] = urldecode($exp[1]);
							}
						}
						$stateObject["type"] = "administrative_area_level_1";
						array_push($geoLocationObject, $stateObject);
					}
					if (isset(self::$queryVars['locality']) && !empty(self::$queryVars['locality'])) {
						if (is_numeric(self::$queryVars['locality'])) {
							$localityObject["id"] = self::$queryVars['locality'];
						} else {
							$exp = explode("---", self::$queryVars['locality']);
							if (count($exp) == 2) {
								// 0 = Rooty Hill
								// 1 = lat=-33.771543,lng=150.84392200000002
								$localityObject["name"] = urldecode($exp[0]);
								$coords = explode(",", $exp[1]);
								if (count($coords) == 2) {
									//  0 = coord=-33.771543
									//  1 = 150.84392200000002
									$localityObject["lat"] = substr($coords[0], 6);
									$localityObject["lng"] = $coords[1];
								}
							}
						}
						$localityObject["type"] = "locality";
						array_push($geoLocationObject, $localityObject);
					}
					$geoLocationJson = ((isset(self::$queryVars['geoLocationJson']) && !empty(self::$queryVars['geoLocationJson'])) ?  self::$queryVars['geoLocationJson'] : null);

					if ($geoLocationJson == null && (isset($geoLocationObject) && !empty($geoLocationObject))) {
						$geoLocationJson = json_encode($geoLocationObject);
						self::$queryVars['geoLocationJson'] = json_encode($geoLocationObject);
					}

					// Build classification criteria parameters array (includes worktype, location, and industry)
					$aClassificationsCriteria = array();

					// Industry Id
					if ((isset(self::$queryVars['indId']) && !empty(self::$queryVars['indId']))) {
						$aClassificationsCriteria[] =  explode(',', self::$queryVars['indId']);
					}
					// Location Id
					if (!Adlogic_job_board::shouldUseNewAPI()) {
						if ((isset(self::$queryVars['locId']) && !empty(self::$queryVars['locId']))) {
							$aClassificationsCriteria[] =  explode(',', self::$queryVars['locId']);
						}
					} else {
						$suburbIds = ((isset(self::$queryVars['locId']) && !empty(self::$queryVars['locId'])) ?  self::$queryVars['locId'] : null);
					}


					// WorkType Id
					if ((isset(self::$queryVars['wtId']) && !empty(self::$queryVars['wtId']))) {
						$aClassificationsCriteria[] =  explode(',', self::$queryVars['wtId']);
					}

					$childrenRecruiterIds = array();
					if ((isset(self::$queryVars['childrenRecruiterIds']) && !empty(self::$queryVars['childrenRecruiterIds']))) {
						$childrenRecruiterIds = explode(',', self::$queryVars['childrenRecruiterIds']);
					}

					/* Set Variables from GET Url Vars for Jobs Class */
					$oJobSearch->set('keyword',					$keyword);
					$oJobSearch->set('classificationsCriteria',	$aClassificationsCriteria);
					$oJobSearch->set('salaryType',				$salaryType);
					$oJobSearch->set('salaryMax',				$salaryMax);
					$oJobSearch->set('salaryMin',				$salaryMin);
					$oJobSearch->set('from',					$from);
					$oJobSearch->set('to',						$to);
					$oJobSearch->set('costCenterId',			$costCenterId);
					$oJobSearch->set('orgUnit',					$orgUnit);
					$oJobSearch->set('childrenRecruiterIds',	$childrenRecruiterIds);
					$oJobSearch->set('geoLocationObject',			$geoLocationJson);
					$oJobSearch->set('suburbIds',			$suburbIds);

					// Get Job Search Results
					if ($query_type == 'archive') {
						$oJobSearchResults = $oJobSearch->getArchiveJobs();
					} else if (($query_type == 'intranet') || (isset(self::$queryVars['listingType']))) {
						$oJobSearch->set('internalExternal', self::$queryVars['listingType']);
						$oJobSearchResults = $oJobSearch->getIntranet();
					} else if ($query_type == 'global') {
						if ((isset($apiSettings['adlogic_joblogic_passphrase'])) && !empty($apiSettings['adlogic_joblogic_passphrase'])) {
							$oJobSearch->set('passphrase', $apiSettings['adlogic_joblogic_passphrase']);
							$oJobSearchResults = $oJobSearch->getForAllRecruiters();
						} else {
							if (isset($childrenRecruiterIds) && !empty($childrenRecruiterIds)) {
								$oJobSearchResults = $oJobSearch->getFiltered();
							} else {
								$oJobSearchResults = $oJobSearch->get();
							}
						}
					} else {
						if (isset($childrenRecruiterIds) && !empty($childrenRecruiterIds)) {
							$oJobSearchResults = $oJobSearch->getFiltered();
						} else {
							$oJobSearchResults = $oJobSearch->get();
						}
					}

					if ($oJobSearchResults) {
						if (!is_string($oJobSearchResults)) {
							// Add Results Per Page for front end pagination
							$oJobSearchResults->JobPostings->addAttribute('resultsPerPage', $itemsPerPage);
							$oJobSearchResults->JobPostings->addAttribute('resultsReturned', count($oJobSearchResults->JobPostings->JobPosting));

							// Get search attributes
							$oSearchResultsAttributes = $oJobSearchResults->JobPostings->attributes();
						}
					} else {
						$oJobSearchResults = array();
					}

					if ($query == null) {
						self::$cached_resultset = $oJobSearchResults;
					}

					return $oJobSearchResults;
				}

				static function search_pagination($atts, $content = '')
				{

					global $wp_rewrite;
					// Enqueue Javascript
					wp_enqueue_script('jquery-adlogic-searchPage');
					// Enqueue Stylesheet
					wp_enqueue_style('adlogic-search-page');

					$uniqueId = uniqid('adlogicSearchPagination_');

					if (isset($atts['query'])) {
						$query = $atts['query'];
						self::$cached_resultset = null;
					} else {
						$query = null;
					}

					if (isset($atts['childrenrecruiterids'])) {
						$query .= $atts['childrenrecruiterids'];
					}

					// Bind pagination to a search if available
					if (isset($atts['search_id'])) {
						$search_bind_id = '#' . $atts['search_id'];
					} else if (!empty($unique_search_id)) {
						$search_bind_id = self::$unique_search_id;
					} else {
						$search_bind_id = 'null';
					}

					// number of pages to display in bar at a time
					if (isset($atts['num_display_pages'])) {
						$num_display_pages = $atts['num_display_pages'];
					} else {
						$num_display_pages = '10';
					}

					if ((isset($atts['type'])) && ($atts['type'] == 'archive')) {
						$oJobSearchResults = self::process_query('archive', $query);
					} else if ((isset($atts['type'])) && ($atts['type'] == 'intranet')) {
						$oJobSearchResults = self::process_query('intranet', $query);
					} else if ((isset($atts['type'])) && ($atts['type'] == 'global')) {
						$oJobSearchResults = self::process_query('global', $query);
					} else {
						$oJobSearchResults = self::process_query(null, $query);
					}
					if (!is_string($oJobSearchResults)) {
						$oSearchResultsAttributes = $oJobSearchResults->JobPostings->attributes();
						$search_vars = self::$search_params;
						// Get Settings from Wordpress
						$searchSettings = get_option('adlogic_search_settings');
						$itemsPerPage = $searchSettings['adlogic_search_results_per_page'];

						ob_start();
						?>
									<script type="text/javascript">
										jQuery(document).ready(function($) {
											$('#<?php print $uniqueId; ?> .adlogic_pagination_ul').adlogicSearchPagination({
												bound_search: '<?php print $search_bind_id; ?>',
												results_count: <?php print $oSearchResultsAttributes['count']; ?>,
												current_page: <?php print((isset(self::$queryVars['from'])) ? ((self::$queryVars['from'] > 0) ? ((self::$queryVars['from'] - 1) / $itemsPerPage) : 0) : 0); ?>,
												items_per_page: <?php print $oSearchResultsAttributes['resultsPerPage']; ?>,
												num_display_pages: <?php print $num_display_pages; ?>
											});
										});
									</script>
									<?php
												$pagination_javascript = ob_get_contents();
												ob_end_clean();

												$pagination_html = '<div id="' . $uniqueId . '" class="adlogic_pagination_bar"><ul class="adlogic_pagination_ul">';
												$j = 1;
												if ($oSearchResultsAttributes['count'] > 0) {
													for ($i = 0; $i < $oSearchResultsAttributes['count']; $i = $i + $itemsPerPage) {
														$searchUrl = '';
														if (!empty($search_vars)) {
															foreach ($search_vars as $key => $value) {
																// pick last value from array
																switch ($key) {
																	case 'Industry':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'Location':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'WorkType':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'SalaryType':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'SalaryMin':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'SalaryMax':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'CostCenter':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'OrgUnit':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																	case 'Keywords':
																		$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																		break;
																}
															}
														}
														if ($wp_rewrite->using_permalinks()) {
															$pagination_html .= '<li style="text-indent: -9999px; float: left;"><a href="' . get_permalink() . 'query/' . $searchUrl . 'Page/' . $j . '">' . $j . '</a></li>';
														} else {
															$pagination_html .= '<li style="text-indent: -9999px; float: left;"><a href="' . get_permalink() . '&/' . $searchUrl . 'Page/' . $j . '">' . $j . '</a></li>';
														}
														$j++;
													}
												}

												$pagination_html .= '</ul></div><br clear="all">';

												return $pagination_javascript . $pagination_html;
											} else {
												return "";
											}
										}

										static function filtered_search_pagination($atts, $content = '')
										{

											global $wp_rewrite;
											// Enqueue Javascript
											wp_enqueue_script('jquery-adlogic-searchPage');
											// Enqueue Stylesheet
											wp_enqueue_style('adlogic-search-page');

											$uniqueId = uniqid('adlogicSearchPagination_');

											if (isset($atts['query'])) {
												$query = $atts['query'];
												self::$cached_resultset = null;
											} else {
												$query = null;
											}

											if (isset($atts['childrenrecruiterids'])) {
												$query .= $atts['childrenrecruiterids'];
											}

											// Bind pagination to a search if available
											if (isset($atts['search_id'])) {
												$search_bind_id = '#' . $atts['search_id'];
											} else if (!empty($unique_search_id)) {
												$search_bind_id = self::$unique_search_id;
											} else {
												$search_bind_id = 'null';
											}

											// number of pages to display in bar at a time
											if (isset($atts['num_display_pages'])) {
												$num_display_pages = $atts['num_display_pages'];
											} else {
												$num_display_pages = '10';
											}

											if ((isset($atts['type'])) && ($atts['type'] == 'archive')) {
												$oJobSearchResults = self::process_query('archive', $query);
											} else if ((isset($atts['type'])) && ($atts['type'] == 'intranet')) {
												$oJobSearchResults = self::process_query('intranet', $query);
											} else if ((isset($atts['type'])) && ($atts['type'] == 'global')) {
												$oJobSearchResults = self::process_query('global', $query);
											} else {
												$oJobSearchResults = self::process_query(null, $query);
											}

											$oSearchResultsAttributes = $oJobSearchResults->JobPostings->attributes();
											$search_vars = self::$search_params;

											// Get Settings from Wordpress
											$searchSettings = get_option('adlogic_search_settings');
											$itemsPerPage = $searchSettings['adlogic_search_results_per_page'];

											ob_start();
											?>
									<script type="text/javascript">
										jQuery(document).ready(function($) {
											$('#<?php print $uniqueId; ?> .adlogic_pagination_ul').adlogicSearchPagination({
												bound_search: '<?php print $search_bind_id; ?>',
												results_count: <?php print $oSearchResultsAttributes['count']; ?>,
												current_page: <?php print((isset(self::$queryVars['from'])) ? ((self::$queryVars['from'] > 0) ? ((self::$queryVars['from'] - 1) / $itemsPerPage) : 0) : 0); ?>,
												items_per_page: <?php print $oSearchResultsAttributes['resultsPerPage']; ?>,
												num_display_pages: <?php print $num_display_pages; ?>
											});
										});
									</script>
									<?php
											$pagination_javascript = ob_get_contents();
											ob_end_clean();

											$pagination_html = '<div id="' . $uniqueId . '" class="adlogic_pagination_bar"><ul class="adlogic_pagination_ul">';
											$j = 1;
											if ($oSearchResultsAttributes['count'] > 0) {
												for ($i = 0; $i < $oSearchResultsAttributes['count']; $i = $i + $itemsPerPage) {
													$searchUrl = '';
													if (!empty($search_vars)) {
														foreach ($search_vars as $key => $value) {
															// pick last value from array
															switch ($key) {
																case 'Industry':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'Location':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'WorkType':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'SalaryType':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'SalaryMin':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'SalaryMax':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'CostCenter':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'OrgUnit':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
																case 'Keywords':
																	$searchUrl .= Adlogic_Job_Board::uriSafe($key . '/' . implode('/', $value) . '/');
																	break;
															}
														}
													}
													if ($wp_rewrite->using_permalinks()) {
														$pagination_html .= '<li style="text-indent: -9999px; float: left;"><a href="' . get_permalink() . 'query/' . $searchUrl . 'Page/' . $j . '">' . $j . '</a></li>';
													} else {
														$pagination_html .= '<li style="text-indent: -9999px; float: left;"><a href="' . get_permalink() . '&/' . $searchUrl . 'Page/' . $j . '">' . $j . '</a></li>';
													}
													$j++;
												}
											}

											$pagination_html .= '</ul></div><br clear="all">';

											return $pagination_javascript . $pagination_html;
										}

										static function search_breadcrumbs($atts, $content = '')
										{
											$cacheSettings = get_option('adlogic_cache_settings');

											// Enqueue Javascript
											wp_enqueue_script('jquery-adlogic-searchPage');
											// Enqueue Stylesheet
											wp_enqueue_style('adlogic-search-page');

											$apiSettings = get_option('adlogic_api_settings');

											$uniqueId = uniqid('adlogicSearchBreadcrumbs_');
											global $wp_rewrite;

											if (isset($atts['query'])) {
												$query = $atts['query'];
												self::$cached_resultset = null;
											} else {
												$query = null;
											}

											// Bind pagination to a search if available
											if (isset($atts['search_id'])) {
												$search_bind_id = '#' . $atts['search_id'];
											} else if (!empty($unique_search_id)) {
												$search_bind_id = self::$unique_search_id;
											} else {
												$search_bind_id = 'null';
											}

											if (empty($atts)) {
												$atts['template'] = 'base';
											}

											if (isset($atts['query'])) {
												$query = $atts['query'];
												self::$cached_resultset = null;
											} else {
												$query = null;
											}

											ob_start();
											?>
										<script type="text/javascript">
											jQuery(document).ready(function($) {
												$('#<?php print $uniqueId; ?>.adlogic_search_breadcrumbs').adlogicSearchBreadcrumbs({
													bound_search: '<?php print $search_bind_id; ?>',
													template: $('#<?php print $uniqueId; ?> .search_breadcrumbs_template').html()
												});
											});
										</script>
										<?php
												$breadcrumbs_javascript = ob_get_contents();
												ob_end_clean();
												$breadcrumbs_html = $breadcrumbs_javascript . '<div id="' . $uniqueId . '" class="adlogic_search_breadcrumbs">';

												switch ($atts['template']) {
													case 'custom':
														break;
													case 'base':
													default:
														if (!empty($atts['template'])) {
															if (defined('MULTISITE') && (MULTISITE == true)) {
																if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_breadcrumbs/' . $atts['template'] . '.html')) {
																	$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_breadcrumbs/' . $atts['template'] . '.html');
																} else if (is_file(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/search_breadcrumbs/' . $atts['template'] . '.html')) {
																	$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/search_breadcrumbs/' . $atts['template'] . '.html');
																} else if (is_file(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/' . $atts['template'] . '.html')) {
																	$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/' . $atts['template'] . '.html');
																} else {
																	$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/default.html');
																}
															} else {
																if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_breadcrumbs/' . $atts['template'] . '.html')) {
																	$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/search_breadcrumbs/' . $atts['template'] . '.html');
																} else if (is_file(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/' . $atts['template'] . '.html')) {
																	$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/' . $atts['template'] . '.html');
																} else {
																	$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/default.html');
																}
															}
														} else {
															$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/search_breadcrumbs/default.html');
														}

														break;
												}
												// Content Replacements
												if (empty(self::$cached_resultset)) {
													self::process_query(null, $query);
												}

												// Required for search query parameters
												require_once(AJB_PLUGIN_PATH . '/lib/classes/location.class.php');
												require_once(AJB_PLUGIN_PATH . '/lib/classes/industry.class.php');
												require_once(AJB_PLUGIN_PATH . '/lib/classes/worktype.class.php');

												// Instantiate Soap Client Object
												$oSoapClient = Adlogic_Job_Board::getSoapConnection();

												// Get Location Query Search Parameters
												$oLocations = new Location($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);
												$cache_file = 'location_cache.xml';

												$locationsSearched = array();
												if (isset(self::$queryVars['locId']) && !empty(self::$queryVars['locId'])) {
													// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
													if ($cacheSettings['adlogic_cache_status'] == 'true') {
														if (Adlogic_Job_Board::cache_check($cache_file)) {
															$locationsXML = Adlogic_Job_Board::cache_read($cache_file);
															$locationsResult = $oLocations->getFromXML($locationsXML);
														} else {
															$locationsXML = $oLocations->get(true);
															Adlogic_Job_Board::cache_store($cache_file, $locationsXML);
															$locationsResult = $oLocations->getFromXML($locationsXML);
														}
													} else {
														$locationsResult = $oLocations->get();
													}

													$aLocationIds = explode(',', self::$queryVars['locId']);
													foreach ($aLocationIds as $locationId) {
														foreach ($locationsResult as $locationResult) {
															if ($locationId == $locationResult->id) {
																$locationsSearched[] = $locationResult;
															}
														}
													}
												}

												// Get Classification Query Search Parameters
												$oIndustries = new Industry($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);
												$cache_file = 'industry_cache.xml';
												$classificationsSearched = array();
												if (isset(self::$queryVars['indId']) && !empty(self::$queryVars['indId'])) {
													// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
													if ($cacheSettings['adlogic_cache_status'] == 'true') {
														if (Adlogic_Job_Board::cache_check($cache_file)) {
															$industriesXML = Adlogic_Job_Board::cache_read($cache_file);
															$industriesResult = $oIndustries->getFromXML($industriesXML);
														} else {
															$industriesXML = $oIndustries->get(true);
															Adlogic_Job_Board::cache_store($cache_file, $industriesXML);
															$industriesResult = $oIndustries->getFromXML($industriesXML);
														}
													} else {
														$industriesResult = $oIndustries->get();
													}

													$aClassificationIds = explode(',', self::$queryVars['indId']);
													foreach ($aClassificationIds as $classificationId) {
														foreach ($industriesResult as $industryResult) {
															if ($classificationId == $industryResult->id) {
																$classificationsSearched[] = $industryResult;
															}
														}
													}
												}

												// Get Worktype Query Search Parameters
												$oWorktypes = new Worktype($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);
												$cache_file = 'worktype_cache.xml';
												$workTypesSearched = array();
												if (isset(self::$queryVars['wtId']) && !empty(self::$queryVars['wtId'])) {
													// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
													if ($cacheSettings['adlogic_cache_status'] == 'true') {
														if (Adlogic_Job_Board::cache_check($cache_file)) {
															$worktypesXML = Adlogic_Job_Board::cache_read($cache_file);
															$worktypesResult = $oWorktypes->getFromXML($worktypesXML);
														} else {
															$worktypesXML = $oWorktypes->get(true);
															Adlogic_Job_Board::cache_store($cache_file, $worktypesXML);
															$worktypesResult = $oWorktypes->getFromXML($worktypesXML);
														}
													} else {
														$worktypesResult = $oWorktypes->get();
													}

													$aWorkTypeIds = explode(',', self::$queryVars['wtId']);
													foreach ($aWorkTypeIds as $workTypeId) {
														foreach ($worktypesResult as $worktypeResult) {
															if ($workTypeId == $worktypeResult->id) {
																$workTypesSearched[] = $worktypeResult;
															}
														}
													}
												}

												$resultsAttributes = self::$cached_resultset->JobPostings->attributes();

												$new_content = $content;

												$content_replacements_array = array(
													'{classification}',
													'{location}',
													'{worktype}',
													'{keywords}',
													'{salary_minimum}',
													'{salary_maximum}',
													'{salary_type}',
													'{current_page}',
													'{total_pages}',
													'{results_per_page}',
													'{from_results}',
													'{to_results}',
													'{count_results}',
													'{total_results}'
												);

												if (count($classificationsSearched)) {
													$classificationsString = '';
													foreach ($classificationsSearched as $i => $classificationSearched) {
														if ($i != 0) {
															$classificationsString .= ', ';
														};
														if ($wp_rewrite->using_permalinks()) {
															$classificationsString .= '<a href="' . get_permalink() . 'query/Industry/' . Adlogic_Job_Board::uriSafe($classificationSearched->searchName) . '/' . $classificationSearched->id . '"  title="' . $classificationSearched->displayName . ' Jobs">' . $classificationSearched->displayName . '</a>';
														} else {
															$classificationsString .= '<a href="' . get_permalink() . '&/Industry/' . Adlogic_Job_Board::uriSafe($classificationSearched->searchName) . '/' . $classificationSearched->id . '"  title="' . $classificationSearched->displayName . ' Jobs">' . $classificationSearched->displayName . '</a>';
														}
													}
												} else {
													$classificationsString = 'All Classifications';
												}

												if (count($locationsSearched)) {
													$locationsString = '';
													foreach ($locationsSearched as $i => $locationsSearched) {
														if ($i != 0) {
															$locationsString .= ', ';
														};
														if ($wp_rewrite->using_permalinks()) {
															$locationsString .= '<a href="' . get_permalink() . 'query/Industry/' . Adlogic_Job_Board::uriSafe($locationsSearched->searchName) . '/' . $locationsSearched->id . '"  title="' . $locationsSearched->displayName . ' Jobs">' . $locationsSearched->displayName . '</a>';
														} else {
															$locationsString .= '<a href="' . get_permalink() . '&/Industry/' . Adlogic_Job_Board::uriSafe($locationsSearched->searchName) . '/' . $locationsSearched->id . '"  title="' . $locationsSearched->displayName . ' Jobs">' . $locationsSearched->displayName . '</a>';
														}
													}
												} else {
													$locationsString = 'All Locations';
												}

												if (count($workTypesSearched)) {
													$worktypesString = '';
													foreach ($workTypesSearched as $i => $workTypeSearched) {
														if ($i != 0) {
															$worktypesString .= ', ';
														};
														if ($wp_rewrite->using_permalinks()) {
															$worktypesString .= '<a href="' . get_permalink() . 'query/Industry/' . Adlogic_Job_Board::uriSafe($workTypeSearched->searchName) . '/' . $workTypeSearched->id . '"  title="' . $workTypeSearched->displayName . ' Jobs">' . $workTypeSearched->displayName . '</a>';
														} else {
															$worktypesString .= '<a href="' . get_permalink() . '&/Industry/' . Adlogic_Job_Board::uriSafe($workTypeSearched->searchName) . '/' . $workTypeSearched->id . '"  title="' . $workTypeSearched->displayName . ' Jobs">' . $workTypeSearched->displayName . '</a>';
														}
													}
												} else {
													$worktypesString = 'All Work Types';
												}

												$salaryType = (isset(self::$queryVars['salaryType']) ? self::$queryVars['salaryType'] : null);
												switch ($salaryType) {
													case 'HourlyRate':
														$salaryMin = (self::$queryVars['salaryMin']);
														$salaryMax = (self::$queryVars['salaryMin']);
														$salaryTypeDesc = 'hour';
														break;
													case 'AnnualPackage':
														$salaryMin = (self::$queryVars['salaryMin'] / 1000) . 'K';
														$salaryMax = (self::$queryVars['salaryMax'] / 1000) . 'K';
														$salaryTypeDesc = 'year';
														break;
													default:
														$salaryMin = 'N/A';
														$salaryMax = 'N/A';
														$salaryTypeDesc = 'N/A';
														break;
												}

												$search_data_array = array(
													$classificationsString,
													$locationsString,
													$worktypesString,
													(self::$queryVars['keyword'] ? self::$queryVars['keyword'] : 'N/A'),
													$salaryMin,
													$salaryMax,
													$salaryTypeDesc,
													((isset(self::$queryVars['from'])) ? ((self::$queryVars['from'] > 0) ? (((self::$queryVars['from'] - 1) / $resultsAttributes->resultsPerPage) + 1) : 1) : 1),
													ceil($resultsAttributes->count / $resultsAttributes->resultsPerPage),
													$resultsAttributes->resultsPerPage,
													(self::$queryVars['from'] ? self::$queryVars['from'] : '1'),
													(self::$queryVars['to'] ? (((int) self::$queryVars['to'] > (int) $resultsAttributes->count) ? $resultsAttributes->count : self::$queryVars['to']) : (((int) $resultsAttributes->resultsPerPage > (int) $resultsAttributes->count) ? $resultsAttributes->count : $resultsAttributes->resultsPerPage)),
													$resultsAttributes->resultsReturned,
													$resultsAttributes->count
												);

												// Translation Replacements
												if (preg_match_all('/{(classification|worktype|location) translation="([^"]*)"}/im', $new_content, $aTranslationMatches) > 0) {
													foreach ($aTranslationMatches[0] as $i => $translationMatch) {
														switch ($aTranslationMatches[1][$i]) {
															case 'classification':
																$aTranslationMatches[1][$i] = (($classificationsString == 'All Classifications') ? 'All ' . $aTranslationMatches[2][$i] : $classificationsString);
																break;
															case 'location':
																$aTranslationMatches[1][$i] = (($locationsString == 'All Locations') ? 'All ' . $aTranslationMatches[2][$i] : $locationsString);
																break;
															case 'worktype':
																$aTranslationMatches[1][$i] = (($worktypesString == 'All Work Types') ? 'All ' . $aTranslationMatches[2][$i] : $worktypesString);
																break;
														}
														$new_content = str_replace($translationMatch, $aTranslationMatches[1][$i], $new_content);
													}
												}
												$new_content = str_replace($content_replacements_array, $search_data_array, $new_content);

												$breadcrumbs_html .= $new_content . '<div class="search_breadcrumbs_template" style="display:none;">' . $content . '</div></div>';
												return $breadcrumbs_html;
											}

											static function isJobFresh($postTime)
											{
												date_default_timezone_set('Australia/Sydney');
												$searchSettings = get_option('adlogic_search_settings');
												$postStr = strtotime($postTime);
												$freshPeriod = (isset($searchSettings['adlogic_ad_fresh_period']) && !empty($searchSettings['adlogic_ad_fresh_period']) ? $searchSettings['adlogic_ad_fresh_period'] : 0);
												$freshPeriodInHours = 2;
												if (ctype_digit($freshPeriod) && (int) $freshPeriod > 0) {
													$freshPeriodInHours = $freshPeriod;
												}
												$current = strtotime("-" . $freshPeriodInHours . " hours");
												if ($postStr >= $current) {
													// Posted in the last 2 hours.
													return "<span class='ajb-job-ad-fresh'>New</span>";
												} else {
													return "";
												}
											}
										}
										
										?>
