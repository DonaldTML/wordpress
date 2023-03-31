<?php

//add_menu_page( 'Adlogic Job Board Settings', 'Adlogic Job Board', 'manage_options', 'adlogic-job-board');
class Adlogic_Settings {
	static function init() {
		add_menu_page( 'MyRecruitment+ Job Board Settings', 'Job Board', 'manage_options', 'adlogic-job-board', array('Adlogic_Settings', 'plugin_options'), AJB_PLUGIN_URL . '/css/images/adlogic-icon_16x16.png');
		add_action( 'admin_head', array( 'Adlogic_Settings', 'admin_css' ) );

		// Check for minimum required settings to have job board work, if not met, then display first time setup menu
		$apiSettings = get_option('adlogic_api_settings');

		if ($apiSettings == false) {
			$Adlogic_First_Setup_Settings = new Adlogic_First_Setup_Settings();
			$Adlogic_First_Setup_Settings->init();
		} else {
			// Initialise API Settings
			$Adlogic_API_Settings = new Adlogic_API_Settings();
			$Adlogic_API_Settings->init();
			// Initialise Search Settings
			$Adlogic_Search_Settings = new Adlogic_Search_Settings();
			$Adlogic_Search_Settings->init();
			// Initialise RSS Settings
			$Adlogic_RSS_Settings = new Adlogic_RSS_Settings();
			$Adlogic_RSS_Settings->init();
			// Initialise Cache Settings
			$Adlogic_Cache_Settings = new Adlogic_Cache_Settings();
			$Adlogic_Cache_Settings->init();
			// Initialise Mobile Settings
			// $Adlogic_Mobile_Settings = new Adlogic_Mobile_Settings();
			// $Adlogic_Mobile_Settings->init();

			// Add a submenu page that's hidden from everyone's view
			add_submenu_page(null, 'MyRecruitment+ Job Board > Reset Settings', 'Reset Settings', 'manage_options', 'adlogic-reset-settings', array(__CLASS__, 'reset_options'));
		}
	}

	static function admin_init() {
		// Check for minimum required settings to have job board work, if not met, then display first time setup menu
		$apiSettings = get_option('adlogic_api_settings');

		if ($apiSettings == false) {
			$Adlogic_First_Setup_Settings = new Adlogic_First_Setup_Settings();
			$Adlogic_First_Setup_Settings->admin_init();
		} else {
			// Initialise API Admin Settings
			$Adlogic_API_Settings = new Adlogic_API_Settings();
			$Adlogic_API_Settings->admin_init();
			// Initialise Search Admin Settings
			$Adlogic_Search_Settings = new Adlogic_Search_Settings();
			$Adlogic_Search_Settings->admin_init();
			// Initialise Search Admin Settings
			$Adlogic_RSS_Settings = new Adlogic_RSS_Settings();
			$Adlogic_RSS_Settings->admin_init();
			// Initialise Cache Admin Settings
			$Adlogic_Cache_Settings = new Adlogic_Cache_Settings();
			$Adlogic_Cache_Settings->admin_init();
			// Initialise Mobile Admin Settings
			// $Adlogic_Mobile_Settings = new Adlogic_Mobile_Settings();
			// $Adlogic_Mobile_Settings->admin_init();

		}
	}

	static function admin_css() {
		wp_register_style( 'adlogic-admin-css', plugins_url('css/admin_settings.css', AJB_PLUGIN_FILE) );
		wp_enqueue_style( 'adlogic-admin-css' );
	}

	static function reset_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		// Delete all the settings!
		delete_option('adlogic_api_settings');
		delete_option('adlogic_rss_settings');
		delete_option('adlogic_search_settings');
		delete_option('adlogic_mobile_settings');
		delete_option('adlogic_cache_settings');
		delete_option('adlogic_first_setup_settings');
		?>
		<div class="wrap adlogic_reset_settings">
			<?php screen_icon(); ?>
			<h2>MyRecruitment+ Job Board Settings</h2>
			<p>Your Job Board settings have been reset!</p>
			<?php submit_button('Setup Job Board', 'primary', 'button', true, array('onclick' => 'window.location.href=\'' . get_admin_url(null, 'admin.php?page=adlogic-job-board', 'admin') . '\'')); ?>
		</div>
		<?php
	}

	static function plugin_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}

	static function setting_page_select($args) {
		$options = get_option($args['setting_section']);
		$cls = isset($args['cls']) ? $args['cls'] : '';
		$legend = isset($args['legend']) ? '<br /><span style="color:#666666;margin-left:2px;">' . $args['legend'] . '</span>' : '';
		echo '<div class="'.$cls.'">';
		wp_dropdown_pages(array('selected' => (isset($options[$args['field_id']]) ? $options[$args['field_id']] : (isset($default_value) ? $default_value : 0)), 'name' => $args['setting_section'].'[' . $args['field_id'] . ']', 'show_option_none' => '- Please Select -'));
		echo $legend;
		echo '</div>';
	}

	static function setting_hidden_field($args) {
		$options = get_option($args['setting_section']);
		$css = isset($args['css']) ? $args['css'] : '';
		$legend = isset($args['legend']) ? '<br /><span style="color:#666666;margin-left:2px;">' . $args['legend'] . '</span>' : '';
		$default_value = isset($args['default']) ? $args['default'] : '';

		echo '<input id="' . $args['field_id'] . '" name="'.$args['setting_section'].'[' . $args['field_id'] . ']" style="' . $css . '" type="hidden" value="' . (isset($options[$args['field_id']]) ? $options[$args['field_id']] : (isset($default_value) ? $default_value : '')). '" />';
		echo $legend;
	}

	static function setting_text_field($args) {
		$options = get_option($args['setting_section']);
		$css = isset($args['css']) ? $args['css'] : '';
		$legend = isset($args['legend']) ? '<br /><span style="color:#666666;margin-left:2px;">' . $args['legend'] . '</span>' : '';
		$default_value = isset($args['default']) ? $args['default'] : '';
		$disabled = (isset($args['disabled'])&&$args['disabled']==true ? "disabled" : "");
		if($args['field_id'] == 'adlogic_ad_fresh_period') {
			?>
			<script>
				jQuery(document).ready(function($){
					var elem = $("#<?php print $args['field_id']; ?>");
					var validElem = $(".ajb-num-validation-div");
					$(elem).change(function() {
						if(!$.isNumeric(elem.val())) {
							$(validElem).text("Value must be numeric.");
							$(validElem).show();
							$(elem).addClass("ajb-error");
							$("#submit").prop("disabled", true);
							//elem.val(2);
						} else {
							if(elem.val() < 0 ) {
								$(validElem).text("Value must be greater than 0.");
								$(validElem).show();
								$(elem).addClass("ajb-error");
								$("#submit").prop("disabled", true);
								//elem.val(2);
							} else {
								$(validElem).text("");
								$(validElem).hide();
								$(elem).removeClass("ajb-error");
								$("#submit").prop("disabled", false);
							}
						}
					});
				});
			</script>
			<?php
		} else if($args['field_id'] == 'adlogic_rss_advertisers') {
			?>
			<script>
				jQuery(document).ready(function($){
					var elem = $("#<?php print $args['field_id']; ?>");
					var validElem = $("#<?php print $args['field_id']; ?>_validation_div");
					var regex = /^[0-9,]+$/;
					$(elem).change(function() {
						// Remove spaces
						var val = elem.val();
						val = val.replace(/\s/g, '');
						elem.val(val);
						if(elem.val()=='') {
							elem.val("<?php print $default_value; ?>");
							$(validElem).text("");
							$(validElem).hide();
							$(elem).removeClass("ajb-error");
							$("#submit").prop("disabled", false);
						} else {
							if(!regex.test(elem.val())) {
								$(validElem).text("Invalid character, only numbers and commas are accepted.");
								$(validElem).show();
								$(elem).addClass("ajb-error");
								$("#submit").prop("disabled", true);
							} else {
								$(validElem).text("");
								$(validElem).hide();
								$(elem).removeClass("ajb-error");
								$("#submit").prop("disabled", false);
							}
						}
					});
				});
			</script>
			<?php
		}
		echo '<input '.$disabled. ' id="' . $args['field_id'] . '" name="'.$args['setting_section'].'[' . $args['field_id'] . ']" style="' . $css . '" type="text" value="' . (isset($options[$args['field_id']]) ? $options[$args['field_id']] : (isset($default_value) ? $default_value : '')). '" />';
		if($args['field_id'] == 'adlogic_ad_fresh_period') {
			?>
			<div class="ajb-num-validation-div" style="display:none;margin-top:1px;float:left;line-height:25px;padding:0 10px;border:1px solid #fd6464;border-left:0;background: #f5a6a6;"></div>
			<div style="clear:both;"><?php print str_replace("<br />", "", $legend); ?></div>
			<?php
		} else if($args['field_id'] == 'adlogic_rss_advertisers') {
			?>
			<div id="<?php print $args['field_id']; ?>_validation_div" class="ajb-num-validation-div" style="display:none;margin-top:1px;float:left;line-height:25px;padding:0 10px;border:1px solid #fd6464;border-left:0;background: #f5a6a6;"></div>
			<div style="clear:both;"><?php print str_replace("<br />", "", $legend); ?></div>
			<?php
		} else {
			echo $legend;
		}
	}

	static function setting_label_field($args) {
		$options = get_option($args['setting_section']);
		$css = isset($args['css']) ? $args['css'] : '';
		$legend = isset($args['legend']) ? '<span style="color:#666666;margin-left:2px;">' . $args['legend'] . '</span>' : '';
		$default_value = isset($args['default']) ? $args['default'] : '';
		echo $legend;
	}

	static function setting_password_field($args) {
		$options = get_option($args['setting_section']);
		$css = isset($args['css']) ? $args['css'] : '';
		$legend = isset($args['legend']) ? '<br /><span style="color:#666666;margin-left:2px;">' . $args['legend'] . '</span>' : '';
		$default_value = isset($args['default']) ? $args['default'] : '';

		echo '<input id="' . $args['field_id'] . '" name="'.$args['setting_section'].'[' . $args['field_id'] . ']" style="' . $css . '" type="password" value="' . (isset($options[$args['field_id']]) ? $options[$args['field_id']] : (isset($default_value) ? $default_value : '')). '" />';
		echo $legend;
	}

	static function setting_select_field($args) {
		$options = get_option($args['setting_section']);
		$css = isset($args['css']) ? $args['css'] : '';
		$legend = isset($args['legend']) ? '<br /><span style="color:#666666;margin-left:2px;">' . $args['legend'] . '</span>' : '';
		$default_value = isset($args['default']) ? $args['default'] : '';
		$selectValues = isset($args['options']) ? $args['options'] : array();
		echo '<select id="' . $args['field_id'] . '" name="'.$args['setting_section'].'[' . $args['field_id'] . ']" style="' . $css . '">';
		foreach ($selectValues as $selectValue => $selectDescription) {
			if ((isset($options[$args['field_id']]) && $selectValue == $options[$args['field_id']])) {
				echo '<option value="' . $selectValue . '" selected="selected">' . $selectDescription . '</option>';
			} else if ((!isset($options[$args['field_id']])) && ($default_value == $selectValue)) {
				echo '<option value="' . $selectValue . '" selected="selected">' . $selectDescription . '</option>';
			} else {
				echo '<option value="' . $selectValue . '">' . $selectDescription . '</option>';
			}
		}
		echo '</select>';
		echo $legend;
	}

	// validate our options
	static function validate_options($input) {
		return $input;
	}
}

