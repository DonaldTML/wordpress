<?php
class Adlogic_Applied_History_Shortcodes {
	static $AppliedHistory;
	static $cached_resultset;
	static $search_params;
	static $applied_jobs_id;
	static $queryVars;

	function init() {
		if (Adlogic_Job_Board::check_setup() == true) {
			// Initialise Shortcodes
			add_shortcode( 'adlogic_applied_history', array('Adlogic_Applied_History_Shortcodes', 'applied_jobs'));
			add_shortcode( 'adlogic_applied_count', array('Adlogic_Applied_History_Shortcodes', 'applied_jobs_count'));
			add_shortcode( 'adlogic_applied_pagination', array('Adlogic_Applied_History_Shortcodes', 'search_pagination'));
			// Add Hook to add meta if necessary 
			if (!is_admin()) {
				add_action('the_posts', array('Adlogic_Applied_History_Shortcodes', 'check_shortcode'));
			}
		}

		if (is_admin()) {
			// Add TinyMCE Editor Buttons for Page Editor
			Adlogic_Job_Board::add_page_editor_button('ajbAppliedHistory', 'jquery.tinyMCEapplyHistoryButton.js');
			
		}
	}
	function check_shortcode($posts) {
		global $shortcode_tags;
		if (!empty($posts)) {
			$pattern = get_shortcode_regex();
	
			foreach ($posts as $post) {
				preg_match_all( "/$pattern/s", $post->post_content, $matches );

				/**
				 * $matches[2] matches the shortcode name
				 * @see do_shortcode_tag()
				 */
				foreach ($matches[2] as $match) {
					if ($match == 'adlogic_applied_history') {
						remove_action( 'wp_head', 'rel_canonical' );
						break 2;
					}
				}
			}
		}
		return $posts;
	}

	function applied_jobs($atts, $content = '') {	
		// Are they logged in?
		if (!Adlogic_Job_Board_Users::isLoggedIn()) {
			return 'You are currently not signed in. Click <a href="javascript:void(0);" onclick="adlogicJobSearch.sessionManager.adlogicSessionManager(\'showDialog\');">here</a> to sign in.';
		}
		// Enqueue Stylesheet
		wp_enqueue_style( 'adlogic-applied-history' );	

		// Enqueue Javascript
		wp_enqueue_script( 'jquery-adlogic-appliedHistory' );

		// Get the class
		require_once(AJB_PLUGIN_PATH . '/lib/classes/applicationHistory.class.php');

		// Get the SOAP connection, it'll be needed later.
		self::$AppliedHistory = $AppliedHistory = new AppliedHistory(Adlogic_Job_Board::getSoapConnection());
		
		$searchSettings = get_option('adlogic_search_settings');
		$apiSettings = get_option('adlogic_api_settings');
		$itemsPerPage = $searchSettings['adlogic_search_results_per_page'];

		if (isset($atts['applied_jobs_id'])&& !empty($atts['applied_jobs_id'])) {
			self::$applied_jobs_id = $uniqueId = $atts['applied_jobs_id'];
		} else {
			self::$applied_jobs_id = $uniqueId = uniqid('adlogicAppliedJobs_');
		}

		if (isset($atts['query'])) {
			$query = $atts['query'];
			self::$cached_resultset = null;
		} else {
			$query = null;
		}
		
		if ((isset($atts['type'])) && ($atts['type'] == 'archive')) {
			$oAppliedJobsResults = self::process_query('archive', $query);
		} else {
			$oAppliedJobsResults = self::process_query(null, $query);
			#$search_results = $AppliedHistory->getHistoricJobs();
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
						$('#<?php print $uniqueId; ?>').adlogicAppliedHistory({
							ajaxServer: adlogicJobSearch.ajaxurl + '?action=getSavedJobs&page_id=<?php print get_the_ID(); ?>',
							template: $('#<?php print $uniqueId; ?> .applied_jobs_template').html()
						});
					});
			</script>
		<?php
		$saved_jobs_javascript = ob_get_contents();
		ob_end_clean();

