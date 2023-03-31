<?php

/**
 * AJAX Server for the Adlogic Job System
 *
 * Description: The AJAX Server acts as a gateway between Adlogic's SOAP Servers and the
 * front end, returning results for the front end to display.
 *
 * @author Abilio Henrique <abilio@adlogic.com.au>
 * @version 1.0
 *
 */

/* Header for results returned
	 * We're Returning JSON Results instead of XML as previously used by Adlogic for lighter frontend interfaces and result sets and speed.
	 */
header("Content-type: application/json");

// Get Settings from Wordpress
$apiSettings = get_option('adlogic_api_settings');
$searchSettings = get_option('adlogic_search_settings');
$cacheSettings = get_option('adlogic_cache_settings');
$mobileSettings = get_option('adlogic_mobile_settings');

// Enable Compressed Output for faster data transfer
if ((ini_get('zlib.output_compression') != 1) && ((!isset($apiSettings['adlogic_ajax_compression'])) || ($apiSettings['adlogic_ajax_compression'] != 'false'))) {
	ob_start("ob_gzhandler");
}

// Default Items Per Page

// Instantiate Soap Client
$oSoapClient = Adlogic_Job_Board::getSoapConnection();
$oCandidateSoapClient = Adlogic_Job_Board::getSoapConnection('candidate');

$itemsPerPage = isset($searchSettings['adlogic_search_results_per_page']) ? $searchSettings['adlogic_search_results_per_page'] : 5;

if (isset($_GET['itemsPerPage'])) {
	$itemsPerPage = $_GET['itemsPerPage'];
}