class Adlogic_API_Settings  extends Adlogic_Settings {
	static function init() {
		add_submenu_page('adlogic-job-board', 'MyRecruitment+ Job Board > API Settings', 'API Settings', 'manage_options', 'adlogic-job-board', array('Adlogic_API_Settings', 'plugin_options'));
	}

	static function admin_init() {
		register_setting( 'adlogic_api_settings', 'adlogic_api_settings', array('Adlogic_API_Settings', 'validate_options') );

		// Adlogic API Settings Section
		add_settings_section('adlogic_api_settings_section', 'MyRecruitment+ API Settings', array('Adlogic_API_Settings', 'section_text'), 'adlogic_plugin_api');

		// Recruiter ID
		add_settings_field(
			'adlogic_recruiter_id',
			'Account ID',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_recruiter_id',
				'css' => 'width: 60px;'
			)
		);

		// Advertiser ID
		add_settings_field(
			'adlogic_advertiser_id',
			'Advertiser ID',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_advertiser_id',
				'css' => 'width: 60px;'
			)
		);

		add_settings_field(
			'adlogic_api_use_new_location_API',
			'Use new REST API',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_api_use_new_location_API',
				'css' => 'width: 100px;',
				'default' => 'false',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				),
				'legend' => 'Defaults to True. Makes the plugin call REST API rather then the old SOAP API'
			)
		);
		
		// SOAP Server Address
		add_settings_field(
			'adlogic_soap_server',
			'API Server',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_soap_server',
				'css' => 'width: 400px;',
				'legend' => 'This setting defaults to <em>http://rec.myrecruitmentplus.com/AdlogicJobPortalService?WSDL</em>. Only change if requested by MyRecruitment+.',
				'default' => 'http://rec.myrecruitmentplus.com/AdlogicJobPortalService?WSD'
			)
		);

		// REST Server Address
		add_settings_field(
			'adlogic_rest_server',
			'API Server(rest)',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_rest_server',
				'css' => 'width: 400px;',
				'legend' => 'This setting defaults to <em>https://rec.myrecruitmentplus.com/restapi/</em>. Only change if requested by MyRecruitment+.',
				'default' => 'https://rec.myrecruitmentplus.com/restapi/'
			)
		);
		// REST API Key
		add_settings_field(
			'adlogic_rest_api_key',
			'API Key',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_rest_api_key',
				'css' => 'width: 400px;',
				'legend' => 'Your accounts API key. This can be found within "Account Settings" of the MyRecruitment+ platform',
				'default' => ''
			)
		);

		// Candidate SOAP Server Address
		add_settings_field(
			'adlogic_candidate_soap_server',
			'Candidate API Server',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_candidate_soap_server',
				'css' => 'width: 400px;',
				'legend' => 'This setting defaults to <em>http://rec.myrecruitmentplus.com/CandidatesWS?WSDL</em>. Only change if requested by MyRecruitment+. Required for authentication system.',
				'default' => 'http://rec.myrecruitmentplus.com/CandidatesWS?WSDL'
			)
		);

		// Search results per page
		add_settings_field(
			'adlogic_custom_application_page',
			'<span style="display:none;">Job Application Page</span>',
			array('Adlogic_API_Settings', 'setting_page_select'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'cls' => 'hidden',
				'field_id' => 'adlogic_custom_application_page',
				'legend' => 'Select the Wordpress Page you wish to use to display your Job Application. Leave unset if you wish to use Custom Job Application Url.'
			)
		);

		// Custom Application Page URL
		add_settings_field(
			'adlogic_custom_application_url',
			'Custom Job Application URL',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_custom_application_url',
				'css' => 'width: 400px;',
				'legend' => 'Leave this blank to default to MyRecruitment+ Application Form. Leave blank if unsure.',
				'default' => ''
			)
		);

		add_settings_field(
			'adlogic_api_flouc_fix',
			'FLOUC Fix',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_api_flouc_fix',
				'css' => 'width: 100px;',
				'default' => 'true',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				),
				'legend' => 'Defaults to Enabled in order to fix Flash of Unstyled Content effect in web browsers. To read more on this effect <a href="http://en.wikipedia.org/wiki/Flash_of_unstyled_content">click here</a> to read more. Leave Enabled if unsure.'
			)
		);

		add_settings_field(
			'adlogic_ajax_compression',
			'AJAX Compression',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_ajax_compression',
				'css' => 'width: 100px;',
				'default' => 'true',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				),
				'legend' => 'Defaults to Enabled for faster AJAX results. Disable if webserver has already compression enabled. Leave as default if unsure.'
			)
		);

		add_settings_field(
			'adlogic_use_minified',
			'Load minified Javascript files?',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_use_minified',
				'css' => 'width: 120px;',
				'default' => 'true',
				'options' => array(
					'false'		=> 'No',
					'true'		=> 'Yes'
				),
				'legend' => ' It is recommended to have this enabled in production environments.'

			)
		);

		add_settings_field(
			'adlogic_use_local_js',
			'Use local Javascript libraries?',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_use_local_js',
				'css' => 'width: 120px;',
				'default' => 'false',
				'options' => array(
					'false'		=> 'No',
					'true'		=> 'Yes'
				),
				'legend' => 'Whether the plugin should load JS libraries from a CDN vs locally.'

			)
		);


		add_settings_field(
			'adlogic_purge_job_details_page',
			'Make job details page unavailable for expired ads?',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_purge_job_details_page',
				'css' => 'width: 120px;',
				'default' => 'false',
				'options' => array(
					'false'		=> 'No',
					'true'		=> 'Yes'
				),
				'legend' => 'If enabled, any expired ads will no longer be accessible on your site and all links to expired ads will no longer function.'

			)
		);

		add_settings_field(
			'adlogic_api_use_new_location_widget',
			'Use new location search widget(To be Deprecated)',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_api_use_new_location_widget',
				'css' => 'width: 100px;',
				'default' => 'false',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				),
				'legend' => 'Defaults to Enabled in order for new job boards to make use of the new geo location search widget'
			)
		);

				add_settings_field(
			'adlogic_api_force_encoding',
			'Force UTF8 Encoding',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_api',
			'adlogic_api_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_api_force_encoding',
				'css' => 'width: 100px;',
				'default' => 'true',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				),
				'legend' => 'If enabled all MyRecruitment+ API responses will be forcibly encoded as UTF8'
			)
		);


		add_settings_section('adlogic_socialmedia_settings_section', 'Social Media API Settings', array('Adlogic_API_Settings', 'socialmedia_section_text'), 'adlogic_plugin_socialmedia_api');

		// LinkedIn API Key for domain
		add_settings_field(
			'adlogic_linkedin_api_key',
			'LinkedIn API Key',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_linkedin_api_key',
				'css' => 'width: 400px;',
				'legend' => 'Please enter API Key as provided by LinkedIn',
				'default' => ''
			)
		);

		// LinkedIn API Key for domain
		add_settings_field(
			'adlogic_linkedin_api_secret',
			'LinkedIn API Secret',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_linkedin_api_secret',
				'css' => 'width: 400px;',
				'legend' => 'Please enter API Secret as provided by LinkedIn',
				'default' => ''
			)
		);

		add_settings_field(
			'adlogic_linkedin_type',
			'LinkedIn Auth Type',
			array('Adlogic_API_Settings', 'setting_select_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_linkedin_type',
				'css' => 'width: 100px;',
				'default' => 'basic',
				'options' => array(
					'basic'		=> 'Basic',
					'advanced'		=> 'Advanced'
				),
				'legend' => 'Defaults to Basic. LinkedIn will need authorise your account for <a href="https://developer.linkedin.com/docs/apply-with-linkedin">Apply With LinkedIn</a> before the Advanced option becomes available. Switching to this option before the approval could cause issues with the Apply with LinkedIn option.'
			)
		);
		// Facebook App Secret
		add_settings_field(
			'adlogic_facebook_app_id',
			'Facebook App ID',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_facebook_app_id',
				'disabled' => (version_compare(PHP_VERSION, '5.4.0', '>')?false:true),
				'css' => 'width: 400px;',
				'legend' =>  (version_compare(PHP_VERSION, '5.4.0', '>')?'Please enter App Id as provided by Facebook':'Facebook Integration requires PHP 5.4.0 or later. Please upgrade your PHP version to access this functionality.')
			)
		);

		// Facebook App Secret
		add_settings_field(
			'adlogic_facebook_app_secret',
			'Facebook App Secret',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_facebook_app_secret',
				'css' => 'width: 400px;',
				'disabled' => (version_compare(PHP_VERSION, '5.4.0', '>')?false:true),
				'legend' =>  (version_compare(PHP_VERSION, '5.4.0', '>')?'Please enter App Secret as provided by Facebook':'Facebook Integration requires PHP 5.4.0 or later. Please upgrade your PHP version to access this functionality.')
			)
		);
		// Facebook App Secret
		add_settings_field(
			'adlogic_google_client_id',
			'Google+ API Client Id',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_google_client_id',
				'css' => 'width: 400px;',
				'legend' => 'Please enter Client Id as provided by Google'
			)
		);

		// Facebook App Secret
		add_settings_field(
			'adlogic_google_client_secret',
			'Google+ API Client Secret',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_google_client_secret',
				'css' => 'width: 400px;',
				'legend' => 'Please enter Client Secret as provided by Google'
			)
		);

		// Indeed API
		add_settings_field(
			'adlogic_indeed_key',
			'Indeed API Token',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_indeed_key',
				'css' => 'width: 400px;',
				'legend' => 'Please enter API Token as provided by Indeed'
			)
		);

		// Indeed Secret Key
		add_settings_field(
			'adlogic_indeed_secret_key',
			'Indeed API Secret Key',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_indeed_secret_key',
				'css' => 'width: 400px;',
				'legend' => 'Please enter Secret API Key as provided by Indeed'
			)
		);

		// ShareThis Publisher Key
		add_settings_field(
			'adlogic_sharethis_publisher_key',
			'ShareThis Publisher Key',
			array('Adlogic_API_Settings', 'setting_text_field'),
			'adlogic_plugin_socialmedia_api',
			'adlogic_socialmedia_settings_section',
			array(
				'setting_section' => 'adlogic_api_settings',
				'field_id' => 'adlogic_sharethis_publisher_key',
				'css' => 'width: 400px;',
				'legend' => 'Optional - Used to track social sharing analytics.  Sign up for a Publisher key <a href="http://sharethis.com/createaccount" target="_blank">here</a>.'
			)
		);

		// Job Logic Password - Display only if Job Logic Board file exists
		if (is_file(AJB_PLUGIN_PATH . '/joblogic_board_activate.txt')) {
			add_settings_field(
				'adlogic_joblogic_passphrase',
				'Job Logic Passphrase',
				array('Adlogic_API_Settings', 'setting_text_field'),
				'adlogic_plugin_api',
				'adlogic_api_settings_section',
				array(
					'setting_section' => 'adlogic_api_settings',
					'field_id' => 'adlogic_joblogic_passphrase',
					'css' => 'width: 200px;',
					'legend' => 'Leave this blank to disable Job Logic functionality.',
					'default' => ''
				)
			);
		}

		add_action( 'admin_notices', array('Adlogic_API_Settings', 'admin_messages') );
	}

	static function admin_messages($test) {
		$errors = get_settings_errors();
		$updatedMessages = '';
		$errorMessages = '';

		if (is_array($errors)) {
			foreach ($errors as $error) {
				if ($error['type'] == 'error') {
					$errorMessages .=  $error['message'];
				} else if ($error['type'] == 'updated') {
					$updatedMessages .=  $error['message'];
				}
			}
		}

		if (!empty($errorMessages)) {
			print '<div class="error">The following errors have been found when submitting your information: <ul>';
			print $errorMessages;
			print '</ul></div>';
		}

		if (!empty($updatedMessages)) {
			print '<div class="updated">Your information has been saved successfully!<ul>';
			print $updatedMessages;
			print '</ul></div>';
		}
	}

	static function plugin_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		?>
		<div class="wrap adlogic_api_settings">
			<?php screen_icon(); ?>
			<h2>MyRecruitment+ Job Board Settings</h2>
			<p>Here you can customise the MyRecruitment+ Job Board to suit your website.</p>
			<form action="options.php" method="post">
				<?php settings_fields('adlogic_api_settings'); ?>
				<?php do_settings_sections('adlogic_plugin_api'); ?>
				<?php do_settings_sections('adlogic_plugin_socialmedia_api'); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	static function section_text() {
		echo '<p>Please enter details as provided by MyRecruitment+</p>';
	}

	static function socialmedia_section_text() {
		?>
		<p>MyRecruitment+ Job Board has a variety of candidate/job-seeker tools that require features provided by either LinkedIn or Facebook to use.</p>
		<p>At minimum either Facebook <strong><em>or</em></strong> LinkedIn API details must be entered to enable these features.</p>
		<p>To apply for a LinkedIn API Key - please click <a href="https://developer.linkedin.com/" target="_blank">here</a> (Requires LinkedIn Registration)<br/>
		To apply for a Facebook App ID - please click <a href="https://developers.facebook.com/" target="_blank">here</a> (Requires Facebook Registration)<br/>
		To apply for a Google+ Client ID - please click <a href="https://code.google.com/apis/console/" target="_blank">here</a> (Requires a Google Account)</p>
		<p>MyRecruitment+ strongly recommends enabling the LinkedIn, Facebook API and/or Google+ Integration for best user experience.</p>
		<p>MyRecruitment+ Job Board also features the ability for users to share job listings on various social media networks. This feature is provided by <a href="http://www.sharethis.com/" target="_blank" title="Find out more about ShareThis">ShareThis</a>&reg;.<br/>
		In order to optionally track social media sharing usage across your site, MyRecruitment+ recommends signing up for a publisher key on their website.</p>
		<p><strong>Note:</strong> Leaving this completely section blank will disable user registration, saved jobs, saved search functionality and applications using Facebook & LinkedIn profiles.</p>
		<?php
	}

	// validate our options
	static function validate_options($input) {
		$validates = true;

		if (empty($input['adlogic_recruiter_id']) || !is_numeric($input['adlogic_recruiter_id'])) {
			add_settings_error($input['adlogic_recruiter_id'], 'adlogic_error_api_recruiter_id', '<li>You <strong>must</strong> enter a valid <em>Recruiter Id</em></li>', 'error');
		}

		if (empty($input['adlogic_advertiser_id']) || !is_numeric($input['adlogic_advertiser_id'])) {
			add_settings_error($input['adlogic_advertiser_id'], 'adlogic_error_api_advertiser_id', '<li>You <strong>must</strong> enter a valid <em>Advertiser Id</em></li>', 'error');
		}

		if (empty($input['adlogic_soap_server']) || !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $input['adlogic_soap_server'])) {
			add_settings_error($input['adlogic_soap_server'], 'adlogic_error_api_advertiser_id', '<li>You <strong>must</strong> enter a valid <em>API Server</em> address</li>', 'error');
		}

		return $input;
	}
}

