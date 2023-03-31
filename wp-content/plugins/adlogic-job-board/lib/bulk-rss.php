<?php
class BulkRSS {
	// Get URL Vars from SEO Friendly stuff
	private $oSoapClient;
	public $searchVars;
	public $currentVar;
	public $varArray = array();
	private $oRssFeed;

	public function __construct($oSoapClient) {
		$this->oSoapClient = $oSoapClient;
		$this->searchVars = explode('/', $_SERVER['QUERY_STRING']);

		$this->currentVar = null;
		$this->varArray = array();
		foreach ($this->searchVars as $searchVar) {
			if ($searchVar != '') {
			    switch ($searchVar) {
				case 'Industry':
				case 'Location':
				case 'WorkType':
				case 'SalaryMin':
				case 'SalaryMax':
				case 'Page':
				case 'RecruiterId':
				case 'Keywords':
				    $this->currentVar = $searchVar;
				    break;
				default:
				    if (!empty($this->currentVar)) {
					$this->varArray[$this->currentVar][] = $searchVar;
				    }
				    break;
			    }
			}
		}

		if (!empty($this->varArray)) {
			foreach ($this->varArray as $key => $value) {
			    // pick last value from array
			    $value = array_pop($value);
			    switch ($key) {
				case 'Industry':
				    $_GET['indId'] = $value;
				    break;
				case 'Location':
				    $_GET['locId'] = $value;
				    break;
				case 'WorkType':
				    $_GET['wtId'] = $value;
				    break;
				case 'SalaryMin':
				    $_GET['salaryMin'] = $value;
				    break;
				case 'SalaryMax':
				    $_GET['salaryMax'] = $value;
				    break;
				case 'Page':
				    $_GET['from'] = $value;
				    break;
				case 'Keywords':
				    $_GET['keyword'] = $value;
				    break;
				case 'RecruiterId':
				    $_GET['recruiterId'] = $value;
				    break;
			    }
			}
		}

		// Fix wordpress epic fail 404 headers which would kill google searches
		header('HTTP/1.1 200 OK');
		header('Content-type: text/xml');

		

		self::createDocument();

	}

