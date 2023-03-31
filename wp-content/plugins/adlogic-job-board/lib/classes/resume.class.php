<?php 
require_once('html2ps/config.inc.php');
require_once('html2ps/pipeline.factory.class.php');
require_once('html2ps/memorypipeline.factory.class.php');

class Resume {

	static function generateLinkedInHtmlResume($linkedInData) {
		// Buffer HTML Output
		ob_start();
		?><!DOCTYPE PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
				<html>
					<head>
						<style type="text/css">
							@CHARSET "UTF-8";
							
							body {
								font-family: helvetica;
								font-face: helvetica;
								font: helvetica;
							}
							
							h2.main-subheading {
								padding-left: 100px;
								background-repeat: no-repeat;
								height: 80px;
							}
							
							h3 {
								margin: 20px 0 10px 0;
							}
							
							tbody.summary {
							}
							tbody.job_position_header {
								
							}
							tbody.job_position {
								
							}
							
							tr.odd {
								background-color: #EFEFEF;
							}
							
							td {
								vertical-align: top;
							}
							
							th {
								text-align: left;
							}
						</style> 
					</head>
					<body>
						<h1>LinkedIn Resume for <?php print $linkedInData->{'formatted-name'}; ?></h1>
						<h2 style="background-image: url(<?php print $linkedInData->{'picture-url'}; ?>);" class="main-subheading">About <a href="<?php print $linkedInData->{'public-profile-url'}; ?>"><?php print $linkedInData->{'formatted-name'}; ?></a></h2>
						<p>LinkedIn Profile last updated: <?php print date('d/m/Y', ($linkedInData->{'last-modified-timestamp'}/1000))?></p>
						<table cellspacing="0" border="0" cellpadding="1">
							<tbody class="summary">
							<tr><td colspan="2"><h3>Summary</h3></td></tr>
							<?php if ((isset($linkedInData->{'email-address'})) && (is_string($linkedInData->{'email-address'}))) : ?>
							<tr>
								<th>Email Address</th>
								<td><a href="mailto:<?php print $linkedInData->{'email-address'}; ?>"><?php print $linkedInData->{'email-address'}; ?></a></td>
							</tr>
							<?php endif; ?>
							<tr>
								<th>Profile URL</th>
								<td><a href="<?php print $linkedInData->{'public-profile-url'}; ?>"><?php print $linkedInData->{'public-profile-url'}; ?></a></td>
							</tr>
							<tr>
								<th>Location</th>
								<td><?php print ((is_string($linkedInData->location->name)) ? $linkedInData->location->name : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Industry</th>
								<td><?php print ((is_string($linkedInData->industry)) ? $linkedInData->industry : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Headline</th>
								<td><?php print ((is_string($linkedInData->headline)) ? $linkedInData->headline : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Summary</th>
								<td><?php print ((is_string($linkedInData->summary)) ? $linkedInData->summary : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Specialties</th>
								<td><?php print ((is_string($linkedInData->specialties)) ? $linkedInData->specialties : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Websites</th>
								<td>
									<?php if (is_array($linkedInData->{'member-url-resources'})): ?>
										<?php foreach($linkedInData->{'member-url-resources'} as $urlResource) :?>
										<p>
											<?php print $urlResource->name; ?><br/>
											<a href="<?php print $urlResource->url; ?>"><?php print $urlResource->url; ?></a>
										</p>
										<?php endforeach; ?>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<th>Interests</th>
								<td><?php print ((is_string($linkedInData->interests)) ? $linkedInData->interests : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Associations</th>
								<td><?php print ((is_string($linkedInData->associations)) ? $linkedInData->associations : 'N/A'); ?></td>
							</tr>
							</tbody>
							<tbody class="job_position_header">
							<tr><td colspan="2"><h3>Work Experience</h3></td></tr>
							</tbody>
								<tbody class="job_position">
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Job Title</th>
										<td><strong><?php print $linkedInData->positions->position->title; ?></strong></td>
									</tr>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Employer</th>
										<td><strong><?php print $linkedInData->positions->position->company->name; ?></strong></td>
									</tr>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Industry</th>
										<td><?php print $linkedInData->positions->position->company->industry; ?></td>
									</tr>
									<?php if (isset($linkedInData->positions->position->{'start-date'})) : ?>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Dates</th>
										<td>
											<?php
												$startDate = date('F Y', mktime(null, null, null, $linkedInData->positions->position->{'start-date'}->month,null, $linkedInData->positions->position->{'start-date'}->year));
												$endDate = ((isset($linkedInData->positions->position->{'end-date'})) ? date('F Y', mktime(null, null, null, $linkedInData->positions->position->{'end-date'}->month,null, $linkedInData->positions->position->{'end-date'}->year)) : 'Current');
												print $startDate . ' - ' . $endDate;
											?>
										</td>
									</tr>
									<?php endif;?>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Summary</th>
										<td><?php print ((is_string($linkedInData->summary)) ? $linkedInData->summary : 'N/A'); ?>&nbsp;</td>
									</tr>
									<tr class="job_position_row spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<tr><td colspan="2"><h3>Education/Qualifications</h3></td></tr>
							<?php foreach($linkedInData->educations->education as $i => $education) :?>
								<tbody class="education_qualification_section">
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Institution</th>
										<td><strong><?php print $education->{'school-name'}; ?></strong></td>
									</tr>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Qualification</th>
										<td><?php print ((is_string($education->degree)) ? $education->degree : 'N/A'); ?></td>
									</tr>
									<?php if (is_string($education->{'field-of-study'})) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Field of Study</th>
										<td><?php 
											print $education->{'field-of-study'}; 
											?></td>
									</tr>
									<?php endif; ?>
									<?php if (isset($education->{'start-date'})) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Dates</th>
										<td>
											<?php
												$startDate = date('Y', mktime(null, null, null, null,null, $education->{'start-date'}->year));
												$endDate = ((isset($education->{'end-date'})) ? date('Y', mktime(null, null, null, null, null, $education->{'end-date'}->year)) : 'Current');
												print $startDate . ' - ' . $endDate;
											?>
										</td>
									</tr>
									<?php endif;?>
									<?php if (is_string($education->activities)) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Activities</th>
										<td><?php print $education->activities; ?></td>
									</tr>
									<?php endif; ?>
									<?php if (is_string($education->notes)) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Notes</th>
										<td><?php print $education->notes; ?></td>
									</tr>
									<?php endif; ?>
									<tr class="education_qualification spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<?php endforeach;?>
							<tr><td colspan="2"><h3>Recommendations Received</h3></td></tr>
							<?php if (is_array($linkedInData->{'recommendations-received'}->recommendation)) : ?>
							<?php foreach($linkedInData->{'recommendations-received'}->recommendation as $i => $recommendation) :?>
								<tbody class="received_recommendations_section">
									<tr class="received_recommendation <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>From</th>
										<td><strong><?php print $recommendation->recommender->{'first-name'} . ' ' . $recommendation->recommender->{'last-name'}; ?></strong></td>
									</tr>
									<tr class="received_recommendation <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Type</th>
										<td><?php print $recommendation->{'recommendation-type'}->code; ?></td>
									</tr>
									<tr class="received_recommendation <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Comment</th>
										<td><?php print $recommendation->{'recommendation-text'}; ?></td>
									</tr>
									<tr class="received_recommendation spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<?php endforeach;?>
							<?php elseif (!empty($linkedInData->{'recommendations-received'}->recommendation)): ?>
								<tbody class="received_recommendations_section">
									<tr class="received_recommendation odd">
										<th>From</th>
										<td><strong><?php print $linkedInData->{'recommendations-received'}->recommendation->recommender->{'first-name'} . ' ' . $linkedInData->{'recommendations-received'}->recommendation->recommender->{'last-name'}; ?></strong></td>
									</tr>
									<tr class="received_recommendation odd">
										<th>Type</th>
										<td><?php print $linkedInData->{'recommendations-received'}->recommendation->{'recommendation-type'}->code; ?></td>
									</tr>
									<tr class="received_recommendation odd">
										<th>Comment</th>
										<td><?php print $linkedInData->{'recommendations-received'}->recommendation->{'recommendation-text'}; ?></td>
									</tr>
									<tr class="received_recommendation spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<?php else: ?>
							<tbody class="received_recommendations_section">
								<tr><td>No recommendations received</td></tr>
							</tbody>
							<?php endif; ?>
						</table>
					</body>
				</html><?php
		$htmlInput = ob_get_contents();
		ob_end_clean();
		$content = $htmlInput;
		return $content;
	}
static function generateLinkedInPopupHtmlResume($linkedInData) {
		// Buffer HTML Output
		ob_start();
		?><!DOCTYPE PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
				<html>
					<head>
						<style type="text/css">
							@CHARSET "UTF-8";
							
							body {
								font-family: helvetica;
								font-face: helvetica;
								font: helvetica;
							}
							
							h2.main-subheading {
								padding-left: 100px;
								background-repeat: no-repeat;
								height: 80px;
							}
							
							h3 {
								margin: 20px 0 10px 0;
							}
							
							tbody.summary {
							}
							tbody.job_position_header {
								
							}
							tbody.job_position {
								
							}
							
							tr.odd {
								background-color: #EFEFEF;
							}
							
							td {
								vertical-align: top;
							}
							
							th {
								text-align: left;
							}
						</style> 
					</head>
					<body>
						<h1>LinkedIn Resume for <?php print $linkedInData['formatted-name']; ?></h1>
						<h2 style="background-image: url(<?php print $linkedInData['picture-url']; ?>);" class="main-subheading">About <a href="<?php print $linkedInData['public-profile-url']; ?>"><?php print $linkedInData['formatted-name']; ?></a></h2>
						<p>LinkedIn Profile last updated: <?php print date('d/m/Y', ($linkedInData['last-modified-timestamp']/1000))?></p>
						<table cellspacing="0" border="0" cellpadding="1">
							<tbody class="summary">
							<tr><td colspan="2"><h3>Summary</h3></td></tr>
							<?php if ((isset($linkedInData['email-address'])) && (is_string($linkedInData['email-address']))) : ?>
							<tr>
								<th>Email Address</th>
								<td><a href="mailto:<?php print $linkedInData['email-address']; ?>"><?php print $linkedInData['email-address']; ?></a></td>
							</tr>
							<?php endif; ?>
							<tr>
								<th>Profile URL</th>
								<td><a href="<?php print $linkedInData['public-profile-url']; ?>"><?php print $linkedInData['public-profile-url']; ?></a></td>
							</tr>
							<tr>
								<th>Location</th>
								<td><?php print ((is_string($linkedInData['location']['name'])) ? $linkedInData['location']['name'] : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Industry</th>
								<td><?php print ((is_string($linkedInData['industry'])) ? $linkedInData['industry'] : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Headline</th>
								<td><?php ((is_string($linkedInData['headline'])) ? $linkedInData['headline'] : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Summary</th>
								<td><?php print ((is_string($linkedInData['summary'])) ? $linkedInData['summary'] : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Date of Birth</th>
								<td><?php print ((is_string($facebookData->birthday)) ? $facebookData->birthday : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Specialties</th>
								<td><?php print ((is_string($linkedInData['specialties'])) ? $linkedInData['specialties'] : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Websites</th>
								<td>
									<?php if (is_array($linkedInData['member-url-resources'])): ?>
										<?php foreach($linkedInData['member-url-resources'] as $urlResource) :?>
										<p>
											<?php print $urlResource->name; ?><br/>
											<a href="<?php print $urlResource['url']; ?>"><?php print $urlResource['url']; ?></a>
										</p>
										<?php endforeach; ?>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<th>Interests</th>
								<td><?php print ((is_string($linkedInData['interests'])) ? $linkedInData['interests'] : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Associations</th>
								<td><?php print ((is_string($linkedInData['associations'])) ? $linkedInData['associations'] : 'N/A'); ?></td>
							</tr>
							</tbody>
							<tbody class="job_position_header">
							<tr><td colspan="2"><h3>Work Experience</h3></td></tr>
							</tbody>
							<?php foreach($linkedInData['positions']['position'] as $i => $position) :?>
								<tbody class="job_position">
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Job Title</th>
										<td><strong><?php print $position['title']; ?></strong></td>
									</tr>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Employer</th>
										<td><strong><?php print $position['company']['name']; ?></strong></td>
									</tr>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Industry</th>
										<td><?php print $position['company']['industry']; ?></td>
									</tr>
									<?php if (isset($position['start-date'])) : ?>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Dates</th>
										<td>
											<?php
												$startDate = date('F Y', mktime(null, null, null, $position['start-date']['month'],null, $position['start-date']['year']));
												$endDate = ((isset($position['end-date'])) ? date('F Y', mktime(null, null, null, $position['end-date']['month'],null, $position['end-date']['year'])) : 'Current');
												print $startDate . ' - ' . $endDate;
											?>
										</td>
									</tr>
									<?php endif;?>
									<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Summary</th>
										<td><?php print ((is_string($linkedInData['summary'])) ? $linkedInData['summary'] : 'N/A'); ?>&nbsp;</td>
									</tr>
									<tr class="job_position_row spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<?php endforeach;?>
							<tr><td colspan="2"><h3>Education/Qualifications</h3></td></tr>
							<?php foreach($linkedInData['educations'] as $i => $education) :?>
								<tbody class="education_qualification_section">
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Institution</th>
										<td><strong><?php print $education['school-name']; ?></strong></td>
									</tr>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Qualification</th>
										<td><?php print ((is_string($education['degree'])) ? $education['degree'] : 'N/A'); ?></td>
									</tr>
									<?php if (is_string($education['field-of-study'])) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Field of Study</th>
										<td><?php 
											print $education['field-of-study']; 
											?></td>
									</tr>
									<?php endif; ?>
									<?php if (isset($education['start-date'])) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Dates</th>
										<td>
											<?php
												if(isset($education['start-date']['year'])) {
													$startDate = date('Y', mktime(null, null, null, null,null, $education['start-date']['year']));
												} else {
													$startDate = 'N/A';
												}
												if(isset($education['end-date']['year'])) {
													$endDate = date('Y', mktime(null, null, null, null, null, $education['end-date']['year']));
												} else {
													$endDate = 'Current';
												}
												print $startDate . ' - ' . $endDate;
											?>
										</td>
									</tr>
									<?php endif;?>
									<?php if (is_string($education['activities'])) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Activities</th>
										<td><?php print $education['activities']; ?></td>
									</tr>
									<?php endif; ?>
									<?php if (is_string($education['notes'])) : ?>
									<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Notes</th>
										<td><?php print $education['notes']; ?></td>
									</tr>
									<?php endif; ?>
									<tr class="education_qualification spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<?php endforeach;?>
							<tr><td colspan="2"><h3>Recommendations Received</h3></td></tr>
							<?php if (is_array($linkedInData['recommendations-received']['recommendation'])) : ?>
							<?php foreach($linkedInData['recommendations-received']['recommendation'] as $i => $recommendation) :?>
								<tbody class="received_recommendations_section">
									<tr class="received_recommendation <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>From</th>
										<td><strong><?php print $recommendation['recommender']['first-name'] . ' ' . $recommendation['recommender']['last-name']; ?></strong></td>
									</tr>
									<tr class="received_recommendation <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Type</th>
										<td><?php print $recommendation['recommendation-type']['code']; ?></td>
									</tr>
									<tr class="received_recommendation <?php print (($i%2) ? 'odd' : 'even'); ?>">
										<th>Comment</th>
										<td><?php print $recommendation['recommendation-text']; ?></td>
									</tr>
									<tr class="received_recommendation spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<?php endforeach;?>
							<?php elseif (!empty($linkedInData['recommendations-received']['recommendation'])): ?>
								<tbody class="received_recommendations_section">
									<tr class="received_recommendation odd">
										<th>From</th>
										<td><strong><?php print $linkedInData['recommendations-received']['recommendation']['recommender']['first-name'] . ' ' . $linkedInData['recommendations-received']['recommendation']['recommender']['last-name']; ?></strong></td>
									</tr>
									<tr class="received_recommendation odd">
										<th>Type</th>
										<td><?php print $linkedInData['recommendations-received']['recommendation']['recommendation-type']['code']; ?></td>
									</tr>
									<tr class="received_recommendation odd">
										<th>Comment</th>
										<td><?php print $linkedInData['recommendations-received']['recommendation']['recommendation-text']; ?></td>
									</tr>
									<tr class="received_recommendation spacer_row">
										<td colspan="2">&nbsp;</td>
									</tr>
								</tbody>
							<?php else: ?>
							<tbody class="received_recommendations_section">
								<tr><td>No recommendations received</td></tr>
							</tbody>
							<?php endif; ?>
						</table>
					</body>
				</html><?php
		$htmlInput = ob_get_contents();
		ob_end_clean();
		$content = $htmlInput;
		return $content;
	}
	static function generateFacebookHtmlResume($facebookData) {
		// Buffer HTML Output

		ob_start();
		// Get facebook picture url directly for html
		$url = 'http://graph.facebook.com/' . $facebookData->id .'/picture?width=80&height=80';
		if(function_exists("curl_init")){
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
		}
		else{
			$url_parts = parse_url($url);
			$sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80));
			$request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n";
			$request .= 'Host: ' . $url_parts['host'] . "\r\n";
			$request .= "Connection: Close\r\n\r\n";
			fwrite($sock, $request);
			$response = fread($sock, 2048);
			fclose($sock);
		}

		$header = "Location: ";
		$pos = strpos($response, $header);
		if($pos === false){
			$profile_picture = false;
		}
		else{
			$pos += strlen($header);
			$profile_picture = substr($response, $pos, strpos($response, "\r\n", $pos)-$pos);
		}

		?><!DOCTYPE PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
					<html>
						<head>
							<style type="text/css">
								@CHARSET "UTF-8";
								
								body {
									font-family: helvetica;
									font-face: helvetica;
									font: helvetica;
								}
								
								h2.main-subheading {
									padding-left: 100px;
									background-repeat: no-repeat;
									height: 80px;
								}
								
								h3 {
									margin: 20px 0 10px 0;
								}
								
								tbody.summary {
								}
								tbody.job_position_header {
									
								}
								tbody.job_position {
									
								}
								
								tr.odd {
									background-color: #EFEFEF;
								}
								
								td {
									vertical-align: top;
								}
								
								th {
									text-align: left;
								}
							</style> 
						</head>
						<body>
							<h1>Facebook Resume for <?php print $facebookData->name; ?></h1>
							<h2 style="background-image: url('<?php print $profile_picture; ?>');" class="main-subheading">About <a href="<?php print $facebookData->link; ?>"><?php print $facebookData->name; ?></a></h2>
							<p>Facebook Profile last updated: <?php print date('d/m/Y', strtotime($facebookData->updated_time)); ?></p>
							<table cellspacing="0" border="0" cellpadding="1">
								<tbody class="summary">
								<tr><td colspan="2"><h3>Summary</h3></td></tr>
								<?php if ((isset($facebookData->email)) && (is_string($facebookData->email))) : ?>
								<tr>
									<th>Email Address</th>
									<td><a href="mailto:<?php print $facebookData->email; ?>"><?php print $facebookData->email; ?></a></td>
								</tr>
								<?php endif; ?>
								<tr>
									<th>Profile URL</th>
									<td><a href="<?php print $facebookData->link; ?>"><?php print $facebookData->link; ?></a></td>
								</tr>
								<tr>
									<th>Location</th>
									<td><?php print ((is_string($facebookData->location->name)) ? $facebookData->location->name : 'N/A'); ?></td>
								</tr>
								<tr>
									<th>Summary</th>
									<td><?php print ((is_string($facebookData->bio)) ? $facebookData->bio : 'N/A'); ?></td>
								</tr>
								
								<tr>
									<th>Websites</th>
									<td>
										<?php if (!empty($facebookData->website)): ?>
												<a href="<?php print $facebookData->website; ?>"><?php print $facebookData->website; ?></a>
										<?php endif; ?>
									</td>
								</tr>
								</tbody>
								<tbody class="job_position_header">
								<tr><td colspan="2"><h3>Work Experience</h3></td></tr>
								</tbody>
								<?php foreach($facebookData->work as $i => $job) :?>
									<tbody class="job_position">
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Job Title</th>
											<td><strong><?php print $job->position->name; ?></strong></td>
										</tr>
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Employer</th>
											<td><strong><?php print $job->employer->name; ?></strong></td>
										</tr>
										<?php if (isset($job->location)) : ?>
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Location</th>
											<td><?php print $job->location->name; ?></td>
										</tr>
										<?php endif; ?>
										<?php if (isset($job->start_date)) : ?>
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Dates</th>
											<td>
												<?php
													$startDate = date('F Y', strtotime($job->start_date));
													$endDate = ((isset($job->end_date)) ? date('F Y', strtotime($job->end_date)) : 'Current');
													print $startDate . ' - ' . $endDate;
												?>
											</td>
										</tr>
										<?php endif;?>
										<?php if (isset($job->description)) : ?>
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Summary</th>
											<td><?php print $job->description; ?>&nbsp;</td>
										</tr>
										<?php endif; ?>
										<tr class="job_position_row spacer_row">
											<td colspan="2">&nbsp;</td>
										</tr>
									</tbody>
								<?php endforeach;?>
								<tr><td colspan="2"><h3>Education/Qualifications</h3></td></tr>
								<?php foreach($facebookData->education as $i => $education) :?>
									<tbody class="education_qualification_section">
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Institution</th>
											<td><strong><?php print $education->school->name; ?></strong></td>
										</tr>
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Education Type</th>
											<td><?php print $education->type; ?></td>
										</tr>
										<?php if (isset($education->degree)) : ?>
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Qualification</th>
											<td><?php print $education->degree->name; ?></td>
										</tr>
										<?php endif; ?>
										<?php if (isset($education->year)) : ?>
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>End Year</th>
											<td>
												<?php print $education->year->name; ?>
											</td>
										</tr>
										<?php endif;?>
										<?php if (isset($education->concentration)) : ?>
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Majors/Fields of Study</th>
											<td><?php foreach ($education->concentration as $concentration) { print '<p>' . $concentration->name . '</p>'; } ?></td>
										</tr>
										<?php endif; ?>
										<tr class="education_qualification spacer_row">
											<td colspan="2">&nbsp;</td>
										</tr>
									</tbody>
								<?php endforeach;?>
							</table>
						</body>
					</html><?php
			$htmlInput = ob_get_contents();
			ob_end_clean();
			$content = $htmlInput;
			return $content;
	}

	static function generatePdfString($htmlString, $fileName) {
		parse_config_file(dirname(__FILE__).'/html2ps/html2ps.config');

		global $g_config;
		$g_config = array(
				'cssmedia'		 => 'screen',
				'renderimages' => true,
				'renderforms'	=> false,
				'renderlinks'	=> true,
				'mode'				 => 'html',
				'debugbox'		 => false,
				'draw_page_border' => false
		);

		$media = Media::predefined('A4');
		$media->set_landscape(false);
		$media->set_margins(array('left'	 => 0,
				'right'	=> 0,
				'top'		=> 0,
				'bottom' => 0));
		$media->set_pixels(1024);
		global $g_px_scale;
		$g_px_scale = mm2pt($media->width() - $media->margins['left'] - $media->margins['right']) / $media->pixels;
		global $g_pt_scale;
		$g_pt_scale = $g_pt_scale * 1.43;
		$p = PipelineFactory::create_default_pipeline("","");

		
		$mp = MemoryPipelineFactory::create_default_pipeline("","", $htmlString, '');
		$mp->destination = new DestinationString($fileName);
		ob_start();
		@$mp->process('', $media);
		$pdfOutput = ob_get_contents();
		ob_end_clean();
		return $pdfOutput;
	}

	static function generateGooglePlusHtmlResume($googlePlusData) {
		// Buffer HTML Output
		ob_start();
		?><!DOCTYPE PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
				<html>
					<head>
						<style type="text/css">
							@CHARSET "UTF-8";
							
							body {
								font-family: helvetica;
								font-face: helvetica;
								font: helvetica;
							}
							
							h2.main-subheading {
								padding-left: 100px;
								background-repeat: no-repeat;
								height: 80px;
							}
							
							h3 {
								margin: 20px 0 10px 0;
							}
							
							tbody.summary {
							}
							tbody.job_position_header {
								
							}
							tbody.job_position {
								
							}
							
							tr.odd {
								background-color: #EFEFEF;
							}
							
							td {
								vertical-align: top;
							}
							
							th {
								text-align: left;
							}
						</style> 
					</head>
					<body>
						<h1>Google+ Resume for <?php print $googlePlusData->displayName; ?></h1>
						<h2 style="background-image: url('<?php print $googlePlusData->Google_Userinfo->picture; ?>?sz=80');" class="main-subheading">About <a href="<?php print $googlePlusData->url; ?>"><?php print $googlePlusData->displayName; ?></a></h2>
						<table cellspacing="0" border="0" cellpadding="1">
							<tbody class="summary">
							<tr><td colspan="2"><h3>Summary</h3></td></tr>
							<?php if ((isset($googlePlusData->Google_Userinfo->email)) && (is_string($googlePlusData->Google_Userinfo->email))) : ?>
							<tr>
								<th>Email Address</th>
								<td><a href="mailto:<?php print $googlePlusData->Google_Userinfo->email; ?>"><?php print $googlePlusData->Google_Userinfo->email; ?></a></td>
							</tr>
							<?php endif; ?>
							<tr>
								<th>Profile URL</th>
								<td><a href="<?php print $googlePlusData->url; ?>"><?php print $googlePlusData->url; ?></a></td>
							</tr>
							<tr>
								<th>Location</th>
								<td><?php
								if ($googlePlusData->placesLived) {
									$sPlaceLived = '';
									foreach ($googlePlusData->placesLived as $placeLived) {
										if ($placeLived->primary == true) {
											$sPlaceLived = $placeLived->value;
										}
									}
									print !empty($sPlaceLived) ? $sPlaceLived : $googlePlusData->placesLived[0]->value;
								} else {
									print 'N/A';
								} 
								?></td>
							</tr>
							<tr>
								<th>Tagline</th>
								<td><?php ((is_string($googlePlusData->tagline)) ? $googlePlusData->tagline : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Summary</th>
								<td><?php print ((is_string($googlePlusData->aboutMe)) ? $googlePlusData->aboutMe : 'N/A'); ?></td>
							</tr>
							<tr>
								<th>Websites</th>
								<td>
									<?php if (is_array($googlePlusData->urls)): ?>
										<?php foreach($googlePlusData->urls as $urlResource) :?>
										<p>
											<a href="<?php print $urlResource->value; ?>"><?php print $urlResource->value; ?></a>
										</p>
										<?php endforeach; ?>
									<?php endif; ?>
								</td>
							</tr>
							</tbody>
							<tbody class="job_position_header">
							<tr><td colspan="2"><h3>Work Experience</h3></td></tr>
							</tbody>
							<?php foreach($googlePlusData->organizations as $i => $position) :?>
								<?php if ($position->type == 'work'): ?>
									<tbody class="job_position">
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Job Title</th>
											<td><strong><?php print $position->title; ?></strong></td>
										</tr>
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Employer</th>
											<td><strong><?php print $position->name; ?></strong></td>
										</tr>
										<?php if (isset($position->startDate)) : ?>
										<tr class="job_position_row <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Dates</th>
											<td>
												<?php
													print $position->startDate  . ' - ' . ( $position->primary == true ? 'present': (!empty($position->endDate)? $position->endDate : 'present'));
												?>
											</td>
										</tr>
										<?php endif;?>
										<tr class="job_position_row spacer_row">
											<td colspan="2">&nbsp;</td>
										</tr>
									</tbody>
								<?php endif;?>
							<?php endforeach;?>
							<tr><td colspan="2"><h3>Education/Qualifications</h3></td></tr>
							<?php foreach($googlePlusData->organizations as $i => $education) :?>
								<?php if ($education->type == 'school'): ?>
									<tbody class="education_qualification_section">
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Institution</th>
											<td><strong><?php print $education->name; ?></strong></td>
										</tr>
										<?php if (is_string($education->title)) : ?>
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Field of Study</th>
											<td><?php 
												print $education->title; 
												?></td>
										</tr>
										<?php endif; ?>
										<?php if (isset($education->startDate)) : ?>
										<tr class="education_qualification <?php print (($i%2) ? 'odd' : 'even'); ?>">
											<th>Dates</th>
											<td>
												<?php
													print $education->startDate  . ' - ' . ( $education->primary == true ? 'present': (!empty($education->endDate)? $education->endDate : 'present'));
												?>
											</td>
										</tr>
										<?php endif;?>
										<tr class="education_qualification spacer_row">
											<td colspan="2">&nbsp;</td>
										</tr>
									</tbody>
								<?php endif; ?>
							<?php endforeach;?>
						</table>
					</body>
				</html><?php
		$htmlInput = ob_get_contents();
		ob_end_clean();
		$content = $htmlInput;
		return $content;
	}
}
?>