class Adlogic_Search_Settings  extends Adlogic_Settings {

	static $page_hook;

	static function init() {
		self::$page_hook = add_submenu_page('adlogic-job-board', 'MyRecruitment+ Job Board > Search Settings', 'Search Settings', 'manage_options', 'adlogic-search-settings', array('Adlogic_Search_Settings', 'plugin_options'));
		//add_filter( 'contextual_help' , array('Adlogic_Search_Settings', 'help_plugin'), 10, 3);
	}

	function help_plugin($contextual_help, $screen_id, $screen) {
		if ($screen_id == self::$page_hook) {
			$contextual_help = 'Some stuff';
		}

		return $contextual_help;
	}

	static function admin_init() {
		// Search Settings
		register_setting( 'adlogic_search_settings', 'adlogic_search_settings', array('Adlogic_Search_Settings', 'validate_options') );

		// Search Settings Section
		add_settings_section('adlogic_search_settings_section', 'Search Results', array('Adlogic_Search_Settings', 'section_text'), 'adlogic_plugin_search');

		// Search results per page
		add_settings_field(
			'adlogic_search_results_per_page',
			'Results Per Page',
			array('Adlogic_Search_Settings', 'setting_text_field'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_search_results_per_page',
				'css' => 'width: 40px;',
				'default' => 10,
				'legend' => 'The number of search results to display per page (default: 5)'
			)
		);

		// No Results found
		add_settings_field(
			'adlogic_search_results_none',
			'No Search Results Returned',
			array('Adlogic_Search_Settings', 'setting_text_field'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_search_results_none',
				'css' => 'width: 400px;',
				'default' => 'Job search returned no results',
				'legend' => 'The message returned when no jobs results match the search criteria requested'
			)
		);

		// Search results per page
		add_settings_field(
			'adlogic_search_results_date_format',
			'Job Posted Date Format',
			array('Adlogic_Search_Settings', 'setting_select_field'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_search_results_date_format',
				'css' => 'width: 100px;',
				'default' => 'd/m/Y',
				'options' => array(
					'd/m/Y'		=> '30/12/2012 (dd/mm/yyyy)',
					'j/n/Y'		=> '1/4/2012 (d/m/yyyy)',
					'd/m/y'		=> '30/12/12 (dd/mm/yy)',
					'j/n/y'		=> '1/4/12 (d/m/yy)',
					'd/m'		=> '30/12 (dd/mm)',
					'j/n'		=> '1/4 (d/m)',
					'm/Y'		=> '12/2012 (mm/yyyy)',
					'n/Y'		=> '4/2012 (m/yyyy)',
					'm/y'		=> '12/12 (mm/yy)',
					'n/y'		=> '4/12 (m/yy)'

				),
				'legend' => 'The format for the Job Posted Date as displayed in the search results (default: dd/mm/yyyy - ie. 30/12/2012)'
			)
		);

		// Search results per page
		add_settings_field(
			'adlogic_job_details_page',
			'Job Details Page',
			array('Adlogic_Search_Settings', 'setting_page_select'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_job_details_page',
				'legend' => 'Select the Wordpress Page you wish to use to display your Job Details'
			)
		);

		// Job Details Page Design
		add_settings_field(
			'adlogic_job_details_page_design',
			'Job Details Page - Apply Button Position: ',
			array('Adlogic_Search_Settings', 'setting_select_field'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_job_details_page_design',
				'css' => 'width: 150px;',
				'default' => false,
				'options' => array(
					'false'		=> 'Below Ad',
					'true'		=> 'Beside Ad'
				),
				'legend' => ''
			)
		);

		// Search results per page
		add_settings_field(
			'adlogic_search_intranet_setting',
			'Intranet/Extranet Options',
			array('Adlogic_Search_Settings', 'setting_select_field'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_search_intranet_setting',
				'css' => 'width: 100px;',
				'default' => 'false',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				),
				'legend' => 'For job boards with both internal and external job placements'
			)
		);



