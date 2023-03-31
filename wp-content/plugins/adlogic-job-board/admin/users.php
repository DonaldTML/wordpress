<?php
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;

use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;
	
	class Adlogic_Job_Board_Users {

		static $oAdlogicUser;
		static $oFacebookUser;
		static $oLinkedInUser;
		static $oGooglePlusUser;
		static $oFacebook;
		static $oLinkedIn;
		static $oGooglePlus;
		static $oGoogleClient;
		static $sFacebookAuthUrl;
		static $sLinkedInAuthUrl;
		static $sGooglePlusAuthUrl;

		function init() {
			$Adlogic_Job_Board = new Adlogic_Job_Board();
			if (($Adlogic_Job_Board->check_setup() == true) && (self::isLoginEnabled()) ) {
				if (!is_admin() && !$Adlogic_Job_Board->is_login_page()) {
					$Adlogic_Job_Board->start_session();

					require_once(AJB_PLUGIN_PATH . '/lib/classes/adlogicUser.class.php');
					self::$oAdlogicUser = new AdlogicUser($Adlogic_Job_Board->getSoapConnection('candidate'));

					// Process API Logins
					add_action('wp', array('Adlogic_Job_Board_Users', 'processLogin'));

					// Load Linkedin and Facebook Javascript APIs
					//add_action('wp_footer', array('Adlogic_Job_Board_Users', 'load_api_javascript'));
	
					// Enqueue/Register Scripts required for User Login Screen
					//add_action('wp_enqueue_scripts', array('Adlogic_Job_Board_Users', 'register_scripts'));
				}
			}
		}

		function isLoggedInVia($type = null) {
			switch ($type) {
				case 'facebook':
					if (!empty(self::$oFacebookUser)) {
						return true;
					} else {
						return false;
					}
					break;
				case 'google':
					if (!empty(Adlogic_Job_Board_Users::$oGooglePlusUser)) {
						return true;
					} else {
						return false;
					}
					break;
				case 'linkedin':
					if (!empty(self::$oLinkedInUser)) {
						return true;
					} else {
						return false;
					}
					break;
				default:
					return false;
			}
		}

		static function isLoggedIn() {
			if (isset($_SESSION['adlogicUserSession'])) {
				return true;
			} else {
				return false;
			}
		}

		static function processLogin() {
			global $wp_rewrite;

			$apiSettings = get_option('adlogic_api_settings');

			//			session_destroy();
			//			die();
			// Check if user has a current session, if not kill session

			$uriParams = parse_url(get_permalink());
			
			if(!isset($uriParams["host"])) {
				return;
			}

			$redirectURI = (isset($uriParams['scheme'])? $uriParams['scheme'] : "http") . '://' . $uriParams["host"] . (isset($uriParams['port']) ? ':' . $uriParams['port'] : '') . $_SERVER['REQUEST_URI'];

			/*if (isset($_SESSION['adlogicUserSession'])) {
				if (!self::$oAdlogicUser->isLoggedIn($_SESSION['adlogicUserSession'])) {
					session_unset();
					session_destroy();
					session_write_close();
					setcookie(session_name(),'',0,'/');
					session_regenerate_id(true);
					header('Location:' . $redirectURI);
				}
			}*/

			// If Facebook is Enabled
			if (self::isFacebookEnabled()) {
				// 27th June 2016
				// If using a version of PHP older than 5.4, an exception can be thrown here and will cause the plugin to fail.
				// We now check and return early if this is the case.
				if (version_compare(PHP_VERSION, '5.4.0', '<')) {
					return;
				}
				if(!isset($_SESSION)) {
					session_start();
				}
				// Facebook OAuth
				/*$oFacebook = self::$oFacebook = new AJB_Facebook(
						array(
								'appId' => $apiSettings['adlogic_facebook_app_id'],
								'secret' => $apiSettings['adlogic_facebook_app_secret']
						)
				);*/
				$oFacebook = self::$oFacebook = FacebookSession::setDefaultApplication($apiSettings['adlogic_facebook_app_id'], $apiSettings['adlogic_facebook_app_secret']);

				// If Wordpress has defined proxy settings, then set Facebook to use these
				if (defined('WP_PROXY_HOST')) {
					$oFacebook->CURL_OPTS[CURLOPT_PROXY] = WP_PROXY_HOST;
				}

				if (defined('WP_PROXY_PORT')) {
					$oFacebook->CURL_OPTS[CURLOPT_PROXYPORT] = WP_PROXY_PORT;
				}

				if (defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
					$oFacebook->CURL_OPTS[CURLOPT_PROXYUSERPWD] = WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD;
				}

				// If we're running windows disable verification for SSL peers due to missing support in windows php curl installs
				if (PHP_OS == 'WINNT') {
					$oFacebook->CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
				}

				if(isset($_SESSION['Facebook']) && !$_SESSION['Facebook']['redirect_uri']) {
					$_SESSION['Facebook']['redirect_uri'] = $redirectURI . '#login_type=facebook';
				}
				
				$helper = new FacebookRedirectLoginHelper(get_bloginfo('url') . '/');
				
				try {
					if ( isset( $_SESSION['access_token'] ) ) {
				        // Check if an access token has already been set.
				        $session = new FacebookSession( $_SESSION['access_token'] );
				    } else {
				        // Get access token from the code parameter in the URL.
				        $session = $helper->getSessionFromRedirect();
				    }
				} catch( FacebookRequestException $ex ) {
					error_log('FacebookAuthAttemptException');
				    error_log(print_r($ex));
				} catch( \Exception $ex ) {
					// When validation fails or other local issues.
				    error_log('LocalException');
				    error_log(print_r($ex));
				}
				
				if ( isset( $session ) ) {
					// Retrieve & store the access token in a session.
				    $_SESSION['access_token'] = $session->getToken();
				    
				    try {
						$oFacebookUserRequest = new FacebookRequest( $session, 'GET', '/me' );
						$oFacebookUserIdResponse = $oFacebookUserRequest->execute();
						// get response
						$oFacebookUserIdGraphObject = $oFacebookUserIdResponse->getGraphObject();
						
						$oFacebookUserId = $oFacebookUserIdGraphObject;
						
						if($oFacebookUserId) {
							try {
								self::$oFacebookUser = $oFacebookUserId;
								
								$urlParams = parse_url($redirectURI);
								// Parse HTTP Query Variables into an array and remove oauth_token and oauth_verifier purely for checking purposes (to avoid duplicate urls in request token cache)
								$queryVars = array();
								parse_str($urlParams['query'], $queryVars);
								if (isset($queryVars['code']) && isset($queryVars['state'])) {
									unset($queryVars['code'], $queryVars['state']);
									$urlParams['query'] = http_build_query($queryVars);
									$requestUrl = $urlParams['scheme'] . '://' . $urlParams['host'] . (!empty($urlParams['port']) ? ':' . $urlParams['port'] : '') . $urlParams['path'] . (!empty($urlParams['query']) ? '?' . $urlParams['query'] : '');
									header('Location: ' . $_SESSION['Facebook']['redirect_uri']);
								}
								
							} catch (FacebookApiException $e) {
								error_log($e);
								$user = null;
							}
						}
						
						
					} catch (FacebookRequestException $ex) {
						error_log('FacebookRequestException');
					    error_log(print_r($ex));
					} catch (\Exception $ex) {
						error_log('Unknown Exception on Facebook Auth');
					    error_log(print_r($ex));
					}
					
					
				} else {
					$permissions_needed = 'user_about_me,user_birthday,user_education_history,user_location,user_website,user_work_history,email';
					self::$sFacebookAuthUrl = $helper->getLoginUrl(array('scope' => $permissions_needed));
				}
				
				
				
				
				
				
				
				/*$oFacebookUserId = $oFacebook->getUser();

				if ($oFacebookUserId) {
					try {
						// Proceed knowing you have a logged in user who's authenticated.
						self::$oFacebookUser = (object) $oFacebook->api('/me');
						// Check if URL has Facebook Auth Parameters enabled, if so remove them and redirect
						// Build request Url
						$urlParams = parse_url($redirectURI);
						
						// Parse HTTP Query Variables into an array and remove oauth_token and oauth_verifier purely for checking purposes (to avoid duplicate urls in request token cache)
						$queryVars = array();
						parse_str($urlParams['query'], $queryVars);
						if (isset($queryVars['code']) && isset($queryVars['state'])) {
							unset($queryVars['code'], $queryVars['state']);
							$urlParams['query'] = http_build_query($queryVars);
							$requestUrl = $urlParams['scheme'] . '://' . $urlParams['host'] . (!empty($urlParams['port']) ? ':' . $urlParams['port'] : '') . $urlParams['path'] . (!empty($urlParams['query']) ? '?' . $urlParams['query'] : '');
							header('Location: ' . $requestUrl . '#login_type=facebook');
						}
					} catch (FacebookApiException $e) {
						error_log($e);
						$user = null;
					}
				} else {
					$permissions_needed = 'publish_stream,user_about_me,user_birthday,user_education_history,user_interests,user_location,user_website,user_work_history,email';
					self::$sFacebookAuthUrl = $oFacebook->getLoginUrl(array('scope' => $permissions_needed, 'display' => 'page'));
				}*/
			}

			if (self::isGooglePlusEnabled()) {
				// Google+ OAuth
				$oGoogleClient = self::$oGoogleClient = new Google_Client();

				// If Wordpress has defined proxy settings, then set Google Client to use these
				$googleCurlOpts = array();
				if (defined('WP_PROXY_HOST')) {
					$googleCurlOpts[CURLOPT_PROXY] = WP_PROXY_HOST;
				}
				
				if (defined('WP_PROXY_PORT')) {
					$googleCurlOpts[CURLOPT_PROXYPORT] = WP_PROXY_PORT;
				}
				
				if (defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
					$googleCurlOpts[CURLOPT_PROXYUSERPWD] = WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD;
				}

				$oGoogleClient->getIo()->setOptions($googleCurlOpts);
				$oGoogleClient->setApplicationName("Adlogic Job Board for Wordpress");
				$oGoogleClient->setClientId($apiSettings['adlogic_google_client_id']);
				$oGoogleClient->setClientSecret($apiSettings['adlogic_google_client_secret']);
				$oGoogleClient->setUseObjects(true);
				$oGoogleClient->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/plus.login'));

				if ($wp_rewrite->using_permalinks()) {
					$oGoogleClient->setRedirectUri(home_url() . '/adlogic-jobs/callback');
				} else {
					$oGoogleClient->setRedirectUri(home_url() . '/' . basename($_SERVER['SCRIPT_FILENAME']) . '/adlogic-jobs/callback');
				}

				// Set Google Client state to contain redirect data json_encoded and base64_encoded
				$oGoogleClient->setState(base64_encode(json_encode(array('redirect_url' => $redirectURI))));

				// Instantiate Google+ Service
				$oGooglePlus = self::$oGooglePlus = new Google_PlusService($oGoogleClient);

				// Get Auth Url
				self::$sGooglePlusAuthUrl = $oGoogleClient->createAuthUrl();

				// Instantiate Google_Oauth2Service to grab additional details from user
				$oGoogleOauth2Service = new Google_Oauth2Service($oGoogleClient);

				if (isset($_GET['code']) && isset($_GET['state'])) {
					// Check if state is an array - if not, is likely to be a Facebook authentication
					$oAuthState = json_decode(base64_decode($_GET['state']));

					if (is_object($oAuthState) && !empty($oAuthState)) {
						// Authenticate using the code received from Google
						$oGoogleClient->authenticate($_GET['code']);
						// Get permanent Access Token and store in Session
						$_SESSION['google'] = array('access_token' => $oGoogleClient->getAccessToken());

						if (isset($oAuthState->redirect_url)) {
							header('Location:' . $oAuthState->redirect_url . '#login_type=google-plus');
							exit(0);
						}
					}
				}

				// If access token exists then set it
				if (isset($_SESSION['google']['access_token'])) {
					$oGoogleClient->setAccessToken($_SESSION['google']['access_token']);
				}
				// Check if access token is valid and retrieve user details
				if ($oGoogleClient->getAccessToken()) {
					// Get Google+ account details
					self::$oGooglePlusUser = $oGooglePlus->people->get('me');
					// Grab additional user details from google
					self::$oGooglePlusUser->Google_Userinfo = $oGoogleOauth2Service->userinfo->get();
				}
			}

			// If linkedin is enabled
			if (self::isLinkedInEnabled()) {
				// Build request Url
				$urlParams = parse_url($redirectURI);

				// Parse HTTP Query Variables into an array and remove oauth_token and oauth_verifier purely for checking purposes (to avoid duplicate urls in request token cache)
				$queryVars = array();
				if(isset($urlParams['query']) && !empty($urlParams['query'])) {
					parse_str($urlParams['query'], $queryVars);
				}
				unset($queryVars['oauth_token'], $queryVars['oauth_verifier']);
				$urlParams['query'] = http_build_query($queryVars);

				$requestUrl = $urlParams['scheme'] . '://' . $urlParams['host'] . (!empty($urlParams['port']) ? ':' . $urlParams['port'] : '') . $urlParams['path'] . (!empty($urlParams['query']) ? '?' . $urlParams['query'] : '');
				// LinkedIn OAuth
				$oLinkedIn = self::$oLinkedIn = new LinkedIn($apiSettings['adlogic_linkedin_api_key'], $apiSettings['adlogic_linkedin_api_secret'], $requestUrl);
				
				if($apiSettings['adlogic_linkedin_type'] == 'basic' || ($apiSettings['adlogic_linkedin_type'] == '')) {
					$oLinkedIn->request_token_path = $oLinkedIn->secure_base_url . "/uas/oauth/requestToken?scope=r_basicprofile+r_emailaddress";
				} else {
					$oLinkedIn->request_token_path = $oLinkedIn->secure_base_url . "/uas/oauth/requestToken?scope=r_fullprofile+r_emailaddress";
				}

				// If Wordpress has defined proxy settings, then set LinkedIn to use these
				if (defined('WP_PROXY_HOST')) {
					$oLinkedIn->curl_opts[CURLOPT_PROXY] = WP_PROXY_HOST;
				}
				
				if (defined('WP_PROXY_PORT')) {
					$oLinkedIn->curl_opts[CURLOPT_PROXYPORT] = WP_PROXY_PORT;
				}
				
				if (defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
					$oLinkedIn->curl_opts[CURLOPT_PROXYUSERPWD] = WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD;
				}

				// Disable verification for SSL Peers in Windows due to likely missing ssl certificate tables
				if (PHP_OS == 'WINNT') {
					$oLinkedIn->curl_opts[CURLOPT_SSL_VERIFYPEER] = false;
				}
				//session_destroy();
				//die();

				// Create request token array if it doesn't exist
				if (!isset($_SESSION['linkedIn']['request_tokens'])) {
					$_SESSION['linkedIn']['request_tokens'] = array();
				}

				// Check whether we have a valid linkedIn Access Token
				if (!isset($_SESSION['linkedIn']['access_token'])) {
					/* 
					 * If we do not have a valid access token, we need to generate relevant LinkedIn auth request URL for the 
					 * current page if one hasn't been generated in the past 5 minutes. We start this by getting a request token.
					 */

					// Check if we've got an existing token and if it's valid (ie. no longer than 5 mins old)
					if (isset($_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]) && ($_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['request_time'] > (time()-1800))) {
						self::$sLinkedInAuthUrl = $_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['auth_url'];
						//print 'old!' . "\n";
						//print self::$sLinkedInAuthUrl;
						
						// Have we got a oauth_token and oauth_verifier token? If so exchange for an access token
						if ((isset($_GET['oauth_token']) && (isset($_GET['oauth_verifier'])))) {
							// Get the old request token used to get the oauth_token
							$oLinkedIn->request_token    =   unserialize($_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['token']);
							// Set the oauth_verifier
							$oLinkedIn->oauth_verifier   =   $_GET['oauth_verifier'];
							// Get the Access Token
							$oLinkedIn->getAccessToken($_GET['oauth_verifier']);

							// Check the access token is valid, if so store the access token in the session, along with the request token and verifier token
							if ($oLinkedIn->access_token->key != NULL) {
								$_SESSION['linkedIn']['access_token']	= serialize($oLinkedIn->access_token);
								$_SESSION['linkedIn']['request_token']	= $_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['token'];
								$_SESSION['linkedIn']['token']			= $_GET['oauth_token'];
								$_SESSION['linkedIn']['verifier']		= $_GET['oauth_verifier'];
								// Refresh the page without the tokens in the url
								header('Location: ' . $requestUrl . '#login_type=linkedIn');
								exit;
							} else {
								// If it is not valid then request another request token
								$oLinkedIn->getRequestToken();
								$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)] = array();
								$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['token'] = serialize($oLinkedIn->request_token);
								$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['auth_url'] = self::$sLinkedInAuthUrl = $oLinkedIn->generateAuthorizeUrl();
								$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['redirect_url'] = $requestUrl;
								$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['request_time'] = time();
								// TODO: add some way of handling invalid sessions
							} 
						}
					} else {
						// If a valid token has not been found, then request one
						$oLinkedIn->getRequestToken();
						$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)] = array();
						$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['token'] = serialize($oLinkedIn->request_token);
						$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['auth_url'] = self::$sLinkedInAuthUrl = $oLinkedIn->generateAuthorizeUrl();
						$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['redirect_url'] = $requestUrl;
						$_SESSION['linkedIn']['request_tokens'][base64_encode($requestUrl)]['request_time'] = time();
						//print 'new!' . "\n";
						//print self::$sLinkedInAuthUrl;
					}
				} else {
					$oLinkedIn->request_token    =   unserialize($_SESSION['linkedIn']['request_token']);
					$oLinkedIn->oauth_verifier   =   $_SESSION['linkedIn']['oauth_verifier'];
					$oLinkedIn->access_token     =   unserialize($_SESSION['linkedIn']['access_token']);
					// Because we have an access token, we can grab the user's profile
					if ($oLinkedIn->access_token->key) {
						$sProfileXML = $oLinkedIn->getProfile('~:(id,first-name,last-name,formatted-name,email-address,headline,industry,location,summary,phone-numbers,date-of-birth,main-address,picture-url,site-standard-profile-request,public-profile-url,last-modified-timestamp,specialties,associations,honors,interests,languages,skills,certifications,courses,recommendations-received,positions,educations,member-url-resources)');
						self::$oLinkedInUser = simplexml_load_string($sProfileXML);
						// We need to yank out the LinkedIn Profile Id as the User Id as different API keys will give different user ids.
						$profileUrl = (string) self::$oLinkedInUser->{'site-standard-profile-request'}->url;
						$profileVars = array();
						parse_str(parse_url($profileUrl, PHP_URL_QUERY), $profileVars);
						
						// Swap variables around
						if (self::$oLinkedInUser) {
							self::$oLinkedInUser->api_user_id = self::$oLinkedInUser->id;
							self::$oLinkedInUser->id = $profileVars['id'];
						}
					}
				}
			}

			// Check if user is logged in using Facebook or Linked in, if so, either login and update profile, or register a new user account
			if (self::$oLinkedInUser || self::$oFacebookUser || self::$oGooglePlusUser) {
				
				// Set some variables here, getProperty throws an error when using empty
				if(!empty(self::$oFacebookUser)) {
					$oFacebookId = self::$oFacebookUser->getProperty('id');
					$oFacebookName = self::$oFacebookUser->getProperty('name');
					$oFacebookEmail = self::$oFacebookUser->getProperty('email');
				}
				
				$aUserDetails = array(
						'linkedin_id'		=> (!empty(self::$oLinkedInUser->id) ? (string) self::$oLinkedInUser->id: null),
						'linkedin_name'		=> (!empty(self::$oLinkedInUser->{'formatted-name'}) ? (string) self::$oLinkedInUser->{'formatted-name'} : null),
						'linkedin_email'	=> (!empty(self::$oLinkedInUser->{'email-address'}) ? (string) self::$oLinkedInUser->{'email-address'} : null),
						'facebook_id'		=> (!empty($oFacebookId) ? self::$oFacebookUser->getProperty('id') : null),
						'facebook_name'		=> (!empty($oFacebookName) ? self::$oFacebookUser->getProperty('name') : null),
						'facebook_email'	=> (!empty($oFacebookEmail) ? self::$oFacebookUser->getProperty('email') : null),
						'google_id'			=> (!empty(self::$oGooglePlusUser->id) ? self::$oGooglePlusUser->id : null),
						'google_name'		=> (!empty(self::$oGooglePlusUser->displayName) ? self::$oGooglePlusUser->displayName : null),
						'google_email'		=> (!empty(self::$oGooglePlusUser->Google_Userinfo->email) ? self::$oGooglePlusUser->Google_Userinfo->email : null)
				);
	
				if (self::$oLinkedInUser) {
					$aUserDetails['name'] = (string) self::$oLinkedInUser->{'first-name'};
					$aUserDetails['surname'] = (string) self::$oLinkedInUser->{'last-name'};
					$aUserDetails['email'] = (string) self::$oLinkedInUser->{'email-address'};
				} else if (self::$oFacebookUser) {
					$aUserDetails['name'] = self::$oFacebookUser->getProperty('first_name');
					$aUserDetails['surname'] = self::$oFacebookUser->getProperty('last_name');
					$aUserDetails['email'] = self::$oFacebookUser->getProperty('email');
				} else if (self::$oGooglePlusUser) {
					$aUserDetails['name'] = self::$oGooglePlusUser->name->givenName;
					$aUserDetails['surname'] = self::$oGooglePlusUser->name->familyName;
					$aUserDetails['email'] = self::$oGooglePlusUser->Google_Userinfo->email;
				}

				if (isset($_SESSION['adlogicUserSession'])) {
					/*
					 *  Here is where we verify whether a users's profile has been updated in anyway
					 *  or whether the user has logged in with an account they have not logged in with before
					 */

					$userDetails = self::$oAdlogicUser->get();

					$needUpdate = false;
					if (
							((!empty($aUserDetails['facebook_id'])) && ($userDetails->facebook_id != $aUserDetails['facebook_id'])) || // Facebook Id Changed/Added
							((!empty($aUserDetails['facebook_email'])) && ($userDetails->facebook_email != $aUserDetails['facebook_email'])) || // Facebook Email Changed/Added
							((!empty($aUserDetails['linkedin_id'])) && ($userDetails->linkedin_id != $aUserDetails['linkedin_id'])) || // LinkedIn Id Changed/Added
							((!empty($aUserDetails['linkedin_email'])) && ($userDetails->linkedin_email != $aUserDetails['linkedin_email'])) || // LinkedIn Email Changed/Added
							((!empty($aUserDetails['google_id'])) && ($userDetails->google_id != $aUserDetails['google_id'])) || // Google Id Changed/Added
							((!empty($aUserDetails['google_email'])) && ($userDetails->google_email != $aUserDetails['google_email'])) // Google Email Changed/Added
						) {
						$needUpdate = true;
					}

					if ($needUpdate == true) {
						// Update any details have changed on facebook/linkedin and update user profile accordingly
						if ((isset($aUserDetails['linkedin_name'])) && (!empty($aUserDetails['linkedin_name']))) {
							self::$oAdlogicUser->set('name', (string) self::$oLinkedInUser->{'first-name'});
							self::$oAdlogicUser->set('surname', (string) self::$oLinkedInUser->{'last-name'});
							self::$oAdlogicUser->set('email', (string) self::$oLinkedInUser->{'email-address'});
						} else if ((isset($aUserDetails['facebook_name'])) && (!empty($aUserDetails['facebook_name']))) {
							self::$oAdlogicUser->set('name', (string) self::$oFacebookUser->getProperty('first_name'));
							self::$oAdlogicUser->set('surname', (string) self::$oFacebookUser->getProperty('last_name'));
							self::$oAdlogicUser->set('email', (string) self::$oFacebookUser->getProperty('email'));
						} else if ((isset($aUserDetails['google_name'])) && (!empty($aUserDetails['google_name']))) {
							self::$oAdlogicUser->set('name', (string) self::$oGooglePlusUser->name->givenName);
							self::$oAdlogicUser->set('surname', (string) self::$oGooglePlusUser->name->familyName);
							self::$oAdlogicUser->set('email', (string) self::$oGooglePlusUser->Google_Userinfo->email);
						}

						if (!empty(self::$oLinkedInUser->id)) {
							self::$oAdlogicUser->set('linkedin_id', (string) self::$oLinkedInUser->id);
						}

						if (!empty(self::$oLinkedInUser->{'email-address'})) {
							self::$oAdlogicUser->set('linkedin_email', (string) self::$oLinkedInUser->{'email-address'});
						}
						
						if (!empty($oFacebookId)) {
							self::$oAdlogicUser->set('facebook_id', (string) self::$oFacebookUser->getProperty('id'));
						}

						if (!empty($oFacebookEmail)) {
							self::$oAdlogicUser->set('facebook_email', (string) self::$oFacebookUser->getProperty('email'));
						}

						if (!empty(self::$oGooglePlusUser->id)) {
							self::$oAdlogicUser->set('google_id', (string) self::$oGooglePlusUser->id);
						}
						
						if (!empty(self::$oGooglePlusUser->Google_Userinfo->email)) {
							self::$oAdlogicUser->set('google_email', (string) self::$oGooglePlusUser->Google_Userinfo->email);
						}

						self::$oAdlogicUser->update();
					}
				} else if (($aUserDetails['linkedin_id'] == NULL) && ($aUserDetails['facebook_id'] == NULL) && ($aUserDetails['google_id'] == NULL)) {
					// User not logged into anything
				} else {
					$sessionHash = self::$oAdlogicUser->login($aUserDetails);
					
	
					if ($sessionHash != false) {
						$_SESSION['adlogicUserSession'] = $sessionHash;
					} else {
						// User doesn't exist - register new user
						$_SESSION['adlogicUserSession'] = self::$oAdlogicUser->register($aUserDetails);
						//die('died');
					}
				}
			}
		}

		function login() {
			self::$oAdlogicUser->login();
		}

		function register() {
			self::$oAdlogicUser->register();
		}

		static function generateLoginDialog() {
			if (self::isLoginEnabled() == false) {
				return;
			}

			// Enqueue Javascript
			wp_enqueue_script( 'jquery-adlogic-users' );
			// Enqueue Stylesheet
			wp_enqueue_style( 'adlogic-user-dialog' );

			$oFacebookUser = self::$oFacebookUser;
			$oLinkedInUser = self::$oLinkedInUser;
			$oGooglePlusUser = self::$oGooglePlusUser;

			?>
			<script type="text/javascript">
			(function($) {
				$(document).ready(function() {
					$('.adlogic_job_board_logon').adlogicSessionManager({ 'loggedIn': <?php print (self::isLoggedIn() ? 'true' : 'false'); ?> });
				});
			})(jQuery);
			</script>
			<div class="adlogic_job_board_logon" style="display:none;" title="Sign In">
			<?php
				print '<div class="message"></div>';
				if ((!$oFacebookUser) && (!$oLinkedInUser) &&  (!$oGooglePlusUser)) {
					print '<div class="status">You are currently not signed in. Please sign in using the options below</div>';
				}

				if (self::isFacebookEnabled()) {
					if ($oFacebookUser) {
						print 'Welcome ' . $oFacebookUser->getProperty('name') . '<br/>';
						print 'You are logged in using Facebook ';
					} else {
						print '<a href="' . self::$sFacebookAuthUrl . '" class="social-media-login facebook">Login Using Facebook</a> ';
					}
				}
				if (self::isLinkedInEnabled()) {
					if ($oLinkedInUser) {
						print 'You are logged in using LinkedIn ';
						print 'Id: ' . $oLinkedInUser->id . '<br/>';
					} else {
						print '<a href="' . self::$sLinkedInAuthUrl . '" class="social-media-login linkedin">Login Using LinkedIn</a>';
					}
				}

				if (self::isGooglePlusEnabled()) {
					if ($oGooglePlusUser) {
						print 'You are logged in using Google+ ';
						print 'Id: ' . $oGooglePlusUser->id . '<br/>';
					} else {
						print '<a href="' . self::$sGooglePlusAuthUrl . '" class="social-media-login google-plus">Login Using Google+</a>';
					}
				}
			?>
			</div>
			<?php
		}

		static function isLoginEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			// Check that either a pair of LinkedIn attributes or Facebook attributes are available
			if (
					(isset($apiSettings['adlogic_candidate_soap_server'])) &&
					(((!empty($apiSettings['adlogic_facebook_app_id'])) && (!empty($apiSettings['adlogic_facebook_app_secret']))) ||
					((!empty($apiSettings['adlogic_linkedin_api_key'])) && (!empty($apiSettings['adlogic_linkedin_api_secret'])))) ||
					((!empty($apiSettings['adlogic_google_client_id'])) && (!empty($apiSettings['adlogic_google_client_secret'])))
				) {
				return true;
			} else {
				return false;
			}

		}

		static function isFacebookEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			// Check if Facebook is Enabled
			if (((!empty($apiSettings['adlogic_facebook_app_id'])) && (!empty($apiSettings['adlogic_facebook_app_secret'])))) {
				// Facebook Enabled
				return true;
			} else {
				return false;
			}
		}

		static function isLinkedInEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			//Check if LinkedIn is Enabled
			if ((!empty($apiSettings['adlogic_linkedin_api_key'])) && (!empty($apiSettings['adlogic_linkedin_api_secret']))) {
				// LinkedIn Enabled
				return true;
			} else {
				return false;
			}
		}

		static function isGooglePlusEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			//Check if Google+ is Enabled
			if ((!empty($apiSettings['adlogic_google_client_id'])) && (!empty($apiSettings['adlogic_google_client_secret']))) {
				// Google+ Enabled
				return true;
			} else {
				return false;
			}
		}
		
		static function isGoogleDriveEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			//Check if Google+ is Enabled
			if ((!empty($apiSettings['adlogic_google_drive_client_id'])) && (!empty($apiSettings['adlogic_google_drive_api_key']))) {
				// Google+ Enabled
				return true;
			} else {
				return false;
			}
		}
		
		static function isDropboxEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			//Check if Google+ is Enabled
			if ((!empty($apiSettings['adlogic_dropbox_api_key'])) && (!empty($apiSettings['adlogic_dropbox_api_key']))) {
				// Google+ Enabled
				return true;
			} else {
				return false;
			}
		}
		
		static function isOneDriveEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			//Check if OneDrive is Enabled
			if ((!empty($apiSettings['adlogic_onedrive_api_key'])) && (!empty($apiSettings['adlogic_onedrive_api_key']))) {
				// OneDrive Enabled
				return true;
			} else {
				return false;
			}
		}
		
		static function isIndeedEnabled() {
			$apiSettings = get_option('adlogic_api_settings');
			//Check if Indeed is Enabled
			if ((!empty($apiSettings['adlogic_indeed_key'])) && (!empty($apiSettings['adlogic_indeed_secret_key']))) {
				// Indeed Enabled
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Function getAuthUrl
		 * 
		 * Returns authentication url for different social media login platforms if enabled
		 * 
		 * @param string $type
		 * @param string $redirectUrl
		 * @return boolean|string
		 */
		function getAuthUrl($type=null, $redirectUrl=null) {
			// Current url is default url if no redirectUrl passed
			if (empty($redirectUrl)) {
				$uriParams = parse_url(get_permalink());
				$redirectUrl = $uriParams['scheme'] . '://' . $uriParams['host'] . ($uriParams['port'] ? ':' . $uriParams['port'] : '') . $_SERVER['REQUEST_URI'];
			}

			switch ($type) {
				case 'facebook':
					if (self::isFacebookEnabled()) {
						// Facebook OAuth
						$oFacebook = self::$oFacebook;

						$permissions_needed = 'publish_stream,user_about_me,user_birthday,user_education_history,user_interests,user_location,user_website,user_work_history,email';
						$aLoginParams = array('scope' => $permissions_needed, 'display' => 'page');

						if (!empty($redirectUrl)) {
							$aLoginParams['redirect_uri'] = $redirectUrl;
						}

						$authUrl = $oFacebook->getLoginUrl($aLoginParams);
					} else {
						return false;
					}
					break;
				case 'linkedin':
					$apiSettings = get_option('adlogic_api_settings');
					if (self::isLinkedInEnabled()) {

						// Build request Url
						$urlParams = parse_url($redirectUrl);

						// Parse HTTP Query Variables into an array and remove oauth_token and oauth_verifier purely for checking purposes (to avoid duplicate urls in request token cache)
						$queryVars = array();
						parse_str($urlParams['query'], $queryVars);
						unset($queryVars['oauth_token'], $queryVars['oauth_verifier']);
						$urlParams['query'] = http_build_query($queryVars);

						$redirectUrl = $urlParams['scheme'] . '://' . $urlParams['host'] . (!empty($urlParams['port']) ? ':' . $urlParams['port'] : '') . $urlParams['path'] . (!empty($urlParams['query']) ? '?' . $urlParams['query'] : '');

						$oLinkedIn = new LinkedIn($apiSettings['adlogic_linkedin_api_key'], $apiSettings['adlogic_linkedin_api_secret'], $redirectUrl);
						
						if($apiSettings['adlogic_linkedin_type'] == 'basic' || ($apiSettings['adlogic_linkedin_type'] == '')) {
							$oLinkedIn->request_token_path = $oLinkedIn->secure_base_url . "/uas/oauth/requestToken?scope=r_basicprofile+r_emailaddress";
						} else {
							$oLinkedIn->request_token_path = $oLinkedIn->secure_base_url . "/uas/oauth/requestToken?scope=r_fullprofile+r_emailaddress";
						}
						
						// If Wordpress has defined proxy settings, then set LinkedIn to use these
						if (defined('WP_PROXY_HOST')) {
							$oLinkedIn->curl_opts[CURLOPT_PROXY] = WP_PROXY_HOST;
						}
						
						if (defined('WP_PROXY_PORT')) {
							$oLinkedIn->curl_opts[CURLOPT_PROXYPORT] = WP_PROXY_PORT;
						}
						
						if (defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')) {
							$oLinkedIn->curl_opts[CURLOPT_PROXYUSERPWD] = WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD;
						}
						
						// Disable verification for SSL Peers in Windows due to likely missing ssl certificate tables
						if (PHP_OS == 'WINNT') {
							$oLinkedIn->curl_opts[CURLOPT_SSL_VERIFYPEER] = false;
						}

						if (!isset($_SESSION['linkedIn']['request_tokens'])) {
							$_SESSION['linkedIn']['request_tokens'] = array();
						}

						// Check if we've got an existing token and if it's valid (ie. no longer than 5 mins old)
						if (isset($_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)]) && ($_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)]['request_time'] > (time()-1800))) {
							$authUrl = $_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)]['auth_url'];

							return $authUrl;
						} else {
							// If a valid token has not been found, then request one
							$oLinkedIn->getRequestToken();
							$_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)] = array();
							$_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)]['token'] = serialize($oLinkedIn->request_token);
							$_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)]['auth_url'] = $authUrl = $oLinkedIn->generateAuthorizeUrl();
							$_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)]['redirect_url'] = $redirectUrl;
							$_SESSION['linkedIn']['request_tokens'][base64_encode($redirectUrl)]['request_time'] = time();
							return $authUrl;
						}
					} else {
						return false;
					}
					break;
				case 'google':
					if (self::isGooglePlusEnabled()) {
						// Google+ OAuth
						$oGoogleClient = self::$oGoogleClient;

						// Set Google Client state to contain redirect data json_encoded and base64_encoded
						$oGoogleClient->setState(base64_encode(json_encode(array('redirect_url' => $redirectUrl))));
						
						// Get Auth Url
						$authUrl = $oGoogleClient->createAuthUrl();
					} else {
						return false;
					}
					break;
				default:
					return false;
			}
			return $authUrl;
		}

		function load_api_javascript() {
			// Enqueue LinkedIn and Facebook Javascript Codes
			$apiSettings = get_option('adlogic_api_settings');
			// Check if Facebook is Enabled
			if (((!empty($apiSettings['adlogic_facebook_app_id'])) && (!empty($apiSettings['adlogic_facebook_app_secret'])))) {
				// Facebook Enabled
				?>
				<script>
					(function($){
						$.getScript(document.location.protocol + '//connect.facebook.net/en_US/all.js');
							$('<div />').attr('id','fb-root').prependTo('body');
							window.fbAsyncInit = function () {
								// init the FB JS SDK
								FB.init({
									appId		: '<?php print $apiSettings['adlogic_facebook_app_id']; ?>', // App ID from the App Dashboard
									channelUrl 	: adlogicJobSearch.ajaxurl.replace('http://', '//') + '?action=facebook_channel', // Channel File for x-domain communication									status		: true, // check the login status upon init?
									cookie		: true, // set sessions cookies to allow your server to access the session?
									oauth		: true,
									status		: true,
									xfbml		: true  // parse XFBML tags on this page?
								});
							};
					})(jQuery);
				</script>
				<?php
			
			}
			if ((!empty($apiSettings['adlogic_linkedin_api_key'])) && (!empty($apiSettings['adlogic_linkedin_api_secret']))) {
				// LinkedIn Enabled
				?>
				<script type="text/javascript" src="http://platform.linkedin.com/in.js">
					api_key: <?php print $apiSettings['adlogic_linkedin_api_key'];?>
				
					credentials_cookie: true
				
					authorize: true
				</script>
				<?php 
			}
		}
	}
?>