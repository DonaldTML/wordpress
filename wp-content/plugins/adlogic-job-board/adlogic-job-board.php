<?php
/**
 * @package Adlogic_Job_Board
 * @version 2.17.0
 */
/*
Plugin Name: My Recruitment+ Job Board
Plugin URI: https://myrecruitmentplus.com
Description: The <strong>MyRecruitment+ Job Board</strong> is a Wordpress Plugin that integrates with the MyRecruitment+ API to create a job portal on your website. Visit <a href="https://myrecruitmentplus.com" target="_blank">myrecruitmentplus.com</a> for more information.
Version: 2.17.0
Author: Martian Logic Pty Ltd
Author URI: https://myrecruitmentplus.com
License: MIT
*/

/*
 * Copyright (c) 2019 Martian Logic Pty Ltd
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

define('AJB_VERSION', '2.17.0');
define('AJB_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('AJB_PLUGIN_PATH', dirname( __FILE__ ));
define('AJB_PLUGIN_FILE', plugin_basename(__FILE__) );
define('AJB_UPDATE_URL', 'http://updates.adlogic.com.au/?plugin=adlogic_job_board&platform=wordpress&current_version=' . AJB_VERSION . '&domain='.home_url());

// Adlogic Job Board Constants

// Platform Constants
define('AJB_PLATFORM_ID_DESKTOP', 1);
define('AJB_PLATFORM_ID_MOBILE', 2);
define('AJB_PLATFORM_ID_SOCIAL_BOARD', 3);
define('AJB_PLATFORM_ID_JOBLOGIC', 4);

// Sub Source Ids (web traffic sources)
define('AJB_SUBSOURCE_ID_ALERTS', 1003); // Job Alerts
define('AJB_SUBSOURCE_ID_GOOGLE', 1004); // Google Search Engine
define('AJB_SUBSOURCE_ID_OSE', 1005); // Other Search Engines
define('AJB_SUBSOURCE_ID_SMS', 1006); // SMS
define('AJB_SUBSOURCE_ID_STF', 1007); // Send Email ToFriend
define('AJB_SUBSOURCE_ID_PDTV', 1011); // pedestrian tv
define('AJB_SUBSOURCE_ID_MUMBRL', 1012); // mumbrella
define('AJB_SUBSOURCE_ID_MNTWk', 1013); // music network
define('AJB_SUBSOURCE_ID_RT', 1014); // radio today
define('AJB_SUBSOURCE_ID_DEFAULT', 10000); // Default Subsource

define('AJB_GOOGLE_MAPS_API_KEY', "AIzaSyB5Q4xZz-pGhjWfi1pAift8jI8UViiJahI");

// Include Libraries
require_once('lib/classes/pluralizer/plu-sin.php');
// Facebook Library
//require_once('lib/classes/facebook/facebook.php');
//if (version_compare(PHP_VERSION, '5.4.0', '>')) {
//	require_once('lib/classes/Facebook4.0/autoload.php');
//}


// LinkedIn Library
//require_once('lib/classes/linkedin/linkedin.php');
// Google+ Libraries
//require_once('lib/classes/google/Google_Client.php');
//require_once('lib/classes/google/contrib/Google_DriveService.php');
//require_once('lib/classes/google/contrib/Google_PlusService.php');
//require_once('lib/classes/google/contrib/Google_Oauth2Service.php');




ini_set("soap.wsdl_cache_enabled", 0);
/** Initialise User Authentication System **/

require_once(dirname( __FILE__ ) . '/admin/users.php');
$Adlogic_Job_Board_Users = new Adlogic_Job_Board_Users();
$Adlogic_Job_Board_Users->init();

/** Initialise Widgets **/

// NuSoap Library - Used as back up if SoapClient not available
// if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
// 	require_once('lib/classes/nusoap_0951/nusoap.php');
// } else {
	require_once('lib/classes/nusoap/nusoap.php');
// }

// Search Widget
require_once(dirname( __FILE__ ) . '/widgets/search.php');
$Adlogic_Search_Widget = new Adlogic_Search_Widget();
$Adlogic_Search_Widget->init();

// Hot Jobs Widget
require_once(dirname( __FILE__ ) . '/widgets/hot_jobs.php');
$Adlogic_HotJobs_Widget = new Adlogic_HotJobs_Widget();
$Adlogic_HotJobs_Widget->init();

// Job Alerts Widget
require_once(dirname( __FILE__ ) . '/widgets/job_alerts.php');
$Adlogic_Alerts_Widget = new Adlogic_Alerts_Widget();
$Adlogic_Alerts_Widget->init();

// Submit CV Widget
require_once(dirname( __FILE__ ) . '/widgets/submit_cv.php');
$Adlogic_Submit_CV_Widget = new Adlogic_Submit_CV_Widget();
$Adlogic_Submit_CV_Widget->init();

// Account Management Widget
require_once(dirname( __FILE__ ) . '/widgets/account_management.php');
$Adlogic_Account_Management_Widget = new Adlogic_Account_Management_Widget();
$Adlogic_Account_Management_Widget->init();

/** Initialise Admin / Settings Section **/
// Admin Menu Sections
require_once(dirname( __FILE__ ) . '/admin/settings_panel.php');

/** Initialise Admin / Sitemap.xml Generator **/
// Add Sitemap XML Generator Plugin
require_once(dirname( __FILE__ ) . '/admin/sitemap.php');

/** Initialise Admin / Plugin Auto Update **/
// Add Plugin Auto Update Hooks
require_once(dirname( __FILE__ ) . '/admin/update.php');

/** Initialise Shortcodes **/
// Add Search Page Shortcode
require_once(dirname( __FILE__ ) . '/shortcodes/search-page.php');
$Adlogic_Search_Shortcodes = new Adlogic_Search_Shortcodes();
$Adlogic_Search_Shortcodes->init();

// Add Job Details Shortcode
require_once(dirname( __FILE__ ) . '/shortcodes/job-details.php');
$Adlogic_Job_Details_Shortcodes = new Adlogic_Job_Details_Shortcodes();
$Adlogic_Job_Details_Shortcodes->init();


// Add Search Page Shortcode
require_once(dirname( __FILE__ ) . '/shortcodes/saved-jobs.php');
$Adlogic_Saved_Jobs_Shortcodes = new Adlogic_Saved_Jobs_Shortcodes();
$Adlogic_Saved_Jobs_Shortcodes->init();

// Add Meta Box Code
//require_once(dirname( __FILE__ ) . '/meta.php');


// Include JS + AJAX Server Listener
add_filter('init', array('Adlogic_Job_Board', 'init'));

// Register Post activation hook
register_activation_hook( __FILE__, array('Adlogic_Job_Board', 'activate_plugin'));

// Register post deactivation hook
register_deactivation_hook( __FILE__, array('Adlogic_Job_Board', 'deactivate_plugin'));

// Rewrite Hooks for plugin queries
add_action( 'generate_rewrite_rules', array('Adlogic_Job_Board', 'generate_rewrite_rules') );
add_filter( 'query_vars', array('Adlogic_Job_Board', 'add_query_vars') );
add_action( 'parse_query', array('Adlogic_Job_Board', 'update_parse_query_filter') );

class Adlogic_Job_Board {

	static $setupErrorMsg;
	static $page_editor_plugins = array();

	static function incomplete_setup() {
		?>
		<div class="error">
			<p><?php print self::$setupErrorMsg; ?></p>
		</div>
		<?php
	}

	function check_setup() {
		// Register the new widget
		$searchSettings = get_option('adlogic_search_settings');
		$rssSettings = get_option('adlogic_rss_settings');

		/* delete_option('adlogic_api_settings');
		delete_option('adlogic_rss_settings');
		delete_option('adlogic_search_settings');
		delete_option('adlogic_mobile_settings');
		delete_option('adlogic_cache_settings');
		delete_option('adlogic_first_setup_settings'); */

		$apiSettings = get_option('adlogic_api_settings');

		if (($apiSettings == false) || ($rssSettings == false) || ($searchSettings == false)) {
			self::$setupErrorMsg = '<strong>MyRecruitment+ Job Board</strong> is <em>Disabled</em> - Please complete the <a href="' . get_admin_url(null, 'admin.php?page=adlogic-job-board', 'admin') . '">setup</a> in order to enable the plugin';
			add_action( 'admin_notices', array('Adlogic_Job_Board', 'incomplete_setup'));
			return false;
		}

		if (empty($apiSettings['adlogic_recruiter_id'])) {
			self::$setupErrorMsg = '<strong>MyRecruitment+ Job Board</strong> is <em>Disabled</em> - You need to enter your Recruiter ID as supplied by MyRecruitment+ in order to enable the plugin';
			add_action( 'admin_notices', array('Adlogic_Job_Board', 'incomplete_setup'));
			return false;
		}

		if (empty($apiSettings['adlogic_advertiser_id'])) {
			self::$setupErrorMsg = '<strong>MyRecruitment+ Job Board</strong> is <em>Disabled</em> - You need to enter your Advertiser ID as supplied by MyRecruitment+ in order to enable the plugin';
			add_action( 'admin_notices', array('Adlogic_Job_Board', 'incomplete_setup'));
			return false;
		}

		if (empty($apiSettings['adlogic_soap_server'])) {
			self::$setupErrorMsg = '<strong>MyRecruitment+ Job Board</strong> is <em>Disabled</em> - You need to enter the API Server as supplied by MyRecruitment+ in order to enable the plugin';
			add_action( 'admin_notices', array('Adlogic_Job_Board', 'incomplete_setup'));
			return false;
		}

		return true;
	}

	static function add_rss_tags() {

		$adlogicRssBaseUrl = '/adlogic-jobs/rss';

		global $wp_rewrite;
		if ($wp_rewrite->using_permalinks()) {
			$adlogicRssUrl = home_url() . $adlogicRssBaseUrl;
		} else {
			$adlogicRssUrl = home_url() . '/' . basename($_SERVER['SCRIPT_FILENAME']) . $adlogicRssBaseUrl;
		}

		$rssSettings = get_option('adlogic_rss_settings');

		print '<link rel="alternate" type="application/rss+xml" title="' . (isset($rssSettings["adlogic_rss_title"]) ? $rssSettings["adlogic_rss_title"] : get_bloginfo("name") ) . '" href="' . $adlogicRssUrl . '" />';
	}