		// Search results per page
		add_settings_field(
			'adlogic_search_enhanced_dropdowns',
			'Enable Enhanced Dropdowns',
			array('Adlogic_Search_Settings', 'setting_select_field'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_search_enhanced_dropdowns',
				'css' => 'width: 100px;',
				'default' => 'false',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				),
				'legend' => 'Enable enhanced dropdown fields for Job Search using the <a href="http://harvesthq.github.io/chosen/" target="_blank">Chosen Plugin</a> (Disabled by default, not compatible with all websites).'
			)
		);

		// Field to determine how "fresh" an ad is
		add_settings_field(
			'adlogic_ad_fresh_period',
			'Time in hours an ad is considered "fresh"',
			array('Adlogic_Search_Settings', 'setting_text_field'),
			'adlogic_plugin_search',
			'adlogic_search_settings_section',
			array(
				'setting_section' => 'adlogic_search_settings',
				'field_id' => 'adlogic_ad_fresh_period',
				'css' => 'width: 40px;float:left;',
				'default' => 2,
				'legend' => 'The time in hours an ad is considered fresh. If an ad is posted within this timeframe, you can use the <em>{job_is_fresh}</em> tag to show that the ad is new.'
			)
		);
	}

	static function plugin_options() {
		
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		?>
		<div class="wrap adlogic_search_settings">
			<?php screen_icon(); ?>
			<h2>MyRecruitment+ Job Board Settings</h2>
			<p>Here you can customise the MyRecruitment+ Job Board to suit your website.</p>
			<form action="options.php" method="post">
				<?php settings_fields('adlogic_search_settings'); ?>
				<?php do_settings_sections('adlogic_plugin_search'); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	static function section_text() {
		echo '<p>Customise your search results options</p>';
	}

}