if (!isset($_GET['action'])) {
	print json_encode(array('error' => 'no action requested'));
	exit(1);
}
// Switch based on the different request types
switch ($_GET['action']) {
	case 'searchJobs': // Search Jobs

		// Requires Jobs Class
		require_once('classes/jobSearch.class.php');
		$oJobSearch = new JobSearch($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		/* Search Criteria */

		// Search Keyword
		// 15th January 2015: Fix to allow & pass through the keyword search
		$keyword = htmlentities((isset($_GET['keyword']) && !empty($_GET['keyword'])) ?  $_GET['keyword'] : '');
		// Salary Type
		$salaryType = ((isset($_GET['salaryType']) && !empty($_GET['salaryType'])) ?  $_GET['salaryType'] : '');
		// Minimum Salary
		$salaryMin = ((isset($_GET['salaryMin']) && is_numeric($_GET['salaryMin'])) ?  $_GET['salaryMin'] : null);
		// Maximum Salary
		$salaryMax = ((isset($_GET['salaryMax']) && is_numeric($_GET['salaryMax'])) ?  $_GET['salaryMax'] : null);

		// Cost Center Id
		$costCenterId = ((isset($_GET['costCenter']) && !empty($_GET['costCenter'])) ?  $_GET['costCenter'] : null);
		// Organisation Unit
		$orgUnit = ((isset($_GET['orgUnit']) && !empty($_GET['orgUnit'])) ?  $_GET['orgUnit'] : null);

		// From Page
		$from = ((isset($_GET['from']) && !empty($_GET['from'])) ?  $_GET['from'] : 1);
		// To Page
		$to = ((isset($_GET['to']) && !empty($_GET['to'])) ?  $_GET['to'] : $itemsPerPage);

		$geoLocationJson = ((isset($_GET['geoLocationJson']) && !empty($_GET['geoLocationJson'])) ?  $_GET['geoLocationJson'] : null);

		$suburbIds = ((isset($_GET['locId']) && !empty($_GET['locId'])) ?  $_GET['locId'] : null);

		// Build classification criteria parameters array (includes worktype, location, and industry)
		$aClassificationsCriteria = array();
		// Industry Id
		if ((isset($_GET['indId']) && !empty($_GET['indId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['indId']);
		}
		// Location Id
		if (!adlogic_job_board::shouldUseNewAPI()) {
			if ((isset($_GET['locId']) && !empty($_GET['locId']))) {
				$aClassificationsCriteria[] =  explode(',', $_GET['locId']);
			}
		}
		// WorkType Id
		if ((isset($_GET['wtId']) && !empty($_GET['wtId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['wtId']);

		}
		/* Set Variables from GET Url Vars for Jobs Class*/
		$oJobSearch->set('keyword',					$keyword);
		$oJobSearch->set('classificationsCriteria',	$aClassificationsCriteria);
		$oJobSearch->set('salaryType',				$salaryType);
		$oJobSearch->set('salaryMax',				$salaryMax);
		$oJobSearch->set('salaryMin',				$salaryMin);
		$oJobSearch->set('from',					$from);
		$oJobSearch->set('to',						$to);
		$oJobSearch->set('costCenterId',			$costCenterId);
		$oJobSearch->set('orgUnit',					$orgUnit);
		$oJobSearch->set('geoLocationObject',		$geoLocationJson);
		$oJobSearch->set('suburbIds',		$suburbIds);


		// Get Job Search Results
		if ((isset($_GET['internalExternal'])) && !empty($_GET['internalExternal'])) {
			$oJobSearch->set('internalExternal',	$_GET['internalExternal']);
			$oJobSearchResults = $oJobSearch->getIntranet();
		} else {
			$oJobSearchResults = $oJobSearch->get();
		}

		/*print_r($oJobSearchResults->JobPostings->JobPosting[0]->locations);
			die();*/

		// Add Results Per Page for front end pagination
		$oJobSearchResults->JobPostings->addAttribute('resultsPerPage', $itemsPerPage);
		$oJobSearchResults->JobPostings->addAttribute('resultsReturned', count($oJobSearchResults->JobPostings->JobPosting));

		// Execute Job Search
		if (isset($_POST['template'])) {
			// Required for search query parameters
			require_once('classes/location.class.php');
			require_once('classes/industry.class.php');
			require_once('classes/worktype.class.php');

			// Get Location Query Search Parameters
			$oLocations = new Location($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);
			$cache_file = 'location_cache.xml';

			$locationsSearched = array();
			if (isset($_GET['locId']) && !empty($_GET['locId'])) {
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

				$aLocationIds = explode(',', $_GET['locId']);
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
			if (isset($_GET['indId']) && !empty($_GET['indId'])) {
				// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
				if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

				$aClassificationIds = explode(',', $_GET['indId']);
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
			if (isset($_GET['wtId']) && !empty($_GET['wtId'])) {
				// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
				if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

				$aWorkTypeIds = explode(',', $_GET['wtId']);
				foreach ($aWorkTypeIds as $workTypeId) {
					foreach ($worktypesResult as $worktypeResult) {
						if ($workTypeId == $worktypeResult->id) {
							$workTypesSearched[] = $worktypeResult;
						}
					}
				}
			}

			print json_encode(array(
				'search_results_html' => Adlogic_Search_Shortcodes::parse_search($oJobSearchResults, stripslashes(urldecode($_POST['template'])), (isset($_GET['date_format']) ? base64_decode($_GET['date_format']) : null), (isset($_GET['page_id']) ? $_GET['page_id'] : null)),
				'search_results_attributes' => $oJobSearchResults->JobPostings->attributes(),
				'search_results_query' => array(
					'parameters' => array(
						'classifications' => $classificationsSearched,
						'locations' => $locationsSearched,
						'worktypes' => $workTypesSearched,
						'salary' => array(
							'min' => isset($salaryMin) ? $salaryMin : null,
							'max' => $salaryMax ? $salaryMax : null,
							'type' => $salaryType ? $salaryType : null
						),
						'keywords' => $keyword,
						'cost_center' => $costCenterId
					),
					'pagination' => array(
						'from'				=> (int) $from,
						'to'				=> (int) $to,
						'results_per_page'	=> (int) $itemsPerPage,
						'results_returned'	=> (int) $oJobSearchResults->JobPostings->attributes()->resultsReturned,
						'total_results'		=> (int) $oJobSearchResults->JobPostings->attributes()->count
					)
				)
			));
		} else {
			print json_encode($oJobSearchResults);
		}
		break;
	case 'searchFilteredJobs': // Search Jobs
		// Requires Jobs Class
		require_once('classes/jobSearch.class.php');
		$oJobSearch = new JobSearch($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		/* Search Criteria */

		// Search Keyword
		// 15th January 2015: Fix to allow & pass through the keyword search
		$keyword = htmlentities((isset($_GET['keyword']) && !empty($_GET['keyword'])) ?  $_GET['keyword'] : '');
		// Salary Type
		$salaryType = ((isset($_GET['salaryType']) && !empty($_GET['salaryType'])) ?  $_GET['salaryType'] : '');
		// Minimum Salary
		$salaryMin = ((isset($_GET['salaryMin']) && is_numeric($_GET['salaryMin'])) ?  $_GET['salaryMin'] : null);
		// Maximum Salary
		$salaryMax = ((isset($_GET['salaryMax']) && is_numeric($_GET['salaryMax'])) ?  $_GET['salaryMax'] : null);

		// Cost Center Id
		$costCenterId = ((isset($_GET['costCenter']) && !empty($_GET['costCenter'])) ?  $_GET['costCenter'] : null);
		// Organisation Unit
		$orgUnit = ((isset($_GET['orgUnit']) && !empty($_GET['orgUnit'])) ?  $_GET['orgUnit'] : null);

		// From Page
		$from = ((isset($_GET['from']) && !empty($_GET['from'])) ?  $_GET['from'] : 1);
		// To Page
		$to = ((isset($_GET['to']) && !empty($_GET['to'])) ?  $_GET['to'] : $itemsPerPage);

		$geoLocationJson = ((isset($_GET['geoLocationJson']) && !empty($_GET['geoLocationJson'])) ?  $_GET['geoLocationJson'] : null);

		// Build classification criteria parameters array (includes worktype, location, and industry)
		$aClassificationsCriteria = array();
		// Industry Id
		if ((isset($_GET['indId']) && !empty($_GET['indId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['indId']);
		}
		// Location Id
		if ((isset($_GET['locId']) && !empty($_GET['locId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['locId']);
		}
		// WorkType Id
		if ((isset($_GET['wtId']) && !empty($_GET['wtId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['wtId']);
		}
		$childrenRecruiterIds = array();
		if ((isset($_GET['childrenRecruiterIds'])) && !empty($_GET['childrenRecruiterIds'])) {
			$childrenRecruiterIds = explode(',', $_GET['childrenRecruiterIds']);
		}
		/* Set Variables from GET Url Vars for Jobs Class*/
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
		$oJobSearch->set('geoLocationObject',		$geoLocationJson);


		// Get Job Search Results
		if ((isset($_GET['internalExternal'])) && !empty($_GET['internalExternal'])) {
			$oJobSearch->set('internalExternal',	$_GET['internalExternal']);
			$oJobSearchResults = $oJobSearch->getIntranet();
		} else {
			$oJobSearchResults = $oJobSearch->getFiltered();
		}

		/*print_r($oJobSearchResults->JobPostings->JobPosting[0]->locations);
			die();*/

		// Add Results Per Page for front end pagination
		$oJobSearchResults->JobPostings->addAttribute('resultsPerPage', $itemsPerPage);
		$oJobSearchResults->JobPostings->addAttribute('resultsReturned', count($oJobSearchResults->JobPostings->JobPosting));

		// Execute Job Search
		if (isset($_POST['template'])) {
			// Required for search query parameters
			require_once('classes/location.class.php');
			require_once('classes/industry.class.php');
			require_once('classes/worktype.class.php');

			// Get Location Query Search Parameters
			$oLocations = new Location($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);
			$cache_file = 'location_cache.xml';

			$locationsSearched = array();
			if (isset($_GET['locId']) && !empty($_GET['locId'])) {
				// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
				if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

				$aLocationIds = explode(',', $_GET['locId']);
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
			if (isset($_GET['indId']) && !empty($_GET['indId'])) {
				// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
				if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

				$aClassificationIds = explode(',', $_GET['indId']);
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
			if (isset($_GET['wtId']) && !empty($_GET['wtId'])) {
				// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
				if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

				$aWorkTypeIds = explode(',', $_GET['wtId']);
				foreach ($aWorkTypeIds as $workTypeId) {
					foreach ($worktypesResult as $worktypeResult) {
						if ($workTypeId == $worktypeResult->id) {
							$workTypesSearched[] = $worktypeResult;
						}
					}
				}
			}

			print json_encode(array(
				'search_results_html' => Adlogic_Search_Shortcodes::parse_search($oJobSearchResults, stripslashes(urldecode($_POST['template'])), (isset($_GET['date_format']) ? base64_decode($_GET['date_format']) : null), (isset($_GET['page_id']) ? $_GET['page_id'] : null)),
				'search_results_attributes' => $oJobSearchResults->JobPostings->attributes(),
				'search_results_query' => array(
					'parameters' => array(
						'classifications' => $classificationsSearched,
						'locations' => $locationsSearched,
						'worktypes' => $workTypesSearched,
						'salary' => array(
							'min' => isset($salaryMin) ? $salaryMin : null,
							'max' => $salaryMax ? $salaryMax : null,
							'type' => $salaryType ? $salaryType : null
						),
						'keywords' => $keyword,
						'cost_center' => $costCenterId
					),
					'pagination' => array(
						'from'				=> (int) $from,
						'to'				=> (int) $to,
						'results_per_page'	=> (int) $itemsPerPage,
						'results_returned'	=> (int) $oJobSearchResults->JobPostings->attributes()->resultsReturned,
						'total_results'		=> (int) $oJobSearchResults->JobPostings->attributes()->count
					)
				)
			));
		} else {
			print json_encode($oJobSearchResults);
		}
		break;
	case 'searchAllRecruiters': // Search Jobs
		// Requires Jobs Class
		require_once('classes/jobSearch.class.php');
		$oJobSearch = new JobSearch($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		/* Search Criteria */

		// Search Keyword
		$keyword = urldecode((isset($_GET['keyword']) && !empty($_GET['keyword'])) ?  $_GET['keyword'] : '');
		// Salary Type
		$salaryType = ((isset($_GET['salaryType']) && !empty($_GET['salaryType'])) ?  $_GET['salaryType'] : empty($_GET['salaryType']));
		// Minimum Salary
		$salaryMin = ((isset($_GET['salaryMin']) && !empty($_GET['salaryMin'])) ?  $_GET['salaryMin'] : null);
		// Maximum Salary
		$salaryMax = ((isset($_GET['salaryMax']) && !empty($_GET['salaryMax'])) ?  $_GET['salaryMax'] : null);

		// Cost Center Id
		$costCenterId = ((isset($_GET['costCenter']) && !empty($_GET['costCenter'])) ?  $_GET['costCenter'] : null);
		// Organisation Unit
		$orgUnit = ((isset($_GET['orgUnit']) && !empty($_GET['orgUnit'])) ?  $_GET['orgUnit'] : null);

		// From Page
		$from = ((isset($_GET['from']) && !empty($_GET['from'])) ?  $_GET['from'] : 1);
		// To Page
		$to = ((isset($_GET['to']) && !empty($_GET['to'])) ?  $_GET['to'] : $itemsPerPage);

		// Build classification criteria parameters array (includes worktype, location, and industry)
		$aClassificationsCriteria = array();
		// Industry Id
		if ((isset($_GET['indId']) && !empty($_GET['indId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['indId']);
		}
		// Location Id
		if ((isset($_GET['locId']) && !empty($_GET['locId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['locId']);
		}
		// WorkType Id
		if ((isset($_GET['wtId']) && !empty($_GET['wtId']))) {
			$aClassificationsCriteria[] =  explode(',', $_GET['wtId']);
		}

		/* Set Variables from GET Url Vars for Jobs Class*/
		$oJobSearch->set('keyword',					$keyword);
		$oJobSearch->set('classificationsCriteria',	$aClassificationsCriteria);
		$oJobSearch->set('salaryType',				$salaryType);
		$oJobSearch->set('salaryMax',				$salaryMax);
		$oJobSearch->set('salaryMin',				$salaryMin);
		$oJobSearch->set('from',					$from);
		$oJobSearch->set('to',						$to);
		$oJobSearch->set('costCenterId',			$costCenterId);
		$oJobSearch->set('orgUnit',					$orgUnit);

		// Get Job Search Results
		if ((isset($apiSettings['adlogic_joblogic_passphrase'])) && !empty($apiSettings['adlogic_joblogic_passphrase'])) {
			$oJobSearch->set('passphrase', $apiSettings['adlogic_joblogic_passphrase']);
			$oJobSearchResults = $oJobSearch->getForAllRecruiters();
		} else {
			$oJobSearchResults = $oJobSearch->get();
		}

		/*print_r($oJobSearchResults->JobPostings->JobPosting[0]->locations);
				 die();*/
		// Add Results Per Page for front end pagination
		$oJobSearchResults->JobPostings->addAttribute('resultsPerPage', $itemsPerPage);
		$oJobSearchResults->JobPostings->addAttribute('resultsReturned', count($oJobSearchResults->JobPostings->JobPosting));

		// Execute Job Search
		if (isset($_POST['template'])) {
			print json_encode(array(
				'search_results_html' => Adlogic_Search_Shortcodes::parse_search($oJobSearchResults, stripslashes(urldecode($_POST['template'])), (isset($_GET['date_format']) ? base64_decode($_GET['date_format']) : null), (isset($_GET['page_id']) ? $_GET['page_id'] : null)),
				'search_results_attributes' => $oJobSearchResults->JobPostings->attributes()
			));
		} else {
			print json_encode($oJobSearchResults);
		}
		break;
	case 'searchHotJobs': // Search for Hot Jobs
		// Requires Jobs Class
		require_once('classes/jobSearch.class.php');
		$oJobSearch = new JobSearch($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		if (isset($_GET['widget_id'])) {
			global $wp_registered_widgets;
			$widget_params = array_pop($wp_registered_widgets[$_GET['widget_id']]['params']);
			$widget_options = get_option('widget_adlogic_hot_jobs_widget');
			$widget_options = $widget_options[$widget_params['number']];
			$itemsPerPage = isset($widget_options['total_hot_jobs']) ? $widget_options['total_hot_jobs'] : $itemsPerPage;
		}

		/* Search Criteria */
		// From Page
		$from = ((isset($_GET['from']) && !empty($_GET['from'])) ?  $_GET['from'] : 1);
		// To Page
		$to = ((isset($_GET['to']) && !empty($_GET['to'])) ?  $_GET['to'] : $itemsPerPage);

		$oJobSearch->set('from',					$from);
		$oJobSearch->set('to',						$to);

		// Get Job Search Results
		if ((isset($_GET['internalExternal'])) && !empty($_GET['internalExternal'])) {
			$oJobSearch->set('internalExternal',	$_GET['internalExternal']);
			$oJobSearchResults = $oJobSearch->getHotJobsIntranet();
		} else {
			// Get Job Hot Job Search Results
			$oJobSearchResults = $oJobSearch->getHotJobs();
		}


		// Add Results Per Page fo~r front end pagination
		$oJobSearchResults->JobPostings->addAttribute('resultsPerPage', $itemsPerPage);
		//print count($oJobSearchResults->JobPostings->JobPosting);

		// Execute Job Search
		print json_encode($oJobSearchResults);
		break;
	case 'searchArchiveAds': // Search for Hot Jobs
		// Requires Jobs Class
		require_once('classes/jobSearch.class.php');
		$oJobSearch = new JobSearch($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		/* Search Criteria */
		// From Page
		$from = ((isset($_GET['from']) && !empty($_GET['from'])) ?  $_GET['from'] : 1);
		// To Page
		$to = ((isset($_GET['to']) && !empty($_GET['to'])) ?  $_GET['to'] : $itemsPerPage);

		// Get Job Hot Job Search Results
		$oJobSearchResults = $oJobSearch->getArchiveJobs();
		// Add Results Per Page fo~r front end pagination
		$oJobSearchResults->JobPostings->addAttribute('resultsPerPage', $itemsPerPage);

		// Execute Job Search
		if (isset($_POST['template'])) {
			print json_encode(array(
				'search_results_html' => Adlogic_Search_Shortcodes::parse_search($oJobSearchResults, stripslashes(urldecode($_POST['template'])), (isset($_GET['date_format']) ? base64_decode($_GET['date_format']) : null), (isset($_GET['page_id']) ? $_GET['page_id'] : null)),
				'search_results_attributes' => $oJobSearchResults->JobPostings->attributes()
			));
		} else {
			print json_encode($oJobSearchResults);
		}
		break;
	case 'getWorktypes': // Return Worktypes
		// Requires Worktype Class
		require_once('classes/worktype.class.php');
		$oWorktypes = new Worktype($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		// Get Ad Count If Set
		if ((isset($_GET['jobCount'])) && ($_GET['jobCount'] == 'true')) {
			$oWorktypes->set('withAdCount', true);

			if ((isset($_GET['onlyFirstLevel'])) && ($_GET['onlyFirstLevel'] == 'true')) {
				$oWorktypes->set('onlyFirstLevel', 'true');
				$cache_file = 'worktype_cache_top_level_count.xml';
			} else {
				$cache_file = 'worktype_cache_count.xml';
			}
		} else {
			if ((isset($_GET['onlyFirstLevel'])) && ($_GET['onlyFirstLevel'] == 'true')) {
				$oWorktypes->set('onlyFirstLevel', 'true');
				$cache_file = 'worktype_cache_top_level.xml';
			} else {
				$cache_file = 'worktype_cache.xml';
			}
		}

		// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
		if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

		// Handle results not returned by getWorktypesForRecruiter method, by calling getWorktypes method as a backup
		if (empty($worktypesResult)) {
			error_log('getWorkTypesForRecruiter returned no results, calling getWorktypes - Contact Adlogic');
			$oWorktypes->recruiterId = null;
			$worktypesResult = $oWorktypes->get();
		}
		print json_encode($worktypesResult);
		break;
	case 'getLocations': // Return Locations

		if (!Adlogic_job_board::shouldUseNewAPI()) {
			// Requires Location Class
			require_once('classes/location.class.php');
			$oLocations = new Location($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);


			// Get Ad Count If Set
			if ((isset($_GET['jobCount'])) && ($_GET['jobCount'] == 'true')) {
				$oLocations->set('withAdCount', true);

				if ((isset($_GET['onlyFirstLevel'])) && ($_GET['onlyFirstLevel'] == 'true')) {
					$oLocations->set('onlyFirstLevel', 'true');
					$cache_file = 'location_cache_top_level_count.xml';
				} else {
					$cache_file = 'location_cache_count.xml';
				}
			} else {
				if ((isset($_GET['onlyFirstLevel'])) && ($_GET['onlyFirstLevel'] == 'true')) {
					$oLocations->set('onlyFirstLevel', 'true');
					$cache_file = 'location_cache_top_level.xml';
				} else {
					$cache_file = 'location_cache.xml';
				}
			}

			// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
			if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

			// Handle results not returned by getAllLocationsForRecruiter method, by calling getAllLocations method as a backup
			if (empty($locationsResult)) {
				error_log('getAllLocationsForRecruiter returned no results, calling getAllLocations - Contact Adlogic');
				$oLocations->recruiterId = null;
				$locationsResult = $oLocations->get();
			}
		} else {
			require_once('classes/newLocation.class.php');
			$oNewLocations = new NewLocation($apiSettings['adlogic_rest_server'], $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id'], $apiSettings['adlogic_rest_api_key']);
			$locationsResult = $oNewLocations->get();
			//$test = $oNewLocations->createSuburbIdString($locationsResult);
		}

		print json_encode($locationsResult);

		break;
	case 'getIndustries': // Return Industries
		// Requires Industry Class
		require_once('classes/industry.class.php');
		$oIndustries = new Industry($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		// Get Ad Count If Set
		if ((isset($_GET['jobCount'])) && ($_GET['jobCount'] == 'true')) {
			$oIndustries->set('withAdCount', true);

			if ((isset($_GET['onlyFirstLevel'])) && ($_GET['onlyFirstLevel'] == 'true')) {
				$oIndustries->set('onlyFirstLevel', 'true');
				$cache_file = 'industry_cache_top_level_count.xml';
			} else {
				$cache_file = 'industry_cache_count.xml';
			}
		} else {
			if ((isset($_GET['onlyFirstLevel'])) && ($_GET['onlyFirstLevel'] == 'true')) {
				$oIndustries->set('onlyFirstLevel', 'true');
				$cache_file = 'industry_cache_top_level.xml';
			} else {
				$cache_file = 'industry_cache.xml';
			}
		}

		// Check if cache is enabled, if so get results from cache or refresh cache if cache is expired
		if (isset($cacheSettings['adlogic_cache_status']) && $cacheSettings['adlogic_cache_status'] == 'true') {
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

		// Handle results not returned by getClassificationsForRecruiter method, by calling getClassifications method as a backup
		if (empty($industriesResult)) {
			error_log('getClassificationsForRecruiter returned no results, calling getClassifications - Contact Adlogic');
			$oIndustries->recruiterId = null;
			$industriesResult = $oIndustries->get();
		}
		print json_encode($industriesResult);
		break;
	case 'getCostCenters': // Return Industries
		// Requires Industry Class
		require_once('classes/costCenter.class.php');
		$oCostCenters = new CostCenter($oSoapClient, $apiSettings['adlogic_recruiter_id']);
		print json_encode($oCostCenters->get());
		break;
	case 'subscribeJobAlerts': // Subscribe user to Job Alerts and return success/failure
		// Requires Applicant Subscriber Class
		require_once('classes/applicantSubscriber.class.php');
		$oApplicantSubscriber = new ApplicantSubscriberWS($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);

		/* Set Variables from Post Vars for ApplicantSubscriberWS Class*/
		// Applicant's Name
		$name = ((isset($_POST['name']) && !empty($_POST['name'])) ?  $_POST['name'] : null);
		// Applicant's Surname
		$surname = ((isset($_POST['surname']) && !empty($_POST['surname'])) ?  $_POST['surname'] : null);
		// Applicant's Email
		$email = ((isset($_POST['email']) && !empty($_POST['email'])) ?  $_POST['email'] : null);
		// Applicant's Contact Number
		$contactNumber = ((isset($_POST['contactNumber']) && !empty($_POST['contactNumber'])) ?  $_POST['contactNumber'] : null);

		// State
		$state = ((isset($_POST['state']) && !empty($_POST['state'])) ?  $_POST['state'] : 'NSW');
		// Country
		$country = ((isset($_POST['country']) && !empty($_POST['country'])) ?  $_POST['country'] : 'Australia');
		// Industry Id
		$industryId = ((isset($_POST['indId']) && !empty($_POST['indId'])) ?  $_POST['indId'] : null);
		// Location Id
		$locationId = ((isset($_POST['locId']) && !empty($_POST['locId'])) ?  $_POST['locId'] : null);
		// WorkType Id
		$worktypeId = ((isset($_POST['wtId']) && !empty($_POST['wtId'])) ?  $_POST['wtId'] : null);
		// Notice Period
		$noticePeriod = ((isset($_POST['noticePeriod']) && !empty($_POST['noticePeriod'])) ?  $_POST['noticePeriod'] : null);
		// Indigenous
		$indigenous = ((isset($_POST['indigenous']) && !empty($_POST['indigenous'])) ?  $_POST['indigenous'] : false);

		/* Set Class variables */
		// Applicant Id (always set to empty since it's a new subscription)
		$oApplicantSubscriber->set('ApplicantId', '');
		$oApplicantSubscriber->set('Name', $name);
		$oApplicantSubscriber->set('Surname', $surname);
		$oApplicantSubscriber->set('Email', $email);
		$oApplicantSubscriber->set('Phone', $contactNumber);
		$oApplicantSubscriber->set('State', $state);
		$oApplicantSubscriber->set('Country', $country);
		$oApplicantSubscriber->set('IndustryArr', $industryId);
		$oApplicantSubscriber->set('RegionArr', $locationId);
		$oApplicantSubscriber->set('WorkTypeArr', $worktypeId);
		$oApplicantSubscriber->set('NoticePeriod', $noticePeriod);
		$oApplicantSubscriber->set('Indigenous', $indigenous);

		print json_encode($oApplicantSubscriber->subscribe(true));
		break;
	case 'logout':
		$oAdlogicUser = new AdlogicUser($oCandidateSoapClient);
		if (isset($_SESSION['adlogicUserSession'])) {
			if ($oAdlogicUser->isLoggedIn($_SESSION['adlogicUserSession'])) {
				$oAdlogicUser->logout($_SESSION['adlogicUserSession']);
				session_unset();
				session_destroy();
				session_write_close();
				session_start();
				print json_encode(array('result' => true));
			} else {
				print json_encode(array('result' => false));
			}
		} else {
			print json_encode(array('result' => false));
		}
		break;
	case 'facebook_channel':

		$cache_expire = 60 * 60 * 24 * 365;
		header("Pragma: public");
		header("Cache-Control: max-age=" . $cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_expire) . ' GMT');
		?>
		<script src="//connect.facebook.net/en_US/all.js"></script>
	<?php
		break;
	case 'getSavedJobs':
		require_once('classes/savedJobs.class.php');
		$oSavedJobs = new SavedJobs($oCandidateSoapClient);

		/* Set Class variables */
		$Adlogic_Job_Board_Users = new Adlogic_Job_Board_Users();
		if ($Adlogic_Job_Board_Users->isLoggedIn()) {
			$oSavedJobs->set('recruiterId',  $apiSettings['adlogic_recruiter_id']); // optional, but restricts results returned
			$oSavedJobs->set('advertiserId', $apiSettings['adlogic_advertiser_id']); // optional, but restricts results returned
			$oSavedJobs->set('sessionHash', $_SESSION['adlogicUserSession']);

			$aSavedJobs = $oSavedJobs->get();

			print json_encode($aSavedJobs);
		} else {
			print json_encode(array('result' => false));
		}
		break;
	case 'getSavedJobIds':
		require_once('classes/savedJobs.class.php');
		$oSavedJobs = new SavedJobs($oCandidateSoapClient);

		/* Set Class variables */
		$Adlogic_Job_Board_Users = new Adlogic_Job_Board_Users();
		if ($Adlogic_Job_Board_Users->isLoggedIn()) {
			$oSavedJobs->set('recruiterId',  $apiSettings['adlogic_recruiter_id']); // optional, but restricts results returned
			$oSavedJobs->set('advertiserId', $apiSettings['adlogic_advertiser_id']); // optional, but restricts results returned
			$oSavedJobs->set('sessionHash', $_SESSION['adlogicUserSession']);

			$aSavedJobs = $oSavedJobs->get();

			$aSavedJobIds = array();

			foreach ($aSavedJobs->JobPostings->JobPosting as $aSavedJob) {
				$aSavedJobAttributes = $aSavedJob->attributes();
				$aSavedJobIds[] = (int) $aSavedJobAttributes->ad_id;
			}

			print json_encode($aSavedJobIds);
		} else {
			print json_encode(array('result' => false));
		}
		break;
	case 'addSavedJob':
		require_once('classes/savedJobs.class.php');
		$oSavedJobs = new SavedJobs($oCandidateSoapClient);


		/* Set Class variables */
		$Adlogic_Job_Board_Users = new Adlogic_Job_Board_Users();
		if ($Adlogic_Job_Board_Users->isLoggedIn()) {
			$oSavedJobs->set('recruiterId',  $apiSettings['adlogic_recruiter_id']); // optional, but restricts results returned
			$oSavedJobs->set('advertiserId', $apiSettings['adlogic_advertiser_id']); // optional, but restricts results returned
			$oSavedJobs->set('sessionHash', $_SESSION['adlogicUserSession']);

			print json_encode($oSavedJobs->add($_GET['jobAdId']));
		} else {
			print json_encode(array('result' => false));
		}
		break;
	case 'removeSavedJob':
		require_once('classes/savedJobs.class.php');
		$oSavedJobs = new SavedJobs($oCandidateSoapClient);


		/* Set Class variables */
		$Adlogic_Job_Board_Users = new Adlogic_Job_Board_Users();
		if ($Adlogic_Job_Board_Users->isLoggedIn()) {
			$oSavedJobs->set('recruiterId',  $apiSettings['adlogic_recruiter_id']); // optional, but restricts results returned
			$oSavedJobs->set('advertiserId', $apiSettings['adlogic_advertiser_id']); // optional, but restricts results returned
			$oSavedJobs->set('sessionHash', $_SESSION['adlogicUserSession']);

			print json_encode($oSavedJobs->remove($_GET['jobAdId']));
		} else {
			print json_encode(array('result' => false));
		}
		break;
	case 'sendJobSMS':
		require_once(AJB_PLUGIN_PATH . '/lib/classes/smsglobal.class.php');
		if (
			!empty($mobileSettings['adlogic_smsglobal_server']) &&
			!empty($mobileSettings['adlogic_smsglobal_username']) &&
			!empty($mobileSettings['adlogic_smsglobal_password']) &&
			!empty($mobileSettings['adlogic_smsglobal_sender_number'])
		) {
			// Shrink the URL
			// create the URL
			$bitly = array();
			if (!empty($mobileSettings['adlogic_bitly_login']) && !empty($mobileSettings['adlogic_bitly_api_key'])) {
				$bitly['login'] = $mobileSettings['adlogic_bitly_login'];
				$bitly['apiKey'] = $mobileSettings['adlogic_bitly_api_key'];
			} else {
				// default to adlogic's login & API Key for those without bit.ly accounts
				$bitly['login'] = 'adlogic';
				$bitly['apiKey'] = 'R_e14640baa7705b8f99a0fbc9f7b3e86b';
			}

			$bitly = 'http://api.bit.ly/shorten?version=2.0.1&longUrl=' . urlencode($_POST['jobAdUrl']) . '&login=' . $bitly['login'] . '&apiKey=' . $bitly['apiKey'] . '&format=json';

			//get the url
			//could also use cURL here
			$response = file_get_contents($bitly);

			$urlData = @json_decode($response, true);
			if ($urlData['results']) {
				$urlData = array_pop($urlData['results']);
				$shrunkUrl = $urlData['shortUrl'];
			} else {
				$shrunkUrl = $_POST['jobAdUrl'];
			}

			$smsContent = 'Here\'s the job you requested, ' . $_POST['jobTitle'] . ' - ' . $shrunkUrl;
			$oSmsGlobal = new SMSGlobal();
			$oSmsGlobal->server_url = $mobileSettings['adlogic_smsglobal_server'];
			$oSmsGlobal->username = $mobileSettings['adlogic_smsglobal_username'];
			$oSmsGlobal->password = $mobileSettings['adlogic_smsglobal_password'];
			$oSmsGlobal->sender_number = $mobileSettings['adlogic_smsglobal_sender_number'];
			print json_encode($oSmsGlobal->SendSMS($_POST['mobileNumber'], $smsContent));
		} else {
			print json_encode(false);
		}
		break;
	case 'sendJobEmail':
		// Requires Rmail Email Library (3rd Party Library)
		require_once('classes/mailer/Rmail.php');
		// Get fields from Form
		$user_name		= 'Job Mail';
		if (isset($_POST['yourEmailAddress'])) {
			$user_email		= $_POST['yourEmailAddress'];
		} else {
			$user_email		= $_POST['emailAddress'];
		}
		$friend_name	= 'Me';
		$friend_email	= $_POST['emailAddress'];
		$subject		= 'Job Alert - ' . $_POST['jobTitle'];

		// Send email using template
		if (isset($_POST['content'])) {
			$emailBody = <<<MESSAGE
Hi there,

A friend of yours thought you might be interested in the following job.

Job Title: {$_POST['jobTitle']}
Link: {$_POST['jobAdUrl']}

Message from your friend:
{$_POST['content']}

Please click the link for more details.
MESSAGE;
		} else {
			$emailBody = <<<MESSAGE
Hi there,

Here's the job link you requested.

Job Title: {$_POST['jobTitle']}
Link: {$_POST['jobAdUrl']}

Please click the link for more details
MESSAGE;
		}

		// Create mailer object
		$oMailer = new Rmail();
		$oMailer->setFrom($user_name . ' <' . $user_email . '>');
		$oMailer->setSubject($subject);
		$oMailer->setText($emailBody);
		$result = $oMailer->send(array($friend_email));
		print json_encode(array('result' => $result));
		break;
	case 'sendToFriend': // Send Email To A Friend with a link to the Job URL
		// Requires Rmail Email Library (3rd Party Library)
		require_once('classes/mailer/Rmail.php');

		// Get fields from Form
		$user_name		= $_POST['user_name'];
		$user_email		= $_POST['user_email'];
		$friend_name	= $_POST['friend_name'];
		$friend_email	= $_POST['friend_email'];
		$subject		= $_POST['subject'];
		$message		= $_POST['message'];

		// Send email using template
		$emailBody = <<<MESSAGE
Hi $friend_name,

Your $user_name has referred you to this job posted by Medical Recruitment, Australia's Quality GP and Medical Industry Recruitment Jobs Agency.

Please see the details below for more information:

$message


Medical Recruitment - Successfully Matching GPs and Practices since 1986
http://www.medicalrecruitment.com.au/
MESSAGE;

		// Create mailer object
		$oMailer = new Rmail();
		$oMailer->setSMTPParams('mail.fdev.com.au', 9999, 'fdev.com.au', true, 'tiaki@fdev.com.au', 't1ak1web');
		$oMailer->setFrom($user_name . ' <' . $user_email . '>');
		$oMailer->setSubject($subject);
		$oMailer->setText($emailBody);
		$result = $oMailer->send(array($friend_email), 'smtp');
		print json_encode(array('result' => $result));
		break;
	default:
		print json_encode(array('error' => 'invalid action requested'));
		exit(1);
		break;
}



?>