	static function add_sharethis_publisher_code() {
		$apiSettings = get_option('adlogic_api_settings');
		print '<script type="text/javascript">if (typeof(stLight) == "object") { stLight.options({publisher: "' . $apiSettings['adlogic_sharethis_publisher_key'] . '"}); } </script>';
	}

	static function add_head_flouc_fix() {
	?>
		<style type="text/css">
			body {
				display: none;
			}
		</style>
		<script type='text/javascript'>
			(function ($) {
				$(document).ready(function() {
					var ie7 = false;
					<!--[if lte IE 7]>
					var ie7 = true;
					<!-- <![endif]-->
					if (ie7 == false) {
						$('body').css('display', 'inherit');
					} else {
						$('body').css('display', 'block');
					}
				});
			})(jQuery);
		</script>
	<?php
	}

	static function add_footer_flouc_fix() {
		?>
		<noscript>
			<style type="text/css">
				body {
					display: inherit;
				}
			</style>
			<!--[if lte IE 7]>
			<style type="text/css">
				body {
					display: block;
				}
			</style>
			<!-- <![endif]-->
		</noscript>
		<?php
	}

	static function init() {
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		// Admin Section Initialisation
		if (is_admin()) {

			$Adlogic_Job_Board->check_setup();
			// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)

			// Load Custom Page Editor Buttons
			add_action('admin_head', array('Adlogic_Job_Board', 'do_page_editor_buttons'));

			// Add relevant links for the plugin in the plugins menu
			add_filter('plugin_action_links', array(__CLASS__, 'do_plugin_links'), 10, 2);
			add_filter('plugin_row_meta', array(__CLASS__, 'do_plugin_meta_links'), 10, 2);

		} else if (!is_admin()) {	// Non-Admin Initialisation

			// Get API Settings
			$apiSettings = get_option('adlogic_api_settings');

			// Add API setting for Candidate Registration WSDL if it doesn't exist
			if (!isset($apiSettings['adlogic_candidate_soap_server']) && $Adlogic_Job_Board->check_setup()) {
				$apiSettings['adlogic_candidate_soap_server'] = 'http://rms.adlogic.com.au/CandidatesWS?wsdl';
				update_option('adlogic_api_settings', $apiSettings);
			}

			// Add Job RSS Feed details to head
			add_action('wp_head', array('Adlogic_Job_Board', 'add_rss_tags'));

			// Add code to fix Flash of Unstyled Content (FLOUC)
			if ((!isset($apiSettings['adlogic_api_flouc_fix'])) || ($apiSettings['adlogic_api_flouc_fix'] == 'true')) {
				add_action('wp_head', array(__CLASS__, 'add_head_flouc_fix'));
				add_action('wp_footer', array(__CLASS__, 'add_footer_flouc_fix'));
			}

			// Add Login Dialog
			add_action('wp_footer', array('Adlogic_Job_Board_Users', 'generateLoginDialog'));

			// Add ShareThis Publisher Id if specified in settings
			if (!empty($apiSettings['adlogic_sharethis_publisher_key'])) {
				// Add javascript code to footer with lowest priority
				add_action('wp_footer', array('Adlogic_Job_Board', 'add_sharethis_publisher_code'), 1000);
			}

			/**
			 * subSourceId Detection
			 * Detects where the traffic source came from
			 */
			// start session
			$Adlogic_Job_Board->start_session();

			// Check Referrer Data for setting subSourceId
			if (isset($_GET['subSourceId'])) {
				switch($_GET['subSourceId']) {
					case AJB_SUBSOURCE_ID_ALERTS:
						break;
					case AJB_SUBSOURCE_ID_GOOGLE:
						break;
					case AJB_SUBSOURCE_ID_OSE:
						break;
					case AJB_SUBSOURCE_ID_SMS:
						break;
					case AJB_SUBSOURCE_ID_STF:
						break;
					case AJB_SUBSOURCE_ID_PDTV:
						break;
					case AJB_SUBSOURCE_ID_MUMBRL:
						break;
					case AJB_SUBSOURCE_ID_MNTWk:
						break;
					case AJB_SUBSOURCE_ID_RT:
						break;
					default:
						break;
				}

				// Store subSourceId in Session
				$_SESSION['adlogicSubSourceId'] = $_GET['subSourceId'];
			} else if ((isset($_SERVER['HTTP_REFERER'])) && (stripos($_SERVER['HTTP_REFERER'], 'google'))) {
				$_SESSION['adlogicSubSourceId'] = AJB_SUBSOURCE_ID_GOOGLE;
			} else if ((isset($_SERVER['HTTP_REFERER'])) && ((stripos($_SERVER['HTTP_REFERER'], 'yahoo')) || (stripos($_SERVER['HTTP_REFERER'], 'bing')))) {
				$_SESSION['adlogicSubSourceId'] = AJB_SUBSOURCE_ID_OSE;
			} else if ((isset($_SERVER['HTTP_REFERER'])) && (stripos($_SERVER['HTTP_REFERER'], 'pedestrian'))) {
				$_SESSION['adlogicSubSourceId'] = AJB_SUBSOURCE_ID_PDTV;
			} else if ((isset($_SERVER['HTTP_REFERER'])) && (stripos($_SERVER['HTTP_REFERER'], 'mumbrella'))) {
				$_SESSION['adlogicSubSourceId'] = AJB_SUBSOURCE_ID_MUMBRL;
			} else if ((isset($_SERVER['HTTP_REFERER'])) && (stripos($_SERVER['HTTP_REFERER'], 'themusicnetwork'))) {
				$_SESSION['adlogicSubSourceId'] = AJB_SUBSOURCE_ID_MNTWk;
			} else if ((isset($_SERVER['HTTP_REFERER'])) && (stripos($_SERVER['HTTP_REFERER'], 'radiotoday'))) {
				$_SESSION['adlogicSubSourceId'] = AJB_SUBSOURCE_ID_RT;
			}

			//New Origin Sourcing
			$Adlogic_Job_Board->checkOriginSourceId();
			

			// Gather Referer Url if any
			$aUrlComponents = "";
			if(isset($_SERVER["HTTP_REFERER"])) {
				$aUrlComponents = parse_url($_SERVER['HTTP_REFERER']);
			}

			$aCurrentSiteComponents = parse_url(home_url());

			// Store Referrer Url in session
			if ((isset($aUrlComponents['host']) && ($aUrlComponents['host'] != $aCurrentSiteComponents['host']))) {
				$_SESSION['adlogicReferrerUrl'] = $_SERVER['HTTP_REFERER'];
			}

			// Register scripts and style sheets for plugin
			$Adlogic_Job_Board->registerScriptsAndStylesheets();

			// Init AJAX Server Listener
			$parsed_url = parse_url(home_url());
			$path = str_replace(home_url(), '', $parsed_url['scheme'] . '://' . $parsed_url['host'] . $_SERVER['REQUEST_URI']);

			// Handle URL Rewriting
			global $wp_rewrite;

			if ($wp_rewrite->using_permalinks()) {
				$adlogicAjaxBaseUrl = '/adlogic-jobs';
				$adlogicRssBaseUrl = '/adlogic-jobs/rss';
				$adlogicBulkRssBaseUrl = '/adlogic-jobs/bulk-rss';
				$adlogicDeactivatePluginBaseUrl = '/adlogic-jobs/deactivate';
				$adlogicCallbackBaseUrl = '/adlogic-jobs/callback';

				$adlogicSettings = array(
					'ajaxurl'	=> home_url() . $adlogicAjaxBaseUrl,
					'rssurl'	=> home_url() . $adlogicRssBaseUrl,
					'bulkrssurl'	=> home_url() . $adlogicBulkRssBaseUrl,
					'mbstring'	=> extension_loaded('mbstring')
				);

				$GLOBALS['adlogic_plugin_settings']['ajaxurl'] = $adlogicSettings['ajaxurl'];
				$GLOBALS['adlogic_plugin_settings']['rssurl'] = $adlogicSettings['rssurl'];
				$GLOBALS['adlogic_plugin_settings']['bulkrssurl'] = $adlogicSettings['bulkrssurl'];

				if (substr($path, 0, strlen($adlogicRssBaseUrl)) == $adlogicRssBaseUrl) {
					require_once dirname(__FILE__) . '/lib/rss.php';
					exit(0);
				} else if(substr($path, 0, strlen($adlogicBulkRssBaseUrl)) == $adlogicBulkRssBaseUrl) {
					require_once dirname(__FILE__) . '/lib/bulk-rss.php';
					$BulkRss = new BulkRss(self::getSoapConnection());
					exit(0);
				} else if (substr($path, 0, strlen($adlogicDeactivatePluginBaseUrl)) == $adlogicDeactivatePluginBaseUrl) {
					// Add plugin kill-switch should it be required due to problems with the plugin malfunctioning
					require_once(ABSPATH . 'wp-admin/includes/plugin.php');
					$plugin_data = get_plugin_data(__FILE__, false);
					if ((isset($_GET['key'])) && (base64_decode($_GET['key']) == 'deactivate_plugin')) {
						deactivate_plugins(AJB_PLUGIN_FILE);
						wp_die( '<strong>' . $plugin_data['Name'] . '</strong> has now been deactivated!<br /><br />Return to <a href="' . get_bloginfo('url') . '">Home</a>.' );
					} else {
						wp_die( '<strong>' . $plugin_data['Name'] . '</strong><br /> <br/>Invalid key specified.<br /><br />Return to <a href="' . get_bloginfo('url') . '">Home</a>.' );
					}
					exit(0);
				} else if (substr($path, 0, strlen($adlogicCallbackBaseUrl)) == $adlogicCallbackBaseUrl) {
					// Do nothing, authentication should happen here
				} else if (substr($path, 0, strlen($adlogicAjaxBaseUrl)) == $adlogicAjaxBaseUrl) {
					require_once dirname( __FILE__ ) . '/lib/ajaxServer.php';
					exit(0);
				} else {
					if ((strstr($path, '/query/')) && (get_page_by_path( $path ) == NULL)) {
						if (substr($path, -1) == '/') {
							$_SERVER['QUERY_STRING'] = substr($path, strpos($path, '/query/')+strlen('/query/'), -1);
						} else {
							$_SERVER['QUERY_STRING'] = substr($path, strpos($path, '/query/')+strlen('/query/'), strlen($path));
						}
					}
				}

			} else {
				$adlogicAjaxBaseUrl = '/adlogic-jobs';
				$adlogicRssBaseUrl = '/adlogic-jobs/rss';
				$adlogicBulkRssBaseUrl = '/adlogic-jobs/bulk-rss';
				$adlogicDeactivatePluginBaseUrl = '/adlogic-jobs/deactivate';
				$adlogicCallbackBaseUrl = '/adlogic-jobs/callback';

				$adlogicSettings = array(
					'ajaxurl'	=> home_url() . '/' . basename($_SERVER['SCRIPT_FILENAME']) . $adlogicAjaxBaseUrl,
					'rssurl'	=> home_url() . '/' . basename($_SERVER['SCRIPT_FILENAME']) . $adlogicRssBaseUrl,
					'bulkrssurl'	=> home_url() . '/' . basename($_SERVER['SCRIPT_FILENAME']) . $adlogicBulkRssBaseUrl,
					'mbstring'	=> extension_loaded('mbstring')
				);

				// Set globals with configuration data
				$GLOBALS['adlogic_plugin_settings']['ajaxurl'] = $adlogicSettings['ajaxurl'];
				$GLOBALS['adlogic_plugin_settings']['rssurl'] = $adlogicSettings['rssurl'];
				$GLOBALS['adlogic_plugin_settings']['bulkrssurl'] = $adlogicSettings['bulkrssurl'];

				if (isset($_SERVER['PATH_INFO'])) {
					if ($_SERVER['PATH_INFO'] == $adlogicRssBaseUrl) {
						require_once dirname(__FILE__) . '/lib/rss.php';
						exit(0);
					} else if ($_SERVER['PATH_INFO'] == $adlogicBulkRssBaseUrl) {
						require_once dirname( __FILE__ ) . '/lib/bulk-rss.php';
						exit(0);
					} else if ($_SERVER['PATH_INFO'] == $adlogicDeactivatePluginBaseUrl) {
						// Add plugin kill-switch should it be required due to problems with the plugin malfunctioning
						require_once(ABSPATH . 'wp-admin/includes/plugin.php');
						$plugin_data = get_plugin_data(__FILE__, false);
						if ((isset($_GET['key'])) && (base64_decode($_GET['key']) == 'deactivate_plugin')) {
							deactivate_plugins(AJB_PLUGIN_FILE);
							wp_die( '<strong>' . $plugin_data['Name'] . '</strong> has now been deactivated!<br /><br />Return to <a href="' . get_bloginfo('url') . '">Home</a>.' );
						} else {
							wp_die( '<strong>' . $plugin_data['Name'] . '</strong><br /> <br/>Invalid key specified.<br /><br />Return to <a href="' . get_bloginfo('url') . '">Home</a>.' );
						}
						exit(0);
					} else if ($_SERVER['PATH_INFO'] == $adlogicCallbackBaseUrl) {
						// Callback URL reached
						Adlogic_Job_Board_Users::processLogin();
						exit(0);
					} else if ($_SERVER['PATH_INFO'] == $adlogicAjaxBaseUrl) {
						require_once dirname( __FILE__ ) . '/lib/ajaxServer.php';
						exit(0);
					}
				}
			}

			// Mobile Browser Redirection
			$apiSettings = get_option('adlogic_api_settings');
			$mobileSettings = get_option('adlogic_mobile_settings');
			
			// For <1.6.4 compatibility
			if (
					(
							!isset($mobileSettings['adlogic_mobile_site']) ||
							empty($mobileSettings['adlogic_mobile_site'])
					) &&
					isset($apiSettings['adlogic_mobile_site']) &&
					(!empty($apiSettings['adlogic_mobile_site']))
			) {
				if (is_array($mobileSettings)) {
					$mobileSettings['adlogic_mobile_site'] = $apiSettings['adlogic_mobile_site'];
				} else {
					$mobileSettings = array('adlogic_mobile_site' => $apiSettings['adlogic_mobile_site']);
				}
				// Update mobile settings
				update_option('adlogic_mobile_settings', $mobileSettings);
				// Re-load mobile settings
				$mobileSettings = get_option('adlogic_mobile_settings');
			}

			// If mobile site is filled in settings, then redirect to mobile site for mobile browsers
			if (isset($mobileSettings['adlogic_mobile_site']) && (!empty($mobileSettings['adlogic_mobile_site']))) {

				$mobile_browser = $Adlogic_Job_Board->is_mobile_browser();

				// If mobile browser then create a session and redirect to mobile site
				if ($mobile_browser == true) {
					/*
					 * Adlogic Mobile Job Board Redirection
					 * 1) Check if there's entered settings for Adlogic Mobile Job Board Search Page and Job Details page
					 * 2) Detect if page being viewed is either a Job Details Page or Job Search Page
					 * 3) If it is, then redirect to those urls rather than the main mobile url
					 */

					if ($real_page_path) {
					$pageObj = get_page_by_path( $real_page_path );
					} else {
						$pageObj = get_page_by_path( $path );
						if (!$pageObj) {
							$pageObj = get_page(get_option('page_on_front'));
						}
					}

					$page_type = 'standard_page';

					if (!empty($pageObj)) {
						$pattern = get_shortcode_regex();
						preg_match_all( "/$pattern/s", $pageObj->post_content, $matches );
						/**
						 * $matches[2] matches the shortcode name
						 * @see do_shortcode_tag()
						*/
						foreach ($matches[2] as $match) {
							if ($match == 'adlogic_job_details') {
								// Matches a job details page
								$page_type = 'job_details';
								break;
							} else if ($match == 'adlogic_search_results') {
								$page_type = 'search_page';
							}
						}
					}

					// Start Session
					$Adlogic_Job_Board->start_session();

					// Allow ability to force to view desktop version
					if ($_GET['force_desktop'] == 'true') {
						$_SESSION['force_desktop'] = true;
					} else if (($_SESSION['force_desktop'] == false) || ($_GET['force_desktop'] == 'false')) {
						$_SESSION['force_desktop'] = false;
						if (($page_type == 'search_page') && (isset($mobileSettings['adlogic_mobile_search_url'])) && (!empty($mobileSettings['adlogic_mobile_search_url']))) {
							header('Location: ' . $mobileSettings['adlogic_mobile_search_url'] . '?' . $_SERVER['QUERY_STRING']);
						} else if (($page_type == 'job_details') && (isset($mobileSettings['adlogic_mobile_job_details_url'])) && (!empty($mobileSettings['adlogic_mobile_job_details_url']))) {
							header('Location: ' . $mobileSettings['adlogic_mobile_job_details_url'] . '?' . $_SERVER['QUERY_STRING']);
						} else {
							header('Location: ' . $mobileSettings['adlogic_mobile_site']);
						}
						exit;
					} else if ($_SESSION['force_desktop'] == true) {
					} else {
						header('Location: ' . $mobileSettings['adlogic_mobile_site']);
						exit;
					}
				}
			}

			// Detect Normal Web Browser on Mobile site, when alternative site is available and no override is in place
			global $wptouch_plugin;
			global $wptouch_pro;

			$mobile_browser = $Adlogic_Job_Board->is_mobile_browser();

			if (
					((
						isset($wptouch_plugin) &&
						$mobile_browser == false
					) ||
					(
						isset($wptouch_pro) &&
						$mobile_browser == false
					)) &&
					!empty($mobileSettings['adlogic_desktop_site'])
			) {
				/*
				 * Adlogic Mobile Job Board Redirection
				* 1) Check if there's entered settings for Adlogic Mobile Job Board Search Page and Job Details page
				* 2) Detect if page being viewed is either a Job Details Page or Job Search Page
				* 3) If it is, then redirect to those urls rather than the main mobile url
				*/

				$real_page_path = substr($path, 0, strpos($path, '/query/'));

				if ($real_page_path) {
					$pageObj = get_page_by_path( $real_page_path );
				} else {
					$pageObj = get_page_by_path( $path );
					if (!$pageObj) {
						$pageObj = get_page(get_option('page_on_front'));
					}
				}

				$page_type = 'standard_page';

				if (!empty($pageObj)) {
					$pattern = get_shortcode_regex();
					preg_match_all( "/$pattern/s", $pageObj->post_content, $matches );

					/**
					 * $matches[2] matches the shortcode name
					 * @see do_shortcode_tag()
					*/
					foreach ($matches[2] as $i => $match) {
						if ($match == 'adlogic_job_details') {
							// Matches a job details page
							$page_type = 'job_details';
							break;
						} else if ($match == 'adlogic_search_results') {
							$page_type = 'search_page';
						} else if ($match == 'wptouch') {
							// WPTouch match found! Check for our shortcode in it's content
							preg_match_all( "/$pattern/s", $matches[5][$i], $wptouch_matches );

							foreach ($wptouch_matches[2] as $ii => $wptouch_match) {
								if ($wptouch_match == 'adlogic_job_details') {
									$page_type = 'job_details';
								}
							}
						}
					}
				}

				// Start Session
				$Adlogic_Job_Board->start_session();

				// Allow ability to force to view desktop version
				if ($_GET['force_mobile'] == 'true') {
					$_SESSION['force_mobile'] = true;
				} else if (($_SESSION['force_mobile'] == false) || ($_GET['force_mobile'] == 'false')) {
					$_SESSION['force_mobile'] = false;
					if (($page_type == 'search_page') && (isset($mobileSettings['adlogic_desktop_search_url'])) && (!empty($mobileSettings['adlogic_desktop_search_url']))) {
						header('Location: ' . $mobileSettings['adlogic_desktop_search_url'] . '?' . $_SERVER['QUERY_STRING']);
					} else if (($page_type == 'job_details') && (isset($mobileSettings['adlogic_desktop_job_details_url'])) && (!empty($mobileSettings['adlogic_desktop_job_details_url']))) {
						header('Location: ' . $mobileSettings['adlogic_desktop_job_details_url'] . '?' . $_SERVER['QUERY_STRING']);
					} else {
						header('Location: ' . $mobileSettings['adlogic_desktop_site']);
					}
					exit;
				} else if ($_SESSION['force_mobile'] == true) {
				} else {
					header('Location: ' . $mobileSettings['adlogic_mobile_site']);
					exit;
				}
			}

			wp_localize_script( 'jquery-adlogic-settings', 'adlogicJobSearch', $adlogicSettings );
		}

		// Set Up Plugin Auto Update
		new Adlogic_Plugin_Update(AJB_VERSION, AJB_UPDATE_URL, AJB_PLUGIN_FILE);

		// We check if we should migrate to the new application form.
		if(isset($apiSettings['adlogic_custom_application_page']) && ($apiSettings['adlogic_custom_application_page'] != null || $apiSettings['adlogic_custom_application_page'] != '')) {
			if (is_array($apiSettings)) {
				$apiSettings['adlogic_custom_application_page'] = '';
				$apiSettings['adlogic_custom_application_url'] = '';
			} else {
				$apiSettings = array(
					'adlogic_custom_application_page' => '',
					'adlogic_custom_application_url' => ''
				);
			}
			// Update api settings
			update_option('adlogic_api_settings', $apiSettings);
		}

	}