class Adlogic_RSS_Settings  extends Adlogic_Settings {
	static function init() {
		add_submenu_page('adlogic-job-board', 'MyRecruitment+ Job Board > RSS Feed', 'RSS Feed', 'manage_options', 'adlogic-rss-settings', array('Adlogic_RSS_Settings', 'plugin_options'));
	}

	static function admin_init() {
		// RSS Feed Settings
		register_setting( 'adlogic_rss_settings', 'adlogic_rss_settings', array('Adlogic_RSS_Settings', 'validate_options') );
		// RSS Settings Section
		add_settings_section('adlogic_rss_settings_section', "RSS Feed", array('Adlogic_RSS_Settings', 'section_text'), 'adlogic_plugin_rss');

		// Feed Title
		add_settings_field(
			'adlogic_rss_title',
			'Feed Title',
			array('Adlogic_RSS_Settings', 'setting_text_field'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_rss_title',
				'css' => 'width: 200px;',
				'legend' => 'The RSS Feed Title appears at the top of your RSS feed'
			)
		);

		// Feed Description
		add_settings_field(
			'adlogic_rss_description',
			'Feed Description',
			array('Adlogic_RSS_Settings', 'setting_text_field'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_rss_description',
				'css' => 'width: 200px;',
				'legend' => 'The RSS Feed description gives your readers a general description of what your RSS feed is about'
			)
		);

		// Feed Category
		add_settings_field(
			'adlogic_rss_category',
			'Feed Category',
			array('Adlogic_RSS_Settings', 'setting_text_field'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_rss_category',
				'css' => 'width: 200px;',
				'default' => 'Recruitment Jobs',
				'legend' => 'The RSS Feed category allows your readers and search engines to sort and categorise your feed. (default: Recruitment Jobs)'
			)
		);

		// Feed Category
		add_settings_field(
			'adlogic_rss_copyright',
			'Feed Copyright',
			array('Adlogic_RSS_Settings', 'setting_text_field'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_rss_copyright',
				'css' => 'width: 200px;',
				'legend' => 'Optional'
			)
		);
		// Search results per page
		add_settings_field(
			'adlogic_job_details_page',
			'Job Details Page',
			array('Adlogic_Search_Settings', 'setting_page_select'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_job_details_page',
				'legend' => 'Select the Wordpress Page you wish to use to display your Job Details'
			)
		);

		// Feed Max Jobs
		add_settings_field(
			'adlogic_rss_max_display_items',
			'Maximum Jobs To Display',
			array('Adlogic_RSS_Settings', 'setting_text_field'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_rss_max_display_items',
				'css' => 'width: 50px;',
				'default' => 50,
				'legend' => 'Default: 50'
			)
		);

		add_settings_field(
			'adlogic_rss_advertisers',
			'Bulk Advertiser ID(s) to pull from',
			array('Adlogic_RSS_Settings', 'setting_text_field'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_rss_advertisers',
				'css' => 'width: 200px;float:left;',
				'default' => '10000,10006',
				'legend' => 'The Bulk RSS feed will pull jobs for these advertisers (comma separated, spaces are automatically removed)<br/>'.
				'<strong>Example:</strong> 10000,10006<br/>Contact <a href="mailto:support@myrecruitmentplus.com">support</a> if you require assistance.'
			)
		);

		add_settings_field(
			'adlogic_rss_hidetimestamp',
			'Hide timestamp on RSS feed',
			array('Adlogic_RSS_Settings', 'setting_select_field'),
			'adlogic_plugin_rss',
			'adlogic_rss_settings_section',
			array(
				'setting_section' => 'adlogic_rss_settings',
				'field_id' => 'adlogic_rss_hidetimestamp',
				'default' => 'false',
				'options' => array(
					'false'		=> 'No',
					'true'		=> 'Yes'
				),
				'legend' => 'If <em>Yes</em> the time stamp will be hidden in the RSS feed & only the date will be shown.'
			)
		);
	}

	static function plugin_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		?>
		<div class="wrap adlogic_rss_settings">
			<?php screen_icon(); ?>
			<h2>MyRecruitment+ Job Board Settings</h2>
			<p>Here you can customise the MyRecruitment+ Job Board to suit your website.</p>
			<form action="options.php" method="post">
				<?php settings_fields('adlogic_rss_settings'); ?>
				<?php do_settings_sections('adlogic_plugin_rss'); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	static function section_text() {
		echo "<p><span style='margin-top:10px;display:block;font-weight:normal;font-size:13px;'>View <a target='_blank' href=\"". home_url() . '/adlogic-jobs/rss/'."\">RSS Feed</a><br/>".
			"The RSS feed provides a list of jobs (limited by the <em>Maximum Jobs To Display</em> value below) this feed only pulls jobs for the <em>Advertiser ID</em> set up in <strong>API Settings</strong> ".
					"<span style='margin-top:10px;display:block;font-weight:normal;font-size:13px;'>View <a target='_blank' href=\"". home_url() . '/adlogic-jobs/bulk-rss/'."\">Bulk RSS Feed</a><br/>".
			"The RSS feed provides a list of jobs (limited by the <em>Maximum Jobs To Display</em> value below) this feed pulls jobs for any Advertiser ID set up in the <em>Bulk Advertiser ID(s) to pull from</em> field below. <br/>This can be used to display both external & internal advertisements without providing multiple feeds.".
					"</p>".
		"<p>Customise your RSS Feed Fields and sections to suit your site</p>";
	}
}

class Adlogic_Cache_Settings  extends Adlogic_Settings {
	static function init() {
		add_submenu_page('adlogic-job-board', 'MyRecruitment+ Job Board > Cache Settings', 'Cache Settings', 'manage_options', 'adlogic-cache-settings', array('Adlogic_Cache_Settings', 'plugin_options'));
	}