		if (empty($atts)) {
			$atts['template'] = 'base';
		}


switch ($atts['template']) {
			case 'custom':
				$new_content = self::parse_search($oAppliedJobsResults, $content, $date_format);
				break;
			case 'base':
			default:
				if (!empty($atts['template'])) {
					if (defined('MULTISITE') && (MULTISITE == true)) {
						if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/applied-jobs/' . $atts['template'] . '.html')) {
							$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/applied-jobs/' . $atts['template'] . '.html');
						} else if (is_file(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/applied-jobs/' . $atts['template'] . '.html')) {
							$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/applied-jobs/' . $atts['template'] . '.html');
						} else if (is_file(AJB_PLUGIN_PATH . '/templates/applied-jobs/' . $atts['template'] . '.html')) {
							$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/applied-jobs/' . $atts['template'] . '.html');
						} else {
							$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/applied-jobs/default.html');
						}
					} else {
						if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/applied-jobs/' . $atts['template'] . '.html')) {
							$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/applied-jobs/' . $atts['template'] . '.html');
						} else if (is_file(AJB_PLUGIN_PATH . '/templates/applied-jobs/' . $atts['template'] . '.html')) {
							$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/applied-jobs/' . $atts['template'] . '.html');
						} else {
							$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/applied-jobs/default.html');
						}
					}
				} else {
					$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/applied-jobs/default.html');
				}