	static function uriSafe($string) {
		// Check if mbstring extension exists to select which method of url replacement to use
		if (!extension_loaded('mbstring')) {
			return str_replace('%2F','/', urlencode(str_replace(array('&','#'), array('and', ' '), (string) $string)));
		} else {
			$string = trim($string);
			$jsonCharacterMap = '[{"2d":"-","20":"-","24":"s","26":"and","30":"0","31":"1","32":"2","33":"3","34":"4","35":"5","36":"6","37":"7","38":"8","39":"9","41":"A","42":"B","43":"C","44":"D","45":"E","46":"F","47":"G","48":"H","49":"I","50":"P","51":"Q","52":"R","53":"S","54":"T","55":"U","56":"V","57":"W","58":"X","59":"Y","61":"a","62":"b","63":"c","64":"d","65":"e","66":"f","67":"g","68":"h","69":"i","70":"p","71":"q","72":"r","73":"s","74":"t","75":"u","76":"v","77":"w","78":"x","79":"y","100":"A","101":"a","102":"A","103":"a","104":"A","105":"a","106":"C","107":"c","108":"C","109":"c","110":"D","111":"d","112":"E","113":"e","114":"E","115":"e","116":"E","117":"e","118":"E","119":"e","120":"G","121":"g","122":"G","123":"g","124":"H","125":"h","126":"H","127":"h","128":"I","129":"i","130":"I","131":"i","132":"IJ","133":"ij","134":"J","135":"j","136":"K","137":"k","138":"k","139":"L","140":"l","141":"L","142":"l","143":"N","144":"n","145":"N","146":"n","147":"N","148":"n","149":"n","150":"O","151":"o","152":"OE","153":"oe","154":"R","155":"r","156":"R","157":"r","158":"R","159":"r","160":"S","161":"s","162":"T","163":"t","164":"T","165":"t","166":"T","167":"t","168":"U","169":"u","170":"U","171":"u","172":"U","173":"u","174":"W","175":"w","176":"Y","177":"y","178":"Y","179":"Z","180":"b","181":"B","182":"b","183":"b","184":"b","185":"b","186":"C","187":"C","188":"c","189":"D","190":"E","191":"F","192":"f","193":"G","194":"Y","195":"h","196":"i","197":"I","198":"K","199":"k","200":"A","201":"a","202":"A","203":"a","204":"E","205":"e","206":"E","207":"e","208":"I","209":"i","210":"R","211":"r","212":"R","213":"r","214":"U","215":"u","216":"U","217":"u","218":"S","219":"s","220":"n","221":"d","222":"8","223":"8","224":"Z","225":"z","226":"A","227":"a","228":"E","229":"e","230":"O","231":"o","232":"Y","233":"y","234":"l","235":"n","236":"t","237":"j","238":"db","239":"qp","240":"<","241":"?","242":"?","243":"B","244":"U","245":"A","246":"E","247":"e","248":"J","249":"j","250":"a","251":"a","252":"a","253":"b","254":"c","255":"e","256":"d","257":"d","258":"e","259":"e","260":"g","261":"g","262":"g","263":"Y","264":"x","265":"u","266":"h","267":"h","268":"i","269":"i","270":"w","271":"m","272":"n","273":"n","274":"N","275":"o","276":"oe","277":"m","278":"o","279":"r","280":"R","281":"R","282":"S","283":"f","284":"f","285":"f","286":"f","287":"t","288":"t","289":"u","290":"Z","291":"Z","292":"3","293":"3","294":"?","295":"?","296":"5","297":"C","298":"O","299":"B","363":"a","364":"e","365":"i","366":"o","367":"u","368":"c","369":"d","386":"A","388":"E","389":"H","390":"i","391":"A","392":"B","393":"r","394":"A","395":"E","396":"Z","397":"H","398":"O","399":"I","400":"E","401":"E","402":"T","403":"r","404":"E","405":"S","406":"I","407":"I","408":"J","409":"jb","410":"A","411":"B","412":"B","413":"r","414":"D","415":"E","416":"X","417":"3","418":"N","419":"N","420":"P","421":"C","422":"T","423":"y","424":"O","425":"X","426":"U","427":"h","428":"W","429":"W","430":"a","431":"6","432":"B","433":"r","434":"d","435":"e","436":"x","437":"3","438":"N","439":"N","440":"P","441":"C","442":"T","443":"Y","444":"qp","445":"x","446":"U","447":"h","448":"W","449":"W","450":"e","451":"e","452":"h","453":"r","454":"e","455":"s","456":"i","457":"i","458":"j","459":"jb","460":"W","461":"w","462":"Tb","463":"tb","464":"IC","465":"ic","466":"A","467":"a","468":"IA","469":"ia","470":"Y","471":"y","472":"O","473":"o","474":"V","475":"v","476":"V","477":"v","478":"Oy","479":"oy","480":"C","481":"c","490":"R","491":"r","492":"F","493":"f","494":"H","495":"h","496":"X","497":"x","498":"3","499":"3","500":"d","501":"d","502":"d","503":"d","504":"R","505":"R","506":"R","507":"R","508":"JT","509":"JT","510":"E","511":"e","512":"JT","513":"jt","514":"JX","515":"JX","531":"U","532":"D","533":"Q","534":"N","535":"T","536":"2","537":"F","538":"r","539":"p","540":"z","541":"2","542":"n","543":"x","544":"U","545":"B","546":"j","547":"t","548":"n","549":"C","550":"R","551":"8","552":"R","553":"O","554":"P","555":"O","556":"S","561":"w","562":"f","563":"q","564":"n","565":"t","566":"q","567":"t","568":"n","569":"p","570":"h","571":"a","572":"n","573":"a","574":"u","575":"j","576":"u","577":"2","578":"n","579":"2","580":"n","581":"g","582":"l","583":"uh","584":"p","585":"o","586":"S","587":"u","4a":"J","4b":"K","4c":"L","4d":"M","4e":"N","4f":"O","5a":"Z","6a":"j","6b":"k","6c":"l","6d":"m","6e":"n","6f":"o","7a":"z","a2":"c","a3":"f","a5":"Y","a7":"s","a9":"c","aa":"a","ae":"r","b2":"2","b3":"3","b5":"u","b6":"p","b9":"1","c0":"A","c1":"A","c2":"A","c3":"A","c4":"A","c5":"A","c6":"AE","c7":"C","c8":"E","c9":"E","ca":"E","cb":"E","cc":"I","cd":"I","ce":"I","cf":"I","d0":"D","d1":"N","d2":"O","d3":"O","d4":"O","d5":"O","d6":"O","d7":"X","d8":"O","d9":"U","da":"U","db":"U","dc":"U","dd":"Y","de":"p","df":"b","e0":"a","e1":"a","e2":"a","e3":"a","e4":"a","e5":"a","e6":"ae","e7":"c","e8":"e","e9":"e","ea":"e","eb":"e","ec":"i","ed":"i","ee":"i","ef":"i","f0":"o","f1":"n","f2":"o","f3":"o","f4":"o","f5":"o","f6":"o","f8":"o","f9":"u","fa":"u","fb":"u","fc":"u","fd":"y","ff":"y","10a":"C","10b":"c","10c":"C","10d":"c","10e":"D","10f":"d","11a":"E","11b":"e","11c":"G","11d":"g","11e":"G","11f":"g","12a":"I","12b":"i","12c":"I","12d":"i","12e":"I","12f":"i","13a":"l","13b":"L","13c":"l","13d":"L","13e":"l","13f":"L","14a":"n","14b":"n","14c":"O","14d":"o","14e":"O","14f":"o","15a":"S","15b":"s","15c":"S","15d":"s","15e":"S","15f":"s","16a":"U","16b":"u","16c":"U","16d":"u","16e":"U","16f":"u","17a":"z","17b":"Z","17c":"z","17d":"Z","17e":"z","17f":"f","18a":"D","18b":"d","18c":"d","18d":"q","18e":"E","18f":"e","19a":"l","19b":"h","19c":"w","19d":"N","19e":"n","19f":"O","1a0":"O","1a1":"o","1a2":"P","1a3":"P","1a4":"P","1a5":"p","1a6":"R","1a7":"S","1a8":"s","1a9":"E","1aa":"l","1ab":"t","1ac":"T","1ad":"t","1ae":"T","1af":"U","1b0":"u","1b1":"U","1b2":"U","1b3":"Y","1b4":"y","1b5":"Z","1b6":"z","1b7":"3","1b8":"3","1b9":"3","1ba":"3","1bb":"2","1bc":"5","1bd":"5","1be":"5","1bf":"p","1c4":"DZ","1c5":"Dz","1c6":"dz","1c7":"Lj","1c8":"Lj","1c9":"lj","1ca":"NJ","1cb":"Nj","1cc":"nj","1cd":"A","1ce":"a","1cf":"I","1d0":"i","1d1":"O","1d2":"o","1d3":"U","1d4":"u","1d5":"U","1d6":"u","1d7":"U","1d8":"u","1d9":"U","1da":"u","1db":"U","1dc":"u","1dd":"e","1de":"A","1df":"a","1e0":"A","1e1":"a","1e2":"AE","1e3":"ae","1e4":"G","1e5":"g","1e6":"G","1e7":"g","1e8":"K","1e9":"k","1ea":"Q","1eb":"q","1ec":"Q","1ed":"q","1ee":"3","1ef":"3","1f0":"J","1f1":"dz","1f2":"dZ","1f3":"DZ","1f4":"g","1f5":"G","1f6":"h","1f7":"p","1f8":"N","1f9":"n","1fa":"A","1fb":"a","1fc":"AE","1fd":"ae","1fe":"O","1ff":"o","20a":"I","20b":"i","20c":"O","20d":"o","20e":"O","20f":"o","21a":"T","21b":"t","21c":"3","21d":"3","21e":"H","21f":"h","22a":"O","22b":"o","22c":"O","22d":"o","22e":"O","22f":"o","23a":"A","23b":"C","23c":"c","23d":"L","23e":"T","23f":"s","24a":"Q","24b":"q","24c":"R","24d":"r","24e":"Y","24f":"y","25a":"e","25b":"3","25c":"3","25d":"3","25e":"3","25f":"j","26a":"i","26b":"I","26c":"I","26d":"I","26e":"h","26f":"w","27a":"R","27b":"r","27c":"R","27d":"R","27e":"r","27f":"r","28a":"u","28b":"v","28c":"A","28d":"M","28e":"Y","28f":"Y","29a":"B","29b":"G","29c":"H","29d":"j","29e":"K","29f":"L","2a0":"q","2a1":"?","2a2":"c","2a3":"dz","2a4":"d3","2a5":"dz","2a6":"ts","2a7":"tf","2a8":"tc","2a9":"fn","2aa":"ls","2ab":"lz","2ac":"ww","2ae":"u","2af":"u","2b0":"h","2b1":"h","2b2":"j","2b3":"r","2b4":"r","2b5":"r","2b6":"R","2b7":"W","2b8":"Y","2df":"x","2e0":"Y","2e1":"1","2e2":"s","2e3":"x","2e4":"c","36a":"h","36b":"m","36c":"r","36d":"t","36e":"v","36f":"x","37b":"c","37c":"c","37d":"c","38a":"I","38c":"O","38e":"Y","38f":"O","39a":"K","39b":"A","39c":"M","39d":"N","39e":"E","39f":"O","3a0":"TT","3a1":"P","3a3":"E","3a4":"T","3a5":"Y","3a6":"O","3a7":"X","3a8":"Y","3a9":"O","3aa":"I","3ab":"Y","3ac":"a","3ad":"e","3ae":"n","3af":"i","3b0":"v","3b1":"a","3b2":"b","3b3":"y","3b4":"d","3b5":"e","3b6":"c","3b7":"n","3b8":"0","3b9":"1","3ba":"k","3bb":"j","3bc":"u","3bd":"v","3be":"c","3bf":"o","3c0":"tt","3c1":"p","3c2":"s","3c3":"o","3c4":"t","3c5":"u","3c6":"q","3c7":"X","3c8":"Y","3c9":"w","3ca":"i","3cb":"u","3cc":"o","3cd":"u","3ce":"w","3d0":"b","3d1":"e","3d2":"Y","3d3":"Y","3d4":"Y","3d5":"O","3d6":"w","3d7":"x","3d8":"Q","3d9":"q","3da":"C","3db":"c","3dc":"F","3dd":"f","3de":"N","3df":"N","3e2":"W","3e3":"w","3e4":"q","3e5":"q","3e6":"h","3e7":"e","3e8":"S","3e9":"s","3ea":"X","3eb":"x","3ec":"6","3ed":"6","3ee":"t","3ef":"t","3f0":"x","3f1":"e","3f2":"c","3f3":"j","3f4":"O","3f5":"E","3f6":"E","3f7":"p","3f8":"p","3f9":"C","3fa":"M","3fb":"M","3fc":"p","3fd":"C","3fe":"C","3ff":"C","40a":"Hb","40b":"Th","40c":"K","40d":"N","40e":"Y","40f":"U","41a":"K","41b":"jI","41c":"M","41d":"H","41e":"O","41f":"TT","42a":"b","42b":"bI","42c":"b","42d":"E","42e":"IO","42f":"R","43a":"K","43b":"JI","43c":"M","43d":"H","43e":"O","43f":"N","44a":"b","44b":"bI","44c":"b","44d":"e","44e":"io","44f":"r","45a":"Hb","45b":"h","45c":"k","45d":"n","45e":"y","45f":"u","46a":"mY","46b":"my","46c":"Im","46d":"Im","46e":"3","46f":"3","47a":"O","47b":"o","47c":"W","47d":"w","47e":"W","47f":"W","48a":"H","48b":"H","48c":"B","48d":"b","48e":"P","48f":"p","49a":"K","49b":"k","49c":"K","49d":"k","49e":"K","49f":"k","4a0":"K","4a1":"k","4a2":"H","4a3":"h","4a4":"H","4a5":"h","4a6":"Ih","4a7":"ih","4a8":"O","4a9":"o","4aa":"C","4ab":"c","4ac":"T","4ad":"t","4ae":"Y","4af":"y","4b0":"Y","4b1":"y","4b2":"X","4b3":"x","4b4":"TI","4b5":"ti","4b6":"H","4b7":"h","4b8":"H","4b9":"h","4ba":"H","4bb":"h","4bc":"E","4bd":"e","4be":"E","4bf":"e","4c0":"I","4c1":"X","4c2":"x","4c3":"K","4c4":"k","4c5":"jt","4c6":"jt","4c7":"H","4c8":"h","4c9":"H","4ca":"h","4cb":"H","4cc":"h","4cd":"M","4ce":"m","4cf":"l","4d0":"A","4d1":"a","4d2":"A","4d3":"a","4d4":"AE","4d5":"ae","4d6":"E","4d7":"e","4d8":"e","4d9":"e","4da":"E","4db":"e","4dc":"X","4dd":"X","4de":"3","4df":"3","4e0":"3","4e1":"3","4e2":"N","4e3":"n","4e4":"N","4e5":"n","4e6":"O","4e7":"o","4e8":"O","4e9":"o","4ea":"O","4eb":"o","4ec":"E","4ed":"e","4ee":"Y","4ef":"y","4f0":"Y","4f1":"y","4f2":"Y","4f3":"y","4f4":"H","4f5":"h","4f6":"R","4f7":"r","4f8":"bI","4f9":"bi","4fa":"F","4fb":"f","4fc":"X","4fd":"x","4fe":"X","4ff":"x","50a":"H","50b":"h","50c":"G","50d":"g","50e":"T","50f":"t","51a":"Q","51b":"q","51c":"W","51d":"w","53a":"d","53b":"r","53c":"L","53d":"Iu","53e":"O","53f":"y","54a":"m","54b":"o","54c":"N","54d":"U","54e":"Y","54f":"S","56a":"d","56b":"h","56c":"l","56d":"lu","56e":"d","56f":"y","57a":"w","57b":"2","57c":"n","57d":"u","57e":"y","57f":"un"}]';
			$decodedCharacterMap = json_decode($jsonCharacterMap);
			$charMap = array_pop($decodedCharacterMap);

			$stringReplaced = '';

			for ($i = 0; $i < strlen($string); $i++) {

				// Convert string from UTF-8 to UCS-4BE encoding - this is so we can map the characters to their proper replacements
				$convertedChar = mb_convert_encoding(substr($string, $i, 1), 'UCS-4BE', 'UTF-8');

				// Check if we were able to convert the character to UCS-4BE - If not replace it with a single dash
				if (strlen($convertedChar) > 0) {
					list(, $ord) = unpack('N', $convertedChar);
				} else {
					list(, $ord) = unpack('N', mb_convert_encoding('-', 'UCS-4BE', 'UTF-8'));
				}

				if (isset($charMap->{dechex($ord)})) {
					$stringReplaced .= $charMap->{dechex($ord)};
				}
			}

			while (strstr($stringReplaced, '--')) {
				$stringReplaced = str_replace('--', '-', $stringReplaced);
			}
			return strtolower($stringReplaced);
		}
	}

