adlogicJobSearch.registered_job_alert_widgets = [];

(function($) {
	$.fn.extend({
		adlogicJobAlertsWidget: function(options) {
			var defaults = {
					widgetId: '',
					dropdownType: {
						'locations' : 'single',
						'classifications' : 'single',
						'worktypes' : 'single'
					},
					topLevelOnly: {
						'locations': false,
						'classifications': false,
						'worktypes': false
					},
                                        hideEmpty: {
						'locations': false,
						'classifications': false,
						'worktypes': false
					}
				}

			var options = $.extend(defaults, options);

			var self;

			var initialized;

			var ajax_handles = {};

			var methods = {
				init : function( options ) {
					self = this;
					var options = $.extend(defaults, options);
					// Check if Job Alert Widget hasn't already been instantiated - if it has, return false
					$.each(adlogicJobSearch.registered_job_alert_widgets, function (idx, widget){
						if (widget == this) { initialized = true; }
					});

					if (initialized == true) { return false; }

					$(document).trigger('adlogicJobSearch.jobAlertsWidget.init', [self, options]);

					// If it hasn't been instantiated, let's activate widget
					adlogicJobSearch.registered_job_alert_widgets = adlogicJobSearch.registered_job_alert_widgets.concat(this);
					
					$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts' ).attr('disabled', true);

					if ($.active == 0) {
						methods.buildDropdowns.apply( self );

						// Re-enable subscribe button
						$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts').click(function() {
							return methods.subscribe.apply( self );
						});

						$( '#' + options.widgetId + ' form').submit(function() {
							return methods.subscribe.apply( self );
						});
						
						$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts' ).attr('disabled', false);

						$(document).unbind('ajaxStop');
					} else {
						$(document).ajaxStop(function() {
							methods.buildDropdowns.apply( self );

							// Re-enable subscribe button
							$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts').click(function() {
								return methods.subscribe.apply( self );
							});

							$( '#' + options.widgetId + ' form').submit(function() {
								return methods.subscribe.apply( self );
							});
							
							$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts' ).attr('disabled', false);

							// Call trigger to let know that job alerts widget is now loaded
							$(document).unbind('ajaxStop');
						});
					}
				},
				buildDropdowns : function() {
					// Check if there's an existing cache, if not create one
					if (typeof(adlogicJobSearch.cache) == 'undefined') {
						adlogicJobSearch.cache = {};
					}

					// If classifications drop down exists, build drop down
					if ($('#' + options.widgetId + '-classification_id').length > 0) {

						// Toggle loading class to add visual cue to element still loading
						$('#' + options.widgetId + '-classification_id').prev('label').toggleClass('loading');

						// Get Classifications
						getClassificationsUrl = adlogicJobSearch.ajaxurl + '?action=getIndustries';

						// Check if only displaying top level
						if (options.topLevelOnly.classifications == true) {
							getClassificationsUrl += '&onlyFirstLevel=true';
						}
                                                if (options.hideEmpty.classifications == true) {
							getClassificationsUrl += '&jobCount=true';
						}

						// If data is not cached or doesn't contain the exact url we want then request the data
						if (
								(typeof(adlogicJobSearch.cache.classifications) == 'undefined') ||
								(
										(typeof(adlogicJobSearch.cache.classificationsUrl) == 'undefined') &&
										((options.adCounts.classifications == true) || (options.hideEmpty.classifications == true))
								) ||
								((typeof(adlogicJobSearch.cache.classificationsUrl) != 'undefined') && (getClassificationsUrl != adlogicJobSearch.cache.classificationsUrl))
							) {

							// Run the AJAX request
							$.ajax(getClassificationsUrl, {
									success: function(data) {
										// Save classification url to cache
										adlogicJobSearch.cache.classificationsUrl = getClassificationsUrl;

										// Build a data array with re-sorted classification data for double drop downs
										adlogicJobSearch.cache.classificationDataArray = new Array;

										// Add raw data to cache
										adlogicJobSearch.cache.classifications = data.cla;
		
										// Process data for easier sorting into select lists
										$.each(data.cla, function(index) {
											classificationType = $(this)[0];
											if ((classificationType.parent == "0") || (typeof(classificationType.parent) === 'undefined')) {
												ad_count = (typeof(classificationType.adCount) == 'undefined') ? false : classificationType.adCount;
												adlogicJobSearch.cache.classificationDataArray.push( { id: classificationType.id, label: classificationType.displayName, count: ad_count, children: [] } ); 
											} else {
												$.each(adlogicJobSearch.cache.classificationDataArray, function(claIdx, claObj) {
													ad_count = (typeof(classificationType.adCount) == 'undefined') ? false : classificationType.adCount;
													if (claObj.id == classificationType.parent) {
														claObj.children.push( { id: classificationType.id, label: classificationType.displayName, count: ad_count, children: [] } );
													} else if (claObj.children.length > 0) {
														$.each(claObj.children, function(subClaIdx, subClaObj) {
															if (subClaObj.id == classificationType.parent) {
																subClaObj.children.push( { id: classificationType.id, label: classificationType.displayName, count: ad_count, children: [] } );
															}
														});
													}
												});
											}
										});
		
										// Render Drop Down
										dropdownOptions = [ {
											select : $('#' + options.widgetId + '-classification_id'),
											data : adlogicJobSearch.cache.classificationDataArray,
											type : 'classification'
										} ];
										
										methods.renderDropdowns.apply( self,  dropdownOptions);
									},
									statusCode: {
										// Handling 500 & 503 errors
										500: function() {
											// Set number of retries
											if (typeof(this.retries) === 'undefined') { this.retries = 0 }
											// Increment number of retries
											this.retries++; ajax_handles.classifications = this;
											// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection 
											if ( this.retries <= 5) { setTimeout(function() { $.ajax(ajax_handles.classifications) }, (1000 * this.retries)); }
										},
										503: function() {
											// Set number of retries
											if (typeof(this.retries) === 'undefined') { this.retries = 0 }
											// Increment number of retries
											this.retries++; ajax_handles.classifications = this;
											// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection 
											if ( this.retries <= 5) { setTimeout(function() { $.ajax(ajax_handles.classifications) }, (1000 * this.retries)); }
										}
									}
							});
						} else { // if data already exists don't re-request
							// Render Drop Down
							dropdownOptions = [ {
								select : $('#' + options.widgetId + '-classification_id'),
								data : adlogicJobSearch.cache.classificationDataArray,
								type : 'classification'
							} ];
							
							methods.renderDropdowns.apply( self,  dropdownOptions);
						}
					}

					// If locations drop down exists, build drop down
					if ($('#' + options.widgetId + '-location_id').length > 0) {

						// Toggle loading class to add visual cue to element still loading
						$('#' + options.widgetId + '-location_id').prev('label').toggleClass('loading');

						// Get Locations
						getLocationsUrl = adlogicJobSearch.ajaxurl + '?action=getLocations';

						// Check if only displaying top level
						if (options.topLevelOnly.locations == true) {
							getLocationsUrl += '&onlyFirstLevel=true';
						}
                                                if (options.hideEmpty.locations == true) {
							getLocationsUrl += '&jobCount=true';
						}
						// If data is not cached or doesn't contain the exact url we want then request the data
						if (
								(typeof(adlogicJobSearch.cache.locations) == 'undefined') ||
								(
										(typeof(adlogicJobSearch.cache.locationsUrl) == 'undefined') &&
										((options.adCounts.locations == true) || (options.hideEmpty.locations == true))
								) ||
								((typeof(adlogicJobSearch.cache.locationsUrl) != 'undefined') && (getLocationsUrl != adlogicJobSearch.cache.locationsUrl))
							) {


							// Run the AJAX request
							$.ajax(getLocationsUrl, {
								success: function(data) {
									// Save location url to cache
									adlogicJobSearch.cache.locationsUrl = getLocationsUrl;

									// Build a data array with re-sorted location data for double drop downs
									adlogicJobSearch.cache.locationDataArray = new Array;

									// Add raw data to cache
									adlogicJobSearch.cache.locations = data.loc;
	
									// Process data for easier sorting into select lists
									// Track Current parent
									var currentParent = '0';
	
									$.each(data.loc, function(index) {
										locationType = $(this)[0];
										if ((locationType.parent == "0") || (typeof(locationType.parent) === 'undefined')) {
											ad_count = (typeof(locationType.adCount) == 'undefined') ? false : locationType.adCount;
											adlogicJobSearch.cache.locationDataArray.push( { id: locationType.id, label: locationType.displayName, count: ad_count, children: [] } ); 
										} else {
											$.each(adlogicJobSearch.cache.locationDataArray, function(locIdx, locObj) {
												ad_count = (typeof(locationType.adCount) == 'undefined') ? false : locationType.adCount;
												if (locObj.id == locationType.parent) {
													locObj.children.push( { id: locationType.id, label: locationType.displayName, count: ad_count, children: [] } );
												} else if (locObj.children.length > 0) {
													$.each(locObj.children, function(subLocIdx, subLocObj) {
														if (subLocObj.id == locationType.parent) {
															subLocObj.children.push( { id: locationType.id, label: locationType.displayName, count: ad_count, children: [] } );
														}
													});
												}
											});
										}
									});
	
									// Render Drop Down
									dropdownOptions = [ {
										select : $('#' + options.widgetId + '-location_id'),
										data : adlogicJobSearch.cache.locationDataArray,
										type : 'location'
									} ];
									
									methods.renderDropdowns.apply( self,  dropdownOptions);
								},
								statusCode: {
									// Handling 500 & 503 errors
									500: function() {
										// Set number of retries
										if (typeof(this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.locations = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection 
										if ( this.retries <= 5) { setTimeout(function() { $.ajax(ajax_handles.locations) }, (1000 * this.retries)); }
									},
									503: function() {
										// Set number of retries
										if (typeof(this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.locations = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection 
										if ( this.retries <= 5) { setTimeout(function() { $.ajax(ajax_handles.locations) }, (1000 * this.retries)); }
									}
								}
							});
						} else { // if data already exists don't re-request
							// Render Drop Down
							dropdownOptions = [ {
								select : $('#' + options.widgetId + '-location_id'),
								data : adlogicJobSearch.cache.locationDataArray,
								type : 'location'
							} ];
							
							methods.renderDropdowns.apply( self,  dropdownOptions);
						}
					}

					// If worktypes drop down exists, build drop down
					if ($('#' + options.widgetId + '-worktype_id').length > 0) {

						// Toggle loading class to add visual cue to element still loading
						$('#' + options.widgetId + '-worktype_id').prev('label').toggleClass('loading');

						// Get Worktypes
						getWorktypesUrl = adlogicJobSearch.ajaxurl + '?action=getWorktypes';

						// Check if only displaying top level
						if (options.topLevelOnly.worktypes == true) {
							getWorktypesUrl += '&onlyFirstLevel=true';
						}
                                                if (options.hideEmpty.worktypes == true) {
							getWorktypesUrl += '&jobCount=true';
						}
						// If data is not cached or doesn't contain the exact url we want then request the data
						if (
								(typeof(adlogicJobSearch.cache.worktypes) == 'undefined') ||
								(
										(typeof(adlogicJobSearch.cache.worktypesUrl) == 'undefined') &&
										((options.adCounts.worktypes == true) || (options.hideEmpty.worktypes == true))
								) ||
								((typeof(adlogicJobSearch.cache.worktypesUrl) != 'undefined') && (getWorktypesUrl != adlogicJobSearch.cache.worktypesUrl))
							) {

							// Run the AJAX request
							$.ajax(getWorktypesUrl, {
								success: function(data) {
									// Save worktype url to cache
									adlogicJobSearch.cache.worktypesUrl = getWorktypesUrl;

									// Build a data array with re-sorted worktype data for double drop downs
									adlogicJobSearch.cache.worktypeDataArray = new Array;

									// Add raw data to cache
									adlogicJobSearch.cache.worktypes = data.wor;
	
									// Process data for easier sorting into select lists
									if (!$.isArray(data.wor)) {
										data.wor = new Array(data.wor);
									}
									
									$.each(data.wor, function(index) {
										worktypeType = $(this)[0];
										if (worktypeType.parent == "0") {
											ad_count = (typeof(worktypeType.adCount) == 'undefined') ? false : worktypeType.adCount;
											adlogicJobSearch.cache.worktypeDataArray.push( { id: worktypeType.id, label: worktypeType.displayName, count: ad_count, children: [] } ); 
										} else {
											$.each(adlogicJobSearch.cache.worktypeDataArray, function(worIdx, worObj) {
												ad_count = (typeof(worktypeType.adCount) == 'undefined') ? false : worktypeType.adCount;
												if (worObj.id == worktypeType.parent) {
													worObj.children.push( { id: worktypeType.id, label: worktypeType.displayName, count: ad_count } );
												}
											});
										}
									});
	
									// Render Drop Down
									dropdownOptions = [ {
										select : $('#' + options.widgetId + '-worktype_id'),
										data : adlogicJobSearch.cache.worktypeDataArray,
										type : 'worktype'
									} ];
									
									methods.renderDropdowns.apply( self,  dropdownOptions);
								}, statusCode: {
									// Handling 500 & 503 errors
									500: function() {
										// Set number of retries
										if (typeof(this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.worktypes = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection 
										if ( this.retries <= 5) { setTimeout(function() { $.ajax(ajax_handles.worktypes) }, (1000 * this.retries)); }
									},
									503: function() {
										// Set number of retries
										if (typeof(this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.worktypes = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection 
										if ( this.retries <= 5) { setTimeout(function() { $.ajax(ajax_handles.worktypes) }, (1000 * this.retries)); }
									}
								}
							});
						} else { // if data already exists don't re-request
							// Render Drop Down
							dropdownOptions = [ {
								select : $('#' + options.widgetId + '-worktype_id'),
								data : adlogicJobSearch.cache.worktypeDataArray,
								type : 'worktype'
							} ];
							
							methods.renderDropdowns.apply( self,  dropdownOptions);
						}
					}
				},
				renderDropdowns : function(opt) {
					var dropdownType;
					var adCounts;
					var hideEmpty;

					// Generate Select Options
					switch (opt.type) {
						case 'classification':
							selectOptions = {
								dropdownType : options.dropdownType.classifications,
								rawcache : adlogicJobSearch.cache.classifications,
								hideEmpty : options.hideEmpty.classifications,
								doubleSelectId : '#' + options.widgetId + '-classification_select_id',
								subSelectId : '#' + options.widgetId + '-sub_classification_select_id',
								translation : options.translations.classifications,
								sub_translation : options.translations.sub_classifications,
								selectedValue : (typeof(indIdDef) != 'undefined' ? indIdDef : null)
							}
							break;
						case 'location':
							selectOptions = {
								dropdownType : options.dropdownType.locations,
								rawcache : adlogicJobSearch.cache.locations,
								hideEmpty : options.hideEmpty.locations,
								doubleSelectId : '#' + options.widgetId + '-location_select_id',
								subSelectId : '#' + options.widgetId + '-sub_location_select_id',
								translation : options.translations.locations,
								sub_translation : options.translations.sub_locations,
								selectedValue : (typeof(locIdDef) != 'undefined' ? locIdDef : null)
							}
							break;
						case 'worktype':
							selectOptions = {
								dropdownType : 'flat', // As worktypes have no children there is no other dropdown type
								rawcache : adlogicJobSearch.cache.worktypes,
								translation : options.translations.worktype,
								hideEmpty : options.hideEmpty.worktypes,
								doubleSelectId : null,
								subSelectId : null,
								sub_translation : '',
								selectedValue : (typeof(wtIdDef) != 'undefined' ? wtIdDef : null)
							}
							break;
					}

					switch (selectOptions.dropdownType) {
						case 'single':
						case 'multiple':
							opt.select.append('<option value="">All ' + selectOptions.translation + '</option>');
							$.each(opt.data, function(dataIdx, dataObj) {
								if ((selectOptions.hideEmpty == true) && (dataObj.count == 0)) {
									return true;
								}

								optionEl = $('<option value="' + dataObj.id + '" class="ajb-parent-opt"><strong>' + dataObj.label + '</strong></option>');
								opt.select.append(optionEl);
								$.each(dataObj.children, function(optionIdx, optionObj) {
									if ((selectOptions.hideEmpty == true) && (optionObj.count == 0)) {
										return true;
									}

									if (selectOptions.adCounts == true) {
										if (optionObj.children.length > 0) {
											opt.select.append('<option value="' + optionObj.id + '" class="ajb-sub-parent-opt">&nbsp;&nbsp;&nbsp;' + optionObj.label + ' (' + optionObj.count +')</option>');
										} else {
											opt.select.append('<option value="' + optionObj.id + '">&nbsp;&nbsp;&nbsp;' + optionObj.label + ' (' + optionObj.count +')</option>');
										}
									} else {
										if (optionObj.children.length > 0) {
											opt.select.append('<option value="' + optionObj.id + '" class="ajb-sub-parent-opt">&nbsp;&nbsp;&nbsp;' + optionObj.label + '</option>');
										} else {
											opt.select.append('<option value="' + optionObj.id + '">&nbsp;&nbsp;&nbsp;' + optionObj.label + '</option>');
										}
									}

									if (optionObj.children.length > 0) {
										$.each(optionObj.children, function(subOptionIdx, subOptionObj) {
											if ((selectOptions.hideEmpty == true) && (subOptionObj.count == 0)) {
												return true;
											}

											if (selectOptions.adCounts == true) {
												opt.select.append('<option value="' + subOptionObj.id + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + subOptionObj.label + ' (' + subOptionObj.count +')</option>');
											} else {
												opt.select.append('<option value="' + subOptionObj.id + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + subOptionObj.label + '</option>');
											}
										});
									}
								});
							});

							/*
							 *  Check if parent options and sub-parent options have got css styles for backgroundColor
							 *  This is relevant mostly for people who've upgraded the plugin and do not have pre-existing styles
							 *  for parent and sub-parent option groups
							 */

							if (opt.select.children('.ajb-parent-opt').css('backgroundColor') == 'transparent' ||
								opt.select.children('.ajb-sub-parent-opt').css('backgroundColor') == 'transparent') {
								opt.select.children('.ajb-parent-opt').css('backgroundColor', '#313131');
								opt.select.children('.ajb-parent-opt').css('color', '#FFFFFF');
								opt.select.children('.ajb-sub-parent-opt').css('backgroundColor', '#4E4E4E');
								opt.select.children('.ajb-sub-parent-opt').css('color', '#FFFFFF');
							}

							if (
								(typeof(selectOptions.selectedValue) != 'undefined') &&
								(selectOptions.selectedValue != null)
							) {
								opt.select.val(selectOptions.selectedValue.split(','));
							}
							break;
						case 'double':
							$(selectOptions.doubleSelectId).append('<option value="">All ' + selectOptions.translation + '</option>');
							$(selectOptions.subSelectId).append('<option value="">All ' + selectOptions.sub_translation + '</option>');

							// Populate dropdown values
							$.each(opt.data, function(optionIdx, optionObj) {
								if ((selectOptions.hideEmpty == true) && (optionObj.count == 0)) {
									return true;
								}
								if (selectOptions.adCounts == true) {
									$(selectOptions.doubleSelectId).append('<option value="' + optionObj.id + '">' + optionObj.label + ' (' + optionObj.count + ')</option>');
								} else {
									$(selectOptions.doubleSelectId).append('<option value="' + optionObj.id + '">' + optionObj.label + '</option>');
								}
							});
							
							// Deattach Previous Events if any
							$(selectOptions.doubleSelectId).unbind('change');
							$(selectOptions.subSelectId).unbind('change');

							// Attach Change Event
							$(selectOptions.doubleSelectId).change(selectOptions, function(e) {
								//selectOptions = e.data.selectOptions;
								if ($(e.target).val() != '') {
									$(e.data.subSelectId).empty();
									opt.select.val($(e.target).val());
									$.each(opt.data, function(optionIdx, optionObj) {
										if (optionObj.id == $(e.target).val()) {
											if ((e.data.hideEmpty == true) && (optionObj.count == 0)) {
												return true;
											}

											if (e.data.adCounts == true) {
												$(e.data.subSelectId).append('<option value="' + $(e.target).val() + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
											} else {
												$(e.data.subSelectId).append('<option value="' + $(e.target).val() + '">All ' + optionObj.label + ' Jobs</option>');
											}

											$.each(optionObj.children, function(childOptionIdx, childOptionObj) {
												if ((e.data.hideEmpty == true) && (childOptionObj.count == 0)) {
													return true;
												}
												if (e.data.adCounts == true) {
													$(e.data.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + ' (' + childOptionObj.count + ')</option>');
												} else {
													$(e.data.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + '</option>');
												}
											});
										}
									});
								} else {
									opt.select.val('');
									$(e.data.subSelectId).empty();
									$(e.data.subSelectId).append('<option value="">All ' + e.data.sub_translation + '</option>');
								}
								$(document).trigger('adlogicJobSearch.jobAlertsWidget.selectUpdate', [self, options]);
							});

							$(selectOptions.subSelectId).change(function(e) {
								opt.select.val($(e.target).val());
								$(document).trigger('adlogicJobSearch.jobAlertsWidget.selectUpdate', [self, options]);
							});

							if (typeof(selectOptions.selectedValue) != 'undefined') {

								// In the case of multiple selected values, select only the first one
								selectOptions.selectedValue = selectOptions.selectedValue.split(',')[0];

								opt.select.val(selectOptions.selectedValue);

								$.each(selectOptions.rawcache, function(dataIdx, dataObj) {
									if (selectOptions.selectedValue == dataObj.id) {
										if (dataObj.parent == '0') {
											$(selectOptions.doubleSelectId).val(dataObj.id);
											$(selectOptions.subSelectId).empty();
											$.each(opt.data, function(optionIdx, optionObj) {
												if (optionObj.id == dataObj.id) {
													if (selectOptions.adCounts == true) {
														$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
													} else {
														$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs</option>');
													}
													$.each(optionObj.children, function(childOptionIdx, childOptionObj) {
														if ((selectOptions.hideEmpty == true) && (childOptionObj.count == 0)) {
															return true;
														}
														
														if (selectOptions.adCounts == true) {
															$(selectOptions.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + ' (' + childOptionObj.count + ')</option>');
														} else {
															$(selectOptions.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + '</option>');
														}
													});
												}
											});
										} else {
											$(selectOptions.subSelectId).empty();
											$.each(opt.data, function(optionIdx, optionObj) {
												if (optionObj.id == dataObj.parent) {
													if (selectOptions.adCounts == true) {
														$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
													} else {
														$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs</option>');
													}

													$.each(optionObj.children, function(childOptionIdx, childOptionObj) {
														if ((selectOptions.hideEmpty == true) && (childOptionObj.count == 0)) {
															return true;
														}
														
														if (selectOptions.adCounts == true) {
															$(selectOptions.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + ' (' + childOptionObj.count + ')</option>');
														} else {
															$(selectOptions.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + '</option>');
														}
													});
												}
											});
											$(selectOptions.doubleSelectId).val(dataObj.parent);
											$(selectOptions.subSelectId).val(dataObj.id);
										}
									}
								});
							}
							break;
						case 'flat':
							opt.select.append('<option value="">All ' + selectOptions.translation + '</option>');

							$.each(opt.data, function(dataIdx, dataObj) {
								if ((selectOptions.hideEmpty == true) && (dataObj.count == 0)) {
									return true;
								}
								if (selectOptions.adCounts == true) {
									opt.select.append('<option value="' + dataObj.id + '">' + dataObj.label + ' (' + dataObj.count +')</option>');
								} else {
									opt.select.append('<option value="' + dataObj.id + '">' + dataObj.label + '</option>');
								}
							});

							if (
								(typeof(selectOptions.selectedValue) != 'undefined') &&
								(selectOptions.selectedValue != null)
							) {
								opt.select.val(selectOptions.selectedValue.split(','));
							}
							break;
					}

					// Toggle loading class from select label
					opt.select.prev('label').toggleClass('loading');
					$(document).trigger('adlogicJobSearch.jobAlertsWidget.loaded', [self, options]);
				},
				subscribe: function() {
					// Validate content
					var firstName = $('#' + options.widgetId + '-first_name').val();
					var surname = $('#' + options.widgetId + '-surname').val();
					var email_address = $('#' + options.widgetId + '-email_address').val();
					var phone_number = $('#' + options.widgetId + '-phone_number').val();
					var classification_id = $('#' + options.widgetId + '-classification_id').val();
					var location_id = $('#' + options.widgetId + '-location_id').val();
					var work_type_id = $('#' + options.widgetId + '-worktype_id').val();
					var alert_frequency = $('#' + options.widgetId + '-alert_frequency').val();

					var jobAlertPost = {};
					var formErrors = false;
					var errorMsg = '';

					if (firstName == '') {
						$('#' + options.widgetId + '-first_name').addClass('adlogic_error');
						formErrors = true;
						errorMsg += "- No First Name Entered\n";
					} else {
						jobAlertPost.name = firstName;
						$('#' + options.widgetId + '-first_name').removeClass('adlogic_error');
					}

					if (surname == '') {
						//$('#' + options.widgetId + '-surname').addClass('adlogic_error');
						//formErrors = true;
					} else {
						jobAlertPost.surname = surname;
					}

					if (/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(email_address)) {
						jobAlertPost.email = email_address;
						$('#' + options.widgetId + '-email_address').removeClass('adlogic_error');
					} else {
						$('#' + options.widgetId + '-email_address').addClass('adlogic_error');
						formErrors = true;
						errorMsg += "- Invalid Email Address\n";
					}

					if (phone_number == '') {
						//$('#' + options.widgetId + '-surname').addClass('adlogic_error');
						//formErrors = true;
					} else {
						jobAlertPost.contactNumber = phone_number;
					}

					if (classification_id == '') {
						//$('#' + options.widgetId + '-surname').addClass('adlogic_error');
						//formErrors = true;
					} else {
						jobAlertPost.indId = classification_id;
					}

					if (location_id == '') {
						//$('#' + options.widgetId + '-surname').addClass('adlogic_error');
						//formErrors = true;
					} else {
						jobAlertPost.locId = location_id;
					}

					if (work_type_id == '') {
						//$('#' + options.widgetId + '-surname').addClass('adlogic_error');
						//formErrors = true;
					} else {
						jobAlertPost.wtId = work_type_id;
					}

					if (alert_frequency == '') {
						//$('#' + options.widgetId + '-surname').addClass('adlogic_error');
						//formErrors = true;
					} else {
						jobAlertPost.noticePeriod = alert_frequency;
					}

					if (formErrors) {
						alert('Please correct the job alert form errors:\n\n' + errorMsg);
					} else {
						var loadingDiv;
						loadingDiv = $('<div class="adlogic_alerts_loading_div"></div>');
						$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts').after(loadingDiv)
						$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts').attr('disabled', true);
						$.ajax(adlogicJobSearch.ajaxurl + '?action=subscribeJobAlerts', {
							data: jobAlertPost,
							type: 'post',
							success: function(data) {
								alert('You\'ve successfully subscribed to the Job Alerts');
								$( '#' + options.widgetId + ' .ajb-subscribe-job-alerts').attr('disabled', false);
								loadingDiv.remove();
								$(document).trigger('adlogicJobSearch.jobAlertsWidget.subscribed', [$(self), data]);
							}
						});
					}

				}
			};

			return this.each(function(method) {
				// Method calling logic
				if ( methods[method] ) {
					return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
				} else if ( typeof method === 'object' || ! method ) {
					return methods.init.apply( this, arguments );
				} else {
					$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicSearchWidget' );
				}
			});
		}
	});
})(jQuery);