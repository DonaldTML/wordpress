(function($) {
	var defaults = {
			'data': null,
			'job_ad_id': null,
			'tracking_code': null,
			'validate_candidate_email': true,
            'use_system_fields': false
		}

	var validFileTypeRegExp = new RegExp('\.(doc|docx|txt|pdf|rtf|htm|html|ppt|pptx|gif|png|jpg)$', 'i');

	var options = $.extend(defaults, options);

	var self;

	var methods = {
			init : function( options ) { 
				var options = $.extend(defaults, options);
				thisObj = this;
				self = $(this);
				// Submit application button click
				this.find('input#ajb-submitApplication').click(function() {
					methods.submit.apply(thisObj, [{ button: this }]);
				});
				// Submit SMS Button Click
				this.find('input#ajb-sendSMS').click(function() {
					methods.smsSend.apply(thisObj, [{ button: this }]);
				});

				// Email to Self Button Click
				this.find('input#ajb-sendEmail').click(function() {
					methods.emailSend.apply(thisObj, [{ button: this, type: 'self' }]);
				});

				// Email to Friend Button Click
				this.find('input#ajb-sendEmailFriend').click(function() {
					methods.emailSend.apply(thisObj, [{ button: this, type: 'friend' }]);
				});
				
				this.find('.ajb_field_file_multiple a.add_file').click(function() {
					if ($(this).parent().find('input').length < 5) {
						newFile = $('<p></p>');
						newFile.html($($(this).parent().find('input').get(0)).clone().val(''));
						newFile.append('<a href="javascript:void(0);" class="remove_file">Remove File</a>');
						$(this).before(newFile);
						if ($(this).parent().find('input').length == 5) {
							$(this).hide();
						}
					}
				});

				this.on('click', '.ajb_field_file_multiple a.remove_file', function() {
					if ($(this).parents('.ajb_field_file_multiple').find('input').length == 5) {
						$(this).parents('.ajb_field_file_multiple').find('a.add_file').show();
					}
					$(this).parent('p').remove();
				});
				// Initialize month picker if it's available.
				var findDateMonthYearField = $(".ajb_field_date_month_year");
				
				// Initialize date picker if it's available
				var findDateField = $(".ajb_field_date");
				
				$(findDateField).each(function(i){
					$(this).datepicker({
						dateFormat: 'dd/mm/yy',
						changeYear: true,
						yearRange: "-30:+0"
					});
				});
				$(findDateMonthYearField).each(function(i){
					var finalVal = '';
					var selectedMonth = '';
					$(this).MonthPicker({
						ShowIcon: true,
						OnAfterMenuClose: function() {
							var selectedMonth = $(findDateMonthYearField[i]).MonthPicker('GetSelectedMonth');
							switch(selectedMonth) {
								case 1:
									selectedMonth = '01'
									break;
								case 2:
									selectedMonth = '02'
									break;
								case 3:
									selectedMonth = '03'
									break;
								case 4:
									selectedMonth = '04'
									break;
								case 5:
									selectedMonth = '05'
									break;
								case 6:
									selectedMonth = '06'
									break;
								case 7:
									selectedMonth = '07'
									break;
								case 8:
									selectedMonth = '08'
									break;
								case 9:
									selectedMonth = '09'
									break;
								case 10:
									selectedMonth = '10'
									break;
								case 11:
									selectedMonth = '11'
									break;
								case 12:
									selectedMonth = '12'
									break;	
							}
							finalVal = selectedMonth + '/' + $(findDateMonthYearField[i]).MonthPicker('GetSelectedYear');
							$(this).val(finalVal);
						} 
						
					});
					$("#ajb-submitApplication").click(function(){
						$(findDateMonthYearField[i]).val(finalVal);
					});
					
				});
				var findMultiSelect = $(".ajb_field_select_multiple");
				$(findMultiSelect).each(function(i) {
					var a = $("option", $(this));
					if(a.length >= 5) {
						// If more than x amount of options are available, display the search option
						$(this).multiSelect({
							selectableHeader: "<input type='text' class='search-input' autocomplete='off'>",
							selectionHeader: "<input type='text' class='search-input' autocomplete='off'>",
							afterInit: function(ms){
							var that = this,
							    $selectableSearch = that.$selectableUl.prev(),
							    $selectionSearch = that.$selectionUl.prev(),
							    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
							    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
							
							that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
							.on('keydown', function(e){
							  if (e.which === 40){
							    that.$selectableUl.focus();
							    return false;
							  }
							});
							
							that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
							.on('keydown', function(e){
							  if (e.which == 40){
							    that.$selectionUl.focus();
							    return false;
							  }
							});
							},
							afterSelect: function(){
							this.qs1.cache();
							this.qs2.cache();
							},
							afterDeselect: function(){
							this.qs1.cache();
							this.qs2.cache();
							}
						});
					} else {
						$(this).multiSelect();
					}
					
				});
				
				if (options.indeed_application_success == true) {
					methods.indeedApplicationSuccess.apply(thisObj);
				}
				$(document).trigger('adlogicJobSearch.jobApplication.init', [self, options]);
			},
			options : function( opt ) {
				if (opt) {
					options = $.extend(true, options, opt);
				}

				return options;
			},
			dropboxFileInfo : function( oProfile ) {
				if (oProfile) {
					options = $.extend(true, options, { 'data':  oProfile });
				}
			},
			// TO-DO
			// Move dropbox into this function
			thirdPartyFileInfo : function( oProfile ) {
				if (oProfile) {
					options = $.extend(true, options, { 'data':  oProfile });
				}
			},
			onLogin : function( oProfile ) {
				if (oProfile) {
					options = $.extend(true, options, { 'data':  oProfile });

					applicationForm = this.children('.ajb-application-details');

					// Grab params from url to determine which form to show if parameter is sent
					if (!$.isEmptyObject( $.deparam.fragment() )) {
						fragParams = $.deparam.fragment()
						if (typeof($.deparam.fragment().login_type) != 'undefined') {
							this.find('#ajb-profile-source').val($.deparam.fragment().login_type);
						}
					}

					switch (this.find('#ajb-profile-source').val()) {
						case 'linkedIn_Account':
							// Hide the entire form because they have already applied for the job.
							$('.ajb-job-application').hide();
							$('.ajb-application-acknowledgement').fadeIn('slow');
						break;
						case 'linkedin':
							// Hide Apply via Indeed Button
							$('.indeedApplyContainer').toggle(false);
							// Show application form
							applicationForm.find('.ajb-application-form').toggle(true);
							// Show profile box
							applicationForm.find('.ajb-profile-box').toggle(true);
							// Hide file upload fields
							applicationForm.find('.ajb-resume-upload').toggle(false);
							// Pre-populate form with known information
							if(options.use_system_fields == false) {
								applicationForm.find('#ajb-first-name').val(oProfile.linkedIn['first-name']);
								applicationForm.find('#ajb-last-name').val(oProfile.linkedIn['last-name']);
								if (typeof(oProfile.linkedIn['email-address']) != 'undefined') {
									applicationForm.find('#ajb-email-address').val(oProfile.linkedIn['email-address']);
								}
							} else {
								applicationForm.find('[name="SystemFirstName"]').val(oProfile.linkedIn['first-name']);
								applicationForm.find('[name="SystemLastName"]').val(oProfile.linkedIn['last-name']);
								// Populate an email address if the field is available
								if (typeof(oProfile.linkedIn['email-address']) != 'undefined') {
									applicationForm.find('[name="SystemEmailAddress"]').val(oProfile.linkedIn['email-address']);
								}
							}
							applicationForm.find('.ajb-profile-box').append('<a href="' + oProfile.linkedIn['public-profile-url'] + '"><img src="' + oProfile.linkedIn['picture-url'] + '" target="_blank"></a>');
							applicationForm.find('.ajb-profile-box').append('<span class="linkedin_username">' + oProfile.linkedIn['formatted-name'] + '</span>');
							applicationForm.find('.ajb-profile-box').append('<span class="linkedin_headline">' + (oProfile.linkedIn.headline ? oProfile.linkedIn.headline : '' ) + '</span>');
							applicationForm.find('.ajb-profile-box').append('<span class="resume_preview"><a href="javascript:void(0);">Preview Resume (PDF)</a></span>');
							applicationForm.fadeIn();
							break;
						case 'facebook':
							// Hide Apply via Indeed Button
							$('.indeedApplyContainer').toggle(false);
							// Show application form
							applicationForm.find('.ajb-application-form').toggle(true);
							// Show profile box
							applicationForm.find('.ajb-profile-box').toggle(true);
							// Hide resume upload fields
							applicationForm.find('.ajb-resume-upload').toggle(false);
							// Pre-populate form with known information
							if(options.use_system_fields == false) {
								applicationForm.find('#ajb-first-name').val(oProfile.facebook['first_name']);
								applicationForm.find('#ajb-last-name').val(oProfile.facebook['last_name']);
								if (typeof(oProfile.facebook['email']) != 'undefined') {
									applicationForm.find('#ajb-email-address').val(oProfile.facebook['email']);
								}
							} else {
								applicationForm.find('[name="SystemFirstName"]').val(oProfile.facebook['first_name']);
								applicationForm.find('[name="SystemLastName"]').val(oProfile.facebook['last_name']);
								// Populate an email address if the field is available
								if (typeof(oProfile.facebook['email']) != 'undefined') {
									applicationForm.find('[name="SystemEmailAddress"]').val(oProfile.facebook['email']);
								}
							}
							applicationForm.find('.ajb-profile-box').append('<a href="' + oProfile.facebook['link'] + '"><img src="https://graph.facebook.com/' + oProfile.facebook['id'] + '/picture?width=80&height=80&return_ssl_resources=1" target="_blank"></a>');
							applicationForm.find('.ajb-profile-box').append('<span class="facebook_username">' + oProfile.facebook['name'] + '</span>');
							applicationForm.find('.ajb-profile-box').append('<span class="facebook_headline">' + (oProfile.facebook['bio'] ? oProfile.facebook['bio'] : '') + '</span>');
							applicationForm.find('.ajb-profile-box').append('<span class="resume_preview"><a href="javascript:void(0);">Preview Resume (PDF)</a></span>');
							applicationForm.fadeIn();
							break;
						case 'google-plus':
							// Hide Apply via Indeed Button
							$('.indeedApplyContainer').toggle(false);
							// Show application form
							applicationForm.find('.ajb-application-form').toggle(true);
							// Show profile box
							applicationForm.find('.ajb-profile-box').toggle(true);
							// Hide resume upload fields
							applicationForm.find('.ajb-resume-upload').toggle(false);
							// Pre-populate form with known information
							if(options.use_system_fields == false) {
								applicationForm.find('#ajb-first-name').val(oProfile.googlePlus.name.givenName);
								applicationForm.find('#ajb-last-name').val(oProfile.googlePlus.name.familyName);
								if (typeof(oProfile.googlePlus.Google_Userinfo.email) != 'undefined') {
									applicationForm.find('#ajb-email-address').val(oProfile.googlePlus.Google_Userinfo.email);
								}
							} else {
								applicationForm.find('[name="SystemFirstName"]').val(oProfile.googlePlus.name.givenName);
								applicationForm.find('[name="SystemLastName"]').val(oProfile.googlePlus.name.familyName);
								// Populate an email address if the field is available
								if (typeof(oProfile.googlePlus.Google_Userinfo.email) != 'undefined') {
									applicationForm.find('[name="SystemEmailAddress"]').val(oProfile.googlePlus.Google_Userinfo.email);
								}
							}
							applicationForm.find('.ajb-profile-box').append('<a href="' + oProfile.googlePlus.url + '"><img src="' + oProfile.googlePlus.Google_Userinfo.picture + '?sz=80" target="_blank"></a>');
							applicationForm.find('.ajb-profile-box').append('<span class="googlePlus_username">' + oProfile.googlePlus.displayName + '</span>');
							applicationForm.find('.ajb-profile-box').append('<span class="googlePlus_headline">' + (oProfile.googlePlus.tagline ? oProfile.googlePlus.tagline : '') + '</span>');
							applicationForm.find('.ajb-profile-box').append('<span class="resume_preview"><a href="javascript:void(0);">Preview Resume (PDF)</a></span>');
							applicationForm.fadeIn();
							break;
						case 'upload':
							// Hide Apply via Indeed Button
							$('.indeedApplyContainer').toggle(false);
							// Show application form
							applicationForm.find('.ajb-application-form').toggle(true);
							// Hide profile box
							applicationForm.find('.ajb-profile-box').toggle(false);
							// Show resume upload inputs
							applicationForm.find('.ajb-resume-upload').toggle(true);
							applicationForm.fadeIn();
							break;
						case 'indeed':
							// Show Apply via Indeed Button
							$('.indeedApplyContainer').toggle(true);
							// Hide profile box
							applicationForm.find('.ajb-profile-box').toggle(false);
							// Hide profile box
							applicationForm.find('.ajb-profile-box').toggle(false);
							// Hide application form
							applicationForm.find('.ajb-application-form').toggle(false);
							break;
					}

					self = $(this);

					this.find('#ajb-profile-source').change(function(o,e) {
						switch ($(this).val()) {
							case 'linkedin':
								// Hide Apply via Indeed Button
								$('.indeedApplyContainer').toggle(false);
								// Show application form
								applicationForm.find('.ajb-application-form').toggle(true);
								// Show profile box
								applicationForm.find('.ajb-profile-box').toggle(true);
								// Hide resume upload fields
								applicationForm.find('.ajb-resume-upload').toggle(false);
								// Pre-populate form with known information
								if(options.use_system_fields == false) {
									applicationForm.find('#ajb-first-name').val(oProfile.linkedIn['first-name']);
									applicationForm.find('#ajb-last-name').val(oProfile.linkedIn['last-name']);
									if (typeof(oProfile.linkedIn['email-address']) != 'undefined') {
										applicationForm.find('#ajb-email-address').val(oProfile.linkedIn['email-address']);
									}
								} else {
									applicationForm.find('[name="SystemFirstName"]').val(oProfile.linkedIn['first-name']);
									applicationForm.find('[name="SystemLastName"]').val(oProfile.linkedIn['last-name']);
									// Populate an email address if the field is available
									if (typeof(oProfile.linkedIn['email-address']) != 'undefined') {
										applicationForm.find('[name="SystemEmailAddress"]').val(oProfile.linkedIn['email-address']);
									}
								}
								applicationForm.find('.ajb-profile-box').empty();
								applicationForm.find('.ajb-profile-box').append('<a href="' + oProfile.linkedIn['public-profile-url'] + '"><img src="' + oProfile.linkedIn['picture-url'] + '" target="_blank"></a>');
								applicationForm.find('.ajb-profile-box').append('<span class="linkedin_username">' + oProfile.linkedIn['formatted-name'] + '</span>');
								applicationForm.find('.ajb-profile-box').append('<span class="linkedin_headline">' + (oProfile.linkedIn.headline ? oProfile.linkedIn.headline : '' ) + '</span>');
								applicationForm.find('.ajb-profile-box').append('<span class="resume_preview"><a href="javascript:void(0);">Preview Resume (PDF)</a></span>');
								applicationForm.fadeIn();
								$(document).trigger('adlogicJobSearch.jobApplication.profile.load', [self, options]);
								break;
							case 'facebook':
								// Hide Apply via Indeed Button
								$('.indeedApplyContainer').toggle(false);
								// Show application form
								applicationForm.find('.ajb-application-form').toggle(true);
								// Show profile box
								applicationForm.find('.ajb-profile-box').toggle(true);
								// Hide resume upload fields
								applicationForm.find('.ajb-resume-upload').toggle(false);
								// Pre-populate form with known information
								if(options.use_system_fields == false) {
									applicationForm.find('#ajb-first-name').val(oProfile.facebook['first_name']);
									applicationForm.find('#ajb-last-name').val(oProfile.facebook['last_name']);
									if (typeof(oProfile.facebook['email']) != 'undefined') {
										applicationForm.find('#ajb-email-address').val(oProfile.facebook['email']);
									}
								} else {
									applicationForm.find('[name="SystemFirstName"]').val(oProfile.facebook['first_name']);
									applicationForm.find('[name="SystemLastName"]').val(oProfile.facebook['last_name']);
									// Populate an email address if the field is available
									if (typeof(oProfile.facebook['email']) != 'undefined') {
										applicationForm.find('[name="SystemEmailAddress"]').val(oProfile.facebook['email']);
									}
								}
								
								applicationForm.find('.ajb-profile-box').empty();
								applicationForm.find('.ajb-profile-box').append('<a href="' + oProfile.facebook['link'] + '"><img src="https://graph.facebook.com/' + oProfile.facebook['id'] + '/picture?width=80&height=80&return_ssl_resources=1" target="_blank"></a>');
								applicationForm.find('.ajb-profile-box').append('<span class="facebook_username">' + oProfile.facebook['name'] + '</span>');
								applicationForm.find('.ajb-profile-box').append('<span class="facebook_headline">' + (oProfile.facebook['bio'] ? oProfile.facebook['bio'] : '') + '</span>');
								applicationForm.find('.ajb-profile-box').append('<span class="resume_preview"><a href="javascript:void(0);">Preview Resume (PDF)</a></span>');
								applicationForm.fadeIn();
								$(document).trigger('adlogicJobSearch.jobApplication.profile.load', [self, options]);
								break;
							case 'google-plus':
								// Hide Apply via Indeed Button
								$('.indeedApplyContainer').toggle(false);
								// Show application form
								applicationForm.find('.ajb-application-form').toggle(true);
								// Show profile box
								applicationForm.find('.ajb-profile-box').toggle(true);
								// Hide resume upload fields
								applicationForm.find('.ajb-resume-upload').toggle(false);
								// Pre-populate form with known information
								
								if(options.use_system_fields == false) {
									applicationForm.find('#ajb-first-name').val(oProfile.googlePlus.name.givenName);
									applicationForm.find('#ajb-last-name').val(oProfile.googlePlus.name.familyName);
									if (typeof(oProfile.googlePlus.Google_Userinfo.email) != 'undefined') {
										applicationForm.find('#ajb-email-address').val(oProfile.googlePlus.Google_Userinfo.email);
									}
								} else {
									applicationForm.find('[name="SystemFirstName"]').val(oProfile.googlePlus.name.givenName);
									applicationForm.find('[name="SystemLastName"]').val(oProfile.googlePlus.name.familyName);
									// Populate an email address if the field is available
									if (typeof(oProfile.googlePlus.Google_Userinfo.email) != 'undefined') {
										applicationForm.find('[name="SystemEmailAddress"]').val(oProfile.googlePlus.Google_Userinfo.email);
									}
								}
								applicationForm.find('.ajb-profile-box').empty();
								applicationForm.find('.ajb-profile-box').append('<a href="' + oProfile.googlePlus.url + '"><img src="' + oProfile.googlePlus.Google_Userinfo.picture + '?sz=80" target="_blank"></a>');
								applicationForm.find('.ajb-profile-box').append('<span class="googlePlus_username">' + oProfile.googlePlus.displayName + '</span>');
								applicationForm.find('.ajb-profile-box').append('<span class="googlePlus_headline">' + (oProfile.googlePlus.tagline ? oProfile.googlePlus.tagline : '') + '</span>');
								applicationForm.find('.ajb-profile-box').append('<span class="resume_preview"><a href="javascript:void(0);">Preview Resume (PDF)</a></span>');
								applicationForm.fadeIn();
								$(document).trigger('adlogicJobSearch.jobApplication.profile.load', [self, options]);
								break;
							case 'upload':
								// Hide Apply via Indeed Button
								$('.indeedApplyContainer').toggle(false);
								// Show application form
								applicationForm.find('.ajb-application-form').toggle(true);
								// Hide profile box
								applicationForm.find('.ajb-profile-box').toggle(false);
								// Show resume upload inputs
								applicationForm.find('.ajb-resume-upload').toggle(true);
								applicationForm.fadeIn();
								$(document).trigger('adlogicJobSearch.jobApplication.profile.upload', [self, options]);
								break;
							case 'indeed':
								// Show Apply via Indeed Button
								$('.indeedApplyContainer').toggle(true);
								// Hide profile box
								applicationForm.find('.ajb-profile-box').toggle(false);
								// Hide profile box
								applicationForm.find('.ajb-profile-box').toggle(false);
								// Hide application form
								applicationForm.find('.ajb-application-form').toggle(false);
								$(document).trigger('adlogicJobSearch.jobApplication.indeed.apply', [self, options]);
								break;
						}
					});

					this.on('click', '.resume_preview', function(event) {
						// get resume event
						submitObject = {};

						switch (applicationForm.find('#ajb-profile-source').val()) {
							case 'linkedin':
								submitObject.linkedInData = linkedin_profile_datastring;
								break;
							case 'facebook':
								submitObject.facebookData = facebook_profile_datastring;
								break;
							case 'google-plus':
								submitObject.googlePlusData = google_plus_profile_datastring;
								break;
						}

						$.download(adlogicJobSearch.ajaxurl + '?action=getResume', submitObject, 'post');
					});
					return true;
				} else {
					return false;
				}

			},
			smsSend: function() {
				mobileNumber = this.find('#ajb-mobile-input').val();
				if (/(^1300\d{6}$)|(^1800|1900|1902\d{6}$)|(^0[2|3|7|8]{1}[0-9]{8}$)|(^13\d{4}$)|(^04\d{2,3}\d{6}$)/i.test(mobileNumber)) {
					submitObject = {
							jobTitle:		options.job_title,
							jobAdUrl:		options.sms_job_url,
							mobileNumber:	mobileNumber
					};
					$.ajax(adlogicJobSearch.ajaxurl + '?action=sendJobSMS', {
						data: submitObject,
						type: 'post',
						success: function(data) {
							alert('SMS Sent!');
							$(document).trigger('adlogicJobSearch.jobApplication.smsSent', [self, options]);
						}
					});
				} else {
					alert('Invalid mobile number! Please try again. Numbers must not contain spaces or brackets.')
				}
			} ,
			emailSend: function(paramsObj) {
				yourEmailAddress = this.find('#ajb-your-email-send-address').val();
				if (paramsObj.type == 'self') {
					emailAddress = this.find('#ajb-email-send-address').val();
				} else {
					emailAddress = this.find('#ajb-friend-email-send-address').val();
				}

				var validate = false;

				if (/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(emailAddress)) {
					validate = true;
				} else {
					alert('Invalid email address. Please check the email address and try again!')
					validate = false;
				}

				if ((validate == true) && (paramsObj.type == 'friend')) {
					if (/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(yourEmailAddress)) {
						validate = true;
					} else {
						alert('Your email address is invalid. Please check your email address and try again!')
						validate = false;
					}
				}
				
				if (validate == true) {
					if (paramsObj.type == 'friend') {
						submitObject = {
								jobTitle:			options.job_title,
								jobAdUrl:			options.email_job_url,
								yourEmailAddress:	yourEmailAddress,
								emailAddress:		emailAddress,
								content:			$('#ajb-message-input').val()
						};
					} else {
						submitObject = {
								jobTitle:			options.job_title,
								jobAdUrl:			options.job_url,
								emailAddress:		emailAddress
						};
					}
	
					$.ajax(adlogicJobSearch.ajaxurl + '?action=sendJobEmail', {
						data: submitObject,
						type: 'post',
						success: function(data) {
							alert('Email Sent!');
							$(document).trigger('adlogicJobSearch.jobApplication.emailSent', [self, options]);
						}
					});
				}
			} ,
			submit : function(params) {
				applicationForm = this.children('.ajb-application-details');
                                
                                if(options.use_system_fields == 'false') {
                                	submitObject = {
                                            jobAdId:				options.job_ad_id,
                                            firstName:				applicationForm.find('#ajb-first-name').val(),
                                            lastName:				applicationForm.find('#ajb-last-name').val(),
                                            emailAddress:			applicationForm.find('#ajb-email-address').val(),
                                            phoneNumber:			applicationForm.find('#ajb-phone-number').val(),
                                            comments:				applicationForm.find('#ajb-comments').val(),
                                            applicationCriteria:	new Array(),
                                            customFieldData:		new Array(),
                                            trackingCode:			options.tracking_code,
                                            validate_candidate_email: options.validate_candidate_email,
                                            advId: 					'',
                                            is_jobkey:   			''
                                    }
                                    if(options.is_job == 'true') {
                                    	submitObject.advId = options.adv_id;
                                    	submitObject.is_job = options.is_job;
                                    }
                                    
                                } else {
                                	submitObject = {
                                            jobAdId:				options.job_ad_id,
                                            firstName:				applicationForm.find('#ajb-first-name').val(),
                                            lastName:				applicationForm.find('#ajb-last-name').val(),
                                            emailAddress:			applicationForm.find('#ajb-email-address').val(),
                                            phoneNumber:			applicationForm.find('#ajb-phone-number').val(),
                                            comments:				applicationForm.find('#ajb-comments').val(),
                                            applicationCriteria:	new Array(),
                                            customFieldData:		new Array(),
                                            trackingCode:			options.tracking_code,
                                            validate_candidate_email: options.validate_candidate_email,
                                            advId: 					'',
                                            is_jobkey:   			''
                                    }
                                    if(options.is_job == true) {
                                    	submitObject.advId = options.adv_id;
                                    	submitObject.is_job = options.is_job;
                                    }
                                    $(".ajb_application_criteria").each(function(i) {
                                        if($(this).attr("mappedField") == '') {

                                        } else {
                                           if($(this).attr("mappedField") == 'Name') {
                                              var SystemFieldValue = $(this).find("input, select");
                                              submitObject.firstName = SystemFieldValue.val();
                                           }
                                           if($(this).attr("mappedField") == 'Surname') {
                                              var SystemFieldValue = $(this).find("input, select");
                                              submitObject.lastName = SystemFieldValue.val();
                                           }
                                           if($(this).attr("mappedField") == 'Email') {
                                              var SystemFieldValue = $(this).find("input, select");
                                              submitObject.emailAddress = SystemFieldValue.val();
                                           }


                                        }
                                    });
                                }
				
				// Validate content

				var jobAlertPost = {};
				var formErrors = false;
				var errorMsg = '';

				if(options.data.upload_type != undefined) {
					submitObject.upload_type = upload_type;
				}
				
				if(options.data.dropbox_selected_file_name != undefined) {
					submitObject.dropbox_file_name = dropbox_selected_file_name;
				}
				if(options.data.dropbox_selected_file_data != undefined) {
					submitObject.dropbox_selected_file_data = dropbox_selected_file_data;
				}
				
				if(options.data.googledrive_resume_selected_file_name != undefined) {
					submitObject.googledrive_resume_selected_file_name = googledrive_resume_selected_file_name;
				}
				if(options.data.googledrive_resume_selected_file_data != undefined) {
					submitObject.googledrive_resume_selected_file_data = googledrive_resume_selected_file_data;
				}
				
				if(options.data.googledrive_coverletter_selected_file_name != undefined) {
					submitObject.googledrive_coverletter_selected_file_name = googledrive_coverletter_selected_file_name;
				}
				if(options.data.googledrive_coverletter_selected_file_data != undefined) {
					submitObject.googledrive_coverletter_selected_file_data = googledrive_coverletter_selected_file_data;
				}
				
				if(options.data.onedrive_selected_file_name != undefined) {
					submitObject.onedrive_selected_file_name = onedrive_selected_file_name;
				}
				if(options.data.onedrive_selected_file_data != undefined) {
					submitObject.onedrive_selected_file_data = onedrive_selected_file_data;
				}
				//googledrive_coverletter_selected_file_name
				
				
				// Validate profile sources & uploaded resume/cv data
				switch (this.find('#ajb-profile-source').val()) {
					case 'linkedIn_Account':
					// Hide the entire form because they have already applied for the job.
					$('.ajb-job-application').hide();
					$('.ajb-application-acknowledgement').fadeIn('slow');
					break;
					case 'linkedin':
						submitObject.linkedInData = $.stringifyJSON(options.data.linkedIn);
						break;
					case 'facebook':
						submitObject.facebookData = $.stringifyJSON(options.data.facebook);
						break;
					case 'google-plus':
						submitObject.googlePlusData = $.stringifyJSON(options.data.googlePlus);
						break;
					case 'upload':
						// Validate uploaded files are accepted file types
						
						// Cover Leter validation
						if((options.data.dropbox_selected_file_name == undefined) && (options.data.dropbox_selected_file_data == undefined)
							&& (options.data.googledrive_selected_file_name == undefined) && (options.data.googledrive_selected_file_data == undefined)
							&& (options.data.onedrive_selected_file_name == undefined) && (options.data.onedrive_selected_file_data == undefined)) {
							
							if(options.is_mobile == true) {
								if((this.find('#ajb-comments').val() == '') && (this.find('#ajb-comments').hasClass('mandatory') == true)) {
									applicationForm.find('#ajb-comments').addClass('adlogic_error');
									formErrors = true;
									errorMsg += "- No Cover Letter (Comments) Submitted\n";
								} else {
									applicationForm.find('#ajb-comments').removeClass('adlogic_error');
								}
							} else {
								if ((this.find('#ajb-cover-letter').val() == '') && (this.find('#ajb-cover-letter').hasClass('mandatory') == false)) {
									applicationForm.find('#ajb-cover-letter').removeClass('adlogic_error');
								} else if ((this.find('#ajb-cover-letter').val() == '') && (this.find('#ajb-cover-letter').hasClass('mandatory') == true)) {
									applicationForm.find('#ajb-cover-letter').addClass('adlogic_error');
									formErrors = true;
									errorMsg += "- No Cover Letter Submitted\n";
								} else if (!validFileTypeRegExp.test(this.find('#ajb-cover-letter').val())) {
									applicationForm.find('#ajb-cover-letter').addClass('adlogic_error');
									formErrors = true;
									errorMsg += "- Invalid Cover Letter File, accepted file types are:\n";
									errorMsg += "doc, docx, txt, pdf, rtf, htm, html, ppt, pptx\n";
								} else {
									applicationForm.find('#ajb-cover-letter').removeClass('adlogic_error');
								}
							}
						
							// It's a mobile device
						
							if(options.is_mobile == true) {
								// Resume is not mandatory
								if(options.resume_mandatory == true) {
									if (this.find('#ajb-resume').val() == '') {
										applicationForm.find('#ajb-resume').addClass('adlogic_error');
										formErrors = true;
										errorMsg += "- No Resume Submitted\n";
									} else if (!validFileTypeRegExp.test(this.find('#ajb-resume').val())) {
										applicationForm.find('#ajb-resume').addClass('adlogic_error');
										formErrors = true;
										errorMsg += "- Invalid Resume File, accepted file types are:\n";
										errorMsg += "doc, docx, txt, pdf, rtf, htm, html, ppt, pptx, jpg, png, gif\n";
									} else {
										applicationForm.find('#ajb-resume').removeClass('adlogic_error');
									}
								} else {
									// Resume IS mandatory
									// Resume validation
									if (this.find('#ajb-resume').val() == '') {
										// Do nothing - resume isn't required.
									} else {
										if (!validFileTypeRegExp.test(this.find('#ajb-resume').val())) {
											applicationForm.find('#ajb-resume').addClass('adlogic_error');
											formErrors = true;
											errorMsg += "- Invalid Resume File, accepted file types are:\n";
											errorMsg += "doc, docx, txt, pdf, rtf, htm, html, ppt, pptx, jpg, png, gif\n";
										} else {
											applicationForm.find('#ajb-resume').removeClass('adlogic_error');
										}
									}
								}
							} else {
								// It is NOT a mobile device
								// Resume validation
								if(options.resume_mandatory == true) {
									if (this.find('#ajb-resume').val() == '') {
											applicationForm.find('#ajb-resume').addClass('adlogic_error');
											formErrors = true;
											errorMsg += "- No Resume Submitted\n";
									} else if (!validFileTypeRegExp.test(this.find('#ajb-resume').val())) {
										applicationForm.find('#ajb-resume').addClass('adlogic_error');
										formErrors = true;
										errorMsg += "- Invalid Resume File, accepted file types are:\n";
										errorMsg += "doc, docx, txt, pdf, rtf, htm, html, ppt, pptx\n";
									} else {
										applicationForm.find('#ajb-resume').removeClass('adlogic_error');
									}
								} else {
									if (this.find('#ajb-resume').val() != '') {
										if (!validFileTypeRegExp.test(this.find('#ajb-resume').val())) {
											applicationForm.find('#ajb-resume').addClass('adlogic_error');
											formErrors = true;
											errorMsg += "- Invalid Resume File, accepted file types are:\n";
											errorMsg += "doc, docx, txt, pdf, rtf, htm, html, ppt, pptx\n";
										}
									}
									
									
									
								}
							}
						}

						break;
				}
				if($('.ajb_application_criteria').attr('mappedField') == 'Name') {
					
				} else {
					if (submitObject.firstName == '') {
						applicationForm.find('#ajb-first-name').addClass('adlogic_error');
						formErrors = true;
						errorMsg += "- No First Name Entered\n";
					} else {
						applicationForm.find('#ajb-first-name').removeClass('adlogic_error');
					}
				}
				if($('.ajb_application_criteria').attr('mappedField') == 'Surname') {
					
				} else {
					if (submitObject.lastName == '') {
						applicationForm.find('#ajb-last-name').addClass('adlogic_error');
						formErrors = true;
						errorMsg += "- No Last Name Entered\n";
					} else {
						applicationForm.find('#ajb-last-name').removeClass('adlogic_error');
					}
				}
				
				if(options.validate_candidate_email == true) {
					if (/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(submitObject.emailAddress)) {
					
						if(options.use_system_fields == true) {
							$(".ajb_application_criteria").each(function(i) {
								if($(this).attr("mappedField") == '') {
		
		                        } else {
		                           if($(this).attr("mappedField") == 'Email') {
		                              var SystemFieldValue = $(this).find("input, select");
		                              SystemFieldValue.removeClass('adlogic_error_email');
		                           }
								}
							});
						} else {
							applicationForm.find('#ajb-email-address').removeClass('adlogic_error');
							
						}

					} else {
						if(options.use_system_fields == true) {
							$(".ajb_application_criteria").each(function(i) {
								if($(this).attr("mappedField") == '') {
		
		                        } else {
		                           if($(this).attr("mappedField") == 'Email') {
		                              var SystemFieldValue = $(this).find("input, select");
		                              SystemFieldValue.addClass('adlogic_error_email');
		                           }
								}
							});
							formErrors = true;
							errorMsg += "- Invalid Email Address\n";
						} else {
							applicationForm.find('#ajb-email-address').addClass('adlogic_error');
							formErrors = true;
							errorMsg += "- Invalid Email Address\n";
							
						}
						
					}
				}
				

				if ($('.ajb_application_criteria').length > 0) {
					$.each($('.ajb_application_criteria'), function(idx, criteriaObj) {
						// Detect criteria input for the job criteria
						criteriaInput = $(criteriaObj).find('input,select,textarea');
						if (criteriaInput.length) {
							// Validate Input
							if (criteriaInput.hasClass('mandatory') && criteriaInput.val() == '') {
								if(!criteriaInput.hasClass('ajb_field_numeric')) {
									criteriaInput.addClass('adlogic_error');
									formErrors = true;
									errorMsg += "- Question '" + $(criteriaObj).find('label').text() + "' was left blank\n";
								}
							} else {
								criteriaInput.removeClass('adlogic_error');
							}
							
							if (criteriaInput.hasClass('ajb_field_numeric')) {
								if($(criteriaInput).hasClass('mandatory')){
									if((criteriaInput.val() == ''))  {
										criteriaInput.addClass('adlogic_error');
										formErrors = true;
										errorMsg += "- Question '" + $(criteriaObj).find('label').text() + "' was left blank\n";
									} else {
										if($.isNumeric(criteriaInput.val())) {
											
										} else {
											criteriaInput.addClass('adlogic_error');
											formErrors = true;
											errorMsg += "- Question '" + $(criteriaObj).find('label').text() + "' has invalid characters\n";
										}
									}
								} else {
									// Blank fields aren't numeric so we'll double check here aswell.
									if(!criteriaInput.val() == '') {
										if($.isNumeric(criteriaInput.val())) {
										
										} else {
											criteriaInput.addClass('adlogic_error');
											formErrors = true;
											errorMsg += "- Question '" + $(criteriaObj).find('label').text() + "' has invalid characters\n";
										}
									}
									
								}
								
							}
							
 							if (criteriaInput.attr('type') != 'file') {
								// Add Criteria Input to submit Object
								if (criteriaInput.hasClass('custom_field')) {
									submitObject.customFieldData.push({
										id: parseInt(criteriaInput.attr('id').replace('ajb_custom_field_id_', '')),
										value: criteriaInput.val()
									});
								} else {
									if($(criteriaInput).is(":hidden")) {

									} else {
										// If the value is set
											var selected = $("option:selected", criteriaInput);
											
	                                    	if($(criteriaInput).attr('multiple')) {
		                                    	if($(criteriaInput).hasClass('mandatory')) {
			                                    	if(selected.length < 1) {
				                                    	$(criteriaObj).find('ms-container').addClass('adlogic_error');
														criteriaInput.addClass('adlogic_error');
														formErrors = true;
														errorMsg += "- Question '" + $(criteriaObj).find('label').text() + "' was left blank\n";
													} else {
														if(selected.length == 1) {
															submitObject.applicationCriteria.push({
																id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
													            value: criteriaInput.val() + '|||'
													            //value: $("option:selected", criteriaInput).val().toString().replace(/,/g, '|||')
															});
														} else {
													    	submitObject.applicationCriteria.push({
																id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
													            value: criteriaInput.val().join("|||")
													            //value: $("option:selected", criteriaInput).val().toString().replace(/,/g, '|||')
															});
														}
													}
		                                    	} else {
													if(selected.length < 1) {
														
													} else {
														if(selected.length == 1) {
															submitObject.applicationCriteria.push({
																id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
													            value: criteriaInput.val() + '|||'
													            //value: $("option:selected", criteriaInput).val().toString().replace(/,/g, '|||')
															});
														} else {
													    	submitObject.applicationCriteria.push({
																id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
													            value: criteriaInput.val().join("|||")
													            //value: $("option:selected", criteriaInput).val().toString().replace(/,/g, '|||')
															});
														}
													}
												}
											} else {
												submitObject.applicationCriteria.push({
													id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
												    value: criteriaInput.val()
												});    
											}
	                                    
	                                    
									}
										/*
												if(criteriaInput.hasClass("wasEssential") && criteriaInput.val() === ''){
													
												} else {
														// If the value is set
														if(criteriaInput.val() != null) {
															var selected = $("option:selected", criteriaInput);
															
					                                    	if($(criteriaInput).attr('multiple')) {
																if(selected.length == 1) {
																	submitObject.applicationCriteria.push({
																		id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
															            value: criteriaInput.val() + '|||'
															            //value: $("option:selected", criteriaInput).val().toString().replace(/,/g, '|||')
																	});
																} else {
															    	submitObject.applicationCriteria.push({
																		id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
															            value: criteriaInput.val().join("|||")
															            //value: $("option:selected", criteriaInput).val().toString().replace(/,/g, '|||')
																	});
																}
															} else {
																submitObject.applicationCriteria.push({
																	id: parseInt(criteriaInput.attr('id').replace('ajb_question_id_', '')),
																    value: criteriaInput.val()
																});    
															}
					                                    
					                                    } else {
					                                    	if(criteriaInput.hasClass('mandatory')) {
					                                            criteriaInput.addClass('adlogic_error');
					                                            formErrors = true;
																errorMsg += "- Question '" + $(criteriaObj).find('label').text() + "' was left blank\n";
															}
					                                    }
												}
										*/
								}
							}
						}
					});
				}

				if (formErrors) {
					alert('Please correct the application form errors:\n\n' + errorMsg);
				} else {
					$(params.button).attr('disabled', true);
					loadingDiv = $('<span class="adlogic_search_loading_div"></span>');
					$(params.button).parent().append(loadingDiv);

					/* 
					 * Since we're posting with uploads we need to use an iframe solution 
					 * to post the files as we cannot use ajax to upload files
					 */

					// Create fields required for posting via iframe
					applicationForm.find('form').attr('action', adlogicJobSearch.ajaxurl + '?action=jobApplication');
					applicationForm.find('form').append('<input type="hidden" class="ajb-job-ad-id" name="jobAdId" value="' + options.job_ad_id + '">');
					applicationForm.find('form').append('<input type="hidden" class="ajb-tracking-code" name="tracking_code" value="' + (options.tracking_code ? options.tracking_code : '') + '">');
					if(options.is_job == true) {
						applicationForm.find('form').append('<input type="hidden" class="ajb-adv-id" name="advId" value="' + submitObject.advId + '">');
						applicationForm.find('form').append('<input type="hidden" class="ajb-is-job" name="isJob" value="' + submitObject.is_job + '">');
					}
					$.each(submitObject.applicationCriteria, function(idx, appCriteria) {
						applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="applicationCriteria[' + idx + '][id]" value="' + appCriteria.id + '">');
						applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="applicationCriteria[' + idx + '][value]" value="' + appCriteria.value + '">');
					});

					$.each(submitObject.customFieldData, function(idx, customField) {
						applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="customFieldData[' + idx + '][id]" value="' + customField.id + '">');
						applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="customFieldData[' + idx + '][value]" value="' + customField.value + '">');
					});
					
					if(options.data.dropbox_selected_file_name != undefined) {
						if(options.data.dropbox_selected_file_name != '') {
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="dropbox_selected_file_name" value="' + dropbox_selected_file_name + '">');
						}
					}
					
					if(options.data.dropbox_selected_file_data != undefined) {
						if(options.data.dropbox_selected_file_data != '') {
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="dropbox_selected_file_data" value=' + dropbox_selected_file_data + '>');
						}
					}
					
					if(options.data.googledrive_selected_file_name != undefined) {
						if(options.data.googledrive_selected_file_name != '') {
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="googledrive_selected_file_name" value="' + googledrive_selected_file_name + '">');
						}
					}
					
					if(options.data.googledrive_selected_file_data != undefined) {
						if(options.data.googledrive_selected_file_data != '') {
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="googledrive_selected_file_data" value="' + googledrive_selected_file_data + '">');
						}
					}
					
					if(options.data.onedrive_selected_file_name != undefined) {
						if(options.data.onedrive_selected_file_name != '') {
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="onedrive_selected_file_name" value="' + onedrive_selected_file_name + '">');
						}
					}
					
					if(options.data.onedrive_selected_file_data != undefined) {
						if(options.data.onedrive_selected_file_data != '') {
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="onedrive_selected_file_data" value="' + onedrive_selected_file_data + '">');
						}
					}
					
					switch (this.find('#ajb-profile-source').val()) {
						case 'linkedin':
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="linkedInData" value="' + linkedin_profile_datastring + '">');
							break;
						case 'facebook':
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="facebookData" value="' + facebook_profile_datastring + '">');
							break;
						case 'google-plus':
							applicationForm.find('form').append('<input type="hidden" class="ajb-criteria-values" name="googlePlusData" value="' + google_plus_profile_datastring + '">');
							break;
					}

					applicationForm.find('form').iframePostForm({
						iframeID: options.bound_application + '_iframe',
						json : true,
						post : function() {
						},
						complete: function(response) {
							if (response.result == true) {
								self.fadeOut('slow');
								$(window).scrollTop(0);
								$('.ajb-application-acknowledgement').fadeIn('slow');
								$(document).trigger('adlogicJobSearch.jobApplication.applicationSuccess', [self, options]);
							} else {
								alert('Your application has been unsuccessful due to the following errors:\n' + response.message + '\n\nPlease check your details and try again.');
							}

							// Reset Form Inputs
							applicationForm.find('form').attr('action', '');
							applicationForm.find('form').find('.ajb-job-ad-id').remove();
							applicationForm.find('form').find('.ajb-criteria-values').remove();
							applicationForm.find('form').find('.ajb-tracking-code').remove();
							applicationForm.find('form').find('.ajb-adv-id').remove();

							// Unbind iframe Post Form from submit action
							applicationForm.find('form').unbind('submit');
							loadingDiv.remove();
							$(params.button).attr('disabled', false);
						}
					});
					applicationForm.find('form').submit();
				}
			},
			indeedApplicationSuccess : function() {
				self = $(this);
				self.fadeOut('slow');
				$(window).scrollTop(0);
				$('.ajb-application-acknowledgement').fadeIn('slow');
				$(document).trigger('adlogicJobSearch.jobApplication.applicationSuccess', [self, options]);
			}
			
	};

	$.fn.adlogicJobApplication = function( method ) {

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicJobApplication' );
		}
	};
})(jQuery);