	/**
	 * Check a string of base64 encoded data to make sure it has actually
	* been encoded.
	*
	* @param $encodedString string Base64 encoded string to validate.
	* @return Boolean Returns true when the given string only contains
	* base64 characters; returns false if there is even one non-base64 character.
	*/
	function checkBase64Encoded($encodedString) {
		$length = strlen($encodedString);

		// Check every character.
		for ($i = 0; $i < $length; ++$i) {
			$c = $encodedString[$i];
			if (
					($c < '0' || $c > '9')
					&& ($c < 'a' || $c > 'z')
					&& ($c < 'A' || $c > 'Z')
					&& ($c != '+')
					&& ($c != '/')
					&& ($c != '=')
			) {
				// Bad character found.
				return false;
			}
		}
		// Only good characters found.
		return true;
	}

	function filter_xml($matches) {
		return trim(htmlspecialchars($matches[1]));
	}

	// Checks cache for stored xml results
	function cache_check($filename = false) {
		$cacheSettings = get_option('adlogic_cache_settings');

		// Check cache settings
		if (($cacheSettings['adlogic_cache_status'] == false) || (!isset($cacheSettings['adlogic_cache_status'])) ) {
			return false;
		}

		if (!isset($cacheSettings['adlogic_cache_timeout'])) {
			return false;
		}


		// If Multisite then cache should be stored against recruiter id
		if (defined('MULTISITE') && (MULTISITE == true)) {
			$apiSettings = get_option('adlogic_api_settings');
			$filename = $apiSettings['adlogic_recruiter_id'] . '_' . $filename;
		}

		// Check if cache index exists
		if (is_file(AJB_PLUGIN_PATH . '/cache/cache_index') && is_readable(AJB_PLUGIN_PATH . '/cache/cache_index')) {
			// Get Cache Index Data
			$cacheIndexContents = file_get_contents(AJB_PLUGIN_PATH . '/cache/cache_index');
			$cacheIndexArray = unserialize($cacheIndexContents);

			// Validate Cache Index Data
			if (is_array($cacheIndexArray)) {
				// Check if file being checked exists in the cache index data
				if (isset($cacheIndexArray[$filename])) {
					// Check if file being checked hasn't expired
					if (($cacheIndexArray[$filename]['timestamp'] + $cacheSettings['adlogic_cache_timeout']) > time()) {
						// Check if file exists in cache folder
						if (is_file(AJB_PLUGIN_PATH . '/cache/' . $filename)) {
							return true;
						} else {
							return false;
						}
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function cache_store($filename = false, $content = null) {

		$cacheSettings = get_option('adlogic_cache_settings');

		// Check cache settings
		if (($cacheSettings['adlogic_cache_status'] == false) || (!isset($cacheSettings['adlogic_cache_status'])) ) {
			return false;
		}

		if (!isset($cacheSettings['adlogic_cache_timeout'])) {
			return false;
		}

		// If Multisite then cache should be stored against recruiter id
		if (defined('MULTISITE') && (MULTISITE == true)) {
			$apiSettings = get_option('adlogic_api_settings');
			$filename = $apiSettings['adlogic_recruiter_id'] . '_' . $filename;
		}

		// Check if cache folder is writeable
		if (is_writable(AJB_PLUGIN_PATH . '/cache/')) {
			// Check if cache index exists
			if (is_file(AJB_PLUGIN_PATH . '/cache/cache_index') && is_readable(AJB_PLUGIN_PATH . '/cache/cache_index')) {
				// Get Cache Index Data
				$cacheIndexContents = file_get_contents(AJB_PLUGIN_PATH . '/cache/cache_index');
				$cacheIndexArray = unserialize($cacheIndexContents);

				// Validate Cache Index Data
				if (is_array($cacheIndexArray)) {
					$cacheIndexArray[$filename]['timestamp'] = time();
					// Open Cache Index file for writing
					$fp = fopen(AJB_PLUGIN_PATH . '/cache/cache_index', "w");
					// Serialize array data and store to cache index file
					$cacheIndexData = serialize($cacheIndexArray);
					fputs ($fp, $cacheIndexData);
					fclose($fp);
					// Create cache file
					$fpc = fopen(AJB_PLUGIN_PATH . '/cache/' . $filename, "w");
					fputs($fpc, $content);
					fclose($fpc);
					return true;
				} else {
					// Cache Index could be corrupt or empty file - so lets rebuild it
					$fp = fopen(AJB_PLUGIN_PATH . '/cache/cache_index', "w");
					$cacheIndexArray = array(
							$filename => array(
									'timestamp' => time()
							)
					);
					// Serialize array data and store to cache index file
					$cacheIndexData = serialize($cacheIndexArray);
					fputs ($fp, $cacheIndexData);
					fclose($fp);

					// Create cache file
					$fpc = fopen(AJB_PLUGIN_PATH . '/cache/' . $filename, "w");
					fputs($fpc, $content);
					fclose($fpc);
					return true;
				}
			} else {
				// Create a new Cache Index
				$fp = fopen(AJB_PLUGIN_PATH . '/cache/cache_index', "w");
				$cacheIndexArray = array(
									$filename => array(
										'timestamp' => time()
									)
								);
				// Serialize array data and store to cache index file
				$cacheIndexData = serialize($cacheIndexArray);
				fputs ($fp, $cacheIndexData);
				fclose($fp);

				// Create cache file
				$fpc = fopen(AJB_PLUGIN_PATH . '/cache/' . $filename, "w");
				fputs($fpc, $content);
				fclose($fpc);
				return true;
			}
		} else {
			error_log('Adlogic Job Board could not write to cache.  Caching disabled. Please make sure that "' . AJB_PLUGIN_PATH . '/cache/" exists and is writeable.');
			return false;
		}
	}

	static function getSoapConnection($type = 'job_board') {
		$apiSettings = get_option('adlogic_api_settings');

		// If for some reason the nusoap library isn't loaded, load nusoap
		if (!class_exists('nusoap_client')) {
			// if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
			// 	require_once('lib/classes/nusoap_0951/nusoap.php');
			// } else {
				require_once('lib/classes/nusoap/nusoap.php');
			// }
		}

		/*
		 * Instantiate Soap Client Object
		* PHP5 Native Soap is used if available, otherwise fallback on NuSoap
		*/

		$nativeSoapClientOptions = array('trace' => 1);
		$nusoapClientOptions = array();

		// Add proxy options if set up in php configuration
		if (defined('WP_PROXY_HOST')) {
			$nativeSoapClientOptions['proxy_host'] = WP_PROXY_HOST;
			$nusoapClientOptions['proxy_host'] = WP_PROXY_HOST;
		} else {
			$nusoapClientOptions['proxy_host'] = false;
		}

		if (defined('WP_PROXY_PORT')) {
			$nativeSoapClientOptions['proxy_port'] = WP_PROXY_PORT;
			$nusoapClientOptions['proxy_port'] = WP_PROXY_PORT;
		} else {
			$nusoapClientOptions['proxy_port'] = false;
		}

		if (defined('WP_PROXY_USERNAME')) {
			$nativeSoapClientOptions['proxy_login'] = WP_PROXY_USERNAME;
			$nusoapClientOptions['proxy_username'] = WP_PROXY_USERNAME;
		} else {
			$nusoapClientOptions['proxy_username'] = false;
		}


		if (defined('WP_PROXY_PASSWORD')) {
			$nativeSoapClientOptions['proxy_password'] = WP_PROXY_PASSWORD;
			$nusoapClientOptions['proxy_password'] = WP_PROXY_PASSWORD;
		} else {
			$nusoapClientOptions['proxy_password'] = false;
		}

		switch ($type) {
			case 'job_board':
				if (!extension_loaded('soap')) {
					if (PHP_SHLIB_SUFFIX === 'dll') {
						if ((function_exists('dl')) && (dl('php_soap.dll'))) {
							$oSoapClient = new SoapClient($apiSettings['adlogic_soap_server'], $nativeSoapClientOptions);
						} else {
							$oSoapClient = new nusoap_client($apiSettings['adlogic_soap_server'], 'wsdl', $nusoapClientOptions['proxy_host'], $nusoapClientOptions['proxy_port'], $nusoapClientOptions['proxy_username'], $nusoapClientOptions['proxy_password']);
							$oSoapClient->setUseCurl(true);
							$oSoapClient->useHTTPPersistentConnection();
						}
					} else {
						if ((function_exists('dl')) && (dl('soap.so'))) {
							$oSoapClient = new SoapClient($apiSettings['adlogic_soap_server'], $nativeSoapClientOptions);
						} else {
							$oSoapClient = new nusoap_client($apiSettings['adlogic_soap_server'], 'wsdl', $nusoapClientOptions['proxy_host'], $nusoapClientOptions['proxy_port'], $nusoapClientOptions['proxy_username'], $nusoapClientOptions['proxy_password']);
							$oSoapClient->setUseCurl(true);
							$oSoapClient->useHTTPPersistentConnection();
						}
					}
				} else {
					try {
						$oSoapClient = new SoapClient($apiSettings['adlogic_soap_server'], $nativeSoapClientOptions);
					} catch (Exception $e) {
						trigger_error('Cannot connect to adlogic server', E_USER_WARNING);
						return false;
					}
				}
				break;
			case 'candidate':
				if (!extension_loaded('soap')) {
					if (PHP_SHLIB_SUFFIX === 'dll') {
						if ((function_exists('dl')) && (dl('php_soap.dll'))) {
							$oSoapClient = new SoapClient($apiSettings['adlogic_candidate_soap_server'], $nativeSoapClientOptions);
						} else {
							$oSoapClient = new nusoap_client($apiSettings['adlogic_candidate_soap_server'], 'wsdl', $nusoapClientOptions['proxy_host'], $nusoapClientOptions['proxy_port'], $nusoapClientOptions['proxy_username'], $nusoapClientOptions['proxy_password']);
							$oSoapClient->setUseCurl(true);
							$oSoapClient->useHTTPPersistentConnection();
						}
					} else {
						if ((function_exists('dl')) && (dl('soap.so'))) {
							$oSoapClient = new SoapClient($apiSettings['adlogic_candidate_soap_server'], $nativeSoapClientOptions);
						} else {
							$oSoapClient = new nusoap_client($apiSettings['adlogic_candidate_soap_server'], 'wsdl', $nusoapClientOptions['proxy_host'], $nusoapClientOptions['proxy_port'], $nusoapClientOptions['proxy_username'], $nusoapClientOptions['proxy_password']);
							$oSoapClient->setUseCurl(true);
							$oSoapClient->useHTTPPersistentConnection();
						}
					}
				} else {
					try {
						$oSoapClient = new SoapClient($apiSettings['adlogic_candidate_soap_server'], $nativeSoapClientOptions);
					} catch (Exception $e) {
						trigger_error('Cannot connect to adlogic server', E_USER_WARNING);
						return false;
					}
				}
				break;
		}

		return $oSoapClient;
	}

	function cache_read($filename) {
		$cacheSettings = get_option('adlogic_cache_settings');

		// Check cache settings
		if (($cacheSettings['adlogic_cache_status'] == false) || (!isset($cacheSettings['adlogic_cache_status'])) ) {
			return false;
		}

		if (!isset($cacheSettings['adlogic_cache_timeout'])) {
			return false;
		}

		// If Multisite then cache should be stored against recruiter id
		if (defined('MULTISITE') && (MULTISITE == true)) {
			$apiSettings = get_option('adlogic_api_settings');
			$filename = $apiSettings['adlogic_recruiter_id'] . '_' . $filename;
		}

		// Check if cache file exists
		if (is_file(AJB_PLUGIN_PATH . '/cache/' . $filename) && is_readable(AJB_PLUGIN_PATH . '/cache/' . $filename)) {
			return file_get_contents(AJB_PLUGIN_PATH . '/cache/' . $filename);
		} else {
			return false;
		}
	}

	static function add_page_editor_button($name, $filename) {
		array_push(self::$page_editor_plugins, array('name' => $name, 'filename' => $filename));
	}

	static function do_page_editor_buttons() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
			return;
		}

		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array('Adlogic_Job_Board', 'add_page_editor_plugin'));
			add_filter('mce_buttons', array('Adlogic_Job_Board', 'register_page_editor_buttons'));
		}
	}

	static function add_page_editor_plugin($plugin_array) {
		foreach (self::$page_editor_plugins as $page_editor_plugin) {
			$plugin_array[$page_editor_plugin['name']] = AJB_PLUGIN_URL . 'js/TinyMCE/' . $page_editor_plugin['filename'];
		}

		return $plugin_array;
	}

	static function do_plugin_links($links, $file) {
		if ($file == AJB_PLUGIN_FILE) {
			$links['settings'] = '<a href="' . get_admin_url(null, 'admin.php?page=adlogic-job-board', 'admin') . '">Settings</a>';
		}
		return $links;
	}

	static function do_plugin_meta_links($links, $file) {
		if ($file == AJB_PLUGIN_FILE) {
			$links[] = '<a href="javascript:void(0);" onclick="if (confirm(\'Are you sure you wish to reset all of your plugin settings?\')) { window.location.href=\'' . get_admin_url(null, 'admin.php?page=adlogic-reset-settings', 'admin') . '\'; }">Reset Settings</a>';
			$links[] = '<a href="' . AJB_PLUGIN_URL .'changelog.php" target="_blank">View Changelog</a>';
		}
		return $links;
	}

	static function register_page_editor_buttons($buttons) {
		foreach (self::$page_editor_plugins as $i => $page_editor_plugin) {
			if ($i == 0) {
				array_push($buttons, '|', $page_editor_plugin['name']);
			} else {
				array_push($buttons, '', $page_editor_plugin['name']);
			}
		}
		return $buttons;
	}

	function is_login_page() {
		return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
	}

	function start_session() {
		// Initialise PHP Session
		if (function_exists('session_status')) {
			if (session_status() == PHP_SESSION_NONE) {
				session_name('AdlogicJobBoard');
				session_start();
			} else if (session_status() == PHP_SESSION_ACTIVE) {
				// Session already active, do nothing.
			} else if (session_status() == PHP_SESSION_DISABLED) {
				// TODO: Do something here to handle php sessions being disabled (throw error or something?)
			}
		} else {
			if (!session_id()) {
				session_name('AdlogicJobBoard');
				session_start();
			}
		}
	}

	static function getPlatform() {
		global $wptouch_pro;
		global $wptouch_plugin;
		$apiSettings = get_option('adlogic_api_settings');

		/*
		 * Check if viewing site on a adlogic's Social Board Product
		 * This is done by checking if the domain is socialapps.adlogic.com.au for the hosted site.
		 */

		if (stripos(get_bloginfo('url'), 'socialapps.adlogic.com.au') != false) {
			return AJB_PLATFORM_ID_SOCIAL_BOARD;
		}

		/*
		 * Check if viewing site on Joblogic job board
		 */

		if (!empty($apiSettings['adlogic_joblogic_passphrase'])) {
			return AJB_PLATFORM_ID_JOBLOGIC;
		}

		// Check if viewing mobile version of website
		if (!empty($wptouch_pro) && $wptouch_pro->showing_mobile_theme == true) {
			return AJB_PLATFORM_ID_MOBILE;
		} else if (!empty($wptouch_plugin) && $wptouch_plugin->applemobile == true) {
			return AJB_PLATFORM_ID_MOBILE;
		}

		// Return desktop if matches no other platform
		return AJB_PLATFORM_ID_DESKTOP;
	}

	function is_mobile_browser() {
		$useragent=$_SERVER['HTTP_USER_AGENT'];

		// Check if browser is a mobile browser
		if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
			return true;
		} else {
			return false;
		}
	}