				$new_content = self::parse_search($oAppliedJobsResults, $content, $date_format);
				break;
		}

		return $saved_jobs_javascript . '<div id="' . $uniqueId . '" class="adlogic_saved_jobs">' . $new_content . '<div class="saved_jobs_template" style="display:none;">' . $content . '</div>'. '</div>';








		// Shortcode attributes
		// Later on when templates could be done.
		/*extract(shortcode_atts(array(
			'template'	=>	'base'
		), $att, 'template' ));

		if(isset($template))
		{
			if(is_dir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/applied-history/'))
			{
				if(file_exists(get_stylesheet_directory() . '/css/adlogic-job-board/templates/applied-history/' . $template . '.html'))
				{
					$file = file_get_contents(get_stylesheet_directory() . '/css/adlogic-job-board/templates/applied-history/' . $template . '.html');
					$AppliedHistory->template = $template;
				} else {
					echo "File doesn't";
				}
			} else {
				echo "dir doesn't";
			}
		} else {
			echo "template isn't set";
		}*/

		$searchSettings = get_option('adlogic_api_settings');


	}

	function applied_jobs_count($att, $content = '') {
		// Get the class
		require_once(AJB_PLUGIN_PATH . '/lib/classes/applicationHistory.class.php');

		// Get the SOAP Connection
		$oSoapClient = Adlogic_Job_Board::getSoapConnection();
		self::$AppliedHistory = $AppliedHistory = new AppliedHistory(Adlogic_Job_Board::getSoapConnection());

		// Gets the total of jobs
		$AppliedHistory->countHistoricJobs();
	}

	function search_pagination($atts, $content = '') {
		// Get the class
		require_once(AJB_PLUGIN_PATH . '/lib/classes/applicationHistory.class.php');
		global $wp_rewrite;
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ba-bbq' );
		wp_enqueue_script( 'jquery-blockUI' );
		wp_enqueue_script( 'jquery-pagination' );
		wp_enqueue_script( 'jquery-adlogic-searchPage' );

		wp_enqueue_style( 'adlogic-search-page' );

		$uniqueId = uniqid('adlogicSearchPagination_');

		if (isset($atts['query'])) {
			$query = $atts['query'];
			self::$cached_resultset = null;
		} else {
			$query = null;
		}

		// Bind pagination to a search if available
		if (isset($atts['search_id']) ) {
			$search_bind_id = '#' . $atts['search_id'];
		} else if (!empty($unique_search_id)) {
			$search_bind_id = self::$unique_search_id;
		} else {
			$search_bind_id = 'null';
		}

		// number of pages to display in bar at a time
		if (isset($atts['num_display_pages']) ) {
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

		$oSearchResultsAttributes = $oJobSearchResults->Advertiser->Applicant->JobPostings->attributes();
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
							current_page: <?php print ((isset(self::$queryVars['from'])) ? ((self::$queryVars['from']>0) ? ((self::$queryVars['from']-1)/$itemsPerPage) : 0): 0); ?>,
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
			for ($i=0; $i<$oSearchResultsAttributes['count']; $i = $i+$itemsPerPage) {
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

	static function process_query( $query_type = false, $query = null ) {

		// check  if we've previously cached results
		if ((!empty(self::$cached_resultset)) && ($query == null)) {
			return self::$cached_resultset;
		}

		// Get Settings from Wordpress
		$apiSettings = get_option('adlogic_api_settings');
		$searchSettings = get_option('adlogic_search_settings');

		require_once(AJB_PLUGIN_PATH . '/lib/classes/applicationHistory.class.php');

		$oSoapClient = Adlogic_Job_Board::getSoapConnection();

		self::$AppliedHistory = $AppliedHistory = new AppliedHistory(Adlogic_Job_Board::getSoapConnection());
		
		if ($query_type == 'archive') {
			$historicJobCriteria = $_SESSION['adlogicUserSession'];
			$aAppliedHistoryJobs = $AppliedHistory->getHistoricJobs($historicJobCriteria);
		} else {
			$historicJobCriteria = $_SESSION['adlogicUserSession'];
			$aAppliedHistoryJobs = $AppliedHistory->getHistoricJobs($historicJobCriteria);
		}
		


		if ($aAppliedHistoryJobs) {
			$aAppliedJobsAttributes = $aAppliedHistoryJobs->Advertiser->Applicant->JobPostings->attributes();
		} else {
			$aAppliedJobsAttributes = array();
		}

		if ($query == null) {
			self::$cached_resultset = $aAppliedHistoryJobs;
		}

		return $aAppliedHistoryJobs;



		/* Set Class variables */
		/*if (Adlogic_Job_Board_Users::isLoggedIn()) {
			$oSavedJobs->set('recruiterId',  $apiSettings['adlogic_recruiter_id']); // optional, but restricts results returned
			$oSavedJobs->set('advertiserId', $apiSettings['adlogic_advertiser_id']); // optional, but restricts results returned
			$oSavedJobs->set('sessionHash', $_SESSION['adlogicUserSession']);
			
		} else {
			return array();
		}
		*/

	}
	static function parse_search($search_results, $content, $date_format = null, $current_page_id = null) {

		global $wp_rewrite;

		if ($current_page_id == null) {
			$current_page_id = get_the_ID();
		}

		$parsed_content = '';
		$searchSettings = get_option('adlogic_search_settings');
		$job_details_page_id = $searchSettings['adlogic_job_details_page'];
		$date_format = (!empty($date_format) ? $date_format : (isset($searchSettings['adlogic_search_results_date_format']) ? $searchSettings['adlogic_search_results_date_format'] : 'd/m/Y'));

		if (!Adlogic_Job_Board_Users::isLoggedIn()) {
			return 'You are currently not signed in. Click <a href="javascript:void(0);" onclick="adlogicJobSearch.sessionManager.adlogicSessionManager(\'showDialog\');">here</a> to sign in.';
		}

		if (count($search_results->Advertiser->Applicant->JobPostings->JobPosting) == 0) {
			return '<div class="adlogic_appled_jobs_no_results">You have not applied for any jobs!</div>';
		}

		foreach ($search_results->Advertiser->Applicant->JobPostings->JobPosting as $oJobPosting) {
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
			foreach ($oJobPosting->locations->location as $sLocation) {
				$oLocationAttributes = $sLocation->attributes()->id;
				$sLocationArray[] = $sLocation;
				if (!empty($sLocation)) {
					if ($wp_rewrite->using_permalinks()) {
						$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . 'query/Location/' . Adlogic_Job_Board::uriSafe($sLocation) . '/' . $oLocationAttributes . '/" title="Jobs in ' . $sLocation . '">' . $sLocation . '</a>' . '</li>';
					} else {
						$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . '&/Location/' . Adlogic_Job_Board::uriSafe($sLocation) . '/' . $oLocationAttributes . '/" title="Jobs in ' . $sLocation . '">' . $sLocation . '</a>' . '</li>';
					}
				}
			}
			$sJobLocationBreadCrumbs = $sLocationList . '</ul>';

			if ($sJobLocationBreadCrumbs == '<ul></ul>') {
				$sJobLocationBreadCrumbs = '';
			}

			// Classification Breadcrumbs
			$sPositionList = '<ul>';
			foreach ($oJobPosting->classifications->classification as $sClassification) {
				$oClassificationAttributes = $sClassification->attributes()->id;
				if (!empty($sClassification)) {
					if ($wp_rewrite->using_permalinks()) {
						$sPositionList  .= '<li><a href="' . get_permalink($current_page_id) . 'query/Industry/' . Adlogic_Job_Board::uriSafe($sClassification) . '/' . $oClassificationAttributes . '/"  title="' . $sClassification . ' Jobs">' . $sClassification . '</a>' . '</li>';
					} else {
						$sPositionList  .= '<li><a href="' . get_permalink($current_page_id) . '&/Industry/' . Adlogic_Job_Board::uriSafe($sClassification) . '/' . $oClassificationAttributes . '/"  title="' . $sClassification . ' Jobs">' . $sClassification . '</a>' . '</li>';
					}
				}
			}

			$sJobClassificationBreadCrumbs = $sPositionList . '</ul>';

			if ($sJobClassificationBreadCrumbs == '<ul></ul>') {
				$sJobClassificationBreadCrumbs = '';
			}

			// Work Type link
			$oWorkTypeAttributes = $oJobPosting->workType->attributes()->id; 
			if ($wp_rewrite->using_permalinks()) {
				$sWorkTypeLink = '<a href="' . get_permalink($current_page_id) . 'query/WorkType/' . Adlogic_Job_Board::uriSafe($oJobPosting->workType) . '/' .$oWorkTypeAttributes . '/"  title="' . $oJobPosting->workType . ' Jobs">' . $oJobPosting->workType . '</a>';
			} else {
				$sWorkTypeLink = '<a href="' . get_permalink($current_page_id) . '&/WorkType/' . Adlogic_Job_Board::uriSafe($oJobPosting->workType) . '/' .$oWorkTypeAttributes . '/"  title="' . $oJobPosting->workType . ' Jobs">' . $oJobPosting->workType . '</a>';
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
				'{job_standout_logo}',
				'{total_results}',
				'{job_save}',
				'{job_apply_date}'
			);
			$slicedLocationArray = array_slice($sLocationArray,0,2);
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
				$oJobPosting->standOut->logoURL,
				count($search_results->Advertiser->Applicant->JobPostings->JobPosting),
				( Adlogic_Job_Board_Users::isLoginEnabled() ? '<div id="save_job_id_' . $oJobAttributes->ad_id . '" class="ajb-save-job"></div>' : '' ),
				$oJobPosting->applyDate
			);

			// Parse Job Posted Dates where formats are included
			$aDateMatches = array();

			if (preg_match_all('/{job_post_date format="([^"]*)"}/im', $new_content, $aDateMatches) > 0) {
				foreach ($aDateMatches[0] as $i => $dateMatch) {
					$new_content = str_replace($dateMatch, date($aDateMatches[1][$i], strtotime($oJobPosting->pubDate)), $new_content);
				}
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
					// Getting the count for classifications
					$locationCount = $aLocationMatches[1][$i];
					// Setting loop counter
					$counter = 1;

					// Location Breadcrumbs
					$sLocationList = '<ul>';
					foreach ($oJobPosting->locations->location as $sLocation) {
						if ($counter > $locationCount) {
							break;
						}
						$oLocationAttributes = $sLocation->attributes();
						if (!empty($sLocation->value)) {
							if ($wp_rewrite->using_permalinks()) {
								$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . 'query/Location/' . Adlogic_Job_Board::uriSafe($sLocation->value) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation->value . '">' . $sLocation->value . '</a>' . '</li>';
							} else {
								$sLocationList .= '<li><a href="' . get_permalink($current_page_id) . '&/Location/' . Adlogic_Job_Board::uriSafe($sLocation->value) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation->value . '">' . $sLocation->value . '</a>' . '</li>';
							}
						}
						$counter++;
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
	
}


?>