	static function admin_init() {
		register_setting( 'adlogic_cache_settings', 'adlogic_cache_settings', array('Adlogic_Cache_Settings', 'validate_options') );

		// Cache Settings Section
		add_settings_section('adlogic_cache_settings_section', 'MyRecruitment+ Cache Settings', array('Adlogic_Cache_Settings', 'section_text'), 'adlogic_plugin_cache');

		// Enable/Disable Cache
		add_settings_field(
			'adlogic_cache_status',
			'Cache Status',
			array('Adlogic_Cache_Settings', 'setting_select_field'),
			'adlogic_plugin_cache',
			'adlogic_cache_settings_section',
			array(
				'setting_section' => 'adlogic_cache_settings',
				'field_id' => 'adlogic_cache_status',
				'css' => 'width: 120px;',
				'default' => 'true',
				'options' => array(
					'false'		=> 'Disable',
					'true'		=> 'Enable'
				)
			)
		);

		// Cache Timeout Settings
		add_settings_field(
			'adlogic_cache_timeout',
			'Cache Timeout',
			array('Adlogic_Cache_Settings', 'setting_select_field'),
			'adlogic_plugin_cache',
			'adlogic_cache_settings_section',
			array(
				'setting_section' => 'adlogic_cache_settings',
				'field_id' => 'adlogic_cache_timeout',
				'css' => 'width: 120px;',
				'default' => '300',
				'options' => array(
					'60'		=> '1 minute',
					'300'		=> '5 minutes',
					'600'		=> '10 minutes',
					'900'		=> '15 minutes',
					'1800'		=> '30 minutes',
					'3600'		=> '1 hour'
				)
			)
		);

		add_action( 'admin_notices', array('Adlogic_Cache_Settings', 'admin_messages') );
	}

	static function admin_messages($test) {
		$errors = get_settings_errors();
		$updatedMessages = '';
		$errorMessages = '';

		if (is_array($errors)) {
			foreach ($errors as $error) {
				if ($error['type'] == 'error') {
					$errorMessages .=  $error['message'];
				} else if ($error['type'] == 'updated') {
					$updatedMessages .=  $error['message'];
				}
			}
		}

		if (!empty($errorMessages)) {
			print '<div class="error">The following errors have been found when submitting your information: <ul>';
			print $errorMessages;
			print '</ul></div>';
		}

		if (!empty($updatedMessages)) {
			print '<div class="updated">Your information has been saved successfully!<ul>';
			print $updatedMessages;
			print '</ul></div>';
		}
	}

	static function plugin_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		?>
		<div class="wrap adlogic_cache_settings">
			<?php screen_icon(); ?>
			<h2>MyRecruitment+ Job Board Settings</h2>
			<p>Here you can customise the MyRecruitment+ Job Board to suit your website.</p>
			<form action="options.php" method="post">
				<?php settings_fields('adlogic_cache_settings'); ?>
				<?php do_settings_sections('adlogic_plugin_cache'); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	static function section_text() {
		echo '<p>Customise your plugin cache options</p>';
		echo '<strong>Cache folder location:</strong> ' . AJB_PLUGIN_PATH . '/cache/<br/>';
		echo '<strong>Cache folder writeable: ' . (is_writable(AJB_PLUGIN_PATH . '/cache/') ? '<span style="color: green;">Yes</span>' : 'No') . '</strong></p>';
		echo '<p><strong>Cache index file location:</strong> ' . AJB_PLUGIN_PATH . '/cache/cache_index<br/>';
		echo '<strong>Cache index file writeable:' . (is_writable(AJB_PLUGIN_PATH . '/cache/cache_index') ?'<span style="color: green;">Yes</span>' : 'No') . '</strong></p>';

	}