/*
 * jQuery stringifyJSON
 * http://github.com/flowersinthesand/jquery-stringifyJSON
 * 
 * Copyright 2011, Donghwan Kim 
 * Licensed under the Apache License, Version 2.0
 * http://www.apache.org/licenses/LICENSE-2.0
 */
// This plugin is heavily based on Douglas Crockford's reference implementation
(function($) {
	
	var escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, 
		meta = {
			'\b' : '\\b',
			'\t' : '\\t',
			'\n' : '\\n',
			'\f' : '\\f',
			'\r' : '\\r',
			'"' : '\\"',
			'\\' : '\\\\'
		};
	
	function quote(string) {
		return '"' + string.replace(escapable, function(a) {
			var c = meta[a];
			return typeof c === "string" ? c : "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4);
		}) + '"';
	}
	
	function f(n) {
		return n < 10 ? "0" + n : n;
	}
	
	function str(key, holder) {
		var i, v, len, partial, value = holder[key], type = typeof value;
				
		if (value && typeof value === "object" && typeof value.toJSON === "function") {
			value = value.toJSON(key);
			type = typeof value;
		}
		
		switch (type) {
		case "string":
			return quote(value);
		case "number":
			return isFinite(value) ? String(value) : "null";
		case "boolean":
			return String(value);
		case "object":
			if (!value) {
				return "null";
			}
			
			switch (Object.prototype.toString.call(value)) {
			case "[object Date]":
				return isFinite(value.valueOf()) ? '"' + value.getUTCFullYear() + "-" + f(value.getUTCMonth() + 1) + "-" + f(value.getUTCDate()) + "T" + 
						f(value.getUTCHours()) + ":" + f(value.getUTCMinutes()) + ":" + f(value.getUTCSeconds()) + "Z" + '"' : "null";
			case "[object Array]":
				len = value.length;
				partial = [];
				for (i = 0; i < len; i++) {
					partial.push(str(i, value) || "null");
				}
				
				return "[" + partial.join(",") + "]";
			default:
				partial = [];
				for (i in value) {
					if (Object.prototype.hasOwnProperty.call(value, i)) {
						v = str(i, value);
						if (v) {
							partial.push(quote(i) + ":" + v);
						}
					}
				}
				
				return "{" + partial.join(",") + "}";
			}
		}
	}
	
	$.stringifyJSON = function(value) {
		if (window.JSON && window.JSON.stringify) {
			return window.JSON.stringify(value);
		}
		
		return str("", {"": value});
	};
	
}(jQuery));

/*
 * --------------------------------------------------------------------
 * jQuery-Plugin - $.download - allows for simple get/post requests for files
 * by Scott Jehl, scott@filamentgroup.com
 * http://www.filamentgroup.com
 * reference article: http://www.filamentgroup.com/lab/jquery_plugin_for_requesting_ajax_like_file_downloads/
 * Copyright (c) 2008 Filament Group, Inc
 * Dual licensed under the MIT (filamentgroup.com/examples/mit-license.txt) and GPL (filamentgroup.com/examples/gpl-license.txt) licenses.
 * --------------------------------------------------------------------
 */
 
jQuery.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};