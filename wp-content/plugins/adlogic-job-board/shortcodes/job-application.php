<?php

class Adlogic_Job_Application_Shortcodes {
	static $jobDetails;
	static $jobCriteria;
	static $page_title;
	static $oJobApplication;
	static $JobXML;
	static $jobXML;
	static $oJobDetails;
	static $jobId;
	static $jobTitle;
	static $isJob;

	function init() {
		if (Adlogic_Job_Board::check_setup() == true) {
			add_shortcode( 'adlogic_job_application', array('Adlogic_Job_Application_Shortcodes', 'start_application'));

			// Add Hook to add meta if necessary 
			if (!is_admin()) {
				add_action('the_posts', array('Adlogic_Job_Application_Shortcodes', 'check_shortcode'));
			}
		}

		// Add TinyMCE Editor Buttons for Page Editor
		if (is_admin()) {
			Adlogic_Job_Board::add_page_editor_button('ajbJobApplication', 'jquery.tinyMCEjobApplicationButton.js');
		}
	}

	function check_shortcode($posts) {
		global $shortcode_tags;
		if (!empty($posts)) {
			//var_dump($posts);
			$pattern = get_shortcode_regex();
	
			foreach ($posts as $post) {
				preg_match_all( "/$pattern/s", $post->post_content, $matches );

				foreach ($matches[2] as $match) {
					if ($match == 'adlogic_job_application') {
						// Get shortcode tag attributes to look for Job Ad Id if present
						$atts = shortcode_parse_atts(trim(array_pop($matches[3])));
						if (isset($atts['jobadid'])) {
							$_GET['adlogic_ad_id'] = $atts['jobadid'];
						}

						self::get_ad_details();
						/*if (self::$jobDetails) {
							$post->post_title = self::$jobDetails->JobTitle;
						}*/
						if($apiSettings['adlogic_use_job_id'] == 'true') {
							$post->post_title = $oJobTitle->title;
						} else {
							$post->post_title = self::$jobDetails->JobTitle;
						}
						add_action('wp_title', array('Adlogic_Job_Application_Shortcodes', 'add_page_title'));
						remove_action( 'wp_head', 'rel_canonical' );
						break 2;
					}
				}
			}
		}
		return $posts;
	}

	function add_page_title($page_title) {
		$oJobAdDetails = self::$jobDetails;
		$oJobTitle = self::$jobTitle;
		if (self::$jobDetails) {
			//self::$page_title = $oJobAdDetails->JobTitle . ' | ' . $page_title;
			if($apiSettings['adlogic_use_job_id'] == 'true') {
				self::$page_title = $oJobTitle->title . ' | ' . $page_title;
			} else {
				self::$page_title = $oJobAdDetails->JobTitle . ' | ' . $page_title;
			}
		} else {
			//self::$page_title = $page_title;
			if($apiSettings['adlogic_use_job_id'] == 'true') {
				self::$page_title = $page_title;
			} else {
				self::$page_title = $page_title;
			}
		}
		return self::$page_title;
	}

