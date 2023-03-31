<?php

class Adlogic_Job_Details_Shortcodes
{
	static $jobDetails;
	static $jobHtml;
	static $page_title;
	static $shouldBePurged;

	function init()
	{
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		if ($Adlogic_Job_Board->check_setup() == true) {
			add_shortcode('adlogic_job_details', array('Adlogic_Job_Details_Shortcodes', 'job_details'));
			add_shortcode('adlogic_consultant_details', array('Adlogic_Job_Details_Shortcodes', 'consultant_details'));
			// Add Hook to add meta if necessary
			if (!is_admin()) {
				add_action('the_posts', array('Adlogic_Job_Details_Shortcodes', 'check_shortcode'));
			}
		}

		// Add TinyMCE Editor Buttons for Page Editor
		if (is_admin()) {
			wp_enqueue_script('wpdialogs');
			wp_enqueue_style('wp-jquery-ui-dialog');
			$Adlogic_Job_Board->add_page_editor_button('ajbJobDetails', 'jquery.tinyMCEjobDetailsButton.js');
			add_action('admin_print_footer_scripts', array(__CLASS__, 'editor_code'), 50);
		}
	}

	static function editor_code()
	{
		?>
		<div class="ajb-editor-dialogs" id="ajb-job-details" style="display:none;">
			<p class="title">Job Details Shortcode Options</p>
			<div class="ajb-editor-main-options">
				<p>
					<label><strong><?php _e('Template:'); ?></strong></label>
					<select id="ajb-job-details-template" name="ajb-job-details-template">
						<option value="">Adlogic</option>
						<option value="custom">Custom (enter HTML between tags)</option>
						<?php
								if (is_dir(AJB_PLUGIN_PATH . '/templates/job_details/')) {
									$hDir = opendir(AJB_PLUGIN_PATH . '/templates/job_details/');
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
								if (is_dir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/job_details/')) {
									$hDir = opendir(get_stylesheet_directory() . '/css/adlogic-job-board/templates/job_details/');
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
				<div id="ajb-job-details-insert" class="ajb-dialog-submit">
					<input type="button" tabindex="100" value="<?php esc_attr_e('Add Shortcode'); ?>" class="button-primary" id="ajb-job-details-shortcode" name="ajb-job-details-shortcode">
				</div>
			</div>
		</div>
		<?php
			}

			static function register_scripts()
			{
				wp_enqueue_script('jquery-adlogic-jobDetailsPage');
				wp_enqueue_style('adlogic-job-details-page');
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
						foreach ($matches[2] as $i => $match) {
							if (($match == 'adlogic_job_details') || ($match == 'wptouch')) {
								if ($match == 'adlogic_job_details') {
									self::get_ad_details();
									if (self::$jobDetails) {
										if (self::$jobDetails->AdStatus == "EXPIRED") {
											if (self::$shouldBePurged == true) {
												$post->post_title = "This job is no longer available";
												if (function_exists("_wp_render_title_tag")) {
													remove_action('wp_head', '_wp_render_title_tag', 1);
													add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
												} else {
													add_action('wp_title', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
												}
											} else {
												$post->post_title = self::$jobDetails->JobTitle;
												if (function_exists("_wp_render_title_tag")) {
													remove_action('wp_head', '_wp_render_title_tag', 1);
													add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
												} else {
													add_action('wp_title', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
												}
											}
										} else {
											$post->post_title = self::$jobDetails->JobTitle;
											if (function_exists("_wp_render_title_tag")) {
												remove_action('wp_head', '_wp_render_title_tag', 1);
												add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
											} else {
												add_action('wp_title', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
											}
										}
										add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_meta_tags'));
										add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'register_scripts'));
										remove_action('wp_head', 'rel_canonical');
									}
									break 2;
								} else if ($match == 'wptouch') {
									// WPTouch match found! Check for our shortcode in it's content
									preg_match_all("/$pattern/s", $matches[5][$i], $wptouch_matches);

									foreach ($wptouch_matches[2] as $i => $wptouch_match) {
										if ($wptouch_match == 'adlogic_job_details') {
											self::get_ad_details();
											if (self::$jobDetails) {
												if (self::$jobDetails->AdStatus == "EXPIRED") {
													if (self::$shouldBePurged == true) {
														$post->post_title = "This job is no longer available";
														if (function_exists("_wp_render_title_tag")) {
															remove_action('wp_head', '_wp_render_title_tag', 1);
															add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
														} else {
															add_action('wp_title', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
														}
													} else {
														$post->post_title = self::$jobDetails->JobTitle;
														if (function_exists("_wp_render_title_tag")) {
															remove_action('wp_head', '_wp_render_title_tag', 1);
															add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
														} else {
															add_action('wp_title', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
														}
													}
												} else {
													$post->post_title = self::$jobDetails->JobTitle;
													if (function_exists("_wp_render_title_tag")) {
														remove_action('wp_head', '_wp_render_title_tag', 1);
														add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
													} else {
														add_action('wp_title', array('Adlogic_Job_Details_Shortcodes', 'add_page_title'));
													}
												}
												add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_meta_tags'));
												add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'register_scripts'));
												remove_action('wp_head', 'rel_canonical');
											}
											break 3;
										}
									}
								}
							}
						}
					}
				}
				return $posts;
			}

			static function add_page_title($page_title)
			{
				$oJobAdDetails = self::$jobDetails;
				if (self::$jobDetails) {
					foreach ($oJobAdDetails->locations->location as $sLocation) {
						$sLocationArray[] = $sLocation->value;
					}
					self::$page_title = ' | ' . $oJobAdDetails->JobTitle . (isset($sLocationArray) ? ' in ' . array_pop($sLocationArray) : '') . ' | ' . $page_title;
				} else {
					self::$page_title = $page_title;
				}
				return self::$page_title;
			}

			static function add_meta_tags()
			{
				global $wp_rewrite;
				$oJobAdDetails = self::$jobDetails;
				if ($oJobAdDetails) {
					if (self::$page_title == null || empty(self::$page_title) || !isset(self::$page_title)) {
						self::$page_title = $oJobAdDetails->JobTitle;
					}
					?>
			<meta name="description" content="<?php print $oJobAdDetails->SearchSummary; ?>" />
			<link rel="canonical" href="<?php print get_permalink() . (!empty($_SERVER['QUERY_STRING']) ? ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . htmlspecialchars(urldecode($_SERVER['QUERY_STRING'])) : ''); ?>" />
			<meta property="og:type" content="website" />
			<meta property="og:url" content="<?php print get_permalink() . (!empty($_SERVER['QUERY_STRING']) ? ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . htmlspecialchars(urldecode($_SERVER['QUERY_STRING'])) : ''); ?>" />
			<meta property="og:title" content="<?php print self::$page_title . get_bloginfo('name'); ?>" />
			<meta property="og:image" content="<?php print $oJobAdDetails->standOut->logoURL; ?>" />
			<meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
			<meta property="og:description" content="<?php print $oJobAdDetails->SearchSummary; ?>" />
			<meta property="og:locale" content="<?php print get_locale(); ?>" />
			<meta name="twitter:card" content="summary" />
			<meta name="twitter:description" content="<?php print $oJobAdDetails->SearchSummary; ?>" />
			<meta name="twitter:title" content="<?php print $oJobAdDetails->JobTitle; ?>" />
			<meta name="twitter:image" content="<?php print $oJobAdDetails->standOut->logoURL; ?>" />
		<?php
				}
			}
			static function add_noindex_tag()
			{
				?>
		<!-- Internal Job -->
		<meta name="robots" content="noindex">
	<?php
		}

		static function return_jobad_details()
		{
			$oJobAdDetails = self::$jobDetails;
			$oJobAttributes = self::$jobDetails->attributes();
			$bulletPointsArray = array();
			$standOutBulletPointsArray = array();
			foreach ($oJobAdDetails->BulletPoints->BulletPoint as $bullet) {
				$bullet = (string) $bullet;
				$bulletPointsArray[] = $bullet;
			}
			foreach ($oJobAdDetails->standOut->BulletPoints->BulletPoint as $bullet) {
				$bullet = (string) $bullet;
				$standOutBulletPointsArray[] = $bullet;
			}

			$locationsArray = array();
			$classificationsArray = array();

			foreach ($oJobAdDetails->locations->location as $location) {
				$locationValue = (string) $location->value;
				$locationId = (string) $location->attributes();
				$thisLocation = array(
					'locationId'	=>	$locationId,
					'locationName'	=>	$locationValue
				);
				$locationsArray[] = $thisLocation;
			}
			foreach ($oJobAdDetails->classifications->classification as $classification) {
				$classificationValue = (string) $classification->value;
				$classificationId = (string) $classification->attributes();
				$thisClassification = array(
					'classificationId'	=>	$classificationId,
					'classificationName'	=>	$classificationValue
				);
				$classificationsArray[] = $thisClassification;
			}


			$jobDetailsArray = array(
				'ad_id' => (string) $oJobAttributes->ad_id,
				'advertiserId' => (string) $oJobAttributes->advertiserId,
				'distribution' => (string) $oJobAttributes->distribution,
				'hotJob' => (string) $oJobAttributes->hotJob,
				'reference' => (string) $oJobAttributes->reference,
				'template' => (string) $oJobAttributes->template,
				'placedBy' => array(
					'Brand' =>	(string) $oJobAdDetails->PlacedBy->Brand,
					'Office' =>	(string) $oJobAdDetails->PlacedBy->Office,
					'Account' =>	(string) $oJobAdDetails->PlacedBy->Account,
				),
				'JobTitle'	=>	(string) $oJobAdDetails->JobTitle,
				'adFooter'	=>	(string) $oJobAdDetails->adFooter,
				'SearchHeadline'	=>	(string) $oJobAdDetails->SearchHeadline,
				'SearchSummary'	=>	(string) $oJobAdDetails->SearchSummary,
				'BulletPoints'	=>	$bulletPointsArray,
				'Enquiry'	=>	array(
					'Phone'	=>	(string) $oJobAdDetails->Enquiry->Phone,
					'Email'	=>	(string) $oJobAdDetails->Enquiry->Email,
					'ApplicationURL'	=>	(string) $oJobAdDetails->Enquiry->ApplicationURL,
					'Name'	=>	(string) $oJobAdDetails->Enquiry->Name,
					'ConsultantEmail'	=>	(string) $oJobAdDetails->Enquiry->ConsultantEmail,
					'ConsultantImage'	=>	(string) $oJobAdDetails->Enquiry->ConsultantImage
				),
				'standOut'	=>	array(
					'logoURL'	=>	(string) $oJobAdDetails->standOut->logoURL,
					'BulletPoints'	=>	$standOutBulletPointsArray
				),
				'locations'	=>	$locationsArray,
				'classifications'	=>	$classificationsArray,
				'workType'	=>	array(
					'workTypeId'	=>	(string) $oJobAdDetails->workType->attributes(),
					'workTypeName'	=>	(string) $oJobAdDetails->workType->value
				),
				'Salary'	=>	array(
					'salaryMinimum'	=>	(string) $oJobAdDetails->Salary->salaryMinimum,
					'salaryMaximum'	=>	(string) $oJobAdDetails->Salary->salaryMaximum,
					'salaryRate'	=>	(string) $oJobAdDetails->Salary->salaryRate,
					'salaryAdditionalText'	=>	(string) $oJobAdDetails->Salary->salaryAdditionalText
				),
				'AdStatus'	=>	(string) $oJobAdDetails->AdStatus,
				'PublishedDate'	=>	(string) $oJobAdDetails->PublishedDate,
				'Subheading'	=>	(string) $oJobAdDetails->Subheading
			);

			if ($oJobAdDetails->OrganisationalUnit) {
				$jobDetailsArray['OrganisationalUnit'] = (string) $oJobAdDetails->OrganisationalUnit->attributes();
			} else {
				$jobDetailsArray['OrganisationalUnit'] = '';
			}
			if ($oJobAdDetails->closeDate) {
				$jobDetailsArray['closeDate'] = date('l jS F', strtotime($oJobAdDetails->closeDate));
			}
			if ($oJobAdDetails->Enquiry->JOB_PORTAL_URL) {
				$jobDetailsArray["Enquiry"]["JOB_PORTAL_URL"] = (string) $oJobAdDetails->Enquiry->JOB_PORTAL_URL;
			}
			if ($oJobAdDetails->Enquiry->AccountName) {
				$jobDetailsArray["Enquiry"]["AccountName"] = (string) $oJobAdDetails->Enquiry->AccountName;
			}
			if ($oJobAdDetails->geoLocation->location) {
				$jobDetailsArray["geoLocation"] = json_decode((string) $oJobAdDetails->geoLocation->location);
			}
			$jobDetailsArrayJson = json_encode($jobDetailsArray);
			?>
		<script type="text/javascript">
			// Returns the details for an ad.
			function return_jobad_details() {
				return <?php print $jobDetailsArrayJson; ?>
			}
		</script>
	<?php
		}

		static function get_ad_details()
		{
			/*
		  * April 15th 2019
		  * If accessing the job_details page from within the admin, return here so we don't attempt to make any requests to MR+ API
		  * Notably an issue when using the Gutenberg editor.
		*/
			if (is_admin()) {
				return;
			}
			if (empty(self::$jobDetails) && empty(self::$jobHtml)) {
				global $wp_rewrite;
				$searchVars = explode('/', htmlspecialchars(urldecode($_SERVER['QUERY_STRING'])));
				$matches = '';
				$templateId = '';
				if (!empty($_GET['templateId'])) {
					$templateId = htmlspecialchars($_GET['templateId']);
				}
				$searchVarsLocal = $searchVars;
				$webToken = '';
				$isJob = false;
				$queryStr = 'jobAdId';
				if (!empty($_GET['subSourceId']) && !empty($_GET['typeId'])) {
					if ($_GET['subSourceId'] == '9999' && $_GET['typeId'] == 'job') {
						$isJob = true;
						$queryStr = 'adlogic_job_id';
					}
				}
				preg_match('/^(\d*)/', array_pop($searchVars), $matches);
				//var_dump($matches);
				if (empty($matches[1])) {
					preg_match('/^(\d*)/', array_pop($searchVars), $matches);
					$jobId = array_pop($matches);
					if (empty($jobId)) {
						$webToken = array_pop($searchVarsLocal);
					}
				} else {
					$jobId = array_pop($matches);
				}

				if (($jobId && is_numeric($jobId)) || (!empty($webToken))) {
					// Get Settings from Wordpress
					$apiSettings = get_option('adlogic_api_settings');

					// Instantiate Soap Client Object
					$oSoapClient = Adlogic_Job_Board::getSoapConnection();

					// Requires Jobs Class
					require_once(AJB_PLUGIN_PATH . '/lib/classes/jobDetails.class.php');
					$oJobDetails = new JobDetails($oSoapClient, $jobId, $webToken, $isJob);

					$customApplicationUrl = '';
					
					// if (!empty($apiSettings['adlogic_custom_application_page'])) {
					// 	$customApplicationUrl = get_permalink($apiSettings['adlogic_custom_application_page']) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . $jobId . '/';
					// } else if (!empty($apiSettings['adlogic_custom_application_url'])) {
					// 	$urlParts = parse_url($apiSettings['adlogic_custom_application_url']);
					// 	if (isset($urlParts['query'])) {
					// 		$customApplicationUrl = str_replace($urlParts['query'], $urlParts['query'] . '&' . $queryStr . '=' . $jobId, $apiSettings['adlogic_custom_application_url']);
					// 	} else {
					// 		if (isset($urlParts['fragment'])) {
					// 			$customApplicationUrl = str_replace('#' . $urlParts['fragment'], '?' . $queryStr . '=' . $jobId . '#' . $urlParts['fragment'], $apiSettings['adlogic_custom_application_url']);
					// 		} else {
					// 			$customApplicationUrl = $apiSettings['adlogic_custom_application_url'] . '?' . $queryStr . '=' . $jobId;
					// 		}
					// 	}
					// }

					// if (!empty($customApplicationUrl)) {
					// 	$oJobDetails->set('applicationFormUrl', $customApplicationUrl);
					// }
					if (!empty($templateId)) {
						$oJobDetails->set('templateId', $templateId);
					}
					self::$jobDetails = $oJobDetails->getDetails(false, Adlogic_Job_Board::getSubSource(), Adlogic_Job_Board::getPlatform());
					// if (self::$jobDetails && !empty($webToken)) {
					// 	$oJobDetails->webToken = '';
					// 	$jobId = self::$jobDetails->attributes()->ad_id;
					// 	$oJobDetails->jobAdId = $jobId;
					// 	if (!empty($apiSettings['adlogic_custom_application_url'])) {
					// 		if (isset($urlParts['query'])) {
					// 			$customApplicationUrl = str_replace($urlParts['query'], $urlParts['query'] . '&' . $queryStr . '=' . $jobId, $apiSettings['adlogic_custom_application_url']);
					// 		} else {
					// 			if (isset($urlParts['fragment'])) {
					// 				$customApplicationUrl = str_replace('#' . $urlParts['fragment'], '?' . $queryStr . '=' . $jobId . '#' . $urlParts['fragment'], $apiSettings['adlogic_custom_application_url']);
					// 			} else {
					// 				$customApplicationUrl = $apiSettings['adlogic_custom_application_url'] . '?' . $queryStr . '=' . $jobId;
					// 			}
					// 		}
					// 	}
					// 	if (!empty($customApplicationUrl)) {
					// 		$oJobDetails->set('applicationFormUrl', $customApplicationUrl);
					// 	}
					// }
					self::$jobHtml = $oJobDetails->getHTML(false, Adlogic_Job_Board::getSubSource(), Adlogic_Job_Board::getPlatform());

					Adlogic_Job_Board::checkOriginSourceId();
					//Check if origin source ID is in session
					if(isset($_SESSION['originSourceId'])){
						//Get Job ID
						function getJobID($input) {
						$regex = '/https:\/\/form\.myrecruitmentplus\.com\/applicationform\?jobAdId=[0-9]+/i';
						preg_match($regex, $input, $match);
						$id = explode("jobAdId=", $match[0]);
						return $id[1];
						}
						//append origin source ID to application form URL
						self::$jobHtml = preg_replace('/https:\/\/form\.myrecruitmentplus\.com\/applicationform\?jobAdId=[0-9]+/i', 'https://form.myrecruitmentplus.com/applicationform?jobAdId='.getJobID(self::$jobHtml).'&originSourceId='.$_SESSION['originSourceId'], self::$jobHtml);
					}	
					
					if (!empty($apiSettings['adlogic_purge_job_details_page'])) {
						if ($apiSettings['adlogic_purge_job_details_page'] == 'true') {
							self::$shouldBePurged = true;
						}
					}
					if (self::$jobDetails->AdStatus == "EXPIRED") {
						if (self::$shouldBePurged == true) {
							add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_noindex_tag'));
						}
					}
					if (isset(self::$jobDetails->attributes()->distribution) && (!empty(self::$jobDetails->attributes()->distribution))) {
						// Internal Only ads
						if (self::$jobDetails->attributes()->distribution == "I") {
							add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'add_noindex_tag'));
						}
					}
					add_action('wp_head', array('Adlogic_Job_Details_Shortcodes', 'return_jobad_details'));
				} else {
					// Return Error 404 for Job Details page not found if no Job Id is passed only for non-logged in admins
					if (!current_user_can('manage_options')) {
						global $wp_query;
						$wp_query->set_404();
					}
				}
			}
			return true;
		}

		function consultant_details($atts, $content = '')
		{

			// Get the details for the ad, we can grab the consultant information aswell.
			self::get_ad_details();

			$oJobPosting = self::$jobDetails;

			$consultantPhone = $oJobPosting->Enquiry->Phone;
			$consultantEmail = $oJobPosting->Enquiry->ConsultantEmail;
			$consultantName	= $oJobPosting->Enquiry->Name;
			$consultantCompany = $oJobPosting->PlacedBy->Office;


			?>
		<div class="ajb-consultant-profile">
			<?php

					if ($atts['hide_image'] == 'true') {
						// DO nothing
					} else {
						if (empty($oJobPosting->Enquiry->ConsultantImage)) {

							?>
					<div class="ajb-consultant-image"></div>

				<?php

							} else {
								?>
					<div class="ajb-consultant-image" style="background-image: url('<?php print $oJobPosting->Enquiry->ConsultantImage; ?>');"></div>
			<?php
						}
					}
					?>
			<h4 class="ajb-consultant-name">
				<span class="ajb-contact-consultant">Contact</span>
				<?php print $consultantName; ?>
			</h4>
			<div class="ajb-connect-with-consultant">
				<ul class="ajb-contact-methods">
					<?php
							if ($atts['hide_email'] == 'true') {
								// DO nothing
							} else {
								?>
						<li class="ajb-consultant-email">
							<a href="mailto:<?php print $consultantEmail; ?>"><?php print $consultantEmail; ?></a>
						</li>
					<?php
							}

							if ($atts['hide_phone'] == 'true') {
								// DO nothing
							} else {
								?>
						<li class="ajb-consultant-phone">
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


			function parse_content($content, $oJobPosting)
			{
				global $wp_rewrite;

				// Get API Settings
				$apiSettings = get_option('adlogic_api_settings');

				$parsed_content = '';


				if ($oJobPosting === NULL) {
					return (current_user_can('manage_options')) ? '<div class="adlogic_job_details_no_results"><p><strong>Admin Notice:</strong><br/>This page should not be linked to directly from your website navigation.</p>Please contact <a href="http://www.adlogic.com.au/" title="adlogic support">adlogic</a> if you require assistance.<br/>This message is only displayed to you as a logged in administrator.</div>' : '<div class="adlogic_job_details_no_results">The job you are looking for could not be found.</div>';
				} else if ($oJobPosting === false) {
					return '<div class="adlogic_job_details_no_results">The job you are looking for could not be found.</div>';
				}

				$oJobAttributes = $oJobPosting->attributes();

				if (!empty($date_format)) {
					$pubDate = date($date_format, strtotime($oJobPosting->pubDate));
				} else {
					$pubDate = $oJobPosting->pubDate;
				}

				// Build Bulletpoints
				$sBulletPointHtml = '<ul>';
				foreach ($oJobPosting->standOut->BulletPoints->BulletPoint as $sBulletPoint) {
					if (!empty($sBulletPoint)) {
						$sBulletPointHtml .= '<li>' . $sBulletPoint . '</li>';
					}
				}
				$sBulletPointHtml .= '</ul>';

				// Location Breadcrumbs
				$sLocationArray = array();

				$sLocationList = '<ul>';
				foreach ($oJobPosting->locations->location as $sLocation) {
					$oLocationAttributes = $sLocation->attributes();
					$sLocationArray[] = $sLocation->value;
					if ($wp_rewrite->using_permalinks()) {
						$sLocationList .= '<li><a href="' . get_permalink() . 'query/Location/' . Adlogic_Job_Board::uriSafe($sLocation->value) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation->value . '">' . $sLocation->value . '</a>' . '</li>';
					} else {
						$sLocationList .= '<li><a href="' . get_permalink() . '&/Location/' . Adlogic_Job_Board::uriSafe($sLocation->value) . '/' . $oLocationAttributes->id . '/" title="Jobs in ' . $sLocation->value . '">' . $sLocation->value . '</a>' . '</li>';
					}
				}
				$sJobLocationBreadCrumbs = $sLocationList . '</ul>';

				// Classification Breadcrumbs
				$sPositionList = '<ul>';
				foreach ($oJobPosting->classifications->classification as $sClassification) {
					$oClassificationAttributes = $sClassification->attributes();
					if ($wp_rewrite->using_permalinks()) {
						$sPositionList  .= '<li><a href="' . get_permalink() . 'query/Industry/' . Adlogic_Job_Board::uriSafe($sClassification->value) . '/' . $oClassificationAttributes->id . '/"  title="' . $sClassification->value . ' Jobs">' . $sClassification->value . '</a>' . '</li>';
					} else {
						$sPositionList  .= '<li><a href="' . get_permalink() . '&/Industry/' . Adlogic_Job_Board::uriSafe($sClassification->value) . '/' . $oClassificationAttributes->id . '/"  title="' . $sClassification->value . ' Jobs">' . $sClassification->value . '</a>' . '</li>';
					}
				}
				$sJobClassificationBreadCrumbs = $sPositionList . '</ul>';

				// Work Type link
				$oWorkTypeAttributes = $oJobPosting->workType->attributes();
				if ($wp_rewrite->using_permalinks()) {
					$sWorkTypeLink = '<a href="' . get_permalink() . 'query/WorkType/' . Adlogic_Job_Board::uriSafe($oJobPosting->workType->value) . '/' . $oWorkTypeAttributes->id . '/"  title="' . $oJobPosting->workType->value . ' Jobs">' . $oJobPosting->workType->value . '</a>';
				} else {
					$sWorkTypeLink = '<a href="' . get_permalink() . '&/WorkType/' . Adlogic_Job_Board::uriSafe($oJobPosting->workType->value) . '/' . $oWorkTypeAttributes->id . '/"  title="' . $oJobPosting->workType->value . ' Jobs">' . $oJobPosting->workType->value . '</a>';
				}

				// Generate custom application url if one is specified
				$customApplicationUrl = '';

				// if (!empty($apiSettings['adlogic_custom_application_page'])) {
				// 	$slicedLocationArray = array_slice($sLocationArray, 0, 2);
				// 	$customApplicationUrl = get_permalink($apiSettings['adlogic_custom_application_page']) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($slicedLocationArray)) . '/' . $oJobAttributes->ad_id . '/';
				// } else if (!empty($apiSettings['adlogic_custom_application_url'])) {
				// 	$urlParts = parse_url($apiSettings['adlogic_custom_application_url']);
				// 	if (isset($urlParts['query'])) {
				// 		$customApplicationUrl = str_replace($urlParts['query'], $urlParts['query'] . '&jobAdId=' . $oJobAttributes->ad_id, $apiSettings['adlogic_custom_application_url']);
				// 	} else {
				// 		if (isset($urlParts['fragment'])) {
				// 			$customApplicationUrl = str_replace('#' . $urlParts['fragment'], '?jobAdId=' . $oJobAttributes->ad_id . '#' . $urlParts['fragment'], $apiSettings['adlogic_custom_application_url']);
				// 		} else {
				// 			$customApplicationUrl = $apiSettings['adlogic_custom_application_url'] . '?jobAdId=' . $oJobAttributes->ad_id;
				// 		}
				// 	}
				// }

				$content_replacements_array = array(
					'{job_id}',
					'{job_link}',
					'{job_title}',
					'{job_description}',
					'{job_bulletpoints}',
					'{job_location_breadcrumbs}',
					'{job_classification_breadcrumbs}',
					'{job_worktype_link}',
					'{job_standout_logo}',
					'{job_placedby_brand}',
					'{job_placedby_office}',
					'{job_placedby_account}',
					'{job_reference}',
					'{job_template_name}',
					'{job_search_headline}',
					'{job_search_summary}',
					'{job_enquiry_phone}',
					'{job_enquiry_email}',
					'{job_enquiry_application_url}',
					'{job_ad_status}',
					'{job_ad_footer}',
					'{job_post_date}',
					'{job_subheading}'
				);
				$slicedLocationArray = array_slice($sLocationArray, 0, 2);
				$job_data_array = array(
					(string) $oJobAttributes->ad_id,
					get_permalink() . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($slicedLocationArray)) . '/' . $oJobAttributes->ad_id . '/',
					(string) $oJobPosting->JobTitle,
					(string) $oJobPosting->JobDescription,
					(string) $sBulletPointHtml,
					$sJobLocationBreadCrumbs,
					$sJobClassificationBreadCrumbs,
					$sWorkTypeLink,
					(string) $oJobPosting->standOut->logoURL,
					(string) $oJobPosting->PlacedBy->Brand,
					(string) $oJobPosting->PlacedBy->Office,
					(string) $oJobPosting->PlacedBy->Account,
					(string) $oJobAttributes->reference,
					(string) $oJobAttributes->template,
					(string) $oJobPosting->SearchHeadline,
					(string) $oJobPosting->SearchSummary,
					(string) $oJobPosting->Enquiry->Phone,
					(string) $oJobPosting->Enquiry->Email,
					(string) (($oJobPosting->AdStatus == "EXPIRED") ? '' : (empty($customApplicationUrl) ? $oJobPosting->Enquiry->ApplicationURL : $customApplicationUrl)),
					(string) $oJobPosting->AdStatus,
					(string) $oJobPosting->adFooter,
					date('d M Y', strtotime($oJobPosting->PublishedDate)),
					(string) $oJobPosting->Subheading
				);

				$parsed_content .= str_replace($content_replacements_array, $job_data_array, $content);

				if (!empty($apiSettings['adlogic_purge_job_details_page'])) {
					if ($apiSettings['adlogic_purge_job_details_page'] == 'true') {
						self::$shouldBePurged = true;
					}
				}

				if ($oJobPosting->AdStatus == "EXPIRED") {
					if (self::$shouldBePurged == true) {
						global $wp_query;
						$wp_query->set_404();
						status_header(404);
						$output = "<p>The job you're looking for is no longer available.</p>";
						return $output;
					}
					print '<div class="adlogic_job_expired"></div>';
					print '<span class="adlogic_job_expired_details">' . $parsed_content . '</span>';
				} else {
					return $parsed_content;
				}
			}

			static function job_details($atts, $content = '')
			{

				// WP Rewrite object
				global $wp_rewrite;

				// Grab Job Ad Details
				self::get_ad_details();
				$apiSettings = get_option('adlogic_api_settings');
				$oJobAdDetails = self::$jobDetails;
				
				
				//Append Original Source ID to application form URL
				$enquiry = $oJobAdDetails->Enquiry;
				$appURL = $enquiry->ApplicationURL;
				
				$appURL = $appURL.'&originSourceId='.$_SESSION['originSourceId'];
				// echo  $appURL;
				$enquiry->ApplicationURL = $appURL;
				// var_dump($oJobAdDetails);

				if ((isset($atts['template']))  && ($atts['template'] != 'base')) {
					switch ($atts['template']) {
						case 'custom':
							return self::parse_content($content, $oJobAdDetails);
							break;
						default:
							if (!empty($atts['template'])) {
								if (defined('MULTISITE') && (MULTISITE == true)) {
									if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_details/' . $atts['template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_details/' . $atts['template'] . '.html');
									} else if (is_file(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/job_details/' . $atts['template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/job_details/' . $atts['template'] . '.html');
									} else if (is_file(AJB_PLUGIN_PATH . '/templates/job_details/' . $atts['template'] . '.html')) {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_details/' . $atts['template'] . '.html');
									} else {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_details/default.html');
									}
								} else {
									if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_details/' . $atts['template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_details/' . $atts['template'] . '.html');
									} else if (is_file(AJB_PLUGIN_PATH . '/templates/job_details/' . $atts['template'] . '.html')) {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_details/' . $atts['template'] . '.html');
									} else {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_details/default.html');
									}
								}
							} else {
								$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_details/default.html');
							}

							return self::parse_content($content, $oJobAdDetails);
							break;
					}
					return false;
				}

				if ($oJobAdDetails) {
					$oJobAttributes = $oJobAdDetails->attributes();

					if (!empty($apiSettings['adlogic_purge_job_details_page'])) {
						if ($apiSettings['adlogic_purge_job_details_page'] == 'true') {
							self::$shouldBePurged = true;
						}
					}
					/* Generate microdata for job */
					// Begin output buffering
					ob_start();
					// Location Breadcrumbs
					$sLocationArray = array();

					foreach ($oJobAdDetails->locations->location as $sLocation) {
						$sLocationArray[] = $sLocation->value;
					}
					$slicedLocationArray = array_slice($sLocationArray, 0, 2);
					?>
				<span itemscope="itemscope" itemtype="http://schema.org/JobPosting" style="display: none;">
					<span itemprop="description" style="margin:0; text-indent: -9000px; float:left; position: absolute;">
						<?php print strip_tags($oJobAdDetails->JobDescription); ?>
					</span>

					<meta itemprop="image" content="<?php print $oJobAdDetails->standOut->logoURL; ?>" />
					<meta itemprop="name" content="" />
					<meta itemprop="url" content="<?php print get_permalink() . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobAdDetails->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($slicedLocationArray)) . '/' . $oJobAttributes->ad_id . '/'; ?>" />
					<span itemprop="baseSalary" itemscope="itemscope" itemtype="https://schema.org/MonetaryAmount">
						<span itemprop="value"><?php print $oJobAdDetails->Salary->salaryMinimum; ?></span>
						<span itemprop="currency">AUD</span>
					</span>
					<meta itemprop="benefits" content="" /> <!-- ignore this/unlikely to implement -->
					<meta itemprop="datePosted" content="<?php print $oJobAdDetails->PublishedDate; ?>" />
					<?php if ($oJobAdDetails->closeDate) { ?>
						<meta itemprop="validThrough" content="<?php print $oJobAdDetails->closeDate; ?>" />
					<?php } ?>
					<meta itemprop="educationRequirements" content="" /> <!-- ignore this/unlikely to implement -->
					<meta itemprop="employmentType" content="<?php print $oJobAdDetails->workType->value; ?>" /> <!-- full-time, part-time, contract, temporary, seasonal, internship -->
					<meta itemprop="experienceRequirements" content="" />
					<span itemprop="hiringOrganization" content="<?php print $oJobAdDetails->Enquiry->AccountName; ?>"></span>
					<meta itemprop="incentives" content="" />
					<?php
								$classificationList = '';
								foreach ($oJobAdDetails->classifications->classification as $classification) {
									$classificationList .= $classification->value . ', ';
								}
								$classificationList = substr($classificationList, 0, -2)
								?>
					<meta itemprop="industry" content="<?php print $classificationList; ?>" />
					<span itemprop="jobLocation" itemscope="itemscope" itemtype="http://schema.org/Place" style="margin:0; text-indent: -9000px; float:left; position: absolute;">
						<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
							<?php foreach ($oJobAdDetails->locations->location as $location) { ?>
								<span itemprop="addressLocality"><?php print $location->value; ?></span>
							<?php  } ?>
							<?php	require_once(AJB_PLUGIN_PATH . '/lib/classes/Utility.class.php');
										if ($oJobAdDetails->geoLocation) {
											$geoLocationArr = Adlogic_Job_Board_Utility::extractGeoLocationJSON($oJobAdDetails->geoLocation->location);
											if (is_array($geoLocationArr)) {
												if ($geoLocationArr["Suburb"] && $geoLocationArr["Suburb"]->name) { ?>
										<span itemprop="addressRegion"><?php print $geoLocationArr["Suburb"]->name; ?></span>
									<?php 			}
														if ($geoLocationArr["PostCode"] && $geoLocationArr["PostCode"]->name) { ?>
										<span itemprop="postalCode"><?php print $geoLocationArr["PostCode"]->name; ?></span>
							<?php 			}
											}
										}
										?>
						</span>
					</span>
					<meta itemprop="occupationalCategory" content="" /> <!-- ignore this/unlikely to implement -->
					<meta itemprop="qualifications" content="" /> <!-- ignore this/unlikely to implement -->
					<meta itemprop="responsibilities" content="" /> <!-- ignore this/unlikely to implement -->
					<meta itemprop="salaryCurrency" content="AUD" /> <!-- unlikely to be dynamic -->
					<meta itemprop="skills" content="" /> <!-- ignore this/unlikely to implement -->
					<meta itemprop="specialCommitments" content="" /> <!-- ignore this/unlikely to implement -->
					<meta itemprop="title" content="<?php print $oJobAdDetails->JobTitle; ?>" />
					<meta itemprop="workHours" content="" /> <!-- ignore this/unlikely to implement -->
				</span>
				
				<?php
				if (empty($oJobAdDetails->PublishedDate)) { ?>
					<?php $JobPublishDateDMY = 'Publish date is not provided.'; ?>
					<?php } else{ $JobPublishDateDMY = date('d M Y', strtotime($oJobAdDetails->PublishedDate)); }?>

				<?php 
				if (!empty($oJobAdDetails->closeDate)) { ?>
					<?php $jobEndDateSt = date('d M Y', strtotime($oJobAdDetails->closeDate)) ?>
					<?php } else{ $jobEndDateSt = "n/a"; }?>
					<?php if (!empty($oJobAdDetails->Salary->salaryAdditionalText)) { ?>
					<?php print ''; ?>
					<?php } else{ $oJobAdDetails->Salary->salaryAdditionalText = "Not provided"; }?>

					<?php $searchSettings = get_option('adlogic_search_settings');?>
					<?php $applyButtonsPos = $searchSettings['adlogic_job_details_page_design']; ?>
					<?php if (empty($applyButtonsPos) || $applyButtonsPos == 'false') { ?>
					<?php 
						$JobDetailsPageDesignButtons == false;
						
						$jobDetailsPageDesign = '<div id="adlogic_job_loading"></div><div id="adlogic_job_details_mrp-container" class="adlogic-job-container">' . self::$jobHtml . '</div></div>'; ?>
					<?php } else{ 
							$JobDetailsPageDesignButtons == true;
							wp_register_style( 'job-pageo', plugins_url('css/styles.css', AJB_PLUGIN_FILE) );
							wp_enqueue_style( 'job-pageo' );
							wp_register_style( 'job-plugin-css', plugins_url('css/plugins.css', AJB_PLUGIN_FILE) );
							wp_enqueue_style( 'job-plugin-css' );
						$jobDetailsPageDesign = '
					<!-- 02 - CONTENT -->
					<div id="mrp-main-wrapper">
										<!-- ============== Job Detail ====================== -->	
										<section class="mrp-tr-single-detail gray-bg">
											<div class="mrp-container">
												<div class="mrp-row justify-content-center">
													<div class="mrp-col-md-6 mrp-col-sm-12 animated fadeInLeft">
															<div class="mrp-tr-single-body" style="padding: 1rem 0 !important;">
					<div id="adlogic_job_loading"></div><div id="adlogic_job_details_mrp-container" class="adlogic-job-container">' . self::$jobHtml . '</div>
					</div></div>
									<!-- Sidebar Start -->
									<div class="mrp-col-md-4 mrp-col-sm-12 animated fadeInRight">
									<div class="mrp-tr-single-box p-4 mt-3 shadow-sm">
										<!-- Apply Button Wrap -->
										<div class="apply-wrap-buttons">
											<div class="mrp-row">
												<div class="mrp-col-lg-12 mrp-col-md-12 mrp-col-sm-12">
													<div class="input-group">
														<button onclick="javascript:openApplicationForm()" data-part="apply-button" class="mrp-btn mrp-apply-button sticky-mobile">Apply for this job </button>
													</div>
												</div>	
											</div>
											<div class="mrp-row">
												<div class="mrp-col-lg-12 mrp-col-md-12 mrp-col-sm-6">
													<div class="input-group">
														<button onclick="javascript:openEmailToFriendForm()" data-part="email-button" class="mrp-btn mrp-email-button save-job" style="text-align:center;">Email to a friend</button>
													</div>
												</div>	
											<!-- <div style="margin-bottom:1.5rem !important;position:relative;margin:auto;" class="sharethis-inline-share-buttons"></div> -->
												 <div class="ajb_social_sharing_mrp-container"><div class="ajb_social_sharing_sites"><div class="ajb_social_sharing_site"><span class="st_twitter" st_url="'.$jobURL.'" st_title="'.$oJobAdDetails->JobTitle.'" st_image="{job_standout_logo}"></span></div><div class="ajb_social_sharing_site"><span class="st_facebook" st_url="'.$jobURL.'" st_title="'.$oJobAdDetails->JobTitle.'" st_image="{job_standout_logo}"></span></div><div class="ajb_social_sharing_site"><span class="st_linkedin" st_url="'.$jobURL.'" st_title="'.$oJobAdDetails->JobTitle.'" st_image="{job_standout_logo}"></span></div><div class="ajb_social_sharing_site"><span class="st_sharethis" st_url="'.$jobURL.'" st_title="'.$oJobAdDetails->JobTitle.'" st_image="{job_standout_logo}"></span></div></div></div> 
											</div>
										</div>
									</div>

							<!-- Job Overview -->
							<div class="mrp-tr-single-box shadow-sm">
								<div class="mrp-tr-single-body">
									<ul class="mrp-extra-service">
										<li>
											<div class="mrp-icon-box-icon-block">
												<div class="mrp-icon-box-text">
													<strong class="d-block">Posted Date</strong>
													'.$JobPublishDateDMY.'
												</div>
											</div>
										</li>
										<li>
											<div class="mrp-icon-box-icon-block">
												<div class="mrp-icon-box-text">
													<strong class="d-block">Location</strong>
													'.$geoLocationArr["Suburb"]->name.' <br>
													'.$geoLocationArr["State"]->code.' /
													'.$geoLocationArr["Country"]->name.'
												</div>
											</div>
										</li>
										<li>
											<div class="mrp-icon-box-icon-block">
												<div class="mrp-icon-box-text">
													<strong class="d-block">Industry</strong>
													'.$oJobAdDetails->classifications->classification.'
												</div>
											</div>
										</li>
										<li>
											<div class="mrp-icon-box-icon-block">
												<div class="mrp-icon-box-text">
													<strong class="d-block">Job Type</strong>
													'.$oJobAdDetails->workType->value.'
												</div>
											</div>
										</li>
										<li>
											<div class="mrp-icon-box-icon-block">
												<div class="mrp-icon-box-text">
													<strong class="d-block">Salary </strong>
													'. $oJobAdDetails->Salary->salaryAdditionalText .'
												</div>
											</div>
										</li>
									</ul>
								</div>
								</div>	</div>
							</div>'; }?>
				<?php

							if ($oJobAdDetails->AdStatus == "EXPIRED") {
								if (self::$shouldBePurged == true) {
									global $wp_query;
									$wp_query->set_404();
									status_header(404);
									$output = "<p>The job you're looking for is no longer available.</p>";
									return $output;
								}
								print '<div class="adlogic_job_expired"></div>';
								print '<span class="adlogic_job_expired_details">' . self::$jobDetails . '</span>';
							} else {
								print '<div id="adlogic_job_mrp-container" class="mrp-adlogic-job-container">';
								print '<div id="adlogic_job_application_complete" style="display:none;background:rgb(69, 69, 69); padding:1px 15px 10px;color:#FFF;height:95px;margin:10px 0px 20px;">';
								print '	<div class="ajb-leftcol" style="float:left;">';
								print '<h3 style="color:#FFF;margin:10px 0;text-align:left;">Application Successful</h3>';
								print '<p style="color:#FFF;text-align:left;">You\'ve successfully completed your application</p>';
								print '</div>';
								print '<div class="ajb-rightcol" style="float:right;padding:20px 5px;">';
								print '<span class="close" style="color:#979797;text-shadow:none;opacity:.8;cursor:pointer;">X</span>';
								print '</div>';
								print '</div>';
								print $jobDetailsPageDesign;
						}
							  ?>
							  
				<script type="text/javascript">
					<?php
							
								if (Adlogic_Job_Board::getSubSource() != null) {
									?> jobURL += "&subSourceId=<?php print Adlogic_Job_Board::getSubSource(); ?>";
					<?php
								}
								?>
					// Override the openApplicationForm function that is returned in the viewAd method
					if (jobURL.indexOf("clientapplications.myrecruitmentplus.com") > -1) {

					} else {
						// its not there
						function openApplicationForm() {
	
							window.location.href = jobURL;
						}
					}
					jQuery(document).ready(function() {
						if (typeof mrPlus_jQnoConflict === "function" && typeof jQuery === "undefined") {
							jQuery = mrPlus_jQnoConflict;
						}
					});


				</script>
				</span>
	<?php
				// End output buffering, get contents and return
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			} else {
				if ($oJobAdDetails === NULL) {
					return (current_user_can('manage_options')) ? '<div class="adlogic_job_details_no_results"><p><strong>Admin Notice:</strong><br/>This page should not be linked to directly from your website navigation.</p>Please contact <a href="http://www.adlogic.com.au/" title="adlogic support">adlogic</a> if you require assistance.<br/>This message is only displayed to you as a logged in administrator.</div>' : '<div class="adlogic_job_details_no_results">The job you are looking for could not be found.</div>';
				} else if ($oJobAdDetails === false) {
					return '<div class="adlogic_job_details_no_results">The job you are looking for could not be found.</div>';
				}
			}
		}
	}
	?>