	// validate our options
	static function validate_options($input) {
		$validates = true;

		/*if (empty($input['adlogic_recruiter_id']) || !is_numeric($input['adlogic_recruiter_id'])) {
			add_settings_error($input['adlogic_recruiter_id'], 'adlogic_error_api_recruiter_id', '<li>You <strong>must</strong> enter a valid <em>Recruiter Id</em></li>', 'error');
		}

		if (empty($input['adlogic_advertiser_id']) || !is_numeric($input['adlogic_advertiser_id'])) {
			add_settings_error($input['adlogic_advertiser_id'], 'adlogic_error_api_advertiser_id', '<li>You <strong>must</strong> enter a valid <em>Advertiser Id</em></li>', 'error');
		}

		if (empty($input['adlogic_soap_server']) || !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $input['adlogic_soap_server'])) {
			add_settings_error($input['adlogic_advertiser_id'], 'adlogic_error_api_advertiser_id', '<li>You <strong>must</strong> enter a valid <em>API Server</em> address</li>', 'error');
		}*/

		return $input;
	}
}
/*class Adlogic_Mobile_Settings  extends Adlogic_Settings {
	static function init() {
		add_submenu_page('adlogic-job-board', 'MyRecruitment+ Job Board > Mobile Settings', 'Mobile Settings', 'manage_options', 'adlogic-mobile-settings', array('Adlogic_Mobile_Settings', 'plugin_options'));
	}

	static function admin_init() {
		register_setting( 'adlogic_mobile_settings', 'adlogic_mobile_settings', array('Adlogic_Mobile_Settings', 'validate_options') );

		// Mobile Settings Section
		add_settings_section('adlogic_mobile_settings_section', 'Adlogic Mobile Settings', array('Adlogic_Mobile_Settings', 'section_text'), 'adlogic_plugin_mobile');

		// If WPtouch or WPtouch Pro are installed, enable mobile website configuration
		if (is_plugin_active('wptouch-pro/wptouch-pro.php') || is_plugin_active('wptouch/wptouch.php')) {
			// Desktop Site URL
			add_settings_field(
				'adlogic_desktop_site',
				'Desktop Site URL',
				array('Adlogic_Mobile_Settings', 'setting_text_field'),
				'adlogic_plugin_mobile',
				'adlogic_mobile_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_desktop_site',
					'css' => 'width: 400px;',
					'default' => '',
					'legend' => 'Leave blank if unsure, or if site is the same as this wordpress installation.'
				)
			);

			add_settings_field(
				'adlogic_desktop_search_url',
				'Desktop Search Base URL',
				array('Adlogic_Mobile_Settings', 'setting_text_field'),
				'adlogic_plugin_mobile',
				'adlogic_mobile_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_desktop_search_url',
					'css' => 'width: 400px;',
					'legend' => 'For use with sites using MyRecruitment+ Job board platform only. Leave blank if unsure, or if site is the same as this wordpress installation.',
					'default' => ''
				)
			);

			add_settings_field(
				'adlogic_desktop_job_details_url',
				'Desktop Job Details Base URL',
				array('Adlogic_Mobile_Settings', 'setting_text_field'),
				'adlogic_plugin_mobile',
				'adlogic_mobile_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_desktop_job_details_url',
					'css' => 'width: 400px;',
					'legend' => 'For use with sites using MyRecruitment+ Job board platform only. Leave blank if unsure, or if site is the same as this wordpress installation.',
					'default' => ''
				)
			);
		}

		// Mobile Site URL
		add_settings_field(
			'adlogic_mobile_site',
			'Mobile Site URL',
			array('Adlogic_Mobile_Settings', 'setting_text_field'),
			'adlogic_plugin_mobile',
			'adlogic_mobile_settings_section',
			array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_mobile_site',
					'css' => 'width: 400px;',
					'legend' => 'Leave this blank to disable mobile redirection. Enter your mobile website address if you have one.',
					'default' => ''
			)
		);

		add_settings_field(
			'adlogic_mobile_search_url',
			'Mobile Search Base URL',
			array('Adlogic_Mobile_Settings', 'setting_text_field'),
			'adlogic_plugin_mobile',
			'adlogic_mobile_settings_section',
			array(
				'setting_section' => 'adlogic_mobile_settings',
				'field_id' => 'adlogic_mobile_search_url',
				'css' => 'width: 400px;',
				'legend' => 'For use with the MyRecruitment+ mobile platform only. Leave blank if unsure.',
				'default' => ''
			)
		);

		add_settings_field(
			'adlogic_mobile_job_details_url',
			'Mobile Job Details Base URL',
			array('Adlogic_Mobile_Settings', 'setting_text_field'),
			'adlogic_plugin_mobile',
			'adlogic_mobile_settings_section',
			array(
				'setting_section' => 'adlogic_mobile_settings',
				'field_id' => 'adlogic_mobile_job_details_url',
				'css' => 'width: 400px;',
				'legend' => 'For use with the MyRecruitment+ mobile platform only. Leave blank if unsure.',
				'default' => ''
			)
		);

		if (is_plugin_active('wptouch-pro/wptouch-pro.php') || is_plugin_active('wptouch/wptouch.php')) {
			add_settings_section('adlogic_sms_settings_section', 'SMS API Settings', array('Adlogic_Mobile_Settings', 'sms_section_text'), 'adlogic_plugin_sms_api');

			// SMSGlobal HTTP-API Url
			add_settings_field(
				'adlogic_smsglobal_server',
				'HTTP-API Url',
				array('Adlogic_Mobile_Settings', 'setting_text_field'),
				'adlogic_plugin_sms_api',
				'adlogic_sms_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_smsglobal_server',
					'css' => 'width: 400px;',
					'legend' => 'Please enter API Server address as provided by SMS Global (default: https://www.smsglobal.com.au/http-api.php). More info <a href="http://www.smsglobal.com/" target="_blank">here</a>.',
					'default' => 'https://www.smsglobal.com.au/http-api.php'
				)
			);

			// SMSGlobal Username
			add_settings_field(
				'adlogic_smsglobal_username',
				'Username',
				array('Adlogic_Mobile_Settings', 'setting_text_field'),
				'adlogic_plugin_sms_api',
				'adlogic_sms_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_smsglobal_username',
					'css' => 'width: 100px;',
					'legend' => 'Please enter username as provided by SMSGlobal. Leave blank to disable SMS features.',
					'default' => ''
				)
			);

			// SMSGlobal Password
			add_settings_field(
				'adlogic_smsglobal_password',
				'Password',
				array('Adlogic_Mobile_Settings', 'setting_password_field'),
				'adlogic_plugin_sms_api',
				'adlogic_sms_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_smsglobal_password',
					'css' => 'width: 100px;',
					'legend' => 'Please enter password as provided by SMSGlobal. Leave blank to disable SMS features.',
					'default' => ''
				)
			);

			// SMSGlobal Sender Number
			add_settings_field(
				'adlogic_smsglobal_sender_number',
				'Sender Number',
				array('Adlogic_Mobile_Settings', 'setting_text_field'),
				'adlogic_plugin_sms_api',
				'adlogic_sms_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_smsglobal_sender_number',
					'css' => 'width: 100px;',
					'legend' => 'Please enter the sender number you\'d like to appear in your sent SMS.',
					'default' => ''
				)
			);

			add_settings_section('adlogic_url_shortening_settings_section', 'URL Shortening API Settings', array('Adlogic_Mobile_Settings', 'url_shortening_section_text'), 'adlogic_plugin_url_shortening_api');

			// Bit.ly Login
			add_settings_field(
				'adlogic_bitly_login',
				'Login/Username',
				array('Adlogic_Mobile_Settings', 'setting_text_field'),
				'adlogic_plugin_url_shortening_api',
				'adlogic_url_shortening_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_bitly_login',
					'css' => 'width: 100px;',
					'legend' => 'Optional - Please enter login/username as provided by Bit.ly.',
					'default' => ''
				)
			);

			// Bit.ly API Key
			add_settings_field(
				'adlogic_bitly_api_key',
				'API Key',
				array('Adlogic_Mobile_Settings', 'setting_password_field'),
				'adlogic_plugin_url_shortening_api',
				'adlogic_url_shortening_settings_section',
				array(
					'setting_section' => 'adlogic_mobile_settings',
					'field_id' => 'adlogic_bitly_api_key',
					'css' => 'width: 100px;',
					'legend' => 'Optional - Please enter API Key as provided by Bit.ly.',
					'default' => ''
				)
			);

		}

		add_action( 'admin_notices', array('Adlogic_Mobile_Settings', 'admin_messages') );
	}

	function sms_section_text() {
		print 'Here you can enter your SMS provider details (currently only SMS Global is supported).<br/>';
		print 'Leaving the username and password blank will result in the SMS functionality being disabled.';
	}

	function url_shortening_section_text() {
		print 'Here you can enter your URL Shortening service details (currently only Bit.ly is supported).<br/>';
		print 'This is an optional feature that will allow you to measure how many clicks are made on urls sent via SMS.';
	}

	static function admin_messages($test) {
		$errors = get_settings_errors();
		$updatedMessages = '';
		$errorMessages = '';

		if (is_array($errors)) {
			foreach ($errors as $error) {
				if ($error['type'] == 'error') {
					$errorMessages .=  $error['message'];
				} else if ($error['type'] == 'updated') {
					$updatedMessages .=  $error['message'];
				}
			}
		}

		if (!empty($errorMessages)) {
			print '<div class="error">The following errors have been found when submitting your information: <ul>';
			print $errorMessages;
			print '</ul></div>';
		}

		if (!empty($updatedMessages)) {
			print '<div class="updated">Your information has been saved successfully!<ul>';
			print $updatedMessages;
			print '</ul></div>';
		}
	}

	static function plugin_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		?>
		<div class="wrap adlogic_mobile_settings">
			<?php screen_icon(); ?>
			<h2>MyRecruitment+ Job Board Settings</h2>
			<p>Here you can customise the MyRecruitment+ Job Board to suit your website.</p>
			<form action="options.php" method="post">
				<?php settings_fields('adlogic_mobile_settings'); ?>
				<?php do_settings_sections('adlogic_plugin_mobile');?>
				<?php do_settings_sections('adlogic_plugin_sms_api'); ?>
				<?php do_settings_sections('adlogic_plugin_url_shortening_api'); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	static function section_text() {
		print '<p>Update the settings for mobile redirection ' . ((is_plugin_active('wptouch-pro/wptouch-pro.php') || is_plugin_active('wptouch/wptouch.php')) ? 'and desktop redirection ' : '') . 'here.</p>';
	}

	// validate our options
	static function validate_options($input) {
		$validates = true;

		return $input;
	}
}*/

class Adlogic_First_Setup_Settings  extends Adlogic_Settings {
	static function init() {
		add_submenu_page('adlogic-job-board', 'MyRecruitment+ Job Board > First Time Setup', 'First Time Setup', 'manage_options', 'adlogic-job-board', array('Adlogic_First_Setup_Settings', 'plugin_options'));
	}