	/**
	 * Function start_application
	 * 
	 * WARNING: HERE BE DRAGONS!
	 * 
	 * ALL YE BE WARNED WHO PARSE THROUGH THESE SEAS OF TRECHEROUS CODE!
	 * 
	 * 
	 * @param unknown $atts
	 * @param string $content
	 * @return string
	 */
		
	
	function start_application($atts, $content = '') {
        // Enqueue Javascript
		wp_enqueue_script( 'jquery-adlogic-jobApplication' );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-monthPicker' );
		wp_enqueue_script( 'jquery-multiSelect' );
		wp_enqueue_script( 'jquery-quicksearch' );
		

		// Enqueue Stylesheet
		wp_enqueue_style( 'adlogic-job-application' );
		wp_enqueue_style( 'monthpicker' );
		wp_enqueue_style( 'jquery-multiSelect' );

		// Get Job Data
		self::get_ad_details();
		$oJobAdDetails = self::$jobDetails;
		$oJobCriteria = self::$jobCriteria;
		$oJobCriteriaAttributes = $oJobCriteria->attributes();
		$adStatus = self::$JobXML->AdStatus;
        
        // We'll use this to check the advId
        $ajb_properties_file = simplexml_load_file(AJB_PLUGIN_URL . 'properties.xml');
        
        if(self::$JobXML->attributes()->advertiserId) {
            $advertiserId = (int) self::$JobXML->attributes()->advertiserId;
        } else {
            $advertiserId = 'null';
        }
        
        $fromIndeed = 'false';
        
        foreach($ajb_properties_file->jobBoard as $jobBoard) {
            if($advertiserId != 'null') {
                if($jobBoard->id == $advertiserId) {
                    $fromIndeed = 'true';   
                }
            }
        }
		
		if(isset($atts['resume_mandatory']) && ($atts['resume_mandatory'] == 'false')) {
			$resume_mandatory = 'false';
		} else {
			$resume_mandatory = 'true';
		}
        
        // Get settings
		$searchSettings = get_option('adlogic_search_settings');
		$apiSettings = get_option('adlogic_api_settings');
		$mobileSettings = get_option('adlogic_mobile_settings');
		$customizationSettings = get_option('adlogic_customization_settings');

		$job_details_page_id = $searchSettings['adlogic_job_details_page'];
		global $wp_rewrite;
		//
		if(Adlogic_Job_Board_Users::isDropboxEnabled()) {
			add_action('wp_footer', array(__CLASS__, 'add_dropbox_javascript'));
		}
		if(Adlogic_Job_Board_Users::isGoogleDriveEnabled()) {
			add_action('wp_footer', array(__CLASS__, 'add_googledrive_javascript'));
		}
		if(Adlogic_Job_Board_Users::isOneDriveEnabled()) {
			add_action('wp_footer', array(__CLASS__, 'add_onedrive_javascript'));
		}
		add_action('wp_footer', array(__CLASS__, 'add_resume_field_js_css'));
		
		// Enqueue jQuery Plugins
		$uniqueId = uniqid('adlogicJobApplication_');

		ob_start();

		if ($oJobAdDetails) {
			$oJobAttributes = $oJobAdDetails->attributes();
			if(($adStatus == "EXPIRED") && ($oJobCriteriaAttributes->candidateRegistration == 'false')) {
			?>
				<div class="ajb-job-expired">
					<h3 class="ajb-job-expired-title">This job has expired!</h3>
					<p class="ajb-job-expired-content">You can no longer apply for this position.</p>
				</div>
			<?php } else { 
			if (Adlogic_Job_Board_Users::isLoginEnabled()) {
				if (Adlogic_Job_Board_Users::isLoggedIn()) {
					if (!empty(Adlogic_Job_Board_Users::$oLinkedInUser)) {
						$oLinkedInProfile = Adlogic_Job_Board_Users::$oLinkedInUser;
						$linkedin_profile_response = json_encode($oLinkedInProfile);
						$linkedin_profile_datastring = base64_encode(json_encode($oLinkedInProfile));
					}
					if (!empty(Adlogic_Job_Board_Users::$oFacebookUser)) {
						$oFacebookProfile = Adlogic_Job_Board_Users::$oFacebookUser;
						$facebook_profile_response = json_encode($oFacebookProfile->asArray());
						$facebook_profile_datastring = base64_encode(json_encode($oFacebookProfile->asArray()));
					}
					if (!empty(Adlogic_Job_Board_Users::$oGooglePlusUser)) {
						$oGooglePlusProfile = Adlogic_Job_Board_Users::$oGooglePlusUser;
						$google_plus_profile_response = json_encode($oGooglePlusProfile);
						$google_plus_profile_datastring = base64_encode(json_encode($oGooglePlusProfile));
					}
				}

				// Build list of logged in accounts and accounts not logged in
				$aLoggedInAccounts = array();
				$aLoggedOutAccounts = array();
				
				if ($oLinkedInProfile) {
					$aLoggedInAccounts[] = array('name' => 'LinkedIn', 'class' => 'linkedin', 'id' => 'linkedin', 'url' => Adlogic_Job_Board_Users::$sLinkedInAuthUrl);
				} else if (Adlogic_Job_Board_Users::isLinkedInEnabled() && !$oLinkedInProfile) {
					$aLoggedOutAccounts[] = array('name' => 'LinkedIn', 'class' => 'linkedin', 'id' => 'linkedIn', 'url' => Adlogic_Job_Board_Users::$sLinkedInAuthUrl);
				}
				if ($oFacebookProfile) {
					$aLoggedInAccounts[] = array('name' => 'Facebook', 'class' => 'facebook', 'id' => 'facebook', 'url' => Adlogic_Job_Board_Users::$sFacebookAuthUrl);
				} else if (Adlogic_Job_Board_Users::isFacebookEnabled() && !$oFacebookProfile) {
					$aLoggedOutAccounts[] = array('name' => 'Facebook', 'class' => 'facebook', 'id' => 'facebook', 'url' => Adlogic_Job_Board_Users::$sFacebookAuthUrl);
				}
				if ($oGooglePlusProfile) {
					$aLoggedInAccounts[] = array('name' => 'Google+', 'class' => 'google-plus', 'id' => 'google-plus', 'url' => Adlogic_Job_Board_Users::$sGooglePlusAuthUrl);
				} else if (Adlogic_Job_Board_Users::isGooglePlusEnabled() && !$oGooglePlusProfile) {
					$aLoggedOutAccounts[] = array('name' => 'Google+', 'class' => 'google-plus', 'id' => 'google-plus', 'url' => Adlogic_Job_Board_Users::$sGooglePlusAuthUrl);
				}
			}
			?>
					<script type="text/javascript">
						linkedin_profile_response = <?php print !empty($linkedin_profile_response) ? $linkedin_profile_response:'null'; ?>;
						linkedin_profile_datastring = "<?php print !empty($linkedin_profile_datastring) ? $linkedin_profile_datastring:''; ?>";
						facebook_profile_response = <?php print !empty($facebook_profile_response) ? $facebook_profile_response:'null'; ?>;
						facebook_profile_datastring = "<?php print !empty($facebook_profile_datastring) ? $facebook_profile_datastring:''; ?>";
						google_plus_profile_response = <?php print !empty($google_plus_profile_response) ? $google_plus_profile_response:'null'; ?>;
						google_plus_profile_datastring = "<?php print !empty($google_plus_profile_datastring) ? $google_plus_profile_datastring:''; ?>";

						jQuery(document).ready(function($) {
							$('#<?php print $uniqueId; ?>').adlogicJobApplication({
								bound_application: '<?php print $uniqueId; ?>',
								job_ad_id: '<?php print self::$jobId; ?>',
								<?php
									if(!empty($_GET['adlogic_job_id'])) {
										?>
										is_job: true,
										<?php
									} else {
										?>
										is_job: false,
										<?php
									}
									if(!empty($_GET['sourceId'])) {
										?>
										adv_id: '<?php print $_GET["sourceId"]; ?>',
										<?php
									} else {
										?>
										adv_id: '10000',
										<?php
									}
								?>
								job_title: '<?php print str_replace("\n", '', addslashes($oJobAdDetails->JobTitle)); ?>',
								job_url: '<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobAdDetails->JobTitle) . '/' . $oJobAttributes->ad_id; ?>',
								sms_job_url: '<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobAdDetails->JobTitle) . '/' . $oJobAttributes->ad_id . ($wp_rewrite->using_permalinks() ? '?' : '&') . 'subSourceId=' . AJB_SUBSOURCE_ID_SMS; ?>',
								email_job_url: '<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobAdDetails->JobTitle) . '/' . $oJobAttributes->ad_id . ($wp_rewrite->using_permalinks() ? '?' : '&') . 'subSourceId=' . AJB_SUBSOURCE_ID_STF; ?>',
								indeed_application_success: <?php print (isset($_GET['indeed-application-success']) ? 'true': 'false' ); ?>,
								validate_candidate_email: <?php print (isset($customizationSettings['adlogic_validate_email']) ? $customizationSettings['adlogic_validate_email'] : 'true' ); ?>,
                                use_system_fields: <?php print (isset($apiSettings['adlogic_system_fields']) ? $apiSettings['adlogic_system_fields'] : 'false'); ?>,
								use_job_ids: <?php print (isset($apiSettings['adlogic_use_job_id']) ? $apiSettings['adlogic_use_job_id'] : 'false'); ?>,
								is_mobile: <?php print ((Adlogic_Job_Board::is_mobile_browser() == true) ? 'true' : 'false'); ?>,
								resume_mandatory: <?php print (isset($customizationSettings['adlogic_resume_mandatory']) ? $customizationSettings['adlogic_resume_mandatory'] : 'true'); ?>
							});
							$('#<?php print $uniqueId; ?>').adlogicJobApplication('onLogin', {linkedIn: linkedin_profile_response, facebook: facebook_profile_response, googlePlus: google_plus_profile_response });

						});
						function indeedApplicationSuccess_<?php print $uniqueId; ?>(indeedButtonObj) {
							$('#<?php print $uniqueId; ?>').adlogicJobApplication('indeedApplicationSuccess');
						}
						
					</script>

					<div id="<?php print $uniqueId; ?>" class="ajb-job-application">
						<?php if ((empty($oLinkedInProfile) && empty($oFacebookProfile) && empty($oGooglePlusProfile)) && (Adlogic_Job_Board::is_mobile_browser())  && ($mobileSettings['adlogic_mobile_application_form'] == 'false')): ?>
						<div class="ajb-auth">
							<?php if (isset($oJobCriteriaAttributes->candidateRegistration) && ($oJobCriteriaAttributes->candidateRegistration == 'true')): ?>
							<h2>Register Now</h2>
							<p>Please select from the following options to register:</p>
							<?php else: ?>
							<h2>Apply Now</h2>
							<p>Please select from the following options to apply for this job:</p>
							<?php endif; 
							if (Adlogic_Job_Board_Users::isLinkedInEnabled()) : ?>
								<a href="<?php print Adlogic_Job_Board_Users::$sLinkedInAuthUrl; ?>" class="linkedInLoginButton">Login with LinkedIn</a>
							<?php endif; 
							if (Adlogic_Job_Board_Users::isFacebookEnabled()) : ?>
							<a href="<?php print Adlogic_Job_Board_Users::$sFacebookAuthUrl; ?>" class="facebookLoginButton">Login with Facebook</a>
							<?php endif; ?>
							<?php if (Adlogic_Job_Board_Users::isGooglePlusEnabled()) : ?>
							<a href="<?php print Adlogic_Job_Board_Users::$sGooglePlusAuthUrl; ?>" class="googlePlusLoginButton">Login with Google+</a>
							
							<?php endif; ?>
							<?php if (Adlogic_Job_Board_Users::isIndeedEnabled()) : ?>
                                <?php if($fromIndeed == 'true'): ?>
                                    <script>(function(d, s, id) {
                                        var js, iajs = d.getElementsByTagName(s)[0];
                                        if (d.getElementById(id)){return;}
                                        js = d.createElement(s); js.id = id;js.async = true;
                                        js.src = "https://apply.indeed.com/indeedapply/static/scripts/app/bootstrap.js";
                                        iajs.parentNode.insertBefore(js, iajs);
                                        }(document, 'script', 'indeed-apply-js'));</script>
                                    <span class="indeed-apply-widget" style="display:block;" 
                                    data-indeed-apply-apiToken="<?php print $apiSettings['adlogic_indeed_key']; ?>" 
                                    data-indeed-apply-jobTitle="<?php print str_replace("\n", '', addslashes($oJobAdDetails->JobTitle)); ?>" 
                                    data-indeed-apply-jobUrl="<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . $oJobAttributes->ad_id; ?>" 
                                    data-indeed-apply-postUrl="<?php print $GLOBALS['adlogic_plugin_settings']['ajaxurl']; ?>?action=indeedJobApplication&jobAdId=<?php print $oJobAttributes->ad_id; ?>&subSourceId=<?php print Adlogic_Job_Board::getSubSource(); ?>&ts=<?php print time(); ?>" 
                                    data-indeed-apply-questions="<?php print $GLOBALS['adlogic_plugin_settings']['ajaxurl']; ?>?action=getQuestions&jobAdId=<?php print $oJobAttributes->ad_id; ?>&ts=<?php print time(); ?>"
                                    data-indeed-apply-onapplied="indeedApplicationSuccess_<?php print $uniqueId; ?>"
                                    data-indeed-apply-continueurl="<?php print get_permalink() . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . $oJobAttributes->ad_id; ?>?indeed-application-success"
                                    data-indeed-apply-json="yes" class="IndeedLoginButton">Login with Indeed</span>
                                <?php endif; ?>
							<?php endif; ?>
						</div>
						<?php elseif ( !empty($oLinkedInProfile) || !empty($oFacebookProfile) || !empty($oGooglePlusProfile) || (Adlogic_Job_Board::is_mobile_browser() == false) || $mobileSettings['adlogic_mobile_application_form'] == 'true'): ?>
						<div class="ajb-application-details">
							<?php if (isset($oJobCriteriaAttributes->candidateRegistration) && ($oJobCriteriaAttributes->candidateRegistration == 'true')): ?>
								<h2>Register Now</h2>
							<?php else: ?>
								<h2>Apply Now</h2>
							<?php endif; ?>
							<form action="" method="post" target="<?php print $uniqueId?>_iframe" enctype="multipart/form-data">
								<?php 
								if (count($aLoggedInAccounts)) {
									?>
									<p>You are currently signed in with:
									<?php 
										foreach ($aLoggedInAccounts as $loggedInAccount) {
											print '<span class="login-status active ' . $loggedInAccount['class'] . '">' . $loggedInAccount['name'] . '</span>';
										}
										foreach ($aLoggedOutAccounts as $loggedOutAccount) {
											print '<a href="' . $loggedOutAccount['url'] . '"><span class="login-status inactive ' . $loggedOutAccount['class'] . '">' . $loggedOutAccount['name'] . '</span></a>';
										}
									?>
									</p>
									<?php if ((count($aLoggedInAccounts) > 1) || (Adlogic_Job_Board::is_mobile_browser() == false)) :?>
									<div class="ajb-profile-source-selector">
										<?php if (isset($oJobCriteriaAttributes->candidateRegistration) && ($oJobCriteriaAttributes->candidateRegistration == 'true')): ?>
											<label for="ajb-profile-source">Select which profile to register with:</label>
										<?php else: ?>
											<label for="ajb-profile-source">Select which profile to apply with:</label>
										<?php endif; ?>
										<select id="ajb-profile-source">
											<?php foreach ($aLoggedInAccounts as $loggedInAccount) : ?>
											<option value="<?php print $loggedInAccount['id']; ?>"><?php print $loggedInAccount['name']; ?></option>
											<?php endforeach; ?>
											<?php if (Adlogic_Job_Board_Users::isIndeedEnabled()) : ?>
                                                <?php if($fromIndeed == 'true'): ?>
											         <option value="indeed">Indeed</option>
                                                <?php endif; ?>
											<?php endif;?>
											<?php if (Adlogic_Job_Board::is_mobile_browser() == false): ?>
											<option value="upload">Uploaded Resume</option>
											<?php endif;?>
										</select>
										<p class="ajb-application-disclaimer"><strong>Note:</strong> We will never post to your social media profile without your express permission.</p>
									</div>
									<?php if (Adlogic_Job_Board_Users::isIndeedEnabled()) : ?>
                                        <?php if($fromIndeed == 'true'): ?>
                                            <script>(function(d, s, id) {
                                                var js, iajs = d.getElementsByTagName(s)[0];
                                                if (d.getElementById(id)){return;}
                                                js = d.createElement(s); js.id = id;js.async = true;
                                                js.src = "https://apply.indeed.com/indeedapply/static/scripts/app/bootstrap.js";
                                                iajs.parentNode.insertBefore(js, iajs);
                                                }(document, 'script', 'indeed-apply-js'));</script>
                                            <div class="indeedApplyContainer">
                                            <?php if (isset($oJobCriteriaAttributes->candidateRegistration) && ($oJobCriteriaAttributes->candidateRegistration == 'true')): ?>
                                                <p>Click the following button to have your registration submitted via Indeed:</p>
                                            <?php else: ?>
                                                <p>Click the following button to have your job application submitted via Indeed:</p>
                                            <?php endif; ?>
                                            <span class="indeed-apply-widget" 
                                            data-indeed-apply-apiToken="<?php print $apiSettings['adlogic_indeed_key']; ?>" 
                                            data-indeed-apply-jobTitle="<?php print str_replace("\n", '', addslashes($oJobAdDetails->JobTitle)); ?>" 
                                            data-indeed-apply-jobUrl="<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . $oJobAttributes->ad_id; ?>" 
                                            data-indeed-apply-postUrl="<?php print $GLOBALS['adlogic_plugin_settings']['ajaxurl']; ?>?action=indeedJobApplication&jobAdId=<?php print $oJobAttributes->ad_id; ?>&subSourceId=<?php print Adlogic_Job_Board::getSubSource(); ?>&ts=<?php print time(); ?>" 
                                            data-indeed-apply-questions="<?php print $GLOBALS['adlogic_plugin_settings']['ajaxurl']; ?>?action=getQuestions&jobAdId=<?php print $oJobAttributes->ad_id; ?>&ts=<?php print time(); ?>"
                                            data-indeed-apply-onapplied="indeedApplicationSuccess_<?php print $uniqueId; ?>"
                                            data-indeed-apply-continueurl="<?php print get_permalink() . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . $oJobAttributes->ad_id; ?>?indeed-application-success"
                                            data-indeed-apply-json="yes" class="IndeedLoginButton">Login with Indeed</span>
                                            </div>
                                        <?php endif; ?>
									<?php endif; ?>
									<?php else: ?>
									<select id="ajb-profile-source">
										<?php foreach ($aLoggedInAccounts as $loggedInAccount) : ?>
										<option value="<?php print $loggedInAccount['id']; ?>"><?php print $loggedInAccount['name']; ?></option>
										<?php endforeach; ?>
									</select>
									<p class="ajb-application-disclaimer"><strong>Note:</strong> We will never post to your social media profile without your express permission.</p>
									<?php
									endif; 
								} else if ((empty($oLinkedInProfile)) && (empty($oFacebookProfile)) && (empty($oGooglePlusProfile))) {
									if((Adlogic_Job_Board::is_mobile_browser() == false) || ($mobileSettings['adlogic_mobile_application_form'] == 'true')) {
																		?>
									<?php if (isset($oJobCriteriaAttributes->candidateRegistration) && ($oJobCriteriaAttributes->candidateRegistration == 'true')): ?>
									<p class="ajb-register-notice">You may register with your own resume/cv and cover letter below<?php if (Adlogic_Job_Board_Users::isLoginEnabled()) : ?>, or instead select the following options to register<?php endif; ?>:</p>
									<?php else: ?>
									<p class="ajb-apply-notice">You may apply with your own resume/cv and cover letter below<?php if (Adlogic_Job_Board_Users::isLoginEnabled()) : ?>, or instead select the following options to apply for this job<?php endif; ?>:</p>
									<?php endif; 
									
									if (Adlogic_Job_Board_Users::isLinkedInEnabled()) : ?>
									<a href="<?php print Adlogic_Job_Board_Users::$sLinkedInAuthUrl; ?>" class="linkedInLoginButton">Login with LinkedIn</a>
									
									<?php
									endif;
									if (Adlogic_Job_Board_Users::isFacebookEnabled()) : ?>
									<a href="<?php print Adlogic_Job_Board_Users::$sFacebookAuthUrl; ?>" class="facebookLoginButton">Login with Facebook</a>
									<?php endif; ?>
									<?php if (Adlogic_Job_Board_Users::isGooglePlusEnabled()) : ?>
									<a href="<?php print Adlogic_Job_Board_Users::$sGooglePlusAuthUrl; ?>" class="googlePlusLoginButton">Login with Google+</a>
									
									<?php endif; ?>
									<?php if (Adlogic_Job_Board_Users::isIndeedEnabled()) : ?>
                                        <?php if($fromIndeed == 'true'): ?>
                                            <script>(function(d, s, id) {
                                                var js, iajs = d.getElementsByTagName(s)[0];
                                                if (d.getElementById(id)){return;}
                                                js = d.createElement(s); js.id = id;js.async = true;
                                                js.src = "https://apply.indeed.com/indeedapply/static/scripts/app/bootstrap.js";
                                                iajs.parentNode.insertBefore(js, iajs);
                                                }(document, 'script', 'indeed-apply-js'));</script>
                                            <span class="indeed-apply-widget" style="display:block;" 
                                            data-indeed-apply-apiToken="<?php print $apiSettings['adlogic_indeed_key']; ?>" 
                                            data-indeed-apply-jobTitle="<?php print str_replace("\n", '', addslashes($oJobAdDetails->JobTitle)); ?>" 
                                            data-indeed-apply-jobUrl="<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . $oJobAttributes->ad_id; ?>" 
                                            data-indeed-apply-postUrl="<?php print $GLOBALS['adlogic_plugin_settings']['ajaxurl']; ?>?action=indeedJobApplication&jobAdId=<?php print $oJobAttributes->ad_id; ?>&subSourceId=<?php print Adlogic_Job_Board::getSubSource(); ?>&ts=<?php print time(); ?>" 
                                            data-indeed-apply-questions="<?php print $GLOBALS['adlogic_plugin_settings']['ajaxurl']; ?>?action=getQuestions&jobAdId=<?php print $oJobAttributes->ad_id; ?>&ts=<?php print time(); ?>"
                                            data-indeed-apply-onapplied="indeedApplicationSuccess_<?php print $uniqueId; ?>"
                                            data-indeed-apply-continueurl="<?php print get_permalink() . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . $oJobAttributes->ad_id; ?>?indeed-application-success"
                                            data-indeed-apply-json="yes" class="IndeedLoginButton">Login with Indeed</span>
                                        <?php endif; ?>
									<?php endif; ?>

									<input id="ajb-profile-source" type="hidden" value="upload" />
									<p class="ajb-application-disclaimer"><strong>Note:</strong> We will never post to your social media profile without your express permission.</p>
									<?php
									}
								}
								?>
								<div class="ajb-application-form">
									<script type="text/javascript">
									// Find a dependent value
									
									function question(i,t,ops,depV) {
											this.id = i;
											this.type = t;
											this.options = ops;
											this.dependentValue = depV;
											
											this.getId = function(){
												return this.id;
											}
											
											this.getType = function(){
												return this.type;
											}
											this.getOptions = function(){
												return this.options;
											}
											this.getDependent = function() {
												return this.dependentValue;
											}
											
									}
									var questionsMap = new Object();
									jQuery(document).ready(function($){
										$(".ajb_field_select").change(function(){
											var a = $(this).val();
											var id = $(this).attr("id");
											dependencyControl(id, a);
											
										});
										
										function dependencyControl(questionId, optionId) {
											//alert(optionId);
											if(optionId === '') {
												var valueArray = [];
												if(questionsMap[questionId]==undefined){
													return;
												}
												var tempArray = questionsMap[questionId].getOptions();
												
												if(tempArray != null) {
													for(jj=0;jj<tempArray.length;jj++){
														valueArray[jj]=tempArray[jj].substr(0, tempArray[jj]);
													}
												}
												$(valueArray).each(function(o){
													$("body").find(".dependent_value_" + valueArray[o]).val("");																																				$("body").find(".dependent_value_" + valueArray[o]).hide();
													dependencyControl($("body").find(".dependent_value_" + valueArray[o]).attr("id"), '');
												});		
											} else {
												var valueArray = [];
												if(questionsMap[questionId]==undefined){
													return;
												}
												var tempArray = questionsMap[questionId].getOptions();
												var hh=0;
												for(jj=0;jj<tempArray.length;jj++){
														if(optionId!=tempArray[jj].substr(0, tempArray[jj])){
															valueArray[hh]=tempArray[jj].substr(0, tempArray[jj]);
															hh++;
														}																	
												}
												
												$(valueArray).each(function(o){
													for(var key in questionsMap){
														if(questionsMap[key]){
														
															if(questionsMap[key].getDependent()==valueArray[o]){
																$("body").find(".dependent_value_" + valueArray[o]).val("");																																				$("body").find(".dependent_value_" + valueArray[o]).hide();	
																dependencyControl(questionsMap[key].getId(), '');
																
															}
														}
													}																	
																														
												});
												$("body").find(".dependent_value_" + optionId).show();
												$(".dependent_value").each(function(i){
													if($(this).is(":hidden")){
														$(this).find(".essential").addClass("wasEssential");
														$(this).find(".wasEssential").removeClass("essential mandatory");
													} else {
														$(this).find(".wasEssential").addClass("essential");
														$(this).find(".wasEssential").addClass("mandatory");
														$(this).find(".wasEssential").removeClass("wasEssential");
													}
												});															
											}
											
										}
									});		
									</script>	
									<?php if (isset($oJobCriteriaAttributes->candidateRegistration) && ($oJobCriteriaAttributes->candidateRegistration == 'true')): ?>
										<p class="ajb-notice-to-register">Fill out the following form to have your registration submitted</p>
									<?php else: ?>
										<p class="ajb-notice-to-apply">Fill out the following form to have your job application submitted</p>
									<?php endif; ?>
									<?php if ((!empty($oLinkedInProfile)) || (!empty($oFacebookProfile)) || (!empty($oGooglePlusProfile))) : ?>
									<div class="ajb-profile-box">
									</div>
									<?php endif; ?>
									<fieldset id="ajb-personal-details">
										<legend><?php print(isset($customizationSettings['adlogic_personal_details']) && ($customizationSettings['adlogic_personal_details'] != '') ? $customizationSettings['adlogic_personal_details'] : 'Personal Details:'); ?></legend>
									<?php
										if (isset($oJobCriteria->jobCriteria)) {
											// sort questions into index order
											$oSortedQuestions = self::$oJobApplication->sortQuestions($oJobCriteria->jobCriteria);

											// Now sort them into types
											$aCustomFieldQuestions = array();
											$aCriteriaQuestions = array();

											foreach ($oSortedQuestions as $oQuestion) {
												switch($oQuestion->attributes()->type) {
													case 'CF': // Custom Field Questions
														$aCustomFieldQuestions[] = $oQuestion;
														break;
													case 'E': // Essential Criteria Questions
													case 'D': // Desirable Criteria Questions
														$aCriteriaQuestions[] = $oQuestion;
														break;
												}
											}
										}
										if(($apiSettings['adlogic_system_fields'] == 'false') || ($apiSettings['adlogic_system_fields'] == '')) {
	                                            ?>
	                                            <div class="ajb-application-name ajb-applicationFields">
	                                                <div class="ajb-first-name-container">
		                                               		<div class="ajb-left">
		                                               	     	<label for="ajb-first-name">First Name<span class="mandatory"></span></label>
		                                                    </div>
		                                                    <div class="ajb-right">
		                                                    	<input type="text" id="ajb-first-name" name="firstName" />
		                                                    </div>
	                                                </div>
	                                                <div class="ajb-last-name-container">
															<div class="ajb-left">
		                                                   		<label for="ajb-last-name">Last Name<span class="mandatory"></span></label>
		                                                	</div>
		                                                    <div class="ajb-right">
		                                                    	<input type="text" id="ajb-last-name" name="lastName" />
		                                                    </div>
	                                                </div>
	                                            </div>
	                                            <div class="ajb-application-email-address ajb-applicationFields">
	                                                <div class="ajb-email-container">
		                                                	<div class="ajb-left">
		                                                    	<label for="ajb-email-address">Email Address<span class="mandatory"></span></label>
		                                                	</div>
		                                                	<div class="ajb-right">
																<input type="text" id="ajb-email-address" name="emailAddress" />
		                                                	</div>
	                                                </div>
	                                            </div>
	                                            <?php
		                                            // If someone is still using our old customization field system.
													if (!isset($atts['show_criteria']) || $atts['show_criteria'] != 'false') {
				
														if (isset($oJobCriteria->jobCriteria)) {
															// sort questions into index order
															$oSortedQuestions = self::$oJobApplication->sortQuestions($oJobCriteria->jobCriteria);
				
															// Now sort them into types
															$aCustomFieldQuestions = array();
															$aCriteriaQuestions = array();
				
															foreach ($oSortedQuestions as $oQuestion) {
																switch($oQuestion->attributes()->type) {
																	case 'CF': // Custom Field Questions
																		$aCustomFieldQuestions[] = $oQuestion;
																		break;
																	case 'E': // Essential Criteria Questions
																	case 'D': // Desirable Criteria Questions
																		$aCriteriaQuestions[] = $oQuestion;
																		break;
																}
															}
				
															if (!empty($aCustomFieldQuestions)) {
																	// Print Essential  & Desired Criteria Questions
																	foreach($aCustomFieldQuestions as $oJobCriterion) {
																		$excludeQuestions = array(
																					'103',	// Additional Documents
																					'14',	// Retention Consent
																					'32'	// Cover Letter
																				);
																		if (!in_array($oJobCriterion->attributes()->id, $excludeQuestions)) {
																			self::renderQuestion($oJobCriterion, $oJobCriteria, self::$isJob);
																		}
																	}
															}
														}
													}
                                        } else {
											if (!isset($atts['show_criteria']) || $atts['show_criteria'] != 'false') {
												
												if (isset($oJobCriteria->jobCriteria)) {
													if (!empty($aCriteriaQuestions)) {

													?>
													<script type="text/javascript">
														jQuery(document).ready(function($){
															$(".ajb-application-name").prepend('<input type="hidden" name="attachResume" id="attachResume" />');	
														})
													</script>
													<?php
															// Print Essential  & Desired Criteria Questions
															foreach($aCriteriaQuestions as $oJobCriterion) {
															$excludeQuestions = array(
																		'103',	// Additional Documents
																		'14',	// Retention Consent
																		'32'	// Cover Letter
																	);
															if (!in_array($oJobCriterion->attributes()->id, $excludeQuestions)) {
																//self::renderQuestion($oJobCriterion, $oJobCriteria);
															}
														}
															
															foreach($aCriteriaQuestions as $questions) {
															if ($questions->attributes()->mappedField != '') {
																self::renderQuestion($questions, $oJobCriteria, self::$isJob);
															}
														}
															
													}
	                                                                                        
	                                            }
											}
										}
									?>
									</fieldset>
									
									<?php
									
									$filterQuestions = array();
									if(!empty($aCriteriaQuestions)) {
										foreach($aCriteriaQuestions as $questionsToFilter) {
											if($questionsToFilter['mappedField'] == '') {
												$filterQuestions[] = $questionsToFilter;
											} else {
												
											}
										}
									}
									if(!empty($filterQuestions)) { 
									?>
									<fieldset id="ajb-application-criteria-fieldset">
										<legend><?php print(isset($customizationSettings['adlogic_criteria_questions']) && ($customizationSettings['adlogic_criteria_questions'] != '') ? $customizationSettings['adlogic_criteria_questions'] : 'Extra Information:'); ?></legend>
												<?php
												
												if (!empty($aCriteriaQuestions)) {
													// Print Essential  & Desired Criteria Questions
													foreach($aCriteriaQuestions as $oJobCriterion) {
														if($oJobCriterion->attributes()->mappedField == '') {
															self::renderQuestion($oJobCriterion, $oJobCriteria, self::$isJob);
														}
													}
												}
												?>
									</fieldset>
	
									<?php 
									}
									if (Adlogic_Job_Board::is_mobile_browser() == false): ?>
									<fieldset id="ajb-applicant-documents">
										
										<legend><?php print(isset($customizationSettings['adlogic_application_documents']) && ($customizationSettings['adlogic_application_documents'] != '') ? $customizationSettings['adlogic_application_documents'] : 'Application Documents:'); ?></legend>
										<div class="ajb-file-uploads ajb-applicationFields">
											<script type="text/javascript">
											jQuery(document).ready(function($){
												var selectOption = $(".ajb-select-coverletter input");
												// Do a check before the option is selected.
												if($(selectOption).val() === 'attach') {
													$(".ajb-application-comments").hide();
													$(".ajb-cover-letter-container").show();
												} else {
													$(".ajb-application-comments").show();
													$(".ajb-cover-letter-container").hide();
												}
												$(selectOption).change(function(){
													if($(this).val() === 'attach') {
														$(".ajb-application-comments").hide();
														$(".ajb-cover-letter-container").show();
													} else {
														$(".ajb-application-comments").show();
														$(".ajb-cover-letter-container").hide();
													}
												});
											});
											</script>
											<div class="ajb-select-coverletter">
												<label for="selectCoverLetter">
													<input type="radio" name="selectCoverLetter" id="selectCoverLetter" checked="checked" value="attach">
													Attach Cover Letter
												</label>
												<label for="writeCoverLetter">
													<input type="radio" name="selectCoverLetter" id="writeCoverLetter" value="write">
													Write one now
												</label>
											</div>
										<div class="ajb-file-uploads ajb-applicationFields">
											<?php
												if (!empty($aCustomFieldQuestions)) {
														// Cover Letter Custom Field options (if set - if not default to default values).
														$cover_letter_field_output = false;
														foreach($aCustomFieldQuestions as $oJobCriterion) {
															$includeQuestions = array(
																		'32'	// Cover Letter
																	);
		
															if (in_array($oJobCriterion->attributes()->id, $includeQuestions)) {
																$css_class = 'custom_field';
																$is_mandatory = ($oJobCriterion->answer->attributes()->mandatory == 'true') ? true : false;
																$cover_letter_field_output = true;
																print '<p class="ajb-cover-letter-container"><label for="ajb-cover-letter" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label>';
																print '<input type="file" name="coverLetterFile" id="ajb-cover-letter" class="ajb_field_file' . (($is_mandatory == true) ? ' mandatory' : '') . '"></p>';
																break;
															}
														}
		
														if ($cover_letter_field_output == false) {
															print '<p class="ajb-cover-letter-container"><label for="ajb-cover-letter">Cover Letter</label><input type="file" name="coverLetterFile" id="ajb-cover-letter"  class="ajb_field_file"></p>';
														}
														?>
														
														<?php
												} else {
													?>
													<p class="ajb-cover-letter-container"><label for="ajb-cover-letter">Cover Letter</label><input type="file" name="coverLetterFile" id="ajb-cover-letter"  class="ajb_field_file"></p>
													<?php
												}
											?>
											
										</div>
									<?php endif; ?>
									<div class="ajb-application-comments ajb-applicationFields">
										<?php if (Adlogic_Job_Board::is_mobile_browser() == true): ?>
											<p><label for="ajb-comments">Comments</label><textarea id="ajb-comments" name="comments" maxlength="2000"></textarea></p>
										<?php else: ?>
											<p><label for="ajb-comments">Type a Cover Letter</label><textarea id="ajb-comments" name="comments" maxlength="2000"></textarea></p>
										<?php endif; ?>
									</div>
									
									<script>
											// Internet Explorer doesn't like opening a file browser using the .click() function.
											// We'll display the old input instead for Internet Explorer 10 and older.
											jQuery(document).ready(function($){
												if($.browser.msie && $.browser.version <= 10.0) {
													//$(".ajb-resume-or").hide();
													$(".ajb-upload-resume").hide();
													$("#ieResumeLabel").show();
													$("#ajb-resume").css({
														"position":"relative",
														"left":"0"
													});
												}
											});
											
										</script>
										<?php if(isset($customizationSettings['adlogic_resume_mandatory']) && ($customizationSettings['adlogic_resume_mandatory'] == 'false') || ($customizationSettings['adlogic_resume_mandatory'] == '')) { ?>
											<label for="resumeFile" id="ieResumeLabel" style="display: none;">Attach your resume</label>
										<?php } else { ?>
											<label for="resumeFile" id="ieResumeLabel" style="display: none;">Attach your resume<span class="mandatory"></span></label>
										<?php } ?>
										<p>
											<input type="file" name="resumeFile" id="ajb-resume" style="position: absolute;left:-99999999px;">
										</p>
									
									<?php
										if (Adlogic_Job_Board::is_mobile_browser()) {
											if(isset($customizationSettings['adlogic_resume_mandatory']) && ($customizationSettings['adlogic_resume_mandatory'] == 'false') || ($customizationSettings['adlogic_resume_mandatory'] == '')) {
												?>
												<div id="resume_not_mandatory">
													<div class="ajb-file-uploads ajb-resume-upload ajb-applicationFields">
													<?php if(Adlogic_Job_Board_Users::isDropboxEnabled() || (Adlogic_Job_Board_Users::isGoogleDriveEnabled() || (Adlogic_Job_Board_Users::isOneDriveEnabled()))) { ?> 
														<p class="ajb-attach-resume-social">Attach your resume (if available):</p>
													<?php } ?>
													
													<?php if(Adlogic_Job_Board_Users::isDropboxEnabled()) { ?>
														<div id="ajb-dropbox-container" class="ajb-dropbox-container"></div>
													<?php } ?>
													<?php if(Adlogic_Job_Board_Users::isGoogleDriveEnabled()) { ?>
														<div id="ajb-googledrive-container">
															<div id="ajb-googledrive-api-load-resume" onclick="javascript:onApiLoad('resume');">
																<span class="ajb-googledrive-icon"></span><span class="ajb-googledrive-text">Choose from Google Drive</span>
															</div>
														</div>
													<?php } ?>
													<?php if(Adlogic_Job_Board_Users::isOneDriveEnabled()) { ?>
														<div id="ajb-onedrive-container" class="ajb-onedrive-container">
															<div id="picker" onclick="ShowOneDrivePicker()">
																<img src="<?php print AJB_PLUGIN_URL ?>css/images/onedrive.png"/>
																<div class="onedrive-text">Choose from OneDrive</div>
															</div>
														</div>
													<?php } ?>
												</div>
												<?php
											} else {
												?>
												<div class="ajb-file-uploads ajb-resume-upload ajb-applicationFields">
													<?php if(Adlogic_Job_Board_Users::isDropboxEnabled() || (Adlogic_Job_Board_Users::isGoogleDriveEnabled() || (Adlogic_Job_Board_Users::isOneDriveEnabled()))) { ?> 
														<p class="ajb-attach-resume-social">Attach your resume<span class="mandatory">*</span>:</p>
													<?php } ?>
													
													<?php if(Adlogic_Job_Board_Users::isDropboxEnabled()) { ?>
														<div id="ajb-dropbox-container" class="ajb-dropbox-container"></div>
													<?php } ?>
													<?php if(Adlogic_Job_Board_Users::isGoogleDriveEnabled()) { ?>
														<div id="ajb-googledrive-container">
															<div id="ajb-googledrive-api-load-resume" onclick="javascript:onApiLoad('resume');">
																<span class="ajb-googledrive-icon"></span><span class="ajb-googledrive-text">Choose from Google Drive</span>
															</div>
														</div>
													<?php } ?>
													<?php if(Adlogic_Job_Board_Users::isOneDriveEnabled()) { ?>
														<div id="ajb-onedrive-container" class="ajb-onedrive-container">
															<div id="picker" onclick="ShowOneDrivePicker()">
																<img src="<?php print AJB_PLUGIN_URL ?>css/images/onedrive.png"/>
																<div class="onedrive-text">Choose from OneDrive</div>
															</div>
														</div>
													<?php } ?>
												<?php
											}
										} else {
											?>
											<div id="ajb-resume-field" class="ajb-file-uploads ajb-resume-upload ajb-applicationFields">
												<div class="ajb-upload-resume">Upload your resume</div>
												<?php if(Adlogic_Job_Board_Users::isDropboxEnabled() || (Adlogic_Job_Board_Users::isGoogleDriveEnabled() || (Adlogic_Job_Board_Users::isOneDriveEnabled()))) { ?>
													<div class="ajb-resume-or">or</div>
												<?php } ?>
												<div class="ajb-upload-resume-options">
													<?php if(Adlogic_Job_Board_Users::isDropboxEnabled()) { ?>
														<div id="ajb-dropbox-container" class="ajb-dropbox-container"></div>
													<?php } ?>
													<?php if(Adlogic_Job_Board_Users::isGoogleDriveEnabled()) { ?>
														<div id="ajb-googledrive-container">
															<div id="ajb-googledrive-api-load-resume" onclick="javascript:onApiLoad('resume');">
																<span class="ajb-googledrive-icon"></span><span class="ajb-googledrive-text">Choose from Google Drive</span>
															</div>
														</div>
													<?php } ?>
													<?php if(Adlogic_Job_Board_Users::isOneDriveEnabled()) { ?>
														<div id="ajb-onedrive-container" class="ajb-onedrive-container">
															<div id="picker" onclick="ShowOneDrivePicker()">
																<img src="<?php print AJB_PLUGIN_URL ?>css/images/onedrive.png"/>
																<div class="onedrive-text">Choose from OneDrive</div>
															</div>
														</div>
													<?php } ?>
												</div>
											<?php
										}
										
									?>
										
									</div>
									<?php
										if (!empty($aCustomFieldQuestions)) {
												// Print Retention Consent and Additional Document Fields (if available)
												foreach($aCustomFieldQuestions as $oJobCriterion) {
													$includeQuestions = array(
																'103'	// Additional Documents
															);
													if (in_array($oJobCriterion->attributes()->id, $includeQuestions)) {
														self::renderQuestion($oJobCriterion, $oJobCriteria, self::$isJob);
													}
												}
												foreach($aCustomFieldQuestions as $oJobCriterion) {
													$includeQuestions = array(
															'14'	// Retention Consent
													);
													if (in_array($oJobCriterion->attributes()->id, $includeQuestions)) {
														self::renderQuestion($oJobCriterion, $oJobCriteria, self::$isJob);
													}
												}
										}
									?>
									</fieldset>
									<div class="ajb-applicationSubmit">
										<input type="button" id="ajb-submitApplication" value="Submit Application" />
									</div>
								</div>
							</form>
						</div>
						<?php endif; ?>
						<?php if (isset($oJobCriteriaAttributes->candidateRegistration) && ($oJobCriteriaAttributes->candidateRegistration == 'false')): ?>
							<?php  if (
								!empty($mobileSettings['adlogic_smsglobal_server']) &&
								!empty($mobileSettings['adlogic_smsglobal_username']) &&
								!empty($mobileSettings['adlogic_smsglobal_password']) &&
								!empty($mobileSettings['adlogic_smsglobal_sender_number'])
							) : ?>
						<fieldset id="ajb-sms-job-frame">
							<legend>SMS this job</legend>
							<div class="ajb-sms-job">
								<h2>SMS me this job!</h2>
								<p>Send an SMS to yourself with the Job Profile</p>
								<div class="ajb-mobile-number"><label for="ajb-mobile-input">Mobile Number:</label><input type="text" id="ajb-mobile-input" name="application-mobile-number" /> <input type="button" id="ajb-sendSMS" value="Send SMS" /></div>
							</div>
						</fieldset>
							<?php endif; ?>
						<fieldset id="ajb-email-me-job-frame">
							<legend>Email me this job</legend>
							<div class="ajb-email-job">
								<!--<h2>Email me this job!</h2>-->
								<p>Send an Email to yourself with the Job Profile</p>
								<div class="ajb-email-address"><label for="ajb-email-input">Email Address:</label><input type="text" id="ajb-email-send-address" name="application-email-address" /> <input type="button" id="ajb-sendEmail" value="Send Email" /></div>
							</div>
						</fieldset>
						<fieldset id="ajb-email-job-frame">
							<legend>Send this Job to a Friend</legend>
							<div class="ajb-email-job-friend">
								<!--<a name="send_to_friend"></a><h2>Send Job to a Friend!</h2>-->
								<p>Send an Email to a Friend with the Job Profile</p>
								<div class="ajb-email-address">
								<label for="ajb-your-email-input">Your Email Address:</label><input type="text" id="ajb-your-email-send-address" name="application-your-email-address" /><br/>
								<label for="ajb-friend-email-input">Friend's Email Address:</label><input type="text" id="ajb-friend-email-send-address" name="application-friend-email-address" /><br/>
								<label for="ajb-message-input">Message (optional):</label>
								<textarea id="ajb-message-input" name="email-message">Hi there,
I found this job on the <?php print bloginfo('site_title'); ?> website that I thought you might be interested in. 
								</textarea><br/>
								<input type="button" id="ajb-sendEmailFriend" value="Send Email" /></div>
					</div>
						</fieldset>
						<?php endif; ?>
					</div>
					<div style="display: none;" class="ajb-application-acknowledgement">
						<?php
							if (!empty($atts['acknowledgement_template'])) {
								if (defined('MULTISITE') && (MULTISITE == true)) {
									if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html');
									} else if (is_file(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogicsocialboard/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html');
									} else if (is_file(AJB_PLUGIN_PATH . '/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html')) {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html');
									} else {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_application_acknowledgement/default.html');
									}
								} else {
									if (is_file(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html')) {
										$content = file_get_contents(get_stylesheet_directory() .  '/css/adlogic-job-board/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html');
									} else if (is_file(AJB_PLUGIN_PATH . '/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html')) {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_application_acknowledgement/' . $atts['acknowledgement_template'] . '.html');
									} else {
										$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_application_acknowledgement/default.html');
									}
								}
							} else {
								$content = file_get_contents(AJB_PLUGIN_PATH . '/templates/job_application_acknowledgement/default.html');
							}
							print $content;
						?>
						<input class="ajb-acknowledge-return" type="button" onclick="location.href='<?php print get_permalink($job_details_page_id) . ($wp_rewrite->using_permalinks() ? 'query/' : '&/') . Adlogic_Job_Board::uriSafe($oJobAdDetails->JobTitle) . '/' . $oJobAttributes->ad_id; ?>';" value="Return to Job"/>
					</div>
				<?php
			}
		}
		$applicationHtml = ob_get_contents();
		ob_end_clean();
		return $applicationHtml;
	}

	function get_ad_details() {
		if (empty(self::$jobDetails) && empty(self::$jobHtml)) {
			$searchVars = explode('/', urldecode($_SERVER['QUERY_STRING']));
			$matches = '';

			if (!isset($_GET['adlogic_job_id'])) {
				if(isset($_GET['adlogic_ad_id'])) {
					// JobAdId Paramater passed in shortcode
					$jobId = $_GET['adlogic_ad_id'];
				} else {
					preg_match('/^(\d*)/', array_pop($searchVars), $matches);
					if (empty($matches[1])) {
						preg_match('/^(\d*)/', array_pop($searchVars), $matches);
						$jobId = array_pop($matches);
						self::$isJob = 'false';
					} else {
						$jobId = array_pop($matches);
						self::$isJob = 'false';
					}
				}
			} else {
				$jobId = $_GET['adlogic_job_id'];
				self::$isJob = 'true';
			}
	
			if ($jobId && is_numeric($jobId)) {
				// Get Settings from Wordpress
				$apiSettings = get_option('adlogic_api_settings');
	
				// Instantiate Soap Client Object
				$oSoapClient = Adlogic_Job_Board::getSoapConnection();

				// Requires Job Application Class
				require_once(AJB_PLUGIN_PATH . '/lib/classes/jobApplication.class.php');
				require_once(AJB_PLUGIN_PATH . '/lib/classes/jobDetails.class.php');

				// Instantiate Job Application Object
				self::$oJobApplication = $oJobApplication = new ApplicantWS(Adlogic_Job_Board::getSoapConnection());
				
				if(isset($_GET['adlogic_job_id'])) {
					$oJobApplication->set('JobId', $jobId);
				} else {
					$oJobApplication->set('JobAdId', $jobId);
				}
				self::$jobId = $jobId;
				
				if(isset($_GET['adlogic_job_id'])) {
					self::$jobCriteria = $oJobApplication->getCriteriaForJobId();
				} else {
					
					self::$jobCriteria = $oJobApplication->getCriteria();
				}
				
				
				
				self::$jobDetails = self::$jobCriteria->jobDetails;
				
				
				// Get ad details.
				self::$oJobDetails = $oJobDetails = new JobDetails(Adlogic_Job_Board::getSoapConnection(), $jobId);
				if(isset($_GET['adlogic_job_id'])) {

				} else {
					self::$JobXML = $oJobDetails->getAdDetails(false, Adlogic_Job_Board::getSubSource(), Adlogic_Job_Board::getPlatform());
					self::$jobXML = self::$JobXML->JobPosting;
				}
			}
		}
		return true;
	}

	function renderQuestion($oJobCriterion, $oJobCriteria, $isJob) {

		// Find all the questions that have a dependent value.
		if($oJobCriterion->attributes()->dependentValueId != 0) {
			$class = ' dependent_value dependent_value_'. $oJobCriterion->attributes()->dependentValueId;
			$depenValue = $oJobCriterion->attributes()->dependentValueId;
			$display = 'none';
		} else {
			$depenValue = '';
			$display = 'block';
		}
		if($oJobCriterion->attributes()->mappedField != '') {
			$addClass = $oJobCriterion->attributes()->mappedField;
			
		} else {
			$addClass = '';
		}
		print '<div class="ajb_application_criteria '. $class . '" mappedfield="'. $addClass . '" dependentValue="'. $depenValue . '" id="criteria_id_' . $oJobCriterion->attributes()->id . '" style="display: ' . $display . ';">';
		/* print '<pre>';
		var_dump($oJobCriterion);
		print '</pre>'; */
		// Field settings based on type
		switch ($oJobCriterion->attributes()->type) {
			case 'D': // Desirable
				if($oJobCriterion->attributes()->mappedField != '') {
					if($oJobCriterion->attributes()->mappedField == 'Name') {
						$field_name_prefix = 'SystemFirstName';
					}
					if($oJobCriterion->attributes()->mappedField == 'Surname') {
						$field_name_prefix = 'SystemLastName';
					}
					if($oJobCriterion->attributes()->mappedField == 'Email') {
						$field_name_prefix = 'SystemEmailAddress';
					}
				} else {
					$field_name_prefix = 'question_id';
				}
					$css_class = 'desirable';
					$is_mandatory = false;
					$id_prefix = 'ajb_question_id';
				break;
			case 'CF': // Desirable
				$css_class = 'custom_field';
				$is_mandatory = ($oJobCriterion->answer->attributes()->mandatory == 'true') ? true : false;
				$id_prefix = 'ajb_custom_field_id';
				$field_name_prefix = 'custom_field_id';
				break;
			case 'E': // Essential
			default:
				if($oJobCriterion->attributes()->mappedField != '') {
					if($oJobCriterion->attributes()->mappedField == 'Name') {
						$field_name_prefix = 'SystemFirstName';
					}
					if($oJobCriterion->attributes()->mappedField == 'Surname') {
						$field_name_prefix = 'SystemLastName';
					}
					if($oJobCriterion->attributes()->mappedField == 'Email') {
						$field_name_prefix = 'SystemEmailAddress';
					}
				} else {
					$field_name_prefix = 'question_id';
				}
					$css_class = 'essential';
					$is_mandatory = ($oJobCriteria->attributes()->essentialCriteriaMandatory == 'true') ? true : false;
					$id_prefix = 'ajb_question_id';
				break;
		}

		switch ($oJobCriterion->answer->attributes()->type) {
			
			case 'select':
				?>
				<script type="text/javascript">
					var optionArray_<?php print $oJobCriterion->attributes()->id; ?> = new Array();
					var idx=0;
				</script>
				<?php
				print '<div class="ajb-left"><label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label></div>';
				print '<div class="ajb-right"><select id="' . $oJobCriterion->attributes()->id . '" class="ajb_field_select' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '">';
				print '<option value="">- Select -</option>';
				foreach ($oJobCriterion->answer->answerOption as $answer) {
					print '<option value="' . $answer->attributes()->value . '" ' . ($answer->attributes()->default ? 'selected="selected"': '') . '>' . $answer . '</option>';
				}
				print '</select></div>';
				// We want to keep the dropdown intact
				?>
				<script type="text/javascript">
				<?php
				foreach ($oJobCriterion->answer->answerOption as $answer) {
						?>
						optionArray_<?php print $oJobCriterion->attributes()->id; ?>[idx]='<?php print $answer->attributes()->value; ?>';
						idx++;
						<?php
				}
				?>
				</script>
					
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var renderQuestion = new question(<?php print $oJobCriterion->attributes()->id; ?>, 
						'select', optionArray_<?php print $oJobCriterion->attributes()->id; ?>, 
						<?php print (($oJobCriterion->attributes()->dependentValueId === 0) ? '' : "'". $oJobCriterion->attributes()->dependentValueId) . "'"; ?>); 
						questionsMap[<?php print $oJobCriterion->attributes()->id; ?>] = renderQuestion;
					});
				</script>
				<?php
				break;
				
			case 'multiple':
                if ($oJobCriterion->answer->attributes()->multiple == 'true') {
                	print '<div class="ajb-left"><label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label></div>';
                    print '<div class="ajb-right"><select id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_select_multiple' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '" multiple>';
                    
                    foreach ($oJobCriterion->answer->answerOption as $answer) {
                    	print '<option value="' . $answer->attributes()->value . '" ' . ($answer->attributes()->default ? 'selected="selected"': '') . '>' . $answer . '</option>';
                    }
                    print '</select></div>';
                }
                ?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var renderQuestion = new question(<?php print $oJobCriterion->attributes()->id; ?>, 
						'select', null,
						<?php print (($oJobCriterion->attributes()->dependentValueId === 0) ? '' : "'". $oJobCriterion->attributes()->dependentValueId) . "'"; ?>); 
						questionsMap[<?php print $oJobCriterion->attributes()->id; ?>] = renderQuestion;
					});
				</script>
				<?php
                break;
			case 'text':
				print '<div class="ajb-left"><label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label></div>';
				print '<div class="ajb-right"><input id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_text' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '" type="text" value="" maxlength="' . (int) $oJobCriterion->answer->attributes()->length . '" /></div>';
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var renderQuestion = new question(<?php print $oJobCriterion->attributes()->id; ?>, 
						'text', null,
						<?php print (($oJobCriterion->attributes()->dependentValueId === 0) ? '' : "'". $oJobCriterion->attributes()->dependentValueId) . "'"; ?>); 
						questionsMap[<?php print $oJobCriterion->attributes()->id; ?>] = renderQuestion;
					});
				</script>
				<?php
				break;
			case 'text area':
				print '<label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label>';
				print '<textarea id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_textarea ajb_field_large_textarea max_length_' . (int) $oJobCriterion->answer->attributes()->length . '' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '"></textarea>';
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var renderQuestion = new question(<?php print $oJobCriterion->attributes()->id; ?>, 
						'textarea', null,
						<?php print (($oJobCriterion->attributes()->dependentValueId === 0) ? '' : "'". $oJobCriterion->attributes()->dependentValueId) . "'"; ?>); 
						questionsMap[<?php print $oJobCriterion->attributes()->id; ?>] = renderQuestion;
					});
				</script>
				<?php
				break;
			case 'numeric':
				print '<div class="ajb-left"><label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label></div>';
				if ((int) $oJobCriterion->answer->attributes()->length <= 50) {
					print '<div class="ajb-right"><input id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_numeric' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '" type="text" value="" maxlength="' . (int) $oJobCriterion->answer->attributes()->length . '" /></div>';
				}
				break;
			// Label only fields
			case 'label':
				//print '<label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label>';
				print '<p for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_label_field' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . '</p>';
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var renderQuestion = new question(<?php print $oJobCriterion->attributes()->id; ?>, 
						'label', null,
						<?php print (($oJobCriterion->attributes()->dependentValueId === 0) ? '' : "'". $oJobCriterion->attributes()->dependentValueId) . "'"; ?>); 
						questionsMap[<?php print $oJobCriterion->attributes()->id; ?>] = renderQuestion;
					});
				</script>
				<?php
				
				break;
			
			// Date field
			case 'date/month-year only':
				print '<div class="ajb-left"><label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label></div>';
				print '<div class="ajb-right"><input id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_date_month_year' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '" type="text" value="" autocomplete="off"  placeholder="Date Format - mm/yyyy" readonly="readonly" /></div>';
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var renderQuestion = new question(<?php print $oJobCriterion->attributes()->id; ?>, 
						'date', null,
						<?php print (($oJobCriterion->attributes()->dependentValueId === 0) ? '' : "'". $oJobCriterion->attributes()->dependentValueId) . "'"; ?>); 
						questionsMap[<?php print $oJobCriterion->attributes()->id; ?>] = renderQuestion;
					});
				</script>
				<?php
				break;	
			case 'date':
				print '<div class="ajb-left"><label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label></div>';
				print '<div class="ajb-right"><input id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_date' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '" type="text" value="" autocomplete="off"  placeholder="Date Format - dd/mm/yyyy" readonly="readonly" /></div>';
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var renderQuestion = new question(<?php print $oJobCriterion->attributes()->id; ?>, 
						'date', null,
						<?php print (($oJobCriterion->attributes()->dependentValueId === 0) ? '' : "'". $oJobCriterion->attributes()->dependentValueId) . "'"; ?>); 
						questionsMap[<?php print $oJobCriterion->attributes()->id; ?>] = renderQuestion;
					});
				</script>
				<?php
				break;				
			case 'file':
				if (!Adlogic_Job_Board::is_mobile_browser()) {
					print '<label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label>';
					if (isset($oJobCriterion->answer->attributes()->multiple) && ((string) $oJobCriterion->answer->attributes()->multiple == 'true')) {
						print '<div class="ajb_field_file_multiple">';
						print '<p><input id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_file' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" type="file" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '[]"></p><span class="ajb_field_file_multiple_controls"></span>';
						print '<a href="javascript:void(0);" class="add_file">Add More Files</a></div>';
					} else {
						print '<p><input id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_file' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" type="file" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '"></p>';
					}
				}
				
				break;
			case 'checkbox':
				if (!empty($oJobCriterion->question)) {
					print '<label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '" class="ajb_field_label' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory == true) ? ' mandatory' : '') . '">' . $oJobCriterion->question . (($is_mandatory === true) ? '<span class="mandatory"></span>' : '') . '</label>';
				}

				$i = 0;
				foreach ($oJobCriterion->answer->answerOption as $answer) {
					print '<label for="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '[' . $i . ']"><input id="' . $id_prefix . '_' . $oJobCriterion->attributes()->id . '[' . $i . ']" class="ajb_field_checkbox' . (!empty($css_class) ? ' ' . $css_class : '') . '' . (($is_mandatory === true) ? ' mandatory' : '') . '" name="' . $field_name_prefix . '_' . $oJobCriterion->attributes()->id . '[]" type="checkbox" value="' . $answer->attributes()->value . '" />' . $answer . '</label>';
					$i++;
				}
				break;
		}
		print '</div>';
	}

	function indeedQuestionsArray($oJobCriterion, $oJobCriteria) {
		$questionArray = array();

		// Field settings based on type
		switch ($oJobCriterion->attributes()->type) {
			case 'D': // Desirable
				$is_mandatory = false;
				$id_prefix = 'D';
				$field_name_prefix = 'question_id';
				break;
			case 'CF': // Desirable
				$is_mandatory = ($oJobCriterion->answer->attributes()->mandatory == 'true') ? true : false;
				$id_prefix = 'CF';
				$field_name_prefix = 'custom_field_id';
				break;
			case 'E': // Essential
			default:
				$is_mandatory = ($oJobCriteria->attributes()->essentialCriteriaMandatory == 'true') ? true : false;
				$id_prefix = 'E';
				$field_name_prefix = 'question_id';
				break;
		}

		$questionArray['id'] = $field_name_prefix . '[' . $id_prefix . ']['. $oJobCriterion->attributes()->id . ']';
		$questionArray['question'] = (string) $oJobCriterion->question;
		$questionArray['required'] = $is_mandatory ? 'true':'false';

		switch ($oJobCriterion->answer->attributes()->type) {
			case 'select':
				$questionArray['type'] = 'select';
				$questionArray['options'] = array();
				foreach ($oJobCriterion->answer->answerOption as $answer) {
					$questionArray['options'][] = array('label' => (string) $answer, 'value' => (string) $answer->attributes()->value);
				}
				break;
			case 'text':
				if ((int) $oJobCriterion->answer->attributes()->length <= 50) {
					$questionArray['type'] = 'text';
				} else {
					$questionArray['type'] = 'textarea';
				}
				break;
			case 'file':
				// Not Supported by Indeed API - we display a textarea instead
				$questionArray['type'] = 'textarea';
				break;
			case 'checkbox':
				// Indeed API doesn't support checkbox, so we replace checkboxes with a Yes/No select dropdown
				if (empty($oJobCriterion->question)) {
					$questionArray['question'] = (string) $oJobCriterion->answer->answerOption;
				}
				$questionArray['type'] = 'select';
				$questionArray['options'] = array();
				$questionArray['options'][] = array('label' => 'Yes', 'value' => 'true');
				$questionArray['options'][] = array('label' => 'No', 'value' => 'false');
				break;
		}

		return $questionArray;
	}
	
	function add_googledrive_javascript() {
		$apiSettings = get_option('adlogic_api_settings');
		?>
		<script type="text/javascript">
			// Google Drive Selector API
			// The Browser API key obtained from the Google Developers Console.
			var developerKey = '<?php print $apiSettings['adlogic_google_drive_api_key']; ?>';
			
			// The Client ID obtained from the Google Developers Console. Replace with your own Client ID.
			var clientId = "<?php print $apiSettings['adlogic_google_drive_client_id']; ?>";
			
			// Scope to use to access user's photos.
			var scope = ['https://www.googleapis.com/auth/drive'];
			
			var pickerApiLoaded = false;
			var oauthToken;
			
			// Use the API Loader script to load google.picker and gapi.auth.
			function onApiLoad(fileType) {
				window.fileType = fileType;
				gapi.load('auth', {'callback': onAuthApiLoad});
				gapi.load('picker', {'callback': onPickerApiLoad});
			}
			
			function onAuthApiLoad() {
				window.gapi.auth.authorize(
				{
					'client_id': clientId,
					'scope': scope,
					'immediate': false
				},
				handleAuthResult);
				window.gapi.client.load('drive', 'v2', handleAuthResult);
			}
			
			function onPickerApiLoad() {
				pickerApiLoaded = true;
				createPicker();
			}
			
			function handleAuthResult(authResult) {
				if (authResult && !authResult.error) {
					oauthToken = authResult.access_token;
					createPicker(fileType);
				}
			}
			
			// Create and render a Picker object for picking user Photos.
			function createPicker() {
				if (pickerApiLoaded && oauthToken) {
					var picker = new google.picker.PickerBuilder().
						addView(google.picker.ViewId.DOCUMENTS).
						setOAuthToken(oauthToken).
						setDeveloperKey(developerKey).
						setCallback(pickerCallback).
						build();
						picker.setVisible(true);
				}
			}
			
			// A simple callback implementation.
			function pickerCallback(data) {
				console.log(fileType);
				var url = 'nothing';
				if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {

					$("#ajb-submitApplication").attr("disabled", "disabled");
					$("#ajb-resume-field .ajb-upload-resume").attr("disabled", "disabled");
					$("#ajb-googledrive-api-load-resume").html('<span class="ajb-googledrive-icon loading"></span><span class="ajb-googledrive-text">Uploading...</span>');
					
					/*$.ajax({
						type: "POST",
						url: adlogicJobSearch.ajaxurl + '?action=getGoogleDriveFile',
						data: googleDriveSubmitObject,
						dataType: "jsonp",
						complete: function(data) {
							var obj = JSON.parse(data.responseText);
							if(obj.fileName.length > 24) {
								var safeTitle = obj.fileName.slice(0, 20);
								safeTitle = safeTitle + "...";
							} else {
								var safeTitle = obj.fileName;
							}
							
							$("#ajb-googledrive-api-load-resume").html('<span class="ajb-googledrive-icon"></span><span class="ajb-googledrive-text">' + safeTitle + '</span>');
							
							$(".ajb-googledrive-icon").addClass("success");
							//.ajb-googledrive-icon.success
							$(".ajb-googledrive-icon").removeClass("loading");
							
							
							$("#ajb-googledrive-api-load-coverletter").removeAttr("disabled");
							$("#ajb-googledrive-api-load-resume").removeAttr("disabled");
							// The request is complete and the data is available to push.
							
							googledrive_selected_file_name = obj.fileName;
							googledrive_selected_file_data = obj.fileData;
							
							$('#<?php print $uniqueId; ?>').adlogicJobApplication('thirdPartyFileInfo', { 
								googledrive_selected_file_name: googledrive_selected_file_name, 
								googledrive_selected_file_data: googledrive_selected_file_data
							});
							$("#ajb-submitApplication").removeAttr("disabled");
						},
						error: function(error) {
							//console.log(error);
						}
					});*/

					printFile(data.docs[0].id);
					//console.log(data);
					//downloadFile(data.docs[0], fileDone);
					//access_token : gapi.auth.getToken().access_token,
					//fileId : data.docs[0].id,
				}
			}
			function fileDone(fileContent, fileName, mimeType) {
				// We're done.
			}
			function printFile(fileId) {
			  var request = gapi.client.drive.files.get({
			    'fileId': fileId
			  });
			  request.execute(function(resp) {
				  	downloadFile(resp, fileDone, resp.title, resp.mimeType);
			  });
			}
			
			/**
			 * Download a file's content.
			 *
			 * @param {File} file Drive File instance.
			 * @param {Function} callback Function to call when the request is complete.
			 */
			function downloadFile(file, callback, fileName, mimeType) {
				if (file.downloadUrl) {
					var googleDriveSubmitObject = {
						access_token: gapi.auth.getToken().access_token,
						downloadUrl: file.downloadUrl,
						fileName: fileName,
						fileExtension: file.fileExtension
					};
					$.ajax({
						type: "POST",
						url: adlogicJobSearch.ajaxurl + '?action=convertFile',
						data: googleDriveSubmitObject,
						dataType: "jsonp",
						complete: function(data) {
							var obj = JSON.parse(data.responseText);
							console.log(obj);
							if(obj.fileName.length > 24) {
								var safeTitle = obj.fileName.slice(0, 20);
								safeTitle = safeTitle + "...";
							} else {
								var safeTitle = obj.fileName;
							}
							
							$("#ajb-googledrive-api-load-resume").html('<span class="ajb-googledrive-icon"></span><span class="ajb-googledrive-text">' + safeTitle + '</span>');
							
							$(".ajb-googledrive-icon").addClass("success");
							//.ajb-googledrive-icon.success
							$(".ajb-googledrive-icon").removeClass("loading");
							
							googledrive_selected_file_name = obj.fileName;
							googledrive_selected_file_data = obj.fileData;
							
							$('#<?php print $uniqueId; ?>').adlogicJobApplication('thirdPartyFileInfo', { 
								googledrive_selected_file_name: googledrive_selected_file_name, 
								googledrive_selected_file_data: googledrive_selected_file_data
							});
							
							$("#ajb-submitApplication").removeAttr("disabled");
						},
						error: function(error) {
							//console.log(error);
						}
					});	  
				  
				/*
			    var accessToken = gapi.auth.getToken().access_token;
			    var xhr = new XMLHttpRequest();
			    xhr.open('GET', file.downloadUrl);
			    xhr.setRequestHeader('Authorization', 'Bearer ' + accessToken);
			    xhr.onload = function() {
			      callback(xhr.responseText, fileName, mimeType);
			    };
			    xhr.onerror = function() {
			      callback(null);
			    };
			    xhr.send();*/
			  } else {
			    callback(null);
			  }
			}
		</script>
		<!-- The Google API Loader script. -->
		<script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
		<script type="text/javascript" src="https://apis.google.com/js/client.js"></script> 
		<?php
	}
	
	function add_onedrive_javascript() {
		$apiSettings = get_option('adlogic_api_settings');
		?>
		<script type="text/javascript" src="https://js.live.net/v5.0/wl.js"></script>
		<script type="text/javascript">
		function ShowOneDrivePicker() {
		  	WL.init({ 
			  	client_id: '<?php print $apiSettings['adlogic_onedrive_api_key']; ?>', 
			  	redirect_uri: '<?php print AJB_PLUGIN_URL; ?>lib/classes/OneDrive/callback.html'
			});

			WL.login({ "scope": "wl.skydrive wl.signin" }).then(
			    function(response) {
			        openFromSkyDrive();
			    },
			    function(response) {
			        log("Failed to authenticate.");
			    }
			);
		}
			
		function openFromSkyDrive() {
		    WL.fileDialog({
		        mode: 'open',
		        select: 'single'
		    }).then(
		        function(response) {
		            var files = response.data.files;
		            console.log(files);
		            // download from files.source
		            $("#ajb-onedrive-container #picker").html('<span class="dropin-btn-status loading"></span> Uploading...');
					$("#ajb-submitApplication").attr("disabled", "disabled");
					// Try to get the file content
					var dropboxSubmitObject = {
						fileLink : files[0].source,
						fileName : files[0].name
					};
					$.ajax({
						type: "POST",
						url: adlogicJobSearch.ajaxurl + '?action=getFileContent',
						data: dropboxSubmitObject,
						//url: files[0].link,
						dataType: "jsonp",
						complete: function(data) {
							$("#ajb-onedrive-container #picker").html('<span class="dropin-btn-status success"></span> ' + files[0].name);
							onedrive_selected_file_name = files[0].name;
							onedrive_selected_file_data = data.responseText;
							
							$('#<?php print $uniqueId; ?>').adlogicJobApplication('thirdPartyFileInfo', { 
								onedrive_selected_file_name: onedrive_selected_file_name, 
								onedrive_selected_file_data: onedrive_selected_file_data
							});
							$("#ajb-submitApplication").removeAttr("disabled");
						},
						error: function(error) {
							//console.log(error);
						}
					});
		        },
		        function(errorResponse) {
		            log("WL.fileDialog errorResponse = " + JSON.stringify(errorResponse));
		        }
		    );
		}
			                    
			function log(message) {
			    var child = document.createTextNode(message);
			    var parent = document.getElementById('JsOutputDiv') || document.body;
			    parent.appendChild(child);
			    parent.appendChild(document.createElement("br"));
			}
		</script>
		
		
		
		
		<?php
	}
	
	function add_dropbox_javascript() {
		$apiSettings = get_option('adlogic_api_settings');
		?>
		<script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?php print $apiSettings['adlogic_dropbox_api_key']; ?>"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				options = {
					success: function(files) {
						//var $ = jQuery;
						$(".dropbox-dropin-btn").html('<span class="dropin-btn-status loading"></span> Uploading...');
						$("#ajb-submitApplication").attr("disabled", "disabled");
						//alert(files[0].link);
						// Try to get the file content
						var dropboxSubmitObject = {
							fileLink : files[0].link,
							fileName : files[0].name
						};
						$.ajax({
							type: "POST",
							url: adlogicJobSearch.ajaxurl + '?action=getFileContent',
							data: dropboxSubmitObject,
							//url: files[0].link,
							dataType: "jsonp",
							complete: function(data) {
								$(".dropbox-dropin-btn").html('<span class="dropin-btn-status"></span> ' + files[0].name);
								dropbox_selected_file_name = files[0].name;
								dropbox_selected_file_data = data.responseText;
								
								$('#<?php print $uniqueId; ?>').adlogicJobApplication('dropboxFileInfo', { dropbox_selected_file_name: dropbox_selected_file_name, dropbox_selected_file_data: dropbox_selected_file_data });
								$("#ajb-submitApplication").removeAttr("disabled");
							},
							error: function(error) {
								//console.log(error);
							}
						});
					},
					cancel: function() {
						
					},
					linkType: "direct",
					multiselect: false,
					extensions: ['.doc', '.docx', '.txt'],
				};
				var button = Dropbox.createChooseButton(options);
				document.getElementById("ajb-dropbox-container").appendChild(button);
			});
		</script>
		<?php
	}
	function add_resume_field_js_css() {
		?>
		<style>
			.ajb-attach-resume-social span.mandatory {
				color: red;
			}
			.ajb-upload-resume {
			    width: 258px;
			    cursor: pointer;
			    display: inline-block;
			    height: 38px;
			    line-height: 36px;
			    text-align: center;
			    padding: 5px 10px;
			    font-size: 14px;
			    border: 1px solid #909090;
			    border-radius: 5px;
			    background-image: -webkit-linear-gradient(top,#f2f2f2 0%,#e0e0e0 100%);   background-image: linear-gradient(to bottom,#f2f2f2 0%,#e0e0e0 100%);   background-repeat: repeat-x;   filter: progid:DXImageTransform.Microsoft.gr;
			    float: left;
			}
			
			.ajb-resume-or {
			    display: inline-block;
			    background: #F5F4F4;
			    color: #C8C8C8;
			    border-radius: 50px;
			    margin: 13px 13px;
			    width: 25px;
			    text-align: center;
			    float: left;
			}
			#ajb-dropbox-container {
				display: inline-block;
			}
			.ajb-upload-resume.ajb-upload-successful {
			    background: rgb(217, 255, 217);
			}
			
			
			fieldset#ajb-applicant-documents {
			    position: relative;
			}
			div[id^="ajb-googledrive-api-load-"] {
			    border: 1px solid #e2e2e2;
			    font-size: 12px;
			    font-weight: bold;
			    color: #666;
			    width: 180px;
			    height: 20px;
			    line-height: 20px;
			    padding: 1px 10px 1px 5px;
			    font-family: "Lucida Grande", "Segoe UI", "Tahoma", "Helvetica Neue", "Helvetica", sans-serif;   
			    font-size: 11px;   
			    font-weight: 600;   
			    cursor: pointer;
			    color: #636363;
			    background: #fcfcfc;   
			    background: -moz-linear-gradient(top, #fcfcfc 0%, #f5f5f5 100%);   
			    background: -webkit-linear-gradient(top, #fcfcfc 0%, #f5f5f5 100%);   
			    background: linear-gradient(to bottom, #fcfcfc 0%, #f5f5f5 100%);   
			    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fcfcfc', endColorstr='#f5f5f5',GradientType=0);
			}
			
			.ajb-upload-resume-options {
			    display: inline-block;
			    height: 60px;
			    /* float: right; */
			    margin-top: -5px;
			}
			
			div#ajb-resume-field {
			}
			
			div#ajb-googledrive-container {
			    margin-top: 10px;
			}
			
			.dropbox-dropin-btn {
			    width: 185px !important;
			}
			.ajb-googledrive-icon {
				background: url('<?php print AJB_PLUGIN_URL; ?>css/images/googledrive.png') no-repeat;
				width: 15px;
				height: 14px;
				display: inline-block;
				float: left;
				margin-top: 2px;
				margin-right: 10px;
			}
			.ajb-googledrive-text {
				display: inline-block;
			}
			.ajb-googledrive-icon.loading,
			#picker span.loading {
				background: url('<?php print AJB_PLUGIN_URL; ?>css/images/ajax-loading-mini.gif');
				height: 16px;
				width: 15px;
			}
			#picker span.loading {
				display: block;
				float: left;
				margin-right: 10px;
			}
			.ajb-googledrive-icon.success,
			#picker span.success {
				background: transparent url('https://www.dropbox.com/static/images/widgets/dbx-saver-status.png') no-repeat;
				width: 15px;
				height: 14px;
				background-position: -15px 0px;
			}
			#picker span.success {
				display: block;
				float: left;
				margin-right: 10px;
				background-position: -15px 1px !important;
			}
			div#picker {
			    border: 1px solid #e2e2e2;        
			    font-size: 12px;        
			    font-weight: bold;        
			    color: #666;        
			    width: 180px;        
			    margin-top: 10px;
			    height: 20px;        
			    line-height: 20px;        
			    padding: 1px 10px 1px 5px;        
			    font-family: "Lucida Grande", "Segoe UI", "Tahoma", "Helvetica Neue", "Helvetica", sans-serif;           
			    font-size: 11px;           
			    font-weight: 600;           
			    cursor: pointer;        
			    color: #636363;        
			    background: #fcfcfc;           
			    background: -moz-linear-gradient(top, #fcfcfc 0%, #f5f5f5 100%);           
			    background: -webkit-linear-gradient(top, #fcfcfc 0%, #f5f5f5 100%);           
			    background: linear-gradient(to bottom, #fcfcfc 0%, #f5f5f5 100%);           
			    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fcfcfc', endColorstr='#f5f5f5',GradientType=0);
			}
			
			#ajb-onedrive-container #picker img {
			    background: none !important;
			    border: none !important;
			    box-shadow: none !important;
			    padding-top: 3px;
			    float: left;
			    margin-right: 10px;
			}
			
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$(".ajb-upload-resume").click(function(){
						if($(".ajb-upload-resume").attr('disabled')) {
							// Do nothing, we assume a 3rd party has been selected (Google Drive, Dropbox, etc.)
						} else {
							$("#ajb-resume").click();
						}
				});
				$('#ajb-resume').change(function(e){
					var realVal = $("#ajb-resume").val().replace(/C:\\fakepath\\/i, '');
					if(realVal != '') {
						if(realVal.length > 30) {
							var safeTitle = realVal.slice(0, 24);
							safeTitle = safeTitle + "...";
						} else {
							var safeTitle = realVal;
						}
						$(".ajb-upload-resume").text(safeTitle);
						$(".ajb-upload-resume").addClass("ajb-upload-successful");
					} else {
						$(".ajb-upload-resume").text('Upload your resume');
						$(".ajb-upload-resume").removeClass("ajb-upload-successful");
					}
				    
				});
			});
		</script>
		<?php
	}
}
?>