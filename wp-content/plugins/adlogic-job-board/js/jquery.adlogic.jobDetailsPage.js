// This javascript file is to load adlogic default template specific extensions
// designed to enhance the job details templates
var hash = window.location.hash.split("#");
var count = 1;
while(count < hash.length) {
	if(hash[count] == "applicationComplete") {
		// We've got our completed application!
			$("#adlogic_job_application_complete").fadeIn(700);
			$("#adlogic_job_application_complete .close").click(function() {
				$("#adlogic_job_application_complete").fadeOut();
			});
			// Super hacky solution to hide the apply button.
			$("#adlogic_job_container button").each(function(i){
				var onclick = $(this).attr("onclick").toString();
				if(onclick.indexOf("openApplicationForm") > -1) {
					$(this).hide();
				}
			});
	}
	count++;
}

(function($) {
	$(document).ready(function() {
		// Check for an adlogic default template (if not, do nothing)
		if ($('.adlogic_job_details_container').length > 0) {
			/* 
			 * Delete empty standout bullets
			 * 
			 * The intention is to delete standout bullets that customers have not filled out in our adlogic backend
			 * or in case they do not use them at all.
			 * 
			 */
			
			// if standout bullets are empty, don't display them.
			$.each($('.adlogic_job_details_container ul.adlogic_job_details_bullet_ul li'), function(idx, liObj) {
				if ($(liObj).html().length == 0) {
					$(liObj).remove();
				}
			});
			// Check if standout UL is empty, if so delete it as well
			if ($('.adlogic_job_details_container ul.adlogic_job_details_bullet_ul li').length == 0) {
				$('.adlogic_job_details_container ul.adlogic_job_details_bullet_ul').remove();
			}

			/*
			 * Delete empty attachment placeholders if they exist in the template
			 * 
			 * The intention is to delete the placeholder div in the job details 
			 * template that contains attachments, if none are added to that specific job
			 */

			// Check for attached files if they exist (if not, do nothing)
			if ($('.adlogic_job_attachments').length > 0) {
				// If no files are attached, then delete the container div
				if ($('.adlogic_job_attachments .adlogic_attached_files').html().trim().length == 0) {
					$('.adlogic_job_attachments').remove();
				}
			}

			/*
			 * On mobile sites remove action bar if job has expired or there is no application job url/or is empty.
			 */
			
			if ($('.adlogic_job_details_container button.adlogic_job_details_apply').length > 0) {
				if($('.adlogic_job_details_container button.adlogic_job_details_apply').attr('onclick') == 'location.href=\'\'') {
					if ($('.adlogic_job_details_container div.adlogic_job_details_button_bar').length > 0) {
						$('.adlogic_job_details_container div.adlogic_job_details_button_bar').html('This position has now expired and is no longer available.');
					}
				}
			} else if ($('.adlogic_job_expired').length > 0) {
				if ($('.adlogic_job_details_container div.adlogic_job_details_button_bar').length > 0) {
					$('.adlogic_job_details_container div.adlogic_job_details_button_bar').html('This position has now expired and is no longer available.');
				}
			}
		}
		
		// Initialise Save Jobs Code
		// Get Saved Jobs
		jobDetailsGetSavedJobs();


		// Setup Saved Jobs events
		$(document).on('click', '.ajb-save-job', function(event) {
			jobAdId = parseInt($(this).attr('id').replace('save_job_id_',''));

			if (adlogicJobSearch.sessionManager.adlogicSessionManager('isLoggedIn')) {
				if ($(this).hasClass('saved')) {
					// Remove from saved jobs
					$.get(adlogicJobSearch.ajaxurl + '?action=removeSavedJob&jobAdId=' + jobAdId, function(data){
						return true;
					});
				} else {
					$.get(adlogicJobSearch.ajaxurl + '?action=addSavedJob&jobAdId=' + jobAdId, function(data){
						return true;
					});
				}

				$(this).toggleClass('saved');
			} else {
				adlogicJobSearch.sessionManager.adlogicSessionManager('showDialog');
			}
		});
		function test123() {
			alert('hello');
		}
		function jobDetailsGetSavedJobs() {
			// If refresh option set, get from server, else get from cache
			method = 'getSavedJobIds';
	
			$.get(adlogicJobSearch.ajaxurl + '?action=' + method, function(data) {
				if ($.isArray(data)) {
					$.each(data, function (idx,obj) {
						$('#save_job_id_' + obj).toggleClass('saved', true);
					});
				}
			});
		}
	});
})(jQuery);