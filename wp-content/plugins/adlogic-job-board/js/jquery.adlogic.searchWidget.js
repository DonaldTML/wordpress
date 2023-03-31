/**
 * Adlogic Job Search
 */

adlogicJobSearch.registered_search_widgets = [];
var suburbStr = "";
var suburbSubStr = "";
(function ($) {
	$.fn.extend({
		adlogicSearchWidget: function (options) {

			var defaults = {
				searchUrl: '',
				widgetId: '',
				translations: {
					'locations': 'Location',
					'sub_locations': 'Sub-location',
					'classifications': 'Classification',
					'sub_classifications': 'Sub-classification',
					'worktype': 'Work Type',
					'costcenter': 'Cost Centre'
				},
				dropdownType: {
					'locations': 'single',
					'classifications': 'single',
					'worktypes': 'flat',
					'costcenters': 'flat'
				},
				adCounts: {
					'locations': false,
					'classifications': false,
					'worktypes': false,
					'costcenters': false
				},
				hideEmpty: {
					'locations': false,
					'classifications': false,
					'worktypes': false,
					'costcenters': false
				},
				topLevelOnly: {
					'locations': false,
					'classifications': false,
					'worktypes': false,
					'costcenters': false
				},
				showThirdLevel: {
					'locations': true,
					'classifications': true,
					'worktypes': true,
					'costcenters': true
				},
				chosenEnabled: false,
				useNewLocation: false,
				useNewLocationAPI: false
			}

			var options = $.extend(defaults, options);

			var self;

			var initialized;

			var ajax_handles = {};

			var methods = {
				mapOptions: {
					constructFieldValFromObject: function () {
						if (typeof geoLocationJsonDef !== "undefined" && MRP.StringUtil.hasData(geoLocationJsonDef)) {
							var geoLocationObject = JSON.parse(geoLocationJsonDef),
								formattedAddr = "",
								locality = "",
								state = "",
								country = "",
								postal_code = "";
							//"[{"name":"Blacktown","type":"locality","lat":"-33.771","lng":"150.9063"},
							//{"name":"New South Wales","code":"NSW","type":"administrative_area_level_1"},
							//{"name":"Australia","type":"country","code":"AU"},{"name":"2148","type":"postal_code"}]"
							for (var int = 0; int < geoLocationObject.length; int++) {
								var cmp = geoLocationObject[int];
								if (cmp.type == 'locality') {
									if (typeof cmp.name !== "undefined" && cmp.name !== "") {
										locality = cmp.name;
									}
								} else if (cmp.type == 'administrative_area_level_1') {
									if (typeof cmp.name !== "undefined" && cmp.name !== "") {
										state = cmp.name;
									}
								} else if (cmp.type == 'country') {
									if (typeof cmp.name !== "undefined" && cmp.name !== "") {
										country = cmp.name;
									}
								} else if (cmp.type == 'postal_code' || cmp.type == 'postal_code_prefix' || cmp.type == 'postal_code_suffix') {
									if (typeof cmp.name !== "undefined" && cmp.name !== "") {
										postal_code = cmp.name;
									}
								}
							}
							if (locality !== "") {
								formattedAddr = locality + ", ";
							}
							if (state !== "") {
								formattedAddr += state + ", ";
							}
							if (country !== "") {
								formattedAddr += country;
							}
							if (postal_code !== "") {
								formattedAddr += ", " + postal_code;
							}
							return formattedAddr;
						}
						return "";
					},
					initMap: function () {
						if (!document.getElementById(options.widgetId + "-location_select_id")) {
							console.error("Cannot initialise map for " + options.widgetId + "-location_select_id" + " because this element does not exist!");
							return;
						}
						if (typeof google == 'undefined') {
							// This happens when Google hasn't loaded yet.
							console.error('Cannot initialise google map for ' + options.widgetId + "-location_select_id" + " because the Google API hasn't finished loading!");
							return;
						}
						var input = document.getElementById(options.widgetId + "-location_select_id");

						/*
						 * When a user presses enter, this function will simulate that they've pressed the down arrow (to select the first record)
						 * than fire off the event to invoke the Google Widget's internal functions which will return the data for us to process.
						 *
						 * Thanks to https://stackoverflow.com/a/11703018
						 */
						var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;

						function addEventListenerWrapper(type, listener) {
							// Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
							// and then trigger the original listener.
							if (type == "keydown") {
								var orig_listener = listener;
								listener = function (event) {
									var suggestion_selected = jQuery(".pac-item-selected").length > 0;
									if (event.which == 13 && !suggestion_selected) {
										var simulated_downarrow = jQuery.Event("keydown", {
											keyCode: 40,
											which: 40
										});
										orig_listener.apply(input, [simulated_downarrow]);
									}
									orig_listener.apply(input, [event]);
								};
							}
							// $(this).find('form')
							_addEventListener.apply(input, [type, listener]);
						}

						input.addEventListener = addEventListenerWrapper;
						input.attachEvent = addEventListenerWrapper;

						var autocomplete = new google.maps.places.Autocomplete(input);

						// Only find results that have a suburb
						// List of available filters: https://developers.google.com/places/supported_types#table3
						var filterType = "(cities)";
						// if(typeof options.geoLocationOpts !== "undefined") {
						// 	if(typeof options.geoLocationOpts.filterType !== "undefined") {
						// 		filterType = options.geoLocationOpts.filterType;
						// 	}
						// }
						// console.log("filter type is --> " , filterType);
						autocomplete.setTypes([filterType]);

						// We don't want any Contact or Atmosphere data as it's not useful & Google Charge extra
						autocomplete.setFields(["address_component", "adr_address", "alt_id", "formatted_address", "geometry", "icon", "id", "name",
							"permanently_closed", "photo", "place_id", "plus_code", "scope", "type", "url", "utc_offset", "vicinity"]);

						if (typeof geoLocationJsonDef !== "undefined" && MRP.StringUtil.hasData(geoLocationJsonDef)) {
							$("#" + options.widgetId + "-location_select_id").attr("geoLocationJson", geoLocationJsonDef);
							$("#" + options.widgetId + "-location_select_id").val(methods.mapOptions.constructFieldValFromObject.apply(self));
						}

						// This is where our processing happens, we'll get key values and put them into an object to send to MR+ when submit.
						autocomplete.addListener('place_changed', function () {
							var place = autocomplete.getPlace();
							if (!place.geometry) {
								window.alert("No details available for input: '" + place.name + "'");
								return;
							}
							var requestArr = [],
								localityObj = {},
								stateObj = {},
								countryObj = {},
								postCodeObj = {},
								address = place.formatted_address,
								localityShortName = "",
								isEmpty = function (object) {
									for (var key in object) {
										if (object.hasOwnProperty(key)) {
											return false;
										}
									}
									return true;
								};
							for (var int = 0; int < place.address_components.length; int++) {
								var cmp = place.address_components[int],
									ii = 0;
								while (ii < cmp.types.length) {
									var type = cmp.types[ii];
									if (type == 'locality') {
										localityObj.name = cmp.long_name;
										localityObj.type = type;
										localityObj.lat = place.geometry.location.lat();
										localityObj.lng = place.geometry.location.lng();
										localityShortName = cmp.short_name;
									} else if (type == 'administrative_area_level_1') {
										stateObj.code = cmp.short_name;
										stateObj.name = cmp.long_name;
										stateObj.type = type;
									} else if (type == 'country') {
										countryObj.code = cmp.short_name;
										countryObj.name = cmp.long_name;
										countryObj.type = type;
									} else if (type == 'postal_code' || type == 'postal_code_prefix' || type == 'postal_code_suffix') {
										postCodeObj.name = cmp.long_name;
										postCodeObj.type = 'postal_code';
									}
									ii++;
								}
							}
							if (isEmpty(localityObj) || isEmpty(countryObj)) {
								alert("A suburb, state and country are required!");
								return;
							}
							if (isEmpty(stateObj)) {
								stateObj.code = localityShortName;
								stateObj.name = localityObj.name;
								stateObj.type = 'administrative_area_level_1';
							}
							requestArr.push(localityObj);
							requestArr.push(stateObj);
							requestArr.push(countryObj);

							if (!isEmpty(postCodeObj)) {
								requestArr.push(postCodeObj);
							}
							$("#" + options.widgetId + "-location_select_id").attr("distance", 50);
							$("#" + options.widgetId + "-location_select_id").attr("geoLocationJson", JSON.stringify(requestArr));
						});
					}
				},
				init: function (options) {
					self = $(this);
					var options = $.extend(defaults, options);

					// Check if Search Widget hasn't already been instantiated - if it has, return false
					$.each(adlogicJobSearch.registered_search_widgets, function (idx, widget) {
						if (widget == self.get(0)) { initialized = true; }
					});

					if (options.chosenEnabled == true) {
						$(document).bind('adlogicJobSearch.searchWidget.init',
							function (e, obj, opts) {
								obj.find('select').chosen({
									width: '100%'
								});
							});

						$(document).bind('adlogicJobSearch.searchWidget.loaded',
							function (e, obj, opts) {
								obj.find('select').trigger('chosen:updated');
							});
						$(document).bind('adlogicJobSearch.searchWidget.selectUpdate',
							function (e, obj, opts) {
								obj.find('select').trigger('chosen:updated');
							});
						$(document).bind('adlogicJobSearch.searchWidget.submit',
							function (e, obj, opts) {
								obj.find('select').trigger('chosen:updated');
							});

						$(document).bind('chosen:showing_dropdown', function (e, obj) {
							chosenObj = obj.chosen;
							chosenObj.winnow_results();
							chosenObj.container.css('zIndex', '1000');

							if (chosenObj.search_results.get(0).scrollWidth > chosenObj.dropdown.width()) {
								sizeDifference = chosenObj.search_results.get(0).scrollWidth - chosenObj.dropdown.width();
								//chosenObj.dropdown.width(chosenObj.search_results.get(0).scrollWidth);
								chosenObj.container.width(chosenObj.search_results.get(0).scrollWidth + 30);
								//chosenObj.search_field.width((chosenObj.search_field.width()+sizeDifference));
							}
						});

						$(document).bind('chosen:hiding_dropdown', function (e, obj) {
							chosenObj = obj.chosen;
							chosenObj.container.css('width', '100%');
							chosenObj.container.css('zIndex', 'auto');
						});
					}

					//if (initialized == true) { return false; }

					// If it hasn't been instantiated, let's activate widget
					adlogicJobSearch.registered_search_widgets = adlogicJobSearch.registered_search_widgets.concat(this);

					// Get Search Variables from anchor values (if any exist) - Thanks Alex Brindal
					if ($.deparam.fragment()) {
						var anchorValues = $.deparam.fragment();
						$.each(anchorValues, function (anchorName, anchorValue) {
							switch (anchorName.toLowerCase()) {
								case 'indid':
									indIdDef = anchorValue;
									break;
								case 'locid':
									locIdDef = anchorValue;
									break;
								case 'wtid':
									wtIdDef = anchorValue;
									break;
								case 'ccid':
									ccIdDef = anchorValue;
									break;
								case 'keyword':
									keyDef = anchorValue;
									break;
								case 'saltype':
									salaryTypeDef = anchorValue;
									break;
								case 'salmin':
									salaryMinDef = anchorValue;
									break;
								case 'salmax':
									salaryMaxDef = anchorValue;
									break;
								case 'geolocationjson':
									geoLocationJsonDef = anchorValue;
									break;
								default:
									break;
							}
						});
					}

					$(document).trigger('adlogicJobSearch.searchWidget.init', [self, options]);
					// Build dropdowns
					methods.buildDropdowns.apply(self);

					if (options.useNewLocation && document.getElementById(options.widgetId + "-location_select_id")) {
						methods.mapOptions.initMap.apply(self);
					}

					// Check if the Keywords search bar is there, and if so, fill with predefined value (if available) - Thanks Alex Brindal
					if (($('#' + options.widgetId + '-keywords').length > 0) && (typeof (keyDef) !== 'undefined')) {
						$('#' + options.widgetId + '-keywords').val(decodeURIComponent(keyDef));
					}
					// Initialise listing type values
					if (typeof (listingTypeDef) !== 'undefined') {
						$('#' + options.widgetId + '-listing_type').val(listingTypeDef);
					}

					// Initialise salary range slider
					options.currentSalaryType = options.salary_range_settings.default_type;

					// Set default values for min/max Annual Package and Hourly Rates from config
					options.salary_range_settings.annual_package_min_val = options.salary_range_settings.annual_package_min;
					options.salary_range_settings.annual_package_max_val = options.salary_range_settings.annual_package_max;
					options.salary_range_settings.hourly_rate_min_val = options.salary_range_settings.hourly_rate_min;
					options.salary_range_settings.hourly_rate_max_val = options.salary_range_settings.hourly_rate_max;

					// Check searched salary values have been set rather than defaults, if so use those instead.
					// Improvements thanks to Alex Brindal
					if ((typeof (salaryTypeDef) != 'undefined') && salaryTypeDef != '') {
						if (salaryTypeDef == "AnnualPackage") {
							options.salary_range_settings.default_type = 'annual';
						} else if (salaryTypeDef == "HourlyRate") {
							options.salary_range_settings.default_type = 'hourly';
						} else {
							options.salary_range_settings.default_type = salaryTypeDef;
						}
					}

					switch (options.salary_range_settings.default_type) {
						case 'annual':
							if ((typeof (salaryMinDef) != 'undefined') && (salaryMinDef != '')) {
								options.salary_range_settings.annual_package_min_val = parseInt(salaryMinDef) / 1000;
							}
							if ((typeof (salaryMaxDef) != 'undefined') && (salaryMaxDef != '')) {
								options.salary_range_settings.annual_package_max_val = parseInt(salaryMaxDef) / 1000;
							}
							break;
						case 'hourly':
							if ((typeof (salaryMinDef) != 'undefined') && (salaryMinDef != '')) {
								options.salary_range_settings.hourly_rate_min_val = parseInt(salaryMinDef);
							}
							if ((typeof (salaryMaxDef) != 'undefined') && (salaryMaxDef != '')) {
								options.salary_range_settings.hourly_rate_max_val = parseInt(salaryMaxDef);
							}
							break;
					}

					// If salary range div exists, render salary slider
					if ($('#' + options.widgetId + '-salary-range').length > 0) {
						options.salary_range_settings.currentSalaryType = options.salary_range_settings.default_type;
						salarySliderObj = $('#' + options.widgetId + '-salary-range');
						salarySliderObj.slider({
							range: true,
							values: [1, 1],
							slide: function (event, ui) {
								switch (options.salary_range_settings.currentSalaryType) {
									case 'annual':
										$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + ui.values[0] + "K - $" + ui.values[1] + "K");
										$('#' + options.widgetId + '-salary-min').val(ui.values[0] * 1000);
										options.salary_range_settings.annual_package_min_val = ui.values[0];
										$('#' + options.widgetId + '-salary-max').val(ui.values[1] * 1000);
										options.salary_range_settings.annual_package_max_val = ui.values[1];
										$('#' + options.widgetId + '-salary-type').val('AnnualPackage');
										break;
									case 'hourly':
										$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + ui.values[0] + " - $" + ui.values[1] + "/hr");
										$('#' + options.widgetId + '-salary-min').val(ui.values[0]);
										options.salary_range_settings.hourly_rate_min_val = ui.values[0];
										$('#' + options.widgetId + '-salary-max').val(ui.values[1]);
										options.salary_range_settings.hourly_rate_max_val = ui.values[1];
										$('#' + options.widgetId + '-salary-type').val('HourlyRate');
										break;
								}
							}
						});

						// Set Default Values
						switch (options.salary_range_settings.currentSalaryType) {
							case 'annual':
								salarySliderObj.slider('option', {
									min: parseInt(options.salary_range_settings.annual_package_min),
									max: parseInt(options.salary_range_settings.annual_package_max),
									step: parseInt(options.salary_range_settings.annual_package_step),
									values: [parseInt(options.salary_range_settings.annual_package_min_val), parseInt(options.salary_range_settings.annual_package_max_val)]
								});
								$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + salarySliderObj.slider("values", 0) +
									"K - $" + salarySliderObj.slider("values", 1) + "K");
								$(this).find('.ajb-salary-annual a').addClass('selected');
								break;
							case 'hourly':
								salarySliderObj.slider('option', {
									min: parseInt(options.salary_range_settings.hourly_rate_min),
									max: parseInt(options.salary_range_settings.hourly_rate_max),
									step: parseInt(options.salary_range_settings.hourly_rate_step),
									values: [parseInt(options.salary_range_settings.hourly_rate_min_val), parseInt(options.salary_range_settings.hourly_rate_max_val)]
								});
								$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + salarySliderObj.slider("values", 0) +
									" - $" + salarySliderObj.slider("values", 1) + "/hr");
								$(this).find('.ajb-salary-hourly a').addClass('selected');
								break;
						}

						$('#' + options.widgetId + '-salary-switcher .ajb-salary-type-selector .ajb-salary-hourly').click(function () {
							options.salary_range_settings.currentSalaryType = 'hourly';
							salarySliderObj.slider('option', {
								min: parseInt(options.salary_range_settings.hourly_rate_min),
								max: parseInt(options.salary_range_settings.hourly_rate_max),
								step: parseInt(options.salary_range_settings.hourly_rate_step),
								values: [parseInt(options.salary_range_settings.hourly_rate_min_val), parseInt(options.salary_range_settings.hourly_rate_max_val)]
							});
							$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + salarySliderObj.slider("values", 0) +
								" - $" + salarySliderObj.slider("values", 1) + "/hr");
							$('#' + options.widgetId + '-salary-type').val('HourlyRate');
							$('#' + options.widgetId + '-salary-min').val(salarySliderObj.slider("values", 0));
							$('#' + options.widgetId + '-salary-max').val(salarySliderObj.slider("values", 1));
							$('.ajb-salary-type-selector a').toggleClass('selected');
						});

						$('#' + options.widgetId + '-salary-switcher .ajb-salary-type-selector .ajb-salary-annual').click(function () {
							options.salary_range_settings.currentSalaryType = 'annual';
							salarySliderObj.slider('option', {
								min: parseInt(options.salary_range_settings.annual_package_min),
								max: parseInt(options.salary_range_settings.annual_package_max),
								step: parseInt(options.salary_range_settings.annual_package_step),
								values: [parseInt(options.salary_range_settings.annual_package_min_val), parseInt(options.salary_range_settings.annual_package_max_val)]
							});

							$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + salarySliderObj.slider("values", 0) +
								"K - $" + salarySliderObj.slider("values", 1) + "K");
							$('#' + options.widgetId + '-salary-type').val('AnnualPackage');
							$('#' + options.widgetId + '-salary-min').val(salarySliderObj.slider("values", 0) * 1000);
							$('#' + options.widgetId + '-salary-max').val(salarySliderObj.slider("values", 1) * 1000);
							$('.ajb-salary-type-selector a').toggleClass('selected');
						});

						$('#' + options.widgetId + '-salary-range a.ui-slider-handle').eq(0).addClass('ajb-salary-min-slider');
						$('#' + options.widgetId + '-salary-range a.ui-slider-handle').eq(1).addClass('ajb-salary-max-slider');
					}

					$(this).find('form').submit(function () {
						$(document).trigger('adlogicJobSearch.searchWidget.submit', [self, options]);
						methods.submit.apply(self);
						return false;
					});

					$(this).find('form').keypress(function (e) {
						if (e.which == 13) {
							// If using the new location widget and it's focused, don't submit the form
							if (options.useNewLocation && e.target && e.target.id === options.widgetId + "-location_select_id") {
								return;
							}
							$(this).submit();
						}
					});

					$(this).find('.ajb-view-all-jobs-button,.ajb-search-for-jobs-button').attr('disabled', true);

					$(document).ajaxStop(function () {
						self.find('.ajb-view-all-jobs-button,.ajb-search-for-jobs-button').attr('disabled', false).unbind('click');

						self.children('form').unbind('submit');
						self.children('form').submit(function () {
							$(document).trigger('adlogicJobSearch.searchWidget.submit', [self, options]);
							methods.submit.apply(self);
							return false;
						});

						self.find('.ajb-search-for-jobs-button').click(function () {
							$(document).trigger('adlogicJobSearch.searchWidget.submit', [self, options]);
							methods.submit.apply(self);
						});

						self.find('.ajb-view-all-jobs-button').click(function () {
							self.find('form')[0].reset();
							$("#" + options.widgetId + "-location_select_id").val('');
							$("#" + options.widgetId + "-location_select_id").removeAttr("geoLocationJson");
							if ($('#' + options.widgetId + '-salary-range').length > 0) {
								$('#' + options.widgetId + '-salary-type').val('');
								$('#' + options.widgetId + '-salary-min').val('');
								$('#' + options.widgetId + '-salary-max').val('');
								switch (options.salary_range_settings.currentSalaryType) {
									case 'annual':
										salarySliderObj.slider('option', {
											min: parseInt(options.salary_range_settings.annual_package_min),
											max: parseInt(options.salary_range_settings.annual_package_max),
											step: parseInt(options.salary_range_settings.annual_package_step),
											values: [parseInt(options.salary_range_settings.annual_package_min), parseInt(options.salary_range_settings.annual_package_max)]
										});
										$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + salarySliderObj.slider("values", 0) +
											"K - $" + salarySliderObj.slider("values", 1) + "K");
										/*$( '#' + options.widgetId + '-salary-type' ).val('AnnualPackage');
										$( '#' + options.widgetId + '-salary-min' ).val(salarySliderObj.slider( "values", 0 )*1000);
										$( '#' + options.widgetId + '-salary-max' ).val(salarySliderObj.slider( "values", 1 )*1000);*/
										break;
									case 'hourly':
										salarySliderObj.slider('option', {
											min: parseInt(options.salary_range_settings.hourly_rate_min),
											max: parseInt(options.salary_range_settings.hourly_rate_max),
											step: parseInt(options.salary_range_settings.hourly_rate_step),
											values: [parseInt(options.salary_range_settings.hourly_rate_min), parseInt(options.salary_range_settings.hourly_rate_max)]
										});
										$('#' + options.widgetId + '-salary-switcher .ajb-salary-amount').html("$" + salarySliderObj.slider("values", 0) +
											" - $" + salarySliderObj.slider("values", 1) + "/hr");
										/*$( '#' + options.widgetId + '-salary-type' ).val('HourlyRate');
										$( '#' + options.widgetId + '-salary-min' ).val(salarySliderObj.slider( "values", 0 ));
										$( '#' + options.widgetId + '-salary-max' ).val(salarySliderObj.slider( "values", 1 ));*/
										break;
								}
							}
							$(document).trigger('adlogicJobSearch.searchWidget.submit', [self, options]);
							methods.submit.apply(self);
						});

						$(document).trigger('adlogicJobSearch.searchWidget.loaded', [self, options]);
					});
				},
				buildDropdowns: function () {
					// Check if there's an existing cache, if not create one
					if (typeof (adlogicJobSearch.cache) == 'undefined') {
						adlogicJobSearch.cache = {};
					}

					// If classifications drop down exists, build drop down
					if ($('#' + options.widgetId + '-classification_id').length > 0) {
						// Toggle loading class to add visual cue to element still loading
						$('#' + options.widgetId + '-classification_id').prev('label').toggleClass('loading');

						// Work out what URL to query API with based on whether we want an adCount or not
						if ((options.adCounts.classifications == true) || (options.hideEmpty.classifications == true)) {
							getClassificationsUrl = adlogicJobSearch.ajaxurl + '?action=getIndustries&jobCount=true';
						} else {
							getClassificationsUrl = adlogicJobSearch.ajaxurl + '?action=getIndustries';
						}

						// Check if only displaying top level
						if (options.topLevelOnly.classifications == true) {
							getClassificationsUrl += '&onlyFirstLevel=true';
						}

						// If data is not cached or doesn't contain the exact url we want then request the data
						if (
							(typeof (adlogicJobSearch.cache.classifications) == 'undefined') ||
							(
								(typeof (adlogicJobSearch.cache.classificationsUrl) == 'undefined') &&
								((options.adCounts.classifications == true) || (options.hideEmpty.classifications == true))
							) ||
							((typeof (adlogicJobSearch.cache.classificationsUrl) != 'undefined') && (getClassificationsUrl != adlogicJobSearch.cache.classificationsUrl))
						) {

							// Run the AJAX request
							$.ajax(getClassificationsUrl, {
								beforeSend: function (xhr) {
									$(window).bind('beforeunload', function () {
										xhr.abort();
									});
								},
								success: function (data) {
									// Save classification url to cache
									adlogicJobSearch.cache.classificationsUrl = getClassificationsUrl;

									// Build a data array with re-sorted classification data for double drop downs
									adlogicJobSearch.cache.classificationDataArray = new Array;

									// Add raw data to cache
									adlogicJobSearch.cache.classifications = data.cla;

									// Process data for easier sorting into select lists
									if (!$.isArray(data.cla)) {
										data.cla = new Array(data.cla);
									}
									$.each(data.cla, function (index) {
										classificationType = $(this)[0];
										if ((classificationType.parent == "0") || (typeof (classificationType.parent) === 'undefined')) {
											ad_count = (typeof (classificationType.adCount) == 'undefined') ? false : classificationType.adCount;
											adlogicJobSearch.cache.classificationDataArray.push({ id: classificationType.id, label: classificationType.displayName, count: ad_count, children: [] });
										} else {
											$.each(adlogicJobSearch.cache.classificationDataArray, function (claIdx, claObj) {
												ad_count = (typeof (classificationType.adCount) == 'undefined') ? false : classificationType.adCount;
												if (claObj.id == classificationType.parent) {
													claObj.children.push({ id: classificationType.id, label: classificationType.displayName, count: ad_count, children: [] });
												} else if (claObj.children.length > 0) {
													$.each(claObj.children, function (subClaIdx, subClaObj) {
														if (subClaObj.id == classificationType.parent) {
															subClaObj.children.push({ id: classificationType.id, label: classificationType.displayName, count: ad_count, children: [] });
														}
													});
												}
											});
										}
									});

									// Render Drop Down
									dropdownOptions = [{
										select: $('#' + options.widgetId + '-classification_id'),
										data: adlogicJobSearch.cache.classificationDataArray,
										type: 'classification'
									}];

									methods.renderDropdowns.apply(self, dropdownOptions);
								},
								statusCode: {
									// Handling 500 & 503 errors
									500: function () {
										// Set number of retries
										if (typeof (this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.classifications = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
										if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.classifications) }, (1000 * this.retries)); }
									},
									503: function () {
										// Set number of retries
										if (typeof (this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.classifications = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
										if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.classifications) }, (1000 * this.retries)); }
									}
								}
							});
						} else { // if data already exists don't re-request
							// Render Drop Down
							dropdownOptions = [{
								select: $('#' + options.widgetId + '-classification_id'),
								data: adlogicJobSearch.cache.classificationDataArray,
								type: 'classification'
							}];

							methods.renderDropdowns.apply(self, dropdownOptions);
						}
					}

					if (options.useNewLocation != 1 && options.useNewLocationAPI == 1) {
						// If locations drop down exists, build drop down
						if ($('#' + options.widgetId + '-location_id').length > 0) {

							// Toggle loading class to add visual cue to element still loading
							$('#' + options.widgetId + '-location_id').prev('label').toggleClass('loading');

							// Work out what URL to query API with based on whether we want an adCount or not
							if ((options.adCounts.locations == true) || (options.hideEmpty.locations == true)) {
								getLocationsUrl = adlogicJobSearch.ajaxurl + '?action=getLocations&jobCount=true';
							} else {
								getLocationsUrl = adlogicJobSearch.ajaxurl + '?action=getLocations';
							}

							// Check if only displaying top level
							if (options.topLevelOnly.locations == true) {
								getLocationsUrl += '&onlyFirstLevel=true';
							}

							// If data is not cached or doesn't contain the exact url we want then request the data
							if (
								(typeof (adlogicJobSearch.cache.locations) == 'undefined') ||
								(
									(typeof (adlogicJobSearch.cache.locationsUrl) == 'undefined') &&
									((options.adCounts.locations == true) || (options.hideEmpty.locations == true))
								) ||
								((typeof (adlogicJobSearch.cache.locationsUrl) != 'undefined') && (getLocationsUrl != adlogicJobSearch.cache.locationsUrl))
							) {

								// Run the AJAX request
								$.ajax(getLocationsUrl, {

									success: function (data) {
										//console.log(data);
										//console.log(data.countries[0].country.State[0].Suburbs);
										// Save location url to cache
										adlogicJobSearch.cache.locationsUrl = getLocationsUrl;

										// Build a data array with re-sorted location data for double drop downs
										adlogicJobSearch.cache.locationDataArray = new Array;
										//console.log(options.useNewLocationAPI);
										// Add raw data to cache
										//CHANGE THIS TO WORK WITH BOTH
										adlogicJobSearch.cache.locations = data.countries;

										//CHANGE THIS TO WORK WITH BOTH
										// Process data for easier sorting into select lists
										if (!$.isArray(data.countries)) {
											data.countries = new Array(data.countries);
										}

										// Track Current parent
										var currentParent = '0';
										//CHANGE THIS TO WORK WITH BOTH

										$.each(data.countries, function (index) {
											locationType = $(this)[0];
											country = locationType.country;
											//console.log(country);
											adlogicJobSearch.cache.locationDataArray.push({ id: country.id, label: country.name, count: false, children: country.state })
											//console.log(adlogicJobSearch.cache.locationDataArray)
											// if ((locationType.parent == "0") || (typeof(locationType.parent) === 'undefined')) {
											// 	ad_count = (typeof(locationType.adCount) == 'undefined') ? false : locationType.adCount;
											// 	adlogicJobSearch.cache.locationDataArray.push( { id: locationType.id, label: locationType.displayName, count: ad_count, children: [] } );
											// } else {
											//	$.each(adlogicJobSearch.cache.locationDataArray, function(locIdx, locObj) {
											// 		ad_count = (typeof(locationType.adCount) == 'undefined') ? false : locationType.adCount;
											// 		if (locObj.id == locationType.parent) {
											// 			locObj.children.push( { id: locationType.id, label: locationType.displayName, count: ad_count, children: [] } );
											// 		} else if (locObj.children.length > 0) {
											// 			$.each(locObj.children, function(subLocIdx, subLocObj) {
											// 				if (subLocObj.id == locationType.parent) {
											// 					subLocObj.children.push( { id: locationType.id, label: locationType.displayName, count: ad_count, children: [] } );
											// 				}
											// 			});
											// 		}
											// 	});
											// }
										});

										// Render Drop Down
										dropdownOptions = [{
											select: $('#' + options.widgetId + '-location_id'),
											data: adlogicJobSearch.cache.locationDataArray,
											type: 'location'
										}];

										methods.renderDropdowns.apply(self, dropdownOptions);
									},
									statusCode: {
										// Handling 500 & 503 errors
										500: function () {
											// Set number of retries
											if (typeof (this.retries) === 'undefined') { this.retries = 0 }
											// Increment number of retries
											this.retries++; ajax_handles.locations = this;
											// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
											if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.locations) }, (1000 * this.retries)); }
										},
										503: function () {
											// Set number of retries
											if (typeof (this.retries) === 'undefined') { this.retries = 0 }
											// Increment number of retries
											this.retries++; ajax_handles.locations = this;
											// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
											if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.locations) }, (1000 * this.retries)); }
										}
									}
								});
							} else { // if data already exists don't re-request
								// Render Drop Down
								dropdownOptions = [{
									select: $('#' + options.widgetId + '-location_id'),
									data: adlogicJobSearch.cache.locationDataArray,
									type: 'location'
								}];

								methods.renderDropdowns.apply(self, dropdownOptions);
							}
						}
					} else if (options.useNewLocation != 1 && options.useNewLocationAPI != 1) {

						// If locations drop down exists, build drop down
						if ($('#' + options.widgetId + '-location_id').length > 0) {

							// Toggle loading class to add visual cue to element still loading
							$('#' + options.widgetId + '-location_id').prev('label').toggleClass('loading');

							// Work out what URL to query API with based on whether we want an adCount or not
							if ((options.adCounts.locations == true) || (options.hideEmpty.locations == true)) {
								getLocationsUrl = adlogicJobSearch.ajaxurl + '?action=getLocations&jobCount=true';
							} else {
								getLocationsUrl = adlogicJobSearch.ajaxurl + '?action=getLocations';
							}

							// Check if only displaying top level
							if (options.topLevelOnly.locations == true) {
								getLocationsUrl += '&onlyFirstLevel=true';
							}

							// If data is not cached or doesn't contain the exact url we want then request the data
							if (
								(typeof (adlogicJobSearch.cache.locations) == 'undefined') ||
								(
									(typeof (adlogicJobSearch.cache.locationsUrl) == 'undefined') &&
									((options.adCounts.locations == true) || (options.hideEmpty.locations == true))
								) ||
								((typeof (adlogicJobSearch.cache.locationsUrl) != 'undefined') && (getLocationsUrl != adlogicJobSearch.cache.locationsUrl))
							) {

								// Run the AJAX request
								$.ajax(getLocationsUrl, {
									success: function (data) {
										// Save location url to cache
										adlogicJobSearch.cache.locationsUrl = getLocationsUrl;

										// Build a data array with re-sorted location data for double drop downs
										adlogicJobSearch.cache.locationDataArray = new Array;

										// Add raw data to cache
										adlogicJobSearch.cache.locations = data.loc;

										// Process data for easier sorting into select lists
										if (!$.isArray(data.loc)) {
											data.loc = new Array(data.loc);
										}

										// Track Current parent
										var currentParent = '0';

										$.each(data.loc, function (index) {
											locationType = $(this)[0];
											if ((locationType.parent == "0") || (typeof (locationType.parent) === 'undefined')) {
												ad_count = (typeof (locationType.adCount) == 'undefined') ? false : locationType.adCount;
												adlogicJobSearch.cache.locationDataArray.push({ id: locationType.id, label: locationType.displayName, count: ad_count, children: [] });
											} else {
												$.each(adlogicJobSearch.cache.locationDataArray, function (locIdx, locObj) {
													ad_count = (typeof (locationType.adCount) == 'undefined') ? false : locationType.adCount;
													if (locObj.id == locationType.parent) {
														locObj.children.push({ id: locationType.id, label: locationType.displayName, count: ad_count, children: [] });
													} else if (locObj.children.length > 0) {
														$.each(locObj.children, function (subLocIdx, subLocObj) {
															if (subLocObj.id == locationType.parent) {
																subLocObj.children.push({ id: locationType.id, label: locationType.displayName, count: ad_count, children: [] });
															}
														});
													}
												});
											}
										});

										// Render Drop Down
										dropdownOptions = [{
											select: $('#' + options.widgetId + '-location_id'),
											data: adlogicJobSearch.cache.locationDataArray,
											type: 'location'
										}];

										methods.renderDropdowns.apply(self, dropdownOptions);
									},
									statusCode: {
										// Handling 500 & 503 errors
										500: function () {
											// Set number of retries
											if (typeof (this.retries) === 'undefined') { this.retries = 0 }
											// Increment number of retries
											this.retries++; ajax_handles.locations = this;
											// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
											if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.locations) }, (1000 * this.retries)); }
										},
										503: function () {
											// Set number of retries
											if (typeof (this.retries) === 'undefined') { this.retries = 0 }
											// Increment number of retries
											this.retries++; ajax_handles.locations = this;
											// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
											if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.locations) }, (1000 * this.retries)); }
										}
									}
								});
							} else { // if data already exists don't re-request
								// Render Drop Down
								dropdownOptions = [{
									select: $('#' + options.widgetId + '-location_id'),
									data: adlogicJobSearch.cache.locationDataArray,
									type: 'location'
								}];

								methods.renderDropdowns.apply(self, dropdownOptions);
							}
						}

					}

					// If worktypes drop down exists, build drop down
					if ($('#' + options.widgetId + '-worktype_id').length > 0) {

						// Toggle loading class to add visual cue to element still loading
						$('#' + options.widgetId + '-worktype_id').prev('label').toggleClass('loading');

						// Work out what URL to query API with based on whether we want an adCount or not
						if ((options.adCounts.worktypes == true) || (options.hideEmpty.worktypes == true)) {
							getWorktypesUrl = adlogicJobSearch.ajaxurl + '?action=getWorktypes&jobCount=true';
						} else {
							getWorktypesUrl = adlogicJobSearch.ajaxurl + '?action=getWorktypes';
						}

						// Check if only displaying top level
						if (options.topLevelOnly.worktypes == true) {
							getWorktypesUrl += '&onlyFirstLevel=true';
						}

						// If data is not cached or doesn't contain the exact url we want then request the data
						if (
							(typeof (adlogicJobSearch.cache.worktypes) == 'undefined') ||
							(
								(typeof (adlogicJobSearch.cache.worktypesUrl) == 'undefined') &&
								((options.adCounts.worktypes == true) || (options.hideEmpty.worktypes == true))
							) ||
							((typeof (adlogicJobSearch.cache.worktypesUrl) != 'undefined') && (getWorktypesUrl != adlogicJobSearch.cache.worktypesUrl))
						) {
							adlogicJobSearch.cache.worktypesUrl = getWorktypesUrl;

							// Build a data array with re-sorted classification data for double drop downs
							adlogicJobSearch.cache.worktypeDataArray = new Array;

							// Run the AJAX request
							$.ajax(getWorktypesUrl, {
								beforeSend: function (xhr) {
									$(window).bind('beforeunload', function () {
										xhr.abort();
									});
								},
								success: function (data) {
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
									$.each(data.wor, function (index) {
										worktypeType = $(this)[0];
										if (worktypeType.parent == "0") {
											ad_count = (typeof (worktypeType.adCount) == 'undefined') ? false : worktypeType.adCount;
											adlogicJobSearch.cache.worktypeDataArray.push({ id: worktypeType.id, label: worktypeType.displayName, count: ad_count, children: [] });
										} else {
											$.each(adlogicJobSearch.cache.worktypeDataArray, function (worIdx, worObj) {
												ad_count = (typeof (worktypeType.adCount) == 'undefined') ? false : worktypeType.adCount;
												if (worObj.id == worktypeType.parent) {
													adlogicJobSearch.cache.worktypeDataArray.push({ id: worktypeType.id, label: worktypeType.displayName, count: ad_count });
												}
											});
										}
									});

									// Render Drop Down
									dropdownOptions = [{
										select: $('#' + options.widgetId + '-worktype_id'),
										data: adlogicJobSearch.cache.worktypeDataArray,
										type: 'worktype'
									}];

									methods.renderDropdowns.apply(self, dropdownOptions);
								}, statusCode: {
									// Handling 500 & 503 errors
									500: function () {
										// Set number of retries
										if (typeof (this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.worktypes = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
										if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.worktypes) }, (1000 * this.retries)); }
									},
									503: function () {
										// Set number of retries
										if (typeof (this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.worktypes = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
										if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.worktypes) }, (1000 * this.retries)); }
									}
								}
							});
						} else { // if data already exists don't re-request
							// Render Drop Down
							dropdownOptions = [{
								select: $('#' + options.widgetId + '-worktype_id'),
								data: adlogicJobSearch.cache.worktypeDataArray,
								type: 'worktype'
							}];

							methods.renderDropdowns.apply(self, dropdownOptions);
						}
					}

					// If Cost Center drop down exists, build drop down
					if ($('#' + options.widgetId + '-costcenter_id').length > 0) {

						// Toggle loading class to add visual cue to element still loading
						$('#' + options.widgetId + '-costcenter_id').prev('label').toggleClass('loading');

						// Work out what URL to query API with based on whether we want an adCount or not
						if ((options.adCounts.costcenters == true) || (options.hideEmpty.costcenters == true)) {
							getCostcentersUrl = adlogicJobSearch.ajaxurl + '?action=getCostCenters&jobCount=true';
						} else {
							getCostcentersUrl = adlogicJobSearch.ajaxurl + '?action=getCostCenters';
						}

						// Check if only displaying top level
						if (options.topLevelOnly.worktypes == true) {
							getWorktypesUrl += '&onlyFirstLevel=true';
						}

						// If data is not cached or doesn't contain the exact url we want then request the data
						if (
							(typeof (adlogicJobSearch.cache.costcenters) == 'undefined') ||
							(
								(typeof (adlogicJobSearch.cache.costcentersUrl) == 'undefined') &&
								((options.adCounts.costcenters == true) || (options.hideEmpty.costcenters == true))
							) ||
							((typeof (adlogicJobSearch.cache.costcentersUrl) != 'undefined') && (getCostcentersUrl != adlogicJobSearch.cache.costcentersUrl))
						) {
							adlogicJobSearch.cache.costcentersUrl = getCostcentersUrl;

							// Build a data array with re-sorted classification data for double drop downs
							adlogicJobSearch.cache.costcenterDataArray = new Array;

							// Run the AJAX request
							$.ajax(getCostcentersUrl, {
								success: function (data) {
									// Save costcenter url to cache
									adlogicJobSearch.cache.costcentersUrl = getCostcentersUrl;

									// Build a data array with re-sorted worktype data for double drop downs
									adlogicJobSearch.cache.costcenterDataArray = new Array;

									// Add raw data to cache
									adlogicJobSearch.cache.costcenters = data.costcenter;

									// Process data for easier sorting into select lists
									if (!$.isArray(data.costcenter)) {
										data.costcenter = new Array(data.costcenter);
									}
									$.each(data.costcenter, function (index) {
										costcenterType = $(this)[0];
										ad_count = (typeof (costcenterType.adCount) == 'undefined') ? false : costcenterType.adCount;
										adlogicJobSearch.cache.costcenterDataArray.push({ id: costcenterType.id, label: costcenterType.displayName, count: ad_count, children: [] });
									});

									// Render Drop Down
									dropdownOptions = [{
										select: $('#' + options.widgetId + '-costcenter_id'),
										data: adlogicJobSearch.cache.costcenterDataArray,
										type: 'costcenter'
									}];

									methods.renderDropdowns.apply(self, dropdownOptions);
								}, statusCode: {
									// Handling 500 & 503 errors
									500: function () {
										// Set number of retries
										if (typeof (this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.costcenters = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
										if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.costcenters) }, (1000 * this.retries)); }
									},
									503: function () {
										// Set number of retries
										if (typeof (this.retries) === 'undefined') { this.retries = 0 }
										// Increment number of retries
										this.retries++; ajax_handles.costcenters = this;
										// If total retries under 5 then retry connection, delay is increased by 1s for each subsequent connection
										if (this.retries <= 5) { setTimeout(function () { $.ajax(ajax_handles.costcenters) }, (1000 * this.retries)); }
									}
								}
							});
						} else { // if data already exists don't re-request
							// Render Drop Down
							dropdownOptions = [{
								select: $('#' + options.widgetId + '-costcenter_id'),
								data: adlogicJobSearch.cache.costcenterDataArray,
								type: 'costcenter'
							}];

							methods.renderDropdowns.apply(self, dropdownOptions);
						}
					}
				},
				renderDropdowns: function (opt) {
					var dropdownType;
					var adCounts;
					var hideEmpty;

					// Generate Select Options
					switch (opt.type) {
						case 'classification':
							selectOptions = {
								dropdownType: options.dropdownType.classifications,
								adCounts: options.adCounts.classifications,
								hideEmpty: options.hideEmpty.classifications,
								rawcache: adlogicJobSearch.cache.classifications,
								doubleSelectId: '#' + options.widgetId + '-classification_select_id',
								subSelectId: '#' + options.widgetId + '-sub_classification_select_id',
								translation: options.translations.classifications,
								sub_translation: options.translations.sub_classifications,
								selectedValue: (typeof (indIdDef) != 'undefined' ? indIdDef : null),
								showThirdLevel: options.showThirdLevel.classifications
							}
							break;
						case 'location':
							selectOptions = {
								dropdownType: options.dropdownType.locations,
								adCounts: options.adCounts.locations,
								hideEmpty: options.hideEmpty.locations,
								rawcache: adlogicJobSearch.cache.locations,
								doubleSelectId: '#' + options.widgetId + '-location_select_id',
								subSelectId: '#' + options.widgetId + '-sub_location_select_id',
								subSubSelectId: '#' + options.widgetId + '-sub_sub_location_select_id',
								translation: options.translations.locations,
								sub_translation: options.translations.sub_locations,
								sub_sub_translation: options.translations.sub_sub_locations,
								selectedValue: (typeof (locIdDef) != 'undefined' ? locIdDef : null),
								showThirdLevel: options.showThirdLevel.locations
							}
							break;
						case 'worktype':
							selectOptions = {
								dropdownType: 'flat', // As worktypes have no children there is no other dropdown type
								adCounts: options.adCounts.worktypes,
								hideEmpty: options.hideEmpty.worktypes,
								rawcache: adlogicJobSearch.cache.worktypes,
								translation: options.translations.worktype,
								doubleSelectId: null,
								subSelectId: null,
								sub_translation: '',
								selectedValue: (typeof (wtIdDef) != 'undefined' ? wtIdDef : null),
								showThirdLevel: options.showThirdLevel.worktypes
							}
							break;
						case 'costcenter':
							selectOptions = {
								dropdownType: 'flat', // As worktypes have no children there is no other dropdown type
								adCounts: options.adCounts.costcenters,
								hideEmpty: options.hideEmpty.costcenters,
								rawcache: adlogicJobSearch.cache.costcenters,
								translation: options.translations.costcenter,
								doubleSelectId: null,
								subSelectId: null,
								sub_translation: '',
								selectedValue: (typeof (ccIdDef) != 'undefined' ? ccIdDef : null),
								showThirdLevel: options.showThirdLevel.costcenters
							}
							break;
					}

					switch (selectOptions.dropdownType) {
						case 'single':
						case 'multiple':
							opt.select.append('<option value="">All ' + selectOptions.translation + '</option>');
							$.each(opt.data, function (dataIdx, dataObj) {
								if ((selectOptions.hideEmpty == true) && (dataObj.count == 0)) {
									return true;
								}

								if (selectOptions.adCounts == true) {
									optionEl = $('<option value="' + dataObj.id + '" class="ajb-parent-opt"><strong>' + dataObj.label + ' (' + dataObj.count + ')</strong></option>');
								} else {
									optionEl = $('<option value="' + dataObj.id + '" class="ajb-parent-opt"><strong>' + dataObj.label + '</strong></option>');
								}

								opt.select.append(optionEl);
								$.each(dataObj.children, function (optionIdx, optionObj) {
									if ((selectOptions.hideEmpty == true) && (optionObj.count == 0)) {
										return true;
									}

									if (selectOptions.adCounts == true) {
										if (optionObj.children.length > 0) {
											opt.select.append('<option value="' + optionObj.id + '" class="ajb-sub-parent-opt">' + (options.chosenEnabled ? '' : '&nbsp;&nbsp;&nbsp;') + optionObj.label + ' (' + optionObj.count + ')</option>');
										} else {
											opt.select.append('<option value="' + optionObj.id + '" class="ajb-child-opt">' + (options.chosenEnabled ? '' : '&nbsp;&nbsp;&nbsp;') + optionObj.label + ' (' + optionObj.count + ')</option>');
										}
									} else {
										if (optionObj.children.length > 0) {
											opt.select.append('<option value="' + optionObj.id + '" class="ajb-sub-parent-opt">' + (options.chosenEnabled ? '' : '&nbsp;&nbsp;&nbsp;') + optionObj.label + '</option>');
										} else {
											opt.select.append('<option value="' + optionObj.id + '" class="ajb-child-opt">' + (options.chosenEnabled ? '' : '&nbsp;&nbsp;&nbsp;') + optionObj.label + '</option>');
										}
									}

									if ((optionObj.children.length > 0) && (selectOptions.showThirdLevel == true)) {
										$.each(optionObj.children, function (subOptionIdx, subOptionObj) {
											if ((selectOptions.hideEmpty == true) && (subOptionObj.count == 0)) {
												return true;
											}

											if (selectOptions.adCounts == true) {
												opt.select.append('<option value="' + subOptionObj.id + '" class="ajb-sub-parent-child-opt">' + (options.chosenEnabled ? '' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') + subOptionObj.label + ' (' + subOptionObj.count + ')</option>');
											} else {
												opt.select.append('<option value="' + subOptionObj.id + '" class="ajb-sub-parent-child-opt">' + (options.chosenEnabled ? '' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') + subOptionObj.label + '</option>');
											}
										});
									}
								});
							});

							if (
								(typeof (selectOptions.selectedValue) != 'undefined') &&
								(selectOptions.selectedValue != null)
							) {
								opt.select.val(selectOptions.selectedValue.split(','));
							}
							break;
						case 'double':
							if (options.useNewLocationAPI == 0) {
								$(selectOptions.doubleSelectId).append('<option value="">All ' + selectOptions.translation + '</option>');
								$(selectOptions.subSelectId).append('<option value="">All ' + selectOptions.sub_translation + '</option>');

								// Populate dropdown values
								$.each(opt.data, function (optionIdx, optionObj) {
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
								$(selectOptions.doubleSelectId).change(selectOptions, function (e) {
									//selectOptions = e.data.selectOptions;
									if ($(e.target).val() != '') {
										$(e.data.subSelectId).empty();
										opt.select.val($(e.target).val());
										$.each(opt.data, function (optionIdx, optionObj) {
											if (optionObj.id == $(e.target).val()) {
												if ((e.data.hideEmpty == true) && (optionObj.count == 0)) {
													return true;
												}

												if (e.data.adCounts == true) {
													$(e.data.subSelectId).append('<option value="' + $(e.target).val() + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
												} else {
													$(e.data.subSelectId).append('<option value="' + $(e.target).val() + '">All ' + optionObj.label + ' Jobs</option>');
												}

												$.each(optionObj.children, function (childOptionIdx, childOptionObj) {
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
									$(document).trigger('adlogicJobSearch.searchWidget.selectUpdate', [self, options]);
								});

								$(selectOptions.subSelectId).change(function (e) {
									opt.select.val($(e.target).val());
									$(document).trigger('adlogicJobSearch.searchWidget.selectUpdate', [self, options]);
								});

								if (
									(typeof (selectOptions.selectedValue) != 'undefined') &&
									(selectOptions.selectedValue != null)
								) {

									// In the case of multiple selected values, select only the first one
									selectOptions.selectedValue = selectOptions.selectedValue.split(',')[0];

									opt.select.val(selectOptions.selectedValue);

									$.each(selectOptions.rawcache, function (dataIdx, dataObj) {
										if (selectOptions.selectedValue == dataObj.id) {
											if (dataObj.parent == '0') {
												$(selectOptions.doubleSelectId).val(dataObj.id);
												$(selectOptions.subSelectId).empty();
												$.each(opt.data, function (optionIdx, optionObj) {
													if (optionObj.id == dataObj.id) {
														if (selectOptions.adCounts == true) {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
														} else {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs</option>');
														}
														$.each(optionObj.children, function (childOptionIdx, childOptionObj) {
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
												$.each(opt.data, function (optionIdx, optionObj) {
													if (optionObj.id == dataObj.parent) {
														if (selectOptions.adCounts == true) {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
														} else {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs</option>');
														}

														$.each(optionObj.children, function (childOptionIdx, childOptionObj) {
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
							} else {
								//NewLocationAPI
								$(selectOptions.doubleSelectId).append('<option value="">All ' + selectOptions.translation + '</option>');
								$(selectOptions.subSelectId).append('<option value="">All ' + selectOptions.sub_translation + '</option>');
								$(selectOptions.subSubSelectId).append('<option value="">All ' + selectOptions.sub_sub_translation + '</option>');
								// Populate dropdown values
								$.each(opt.data, function (optionIdx, optionObj) {
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
								$(selectOptions.subSubSelectId).unbind('change');

								// Attach Change Event
								$(selectOptions.doubleSelectId).change(selectOptions, function (e) {
									//selectOptions = e.data.selectOptions;

									var a = document.getElementById(options.widgetId + "-location_select_id");
									if (a.selectedIndex > 0) {
										suburbStr = "";
										states = opt.data[a.selectedIndex - 1];
										//console.log(states);
										$.each(states.children, function (optionIdx, optionObj) {
											//console.log(optionObj);

											//console.log(childObj);
											$.each(optionObj.suburbs, function (optionIdx, subObj) {
												//console.log(subObj);
												suburbStr = suburbStr.concat(subObj.id, ",");
											});


										});
										suburbStr = suburbStr.substring(0, suburbStr.length - 1);
										//console.log(suburbStr);
										opt.select.val('');
										opt.select.val(suburbStr);
										if ($(e.target).val() != '') {
											$(e.data.subSelectId).empty();
											//opt.select.val($(e.target).val());
											$.each(opt.data, function (optionIdx, optionObj) {
												if (optionObj.id == $(e.target).val()) {
													if ((e.data.hideEmpty == true) && (optionObj.count == 0)) {
														return true;
													}

													if (e.data.adCounts == true) {
														$(e.data.subSelectId).append('<option value="">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
													} else {

														$(e.data.subSelectId).append('<option value="' + suburbStr + '">All ' + optionObj.label + ' Jobs</option>');
													}

													$.each(optionObj.children, function (childOptionIdx, childOptionObj) {
														if ((e.data.hideEmpty == true) && (childOptionObj.count == 0)) {
															return true;
														}
														if (e.data.adCounts == true) {
															$(e.data.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + ' (' + childOptionObj.count + ')</option>');
														} else {
															$(e.data.subSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.name + '</option>');
														}
													});
													$(e.data.subSubSelectId).empty();
													$(e.data.subSubSelectId).append('<option value="">All ' + e.data.sub_sub_translation + '</option>');
												}
											});

										}








										//suburbStr = "";
										//suburbSubStr = "";
										//$(e.data.subSubSelectId).empty();
										//$(e.data.subSubSelectId).append('<option value="">All ' + e.data.sub_sub_translation + '</option>');
										//opt.select.val(suburbStr);
									} else {

										suburbStr = "";
										suburbSubStr = "";
										opt.select.val('');
										$(e.data.subSelectId).empty();
										$(e.data.subSelectId).append('<option value="">All ' + e.data.sub_translation + '</option>');
										$(e.data.subSubSelectId).empty();
										$(e.data.subSubSelectId).append('<option value="">All ' + e.data.sub_sub_translation + '</option>');
									}
									$(document).trigger('adlogicJobSearch.searchWidget.selectUpdate', [self, options]);
								});

								$(selectOptions.subSelectId).change(function (e) {
									selectOptions = {
										dropdownType: options.dropdownType.locations,
										adCounts: options.adCounts.locations,
										hideEmpty: options.hideEmpty.locations,
										rawcache: adlogicJobSearch.cache.locations,
										doubleSelectId: '#' + options.widgetId + '-location_select_id',
										subSelectId: '#' + options.widgetId + '-sub_location_select_id',
										subSubSelectId: '#' + options.widgetId + '-sub_sub_location_select_id',
										translation: options.translations.locations,
										sub_translation: options.translations.sub_locations,
										sub_sub_translation: options.translations.sub_sub_locations,
										selectedValue: (typeof (locIdDef) != 'undefined' ? locIdDef : null),
										showThirdLevel: options.showThirdLevel.locations
									}
									//selectOptions = e.data.selectOptions;
									e.data = selectOptions;
									var a = document.getElementById(options.widgetId + "-location_select_id");
									var b = document.getElementById(options.widgetId + "-sub_location_select_id")
									if (b.selectedIndex > 0) {
										suburbSubStr = '';
										states = opt.data[a.selectedIndex - 1];
										
										suburbs = states.children[b.selectedIndex - 1];
										$.each(suburbs.suburbs, function (optionIdx, optionObj) {
											




											suburbSubStr = suburbSubStr.concat(optionObj.id, ",");



										});
										suburbSubStr = suburbSubStr.substring(0, suburbSubStr.length - 1);
										
										opt.select.val(suburbSubStr);
									}
									else {
										suburbSubStr = "";
										opt.select.val(suburbStr);
										$(e.data.subSubSelectId).empty();
									}

									if ($(e.target).val() != suburbStr) {
										$(e.data.subSubSelectId).empty();
										//opt.select.val($(e.target).val());
										//$.each(states.children, function (optionIdx, optionObj) {
										optionObj = states.children[b.selectedIndex - 1];

										if (typeof optionObj !== 'undefined') {
											if (e.data.adCounts == true) {
												$(e.data.subSubSelectId).append('<option value=""> All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
											} else {
												$(e.data.subSubSelectId).append('<option value="' + suburbSubStr + '">All ' + optionObj.name + ' Jobs</option>');
											}

											$.each(optionObj.suburbs, function (childOptionIdx, childOptionObj) {
												if ((e.data.hideEmpty == true) && (childOptionObj.count == 0)) {
													return true;
												}
												if (e.data.adCounts == true) {
													$(e.data.subSubSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.label + ' (' + childOptionObj.count + ')</option>');
												} else {
													$(e.data.subSubSelectId).append('<option value="' + childOptionObj.id + '">' + childOptionObj.name + '</option>');
												}
											});
										} else {
											$(e.data.subSubSelectId).append('<option value="">All ' + selectOptions.sub_sub_translation + '</option>');
											opt.select.val(suburbStr);
										}



										//});
									} else {
										opt.select.val(suburbStr);
										$(e.data.subSubSelectId).empty();
										$(e.data.subSubSelectId).append('<option value="">All ' + e.data.sub_sub_translation + '</option>');
									}
									$(document).trigger('adlogicJobSearch.searchWidget.selectUpdate', [self, options]);


									//opt.select.val($(e.target).val());
									//$(document).trigger('adlogicJobSearch.searchWidget.selectUpdate', [self, options]);
								});

								$(selectOptions.subSubSelectId).change(function (e) {
									opt.select.val($(e.target).val());
								});


								if (
									(typeof (selectOptions.selectedValue) != 'undefined') &&
									(selectOptions.selectedValue != null)
								) {

									// In the case of multiple selected values, select only the first one
									selectOptions.selectedValue = selectOptions.selectedValue.split(',')[0];

									opt.select.val(selectOptions.selectedValue);

									$.each(selectOptions.rawcache, function (dataIdx, dataObj) {
										if (selectOptions.selectedValue == dataObj.id) {
											if (dataObj.parent == '0') {
												$(selectOptions.doubleSelectId).val(dataObj.id);
												$(selectOptions.subSelectId).empty();
												$.each(opt.data, function (optionIdx, optionObj) {
													if (optionObj.id == dataObj.id) {
														if (selectOptions.adCounts == true) {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
														} else {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs</option>');
														}
														$.each(optionObj.children, function (childOptionIdx, childOptionObj) {
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
												$.each(opt.data, function (optionIdx, optionObj) {
													if (optionObj.id == dataObj.parent) {
														if (selectOptions.adCounts == true) {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs (' + optionObj.count + ')</option>');
														} else {
															$(selectOptions.subSelectId).append('<option value="' + dataObj.id + '">All ' + optionObj.label + ' Jobs</option>');
														}

														$.each(optionObj.children, function (childOptionIdx, childOptionObj) {
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
							}
							break;
						case 'flat':
							opt.select.append('<option value="">All ' + selectOptions.translation + '</option>');

							$.each(opt.data, function (dataIdx, dataObj) {
								if ((selectOptions.hideEmpty == true) && (dataObj.count == 0)) {
									return true;
								}
								if (selectOptions.adCounts == true) {
									opt.select.append('<option value="' + dataObj.id + '">' + dataObj.label + ' (' + dataObj.count + ')</option>');
								} else {
									opt.select.append('<option value="' + dataObj.id + '">' + dataObj.label + '</option>');
								}
							});

							if (
								(typeof (selectOptions.selectedValue) != 'undefined') &&
								(selectOptions.selectedValue != null)
							) {
								opt.select.val(selectOptions.selectedValue.split(','));
							}
							break;
					}

					// Toggle loading class from select label
					opt.select.prev('label').toggleClass('loading');
				},
				submit: function () {

					//self.find('.ajb-view-all-jobs-button,.ajb-search-for-jobs-button').attr('disabled', true);

					// Get Form Values
					if (options.dropdownType.classifications == 'double') {
						selectedIndLabel = $('#' + options.widgetId + '-sub_classification_select_id option:selected').text();
					} else if (options.dropdownType.classifications == 'multiple') {
						selectedIndLabel = '';
						$('#' + options.widgetId + '-classification_id option:selected').each(function (id, opt) {
							selectedIndLabel += trim($(opt).text()) + '-';
						});

						selectedIndLabel = selectedIndLabel.substring(0, (selectedIndLabel.length - 1));
					} else {
						selectedIndLabel = $('#' + options.widgetId + '-classification_id option:selected').text();
					}

					if ((typeof ($('#' + options.widgetId + '-classification_id').val()) != 'undefined') && ($('#' + options.widgetId + '-classification_id').val() != null) && ($('#' + options.widgetId + '-classification_id').val() != '')) {
						if ($.isArray($('#' + options.widgetId + '-classification_id'))) {
							selectedIndValue = $('#' + options.widgetId + '-classification_id').val().join();
						} else {
							selectedIndValue = $('#' + options.widgetId + '-classification_id').val();
						}
					} else {
						selectedIndValue = '';
					}

					selectedLocObj = {};
					if (options.useNewLocation) {
						selectedLocLabel = $("#" + options.widgetId + "-location_select_id").val();
						selectedLocValue = '';
						if (MRP.StringUtil.hasData($("#" + options.widgetId + "-location_select_id").attr("geoLocationJson"))) {
							selectedLocObj = $("#" + options.widgetId + "-location_select_id").attr("geoLocationJson");
						}
					} else {
						if (options.dropdownType.locations == 'double') {
							selectedLocLabel = $('#' + options.widgetId + '-sub_location_select_id option:selected').text();
						} else if (options.dropdownType.locations == 'multiple') {
							selectedLocLabel = '';
							$('#' + options.widgetId + '-location_id option:selected').each(function (id, opt) {
								selectedLocLabel += trim($(opt).text()) + '-';
							});

							selectedLocLabel = selectedLocLabel.substring(0, (selectedLocLabel.length - 1));
						} else {
							selectedLocLabel = $('#' + options.widgetId + '-location_id option:selected').text();
						}

						if ((typeof ($('#' + options.widgetId + '-location_id').val()) != 'undefined') && ($('#' + options.widgetId + '-location_id').val() != null) && ($('#' + options.widgetId + '-location_id').val() != '')) {
							if ($.isArray($('#' + options.widgetId + '-location_id').val())) {
								selectedLocValue = $('#' + options.widgetId + '-location_id').val().join();
							} else {
								selectedLocValue = $('#' + options.widgetId + '-location_id').val();
							}
						} else {
							selectedLocValue = '';
						}
					}

					if (options.dropdownType.worktypes == 'multiple') {
						selectedWtLabel = '';
						$('#' + options.widgetId + '-worktype_id option:selected').each(function (id, opt) {
							selectedWtLabel += trim($(opt).text()) + '-';
						});

						selectedWtLabel = selectedWtLabel.substring(0, (selectedWtLabel.length - 1));
					} else {
						selectedWtLabel = $('#' + options.widgetId + '-worktype_id option:selected').text();
					}

					if ((typeof ($('#' + options.widgetId + '-worktype_id').val()) != 'undefined') && ($('#' + options.widgetId + '-worktype_id').val() != null) && ($('#' + options.widgetId + '-worktype_id').val() != '')) {
						if ($.isArray($('#' + options.widgetId + '-worktype_id').val())) {
							selectedWtValue = $('#' + options.widgetId + '-worktype_id').val().join();
						} else {
							selectedWtValue = $('#' + options.widgetId + '-worktype_id').val();
						}
					} else {
						selectedWtValue = '';
					}

					selectedSalaryType = $('#' + options.widgetId + '-salary-type').val();
					selectedSalaryMin = $('#' + options.widgetId + '-salary-min').val();
					selectedSalaryMax = $('#' + options.widgetId + '-salary-max').val();

					if ($('#' + options.widgetId + '-listing_type').val() != 'undefined') {
						selectedListingType = $('#' + options.widgetId + '-listing_type').val();
					}

					if ($('#' + options.widgetId + '-costcenter_id').val() != 'undefined') {
						selectedCostCenter = $('#' + options.widgetId + '-costcenter_id').val();
					}

					if ($('#' + options.widgetId + '-orgunit_id').val() != 'undefined') {
						selectedOrgUnit = $('#' + options.widgetId + '-orgunit_id').val();
					}

					if (
						(typeof (adlogicJobSearch.registered_search_results) != 'undefined') &&
						(adlogicJobSearch.registered_search_results.length > 0)
					) {
						adlogicJobSearch.discardHashChange = true;

						$.each(adlogicJobSearch.registered_search_results, function (index, searchObj) {
							currentOptions = searchObj.adlogicJobSearch('options');
							searchOptions = {
								location_id: selectedLocValue,
								industry_id: selectedIndValue,
								work_type_id: selectedWtValue,
								salary_type: selectedSalaryType,
								salary_min: selectedSalaryMin,
								salary_max: selectedSalaryMax,
								keywords: $('#' + options.widgetId + '-keywords').val(),
								currentPage: 0,
								from: 1,
								to: currentOptions.items_per_page,
								location_name: selectedLocLabel,
								industry_name: selectedIndLabel,
								worktype_name: selectedWtLabel,
								geoLocationJson: selectedLocObj
							};

							if (typeof (selectedListingType) !== 'undefined') {
								searchOptions.internalExternal = selectedListingType;
							}

							if (typeof (selectedCostCenter) !== 'undefined') {
								searchOptions.cost_center_id = selectedCostCenter;
							}

							if (typeof (selectedOrgUnit) !== 'undefined') {
								searchOptions.org_unit_id = selectedOrgUnit;
							}

							searchObj.adlogicJobSearch('options', {
								searchParams: searchOptions
							});

							searchObj.adlogicJobSearch('update', { hash_update: true });
						});
					} else {
						// build search URL
						searchUrl = options.searchUrl;
						searchUrl += (selectedIndValue ? 'Industry/' + uriSafe(selectedIndLabel) + '/' + selectedIndValue + '/' : '');
						searchUrl += (selectedLocValue ? 'Location/' + uriSafe(selectedLocLabel) + '/' + selectedLocValue + '/' : '');
						searchUrl += (selectedWtValue ? 'WorkType/' + uriSafe(selectedWtLabel) + '/' + selectedWtValue + '/' : '');
						searchUrl += (selectedSalaryType ? 'SalaryType/' + selectedSalaryType + '/' : '');
						searchUrl += (selectedSalaryMin ? 'SalaryMin/' + selectedSalaryMin + '/' : '');
						searchUrl += (selectedSalaryMax ? 'SalaryMax/' + selectedSalaryMax + '/' : '');

						if (typeof (selectedCostCenter) !== 'undefined') {
							searchUrl += 'CostCenter/' + selectedCostCenter + '/';
						}

						if (typeof (selectedOrgUnit) !== 'undefined') {
							searchUrl += 'OrgUnit/' + selectedOrgUnit + '/';
						}

						if (typeof (selectedListingType) != 'undefined') {
							searchUrl += 'ListingType/' + selectedListingType + '/';
						}

						selectedLocObj = {};
						if (options.useNewLocation) {
							selectedLocLabel = $("#" + options.widgetId + "-location_select_id").val();
							selectedLocValue = '';
							if (MRP.StringUtil.hasData($("#" + options.widgetId + "-location_select_id").attr("geoLocationJson"))) {
								selectedLocObj = $("#" + options.widgetId + "-location_select_id").attr("geoLocationJson");
								var json = JSON.parse(selectedLocObj);
								for (var int = 0; int < json.length; int++) {
									var obj = json[int];
									var type = json[int].type;
									// geoLocationDTO.setSuburb(jsonobject.getString("name"));
									// geoLocationDTO.setSuburbLat(jsonobject.getString("lat"));
									// geoLocationDTO.setSuburbLng(jsonobject.getString("lng"));
									// geoLocationDTO.setState(jsonobject.getString("name"));
									// geoLocationDTO.setStateCode(jsonobject.getString("code"));
									// geoLocationDTO.setCountry(jsonobject.getString("name"));
									// geoLocationDTO.setCountryCode(jsonobject.getString("code"));
									if (type === "locality") {
										searchUrl += "Locality/" + obj.name + "---coords=" + obj.lat + "," + obj.lng + "/";
										// objectUrl += uriSafe(obj.name)+"|||localityLat="+obj.lat+"|||localityLng="+obj.lng;
										// objectUrl += "&lat="+obj.lat;
										// objectUrl += "&lng="+obj.lng;
									} else if (type === "administrative_area_level_1") {
										// State/New South Wales---NSW
										searchUrl += "State/" + obj.name + "---" + obj.code + "/";
										// objectUrl += "|||state="+uriSafe(obj.name)+"|||stateCode="+uriSafe(obj.code);
										// objectUrl += "&administrative_area_level_1="+obj.name;
										// objectUrl += "&administrative_area_level_1_code="+obj.code;
									} else if (type === "country") {
										// Country/Australia---AU
										searchUrl += "Country/" + obj.name + "---" + obj.code + "/";
										// objectUrl += "|||country="+uriSafe(obj.name)+"|||countryCode="+uriSafe(obj.code);
										// objectUrl += "&country="+obj.name;
										// objectUrl += "&country_code="+obj.code;
									} else if (type === "postal_code") {
										// objectUrl += "|||postal_code="+obj.name;
									}
								}
							}
						}

						var safeKeyWord = uriSafe($('#' + options.widgetId + '-keywords').val());

						if (safeKeyWord.search("-") > -1) {
							safeKeyWord = safeKeyWord.replace(/-/g, "%20");
						}

						// if(MRP.StringUtil.hasData(objectUrl)) {
						// 	searchUrl += 'Locality/' + objectUrl;
						// }

						searchUrl += ($('#' + options.widgetId + '-keywords').val() ? 'Keywords/' + safeKeyWord + '/' : '');

						if (searchUrl == options.searchUrl) {
							searchUrl = searchUrl.replace('query/', '');
						}
						location.href = searchUrl;
					}
				}
			}

			return this.each(function (method) {
				// Method calling logic
				if (methods[method]) {
					return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
				} else if (typeof method === 'object' || !method) {
					return methods.init.apply(this, arguments);
				} else {
					$.error('Method ' + method + ' does not exist on jQuery.adlogicSearchWidget');
				}
			});
		}
	});
})(jQuery);
