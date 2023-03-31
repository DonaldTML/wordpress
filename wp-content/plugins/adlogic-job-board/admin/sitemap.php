<?php

// This plugin is reliant on the Google Sitemap Generator version >=4.0

class Adlogic_Job_Sitemap {

	function init() {
			//sm_build_index is called when the root sitemap is being generated
			//sm_build_content is called when a content sitemap is being generated
			add_action("sm_build_index",array('Adlogic_Job_Sitemap',"index"),20,1);
			add_action("sm_build_content",array('Adlogic_Job_Sitemap',"content"),20,3);
	}

	function index($oGoogleSitemapGenerator) {
		$oGoogleSitemapGenerator->AddSitemap('adl_job_board', 'jobs', time());
	}

	function content($oGoogleSitemapGenerator, $type, $params) {
		global $wp_rewrite;

		switch($type) {
			case 'adl_job_board':
				// Get Settings from Wordpress
				$apiSettings = get_option('adlogic_api_settings');
				$searchSettings = get_option('adlogic_search_settings');

				// Instantiate Soap Client Object
				$oSoapClient = Adlogic_Job_Board::getSoapConnection();
			
				// Requires Job Search Class
				require_once(AJB_PLUGIN_PATH . '/lib/classes/jobSearch.class.php');
				$oJobSearch = new JobSearch($oSoapClient, $apiSettings['adlogic_advertiser_id'], $apiSettings['adlogic_recruiter_id']);
				$oJobSearch->set('to', 200);

				$oJobSearchResults = $oJobSearch->get();
				if ($oJobSearchResults) {
					foreach ($oJobSearchResults->JobPostings->JobPosting as $oJobPosting) {
						// Get Job Attributes
						$oJobAttributes = $oJobPosting->attributes();
						// Build Location Array
						foreach ($oJobPosting->locations->location as $sLocation) {
							$sLocationArray[] = $sLocation->value;
						}

						// Build Job Details Page Url
						if ($wp_rewrite->using_permalinks()) {
							$jobUrl = get_permalink($searchSettings['adlogic_job_details_page']). 'query/' . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($sLocationArray)) . '/' . $oJobAttributes->ad_id;
						} else {
							$jobUrl = get_permalink($searchSettings['adlogic_job_details_page']). '&/' . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($sLocationArray)) . '/' . $oJobAttributes->ad_id;
						} 

						$oGoogleSitemapGenerator->AddUrl($jobUrl, strtotime($oJobPosting->pubDate),"daily",0.9);
					}
				}
				break;
		}
	}
}

if(function_exists("add_action")) add_action('sm_init', array('Adlogic_Job_Sitemap', 'init'),1003,0);
?>