	public function createDocument() {
		$apiSettings = get_option('adlogic_api_settings');
		$rssSettings = get_option('adlogic_rss_settings');
		require_once('classes/jobSearch.class.php');
		if(isset($_GET['recruiterId']) && !empty($_GET['recruiterId'])) {
			$recruiterId = $_GET['recruiterId'];
		} else {
			$recruiterId = $apiSettings['adlogic_recruiter_id'];
		}

		$advertisersStr = $rssSettings['adlogic_rss_advertisers'];
		if(!isset($advertisersStr)||empty($advertisersStr)) {
			$advertisersStr = $apiSettings['adlogic_advertiser_id'];
		}
		$this->oRssFeed = new DOMDocument;
		$this->oRssFeed->preserveWhiteSpace = false;
		$this->oRssFeed->formatOutput = true;
		$this->oRssFeed->appendChild($this->oRssFeed->createElement('rss'));
		$this->oRssFeed->documentElement->setAttribute('version', '2.0');

		$oChannelEl = $this->oRssFeed->createElement('channel');

		$this->oRssFeed->documentElement->appendChild($oChannelEl);
		$oChannelEl->appendChild($this->oRssFeed->createElement('title', $rssSettings['adlogic_rss_title']));
		$oDescriptionEl = $this->oRssFeed->createElement('description');
		$oDescriptionEl->appendChild($this->oRssFeed->createCDATASection($rssSettings['adlogic_rss_description']));
		$oChannelEl->appendChild($oDescriptionEl);

		$oChannelEl->appendChild($this->oRssFeed->createElement('category', $rssSettings['adlogic_rss_category']));
		//$oChannelEl->appendChild($oRssFeed->createElement('link', RSS_FEED_LINK));
		$oChannelEl->appendChild($this->oRssFeed->createElement('language', 'en-AU'));
		$oCopyrightEl = $this->oRssFeed->createElement('copyright');
		$oCopyrightEl->appendChild($this->oRssFeed->createCDATASection($rssSettings['adlogic_rss_copyright']));
		$oChannelEl->appendChild($oCopyrightEl);
		if($rssSettings['adlogic_rss_hidetimestamp']=='true') {
			$oChannelEl->appendChild($this->oRssFeed->createElement('pubDate', date('D, d M Y')));
		} else {
			$oChannelEl->appendChild($this->oRssFeed->createElement('pubDate', date(DATE_RSS)));
		}
		$oChannelEl->appendChild($this->oRssFeed->createElement('docs', 'http://' . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI']));

		$oJobSearch = new JobSearch($this->oSoapClient, $advertisersStr, $recruiterId);
		// Search Criteria
		self::setSearchingParams($oJobSearch);

		// Get Job Search Results
		$oJobSearchResults = $oJobSearch->get();

		if(!empty($oJobSearchResults->JobPostings->JobPosting)) {
			self::createPostingBlockForRss($oJobSearchResults, $this->oRssFeed, $oChannelEl);
		}

		print($this->oRssFeed->saveXML());
	}

	public function setSearchingParams($oJobSearch) {
		// Search Keyword
		$keyword = ((isset($_GET['keyword']) && !empty($_GET['keyword'])) ?  $_GET['keyword']: '');
		// Salary Type
		$salaryType = ((isset($_GET['salaryType']) && !empty($_GET['salaryType'])) ?  $_GET['salaryType']: empty($_GET['salaryType']));
		// Minimum Salary
		$salaryMin = ((isset($_GET['salaryMin']) && !empty($_GET['salaryMin'])) ?  $_GET['salaryMin']: null);
		// Maximum Salary
		$salaryMax = ((isset($_GET['salaryMax']) && !empty($_GET['salaryMax'])) ?  $_GET['salaryMax']: null);

		// From Page
		$from = ((isset($_GET['from']) && !empty($_GET['from'])) ?  $_GET['from']: 1);
		// To Page
		$to = ((isset($_GET['to']) && !empty($_GET['to'])) ?  $_GET['to']: (isset($rssSettings['adlogic_rss_max_display_items']) ? $rssSettings['adlogic_rss_max_display_items'] : 50));

		// Build classification criteria parameters array (includes worktype, location, and industry)
		$aClassificationsCriteria = array();
		// Industry Id
		if ((isset($_GET['indId']) && !empty($_GET['indId']))) {
			$aClassificationsCriteria[] =  array($_GET['indId']);
		}
		// Location Id
		if ((isset($_GET['locId']) && !empty($_GET['locId']))) {
			$aClassificationsCriteria[] =  array($_GET['locId']);
		}
		// WorkType Id
		if((isset($_GET['wtId']) && !empty($_GET['wtId']))) {
			$aClassificationsCriteria[] =  array($_GET['wtId']);
		}
		/* Set Variables from GET Url Vars for Jobs Class*/
		$oJobSearch->set('keyword',					$keyword);
		$oJobSearch->set('classificationsCriteria',	$aClassificationsCriteria);
		$oJobSearch->set('salaryType',				$salaryType);
		$oJobSearch->set('salaryMax',				$salaryMax);
		$oJobSearch->set('salaryMin',				$salaryMin);
		$oJobSearch->set('from',					$from);
		$oJobSearch->set('to',						$to);
	}

	public function createPostingBlockForRss($oJobSearchResults, $oRssFeed, $oChannelEl) {
		global $wp_rewrite;
		$apiSettings = get_option('adlogic_api_settings');
		$rssSettings = get_option('adlogic_rss_settings');
		foreach ($oJobSearchResults->JobPostings->JobPosting as $oJobPosting) {
		    // Get JobPosting XML Attributes
		    $oJobAttributes = $oJobPosting->attributes();
		    $oJobItemEl = $oRssFeed->createElement('item');
		    $oJobTitleEl = $oRssFeed->createElement('title');
		    $oJobTitleEl->appendChild($oRssFeed->createCDATASection($oJobPosting->JobTitle));
		    $oJobItemEl->appendChild($oJobTitleEl);
		    $oDescriptionEl = $oRssFeed->createElement('description');
		    $oDescriptionEl->appendChild($oRssFeed->createCDATASection($oJobPosting->JobDescription));
		    $oJobItemEl->appendChild($oDescriptionEl);

		    $sLocationList = '';
		    $sLocationArray = array();
		    foreach ($oJobPosting->locations->location as $sLocation) {
			$sLocationList .= $sLocation->value . '/';
			$sLocationArray[] = $sLocation->value;
		    }
		    $sLocationList = substr($sLocationList, 0, -1);

		    $slicedLocationArray = array_slice($sLocationArray,0,2);
		    
		    $oJobItemEl->appendChild($oRssFeed->createElement('link', get_permalink($rssSettings['adlogic_job_details_page']) . ($wp_rewrite->using_permalinks() ? 'query/' : '&amp;/') . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($slicedLocationArray)) . '/' . $oJobAttributes->ad_id . '/'));
		    $oJobItemEl->appendChild($oRssFeed->createElement('guid', get_permalink($rssSettings['adlogic_job_details_page']) . ($wp_rewrite->using_permalinks() ? 'query/' : '&amp;/') . Adlogic_Job_Board::uriSafe($oJobPosting->JobTitle) . '/in/' . Adlogic_Job_Board::uriSafe(array_pop($slicedLocationArray)) . '/' . $oJobAttributes->ad_id . '/'));

				if($rssSettings['adlogic_rss_hidetimestamp']=='true') {
					$oJobItemEl->appendChild($oRssFeed->createElement('pubDate', date('D, d M Y', strtotime($oJobPosting->pubDate))));
				} else {
					$oJobItemEl->appendChild($oRssFeed->createElement('pubDate', date(DATE_RSS, strtotime($oJobPosting->pubDate))));
				}

		    $oLocationCategoryEl = $oRssFeed->createElement('category');
		    $oLocationCategoryEl->appendChild($oRssFeed->createCDATASection($sLocationList));
		    $oJobItemEl->appendChild($oLocationCategoryEl);

		    $sPositionList = '';
		    foreach ($oJobPosting->classifications->classification as $sClassification) {
			$sPositionList  .= $sClassification->value . '/';
		    }
		    $sPositionList = substr($sPositionList, 0, -1);

		    $oPositionCategoryEl = $oRssFeed->createElement('category');
		    $oPositionCategoryEl->appendChild($oRssFeed->createCDATASection($sPositionList));
		    $oJobItemEl->appendChild($oPositionCategoryEl);

		    $oChannelEl->appendChild($oJobItemEl);
		}
	}
}
?>