	static function admin_init() {
		// RSS Feed Settings
		register_setting( 'adlogic_first_setup_settings', 'adlogic_first_setup_settings', array('Adlogic_First_Setup_Settings', 'validate_options') );

		// RSS Settings Section
		add_settings_section('adlogic_first_setup_settings_section', 'First Time Setup', array('Adlogic_First_Setup_Settings', 'section_text'), 'adlogic_plugin_first_setup');

		// Recruiter ID
		add_settings_field(
			'adlogic_recruiter_id',
			'Account ID',
			array(__CLASS__, 'setting_text_field'),
			'adlogic_plugin_first_setup',
			'adlogic_first_setup_settings_section',
			array(
				'setting_section' => 'adlogic_first_setup_settings',
				'field_id' => 'adlogic_recruiter_id',
				'css' => 'width: 60px;'
			)
		);

		// Advertiser ID
		// add_settings_field(
		// 	'adlogic_advertiser_id',
		// 	'Advertiser ID',
		// 	array(__CLASS__, 'setting_text_field'),
		// 	'adlogic_plugin_first_setup',
		// 	'adlogic_first_setup_settings_section',
		// 	array(
		// 		'setting_section' => 'adlogic_first_setup_settings',
		// 		'field_id' => 'adlogic_advertiser_id',
		// 		'css' => 'width: 60px;'
		// 	)
		// );
		add_settings_field(
			'adlogic_advertiser_id',
			'<span style="width:230px;display:inline-block;">I want to use the Job Board on my</span>',
			array(__CLASS__, 'setting_select_field'),
			'adlogic_plugin_first_setup',
			'adlogic_first_setup_settings_section',
			array(
				'setting_section' => 'adlogic_first_setup_settings',
				'field_id' => 'adlogic_advertiser_id',
				'css' => 'width: 90px;',
				'default' => 10000,
				'options' => array(
					10000	=> 'Website',
					10006	=> 'Intranet'
				)
			)
		);

		// Search results per page
		// add_settings_field(
		// 	'adlogic_automatic_page_create',
		// 	'Auto-create Job Board Pages',
		// 	array(__CLASS__, 'setting_select_field'),
		// 	'adlogic_plugin_first_setup',
		// 	'adlogic_first_setup_settings_section',
		// 	array(
		// 		'setting_section' => 'adlogic_first_setup_settings',
		// 		'field_id' => 'adlogic_automatic_page_create',
		// 		'css' => 'width: 60px;',
		// 		'default' => 'true',
				// 'options' => array(
				// 	'true'		=> 'Yes',
				// 	'false'		=> 'No'
				// 	),
		// 			'legend' => 'Selecting \'Yes\' will automatically create relevant job board pages for you. If you select \'No\', these will need to be done manually.'
		// 		)
		// );

		// add_settings_field(
		// 	'adlogic_use_new_location_widget',
		// 	'Use new location search widget',
		// 	array('Adlogic_API_Settings', 'setting_select_field'),
		// 	'adlogic_plugin_first_setup',
		// 	'adlogic_first_setup_settings_section',
		// 	array(
		// 		'setting_section' => 'adlogic_first_setup_settings',
		// 		'field_id' => 'adlogic_use_new_location_widget',
		// 		'css' => 'width: 100px;',
		// 		'default' => 'true',
		// 		'options' => array(
		// 			'false'		=> 'Disable',
		// 			'true'		=> 'Enable'
		// 		),
		// 		'legend' => 'Defaults to Enabled in order for new job boards to make use of the new geo location search widget'
		// 	)
		// );

		add_action( 'admin_notices', array(__CLASS__, 'admin_messages') );
	}

	static function admin_messages($test) {
		$errors = get_settings_errors();
		$updatedMessages = '';
		$errorMessages = '';

		if (is_array($errors)) {
			foreach ($errors as $error) {
				if ($error['type'] == 'error') {
					$errorMessages .=  $error['message'];
				} else if ($error['type'] == 'updated') {
					$updatedMessages .=  $error['message'];
				}
			}
		}

		if (!empty($errorMessages)) {
			print '<div class="error">The following errors have been found when submitting your information: <ul>';
			print $errorMessages;
			print '</ul></div>';
		}

		if (!empty($updatedMessages)) {
			print '<div class="updated">Your job board has been successfully setup!<ul>';
			print $updatedMessages;
			print '</ul></div>';
		}
	}

	static function plugin_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		?>
		<div class="wrap adlogic_first_setup_settings">
			<?php screen_icon(); ?>
			<h2>MyRecruitment+ Job Board Settings</h2>
			<p>Here you can customise the MyRecruitment+ Job Board to suit your website.</p>
			<form action="options.php" method="post">
				<?php settings_fields('adlogic_first_setup_settings'); ?>
				<?php do_settings_sections('adlogic_plugin_first_setup'); ?>
				<?php submit_button('Setup My Job Board!'); ?>
			</form>
		</div>
		<?php
	}

	// validate our options
	static function validate_options($input) {
		$validates = true;

		if (empty($input['adlogic_recruiter_id']) || !is_numeric($input['adlogic_recruiter_id'])) {
			add_settings_error($input['adlogic_recruiter_id'], 'adlogic_error_api_recruiter_id', '<li>You <strong>must</strong> enter a valid <em>Account Id</em></li>', 'error');
			$validates = false;
		}

		if (empty($input['adlogic_advertiser_id']) || !is_numeric($input['adlogic_advertiser_id'])) {
			add_settings_error($input['adlogic_advertiser_id'], 'adlogic_error_api_advertiser_id', '<li>You <strong>must</strong> enter a valid <em>Advertiser Id</em></li>', 'error');
			$validates = false;
		}

		if ($validates == true) {
			/** Setup each settings defaults up-front **/

			// Automatically create pages for Job Search, Job Details, Job Application and Saved Jobs Pages
			$jobSearchPageConfig = array(
					'post_content'   => '[adlogic_search_pagination][adlogic_search_results][adlogic_search_pagination]', //The full text of the post.
					'post_status'    => 'publish', //Set the status of the new post.
					'post_title'     => 'Job Search', //The title of your post.
					'post_type'      => 'page' //You may want to insert a regular post, page, link, a menu item or some custom post type
			);
			$jobSearchPageId = wp_insert_post($jobSearchPageConfig);

			// Job Details Page
			$jobDetailsPageConfig = array(
					'post_content'   => '[adlogic_job_details]', //The full text of the post.
					'post_status'    => 'publish', //Set the status of the new post.
					'post_title'     => 'Job Details', //The title of your post.
					'post_type'      => 'page' //You may want to insert a regular post, page, link, a menu item or some custom post type
			);
			$jobDetailsPageId = wp_insert_post($jobDetailsPageConfig);

			// Saved Jobs Page
			$savedJobsPageConfig = array(
					'post_content'   => '[adlogic_saved_jobs]', //The full text of the post.
					'post_status'    => 'publish', //Set the status of the new post.
					'post_title'     => 'Saved Jobs', //The title of your post.
					'post_type'      => 'page' //You may want to insert a regular post, page, link, a menu item or some custom post type
			);
			$savedJobsPageId = wp_insert_post($savedJobsPageConfig);
			
			// API Settings
			$apiSettings = array(
							'adlogic_recruiter_id'				=> $input['adlogic_recruiter_id'],
							'adlogic_advertiser_id'				=> $input['adlogic_advertiser_id'],
							'adlogic_soap_server'				=> 'http://rec.myrecruitmentplus.com/AdlogicJobPortalService?WSDL',
							'adlogic_candidate_soap_server'		=> 'http://rec.myrecruitmentplus.com/CandidatesWS?wsdl',
							'adlogic_custom_application_page'	=> (isset($applicationPageId) ? $applicationPageId : null),
							'adlogic_ajax_compression'			=> true,
							'adlogic_custom_application_url'	=>	'',
							'adlogic_api_use_new_location_widget' => true//$input['adlogic_use_new_location_widget']
						);
						
			update_option('adlogic_api_settings', $apiSettings);

			$searchSettings = array(
				'adlogic_search_results_per_page'		=> '5',
				'adlogic_search_results_none'			=> 'Job search returned no results',
				'adlogic_search_results_date_format'	=> 'd/m/Y',
				'adlogic_job_details_page'				=> (isset($jobDetailsPageId) ? $jobDetailsPageId : null),
				'adlogic_search_intranet_setting'		=> 'false'
			);

			update_option('adlogic_search_settings', $searchSettings);

			$rssSettings = array(
					'adlogic_rss_category'		=> 'Recruitment Jobs',
					'adlogic_job_details_page'	=> (isset($jobDetailsPageId) ? $jobDetailsPageId : null),
					'adlogic_rss_max_items'		=> '200'
			);

			update_option('adlogic_rss_settings', $rssSettings);

			$cacheSettings = array(
								'adlogic_cache_status' => 'true',
								'adlogic_cache_timeout' => '300',
							);

			update_option('adlogic_cache_settings', $rssSettings);
			return false;
		} else {
			return $input;
		}
	}

	static function section_text() {
		?>
		<p>Welcome to the First-Time Setup for your new MyRecruitment+ Job Board!</p>
		<p>Please enter the following details as provided by MyRecruitment+</p>
		<?php
		
	}
}

// Add Adlogic Job Search Menu Item
add_action('admin_menu', array('Adlogic_Settings', 'init'));

// add the admin settings and such
add_action('admin_init', array('Adlogic_Settings', 'admin_init'));

?>