	static function getReferrerUrl() {
		if (!empty($_SESSION['adlogicReferrerUrl'])) {
			return $_SESSION['adlogicReferrerUrl'];
		} else {
			return null;
		}
	}

	static function getSubSource() {
		if (!empty($_SESSION['adlogicSubSourceId'])) {
			return $_SESSION['adlogicSubSourceId'];
		} else {
			// if no subSourceId found - return null
			return null;
		}
	}

	static function activate_plugin() {
		// Plugin Activation check code

		// Flush Rewrite Rules for Job Board
		self::flush_rewrite_rules();
	}

	static function deactivate_plugin() {
		// Plugin Deactivation code

		// Flush Rewrite Rules for Job Board
		self::flush_rewrite_rules(true);
	}

	function registerScriptsAndStylesheets() {
		$apiSettings = get_option('adlogic_api_settings');
		// Enqueue jQuery now as we need it everywhere for our plugin
		wp_enqueue_script( 'jquery' );

		if(!isset($apiSettings['adlogic_use_local_js']) || $apiSettings['adlogic_use_local_js'] == 'false' || empty($apiSettings['adlogic_use_local_js'])) {
			// jQueryUI
			wp_register_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js', array('jquery'), '1.12.1');
		} else {
			// jQueryUI
			wp_register_script( 'jquery-ui', plugins_url('/js/jquery-ui.min.js', AJB_PLUGIN_FILE), array('jquery'), '1.12.1');
		}

		$parsed_url = parse_url(home_url());

		// Google Maps API JS
		wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key='.AJB_GOOGLE_MAPS_API_KEY.'&libraries=places&v=3&language=en-AU', null, AJB_VERSION);
		wp_enqueue_script( 'google-maps' );

		// jQuery Touch Punch
		wp_register_script( 'jquery-ui-touch-punch', plugins_url('/js/jquery.ui.touch-punch.min.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

		// jQuery Chosen Dropdown Library
		wp_register_script( 'jquery-chosen', plugins_url('js/jquery.chosen.min.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

		if(isset($apiSettings['adlogic_use_minified']) && ($apiSettings['adlogic_use_minified'] == 'true' || $apiSettings['adlogic_use_minified'] == '')) {

			// jQuery Pagination
			wp_register_script( 'jquery-pagination', plugins_url('js/minified/jquery.pagination.min.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

			// jQuery iFrame Post Form
			wp_register_script( 'jquery-iframe-post-form', plugins_url('js/minified/jquery.iframe-post-form.min.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

			// jQuery BlockUI
			wp_register_script( 'jquery-blockUI', plugins_url('js/minified/jquery.blockUI.min.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

		} else {

			// jQuery Pagination
			wp_register_script( 'jquery-pagination', plugins_url('js/jquery.pagination.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

			// jQuery iFrame Post Form
			wp_register_script( 'jquery-iframe-post-form', plugins_url('js/jquery.iframe-post-form.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

			// jQuery BlockUI
			wp_register_script( 'jquery-blockUI', plugins_url('js/jquery.blockUI.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );
		}

		// jQuery BBQ - Ben Alman's Back Button & Query Library
		wp_register_script( 'jquery-ba-bbq', plugins_url('js/jquery.ba-bbq.min.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );


		// jQuery bxSlider
		wp_register_script( 'jquery-bxSlider', plugins_url('js/jquery.bxslider.min.js', AJB_PLUGIN_FILE), array('jquery'), AJB_VERSION );

		/*
		 * Register Plugin Scripts
		 */
		if(isset($apiSettings['adlogic_use_minified']) && ($apiSettings['adlogic_use_minified'] == 'true' || $apiSettings['adlogic_use_minified'] == '')) {

			// Adlogic Settings and Utilities Javascript File
			wp_register_script( 'jquery-adlogic-settings', plugins_url('/js/minified/jquery.adlogic.settings.min.js', __FILE__), array('jquery'), AJB_VERSION );
			// Enqueue settings & utilities now as we need it everywhere for our plugin
			wp_enqueue_script( 'jquery-adlogic-settings' );

			// Adlogic Hot Jobs Widget
			wp_register_script( 'jquery-adlogic-hotJobsWidget', plugins_url('js/minified/jquery.adlogic.hotJobsWidget.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-bxSlider', 'jquery-adlogic-settings'), AJB_VERSION );

			// Adlogic Job Alerts Widget
			wp_register_script( 'jquery-adlogic-jobAlertsWidget', plugins_url('/js/minified/jquery.adlogic.jobAlertsWidget.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic Job Details
			wp_register_script( 'jquery-adlogic-jobDetailsPage', plugins_url('js/minified/jquery.adlogic.jobDetailsPage.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic Job Search Page
			wp_register_script( 'jquery-adlogic-searchPage', plugins_url('js/minified/jquery.adlogic.searchPage.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ba-bbq', 'jquery-blockUI', 'jquery-pagination' ), AJB_VERSION );

			// Adlogic Saved Jobs
			wp_register_script( 'jquery-adlogic-savedJobs', plugins_url('js/minified/jquery.adlogic.savedJobs.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ba-bbq', 'jquery-blockUI', 'jquery-pagination' ), AJB_VERSION );

			// Adlogic Search Widget
			wp_register_script( 'jquery-adlogic-searchWidget', plugins_url('/js/minified/jquery.adlogic.searchWidget.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ba-bbq', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic User Management
			wp_register_script( 'jquery-adlogic-users', plugins_url('/js/minified/jquery.adlogic.users.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic Account Management Widget
			wp_register_script( 'jquery-adlogic-acccountManagementWidget', plugins_url('js/minified/jquery.adlogic.acccountManagementWidget.min.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-adlogic-users'), AJB_VERSION );


		} else {
			// Adlogic Settings and Utilities Javascript File
			wp_register_script( 'jquery-adlogic-settings', plugins_url('/js/jquery.adlogic.settings.js', __FILE__), array('jquery'), AJB_VERSION );
			// Enqueue settings & utilities now as we need it everywhere for our plugin
			wp_enqueue_script( 'jquery-adlogic-settings' );

			// Adlogic Hot Jobs Widget
			wp_register_script( 'jquery-adlogic-hotJobsWidget', plugins_url('js/jquery.adlogic.hotJobsWidget.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-bxSlider', 'jquery-adlogic-settings'), AJB_VERSION );

			// Adlogic Job Alerts Widget
			wp_register_script( 'jquery-adlogic-jobAlertsWidget', plugins_url('/js/jquery.adlogic.jobAlertsWidget.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic Job Details
			wp_register_script( 'jquery-adlogic-jobDetailsPage', plugins_url('js/jquery.adlogic.jobDetailsPage.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic Job Search
			wp_register_script( 'jquery-adlogic-searchPage', plugins_url('js/jquery.adlogic.searchPage.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ba-bbq', 'jquery-blockUI', 'jquery-pagination' ), AJB_VERSION );

			// Adlogic Saved Jobs
			wp_register_script( 'jquery-adlogic-savedJobs', plugins_url('js/jquery.adlogic.savedJobs.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ba-bbq', 'jquery-blockUI', 'jquery-pagination' ), AJB_VERSION );

			// Adlogic Search Widget
			wp_register_script( 'jquery-adlogic-searchWidget', plugins_url('/js/jquery.adlogic.searchWidget.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ba-bbq', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic User Management
			wp_register_script( 'jquery-adlogic-users', plugins_url('/js/jquery.adlogic.users.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-ui', 'jquery-ui-touch-punch'), AJB_VERSION );

			// Adlogic Account Management Widget
			wp_register_script( 'jquery-adlogic-acccountManagementWidget', plugins_url('js/jquery.adlogic.acccountManagementWidget.js', AJB_PLUGIN_FILE), array('jquery', 'jquery-adlogic-users'), AJB_VERSION );

		}

		/**
		 * Register Styles sheets
		 *
		 * Each stylesheet should be registered in an array like so:
		 *			array(
		 *				'id' => '', // Required - the stylesheet id
		 *				'filename' => '', // Required - the stylesheet filename, or url (if url - use location value as remote)
		 *				'dependencies' => array(), // Optional - dependent stylesheets that should be loaded before this one
		 *				'version' => AJB_VERSION, // Optional - The stylesheet version
		 *				'location' => 'local' //Optional - defaults to local - 'local' Or 'remote' - use remote if url linked externally
		 *				'media' => 'all' // Optional - defaults to all - CSS media types - see list here @link http://www.w3.org/TR/CSS2/media.html#media-types
		 *				'deregister_existing' => false // Deregisters existing instances of the stylesheet in with the same id before registering this stylesheet
		 *			)
		 */

		$aStyleSheets = array (
					// jQuery UI Theme
					array(
						'id' => 'jquery-ui-theme',
						'filename' => 'jquery-ui.css',
						'version' => AJB_VERSION,
						'deregister_existing' => true
					),
					// Hot Jobs Widget CSS
					array(
						'id' => 'adlogic-hotjobs-widget',
						'filename' => 'hot_jobs_widget.css',
						'version' => AJB_VERSION
					),
					// Hot Jobs Widget CSS
					array(
							'id' => 'jquery-chosen',
							'filename' => 'chosen.css',
							'version' => AJB_VERSION,
							'deregister_existing' => true
					),
					// Search Page CSS
					array (
						'id' => 'adlogic-search-page',
						'filename' => 'search_page.css',
						'version' => AJB_VERSION
					),
					// Job Details Page CSS
					array (
							'id' => 'adlogic-job-details-page',
							'filename' => 'job_details.css',
							'dependencies' => array('jquery-ui-theme'),
							'version' => AJB_VERSION
					),
					// Search Widget CSS
					array (
							'id' => 'adlogic-search-widget',
							'filename' => 'search_widget.css',
							'dependencies' => array('jquery-ui-theme'),
							'version' => AJB_VERSION
					),
					// Submit CV Widget CSS
					array (
							'id' => 'adlogic-submit-cv-widget',
							'filename' => 'submit_cv_widget.css',
							'dependencies' => array('jquery-ui-theme'),
							'version' => AJB_VERSION
					),
					// Account Management Widget CSS
					array (
							'id' => 'adlogic-account-management-widget',
							'filename' => 'account_management_widget.css',
							'version' => AJB_VERSION
					),
					// Job Alerts Widget CSS
					array (
							'id' => 'adlogic-job-alerts-widget',
							'filename' => 'job_alerts_widget.css',
							'version' => AJB_VERSION
					),
					// Saved Jobs Page CSS
					array (
							'id' => 'adlogic-saved-jobs-page',
							'filename' => 'saved_jobs.css',
							'version' => AJB_VERSION
					),
					// User/Authentication Dialog CSS
					array (
							'id' => 'adlogic-user-dialog',
							'filename' => 'user_dialog.css',
							'dependencies' => array('jquery-ui-theme'),
							'version' => AJB_VERSION
					),
				);

		foreach ($aStyleSheets as $i => $aStyleSheet) {
			if (isset($aStyleSheet['deregister_existing']) && $aStyleSheet['deregister_existing'] == true) {
				wp_deregister_style($aStyleSheet['id']);
			}

			if (isset($aStyleSheet['location']) && $aStyleSheet['location'] == 'remote') {
				wp_register_style( $aStyleSheet['id'], $aStyleSheet['filename'], (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
			} else {
				if (defined('MULTISITE') && (MULTISITE == true)) {
					if (is_file(get_option('upload_path') .  '/css/' . $aStyleSheet['filename'])) {
						wp_register_style( $aStyleSheet['id'], '/' . get_option('upload_path') . '/css/' . $aStyleSheet['filename'], (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					} else if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'])) {
						wp_register_style( $aStyleSheet['id'], get_stylesheet_directory_uri() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'], (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					} else if (is_file(get_template_directory() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'])) {
						wp_register_style( $aStyleSheet['id'], get_template_directory_uri() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'], (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					} else if (is_file(get_stylesheet_directory() .  '/css/adlogicsocialboard/' . $aStyleSheet['filename'])) {
						wp_register_style( $aStyleSheet['id'], get_stylesheet_directory_uri() .  '/css/adlogicsocialboard/' . $aStyleSheet['filename'], (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					} else {
						wp_register_style( $aStyleSheet['id'], plugins_url('css/' . $aStyleSheet['filename'], AJB_PLUGIN_FILE), (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					}
				} else {
					if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'])) {
						wp_register_style( $aStyleSheet['id'], get_stylesheet_directory_uri() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'], (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					} else if (is_file(get_template_directory() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'])) {
						wp_register_style( $aStyleSheet['id'], get_template_directory_uri() .  '/css/adlogic-job-board/' . $aStyleSheet['filename'], (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					} else {
						wp_register_style( $aStyleSheet['id'], plugins_url('css/' . $aStyleSheet['filename'], AJB_PLUGIN_FILE), (isset($aStyleSheet['dependencies']) ? $aStyleSheet['dependencies'] : null), (isset($aStyleSheet['version']) ? $aStyleSheet['version'] : null), (isset($aStyleSheet['media']) ? $aStyleSheet['media'] : 'all') );
					}
				}
			}
		}
	}

	static function generate_rewrite_rules() {
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag('%query%','(.*)', 'query=');
		$rewrite_keywords_structure = $wp_rewrite->root . '%pagename%/query/%query%/';
		$new_rule = $wp_rewrite->generate_rewrite_rules($rewrite_keywords_structure);
		$wp_rewrite->rules = $new_rule + $wp_rewrite->rules;

		$rewrite_keywords_structure = $wp_rewrite->root . '/query/%query%/';
		$new_rule = $wp_rewrite->generate_rewrite_rules($rewrite_keywords_structure);

		$wp_rewrite->rules = $new_rule + $wp_rewrite->rules;

		return $wp_rewrite->rules;
	}

	static function update_parse_query_filter($wp_query) {
		// Correct is_* for page_on_front and page_for_posts
		if ( $wp_query->is_home && 'page' == get_option('show_on_front') && get_option('page_on_front') ) {
			$_query = wp_parse_args($wp_query->query);

			if ( empty($_query) || isset($_query['query']) ) {
				$wp_query->is_page = true;
				$wp_query->is_home = false;
				$wp_query->set('page_id', get_option('page_on_front'));
				// Correct <!--nextpage--> for page_on_front
				$paged = $wp_query->get('paged');
				if ( !empty($paged) ) {
					$wp_query->set('page', $paged);
					unset($paged);
				}
			}
		}
	}

	static function flush_rewrite_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	static function add_query_vars( $public_query_vars ) {
		$public_query_vars[] = 'query';
		return $public_query_vars;
	}

	static function shouldUseNewLocationField() {
		$apiSettings = get_option('adlogic_api_settings');
		if($apiSettings['adlogic_api_use_new_location_widget']=='true') {
			return 1;
		}
		return 0;
	}

	static function shouldUseNewAPI() {
		$apiSettings = get_option('adlogic_api_settings');
		if($apiSettings['adlogic_api_use_new_location_API']=='true') {
			return 1;
		}
		return 0;
	}
	//new origin sourcing
	static function checkOriginSourceId(){
		if(isset($_GET['originSourceId'])){
			$_SESSION['originSourceId'] = $_GET['originSourceId'];
		}
	}
}

?>
