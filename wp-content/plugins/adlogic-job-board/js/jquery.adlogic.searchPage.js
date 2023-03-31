/**
 * Search Page code
 */

adlogicJobSearch.registered_search_results = [];
adlogicJobSearch.unregistered_search_results = [];
adlogicJobSearch.registered_pagination_bars = [];
adlogicJobSearch.registered_breadcrumbs = [];
adlogicJobSearch.discardHashChange = false;

// Search Results

$(document).ready(function () {
      
	create_custom_dropdowns();
	function filter(){
	   
	};

	function create_custom_dropdowns() {
		$('select').each(function (i, select) {
			if (!$(this).next().hasClass('dropdown-select')) {
				$(this).after('<div class="dropdown-select wide ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
				var dropdown = $(this).next();
				var options = $(select).find('option');
				var selected = $(this).find('option:selected');
				dropdown.find('.current').html(selected.data('display-text') || selected.text());
				options.each(function (j, o) {
					var display = $(o).data('display-text') || '';
					dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '">' + $(o).text() + '</li>');
				});
			}
		});
	
		$('.dropdown-select ul').before('<div class="dd-search"><input autocomplete="off"  class="dd-searchbox" type="text"></div>');
	}
	
	$(".dd-searchbox").keyup(function(){
		var valThis = $(this).val();
		console.log(valThis)
		$(this).parents(".dropdown-select").find(' ul > li').each(function(){
		 var text = $(this).text();
			(text.toLowerCase().indexOf(valThis.toLowerCase()) > -1) ? $(this).show() : $(this).hide();         
	   });
	})
	// Event listeners
	
	// Open/close
	$(document).on('click', '.dropdown-select', function (event) {
		if($(event.target).hasClass('dd-searchbox')){
			return;
		}
		$('.dropdown-select').not($(this)).removeClass('open');
		$(this).toggleClass('open');
		if ($(this).hasClass('open')) {
			$(this).find('.option').attr('tabindex', 0);
			$(this).find('.selected').focus();
		} else {
			$(this).find('.option').removeAttr('tabindex');
			$(this).focus();
		}
	});
	
	// Close when clicking outside
	$(document).on('click', function (event) {
		if ($(event.target).closest('.dropdown-select').length === 0) {
			$('.dropdown-select').removeClass('open');
			$('.dropdown-select .option').removeAttr('tabindex');
		}
		event.stopPropagation();
	});
   
	// Search
	
	// Option click
	$(document).on('click', '.dropdown-select .option', function (event) {
		$(this).closest('.list').find('.selected').removeClass('selected');
		$(this).addClass('selected');
		var text = $(this).data('display-text') || $(this).text();
		$(this).closest('.dropdown-select').find('.current').text(text);
		$(this).closest('.dropdown-select').prev('select').val($(this).data('value')).trigger('change');
	});
	
	// Keyboard events
	$(document).on('keydown', '.dropdown-select', function (event) {
		var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
		// Space or Enter
		//if (event.keyCode == 32 || event.keyCode == 13) {
		if (event.keyCode == 13) {
			if ($(this).hasClass('open')) {
				focused_option.trigger('click');
			} else {
				$(this).trigger('click');
			}
			return false;
			// Down
		} else if (event.keyCode == 40) {
			if (!$(this).hasClass('open')) {
				$(this).trigger('click');
			} else {
				focused_option.next().focus();
			}
			return false;
			// Up
		} else if (event.keyCode == 38) {
			if (!$(this).hasClass('open')) {
				$(this).trigger('click');
			} else {
				var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
				focused_option.prev().focus();
			}
			return false;
			// Esc
		} else if (event.keyCode == 27) {
			if ($(this).hasClass('open')) {
				$(this).trigger('click');
			}
			return false;
		}
	});
});
(function($) {
	//Bind a callback that executes when document.location.hash changes.
	$(window).bind( "hashchange", function(e) {
		if (adlogicJobSearch.discardHashChange == false) {
			$.each(adlogicJobSearch.registered_search_results, function(index,searchObj) {
				searchObj.adlogicJobSearch('hashOptions', $.deparam.fragment())
			});

			if (typeof($.deparam.fragment().page) != 'undefined') {
				$.each(adlogicJobSearch.registered_pagination_bars, function(index,searchObj) {
					searchObj.adlogicSearchPagination('setPage', $.deparam.fragment().page);
				});
			}
		} else {
			// Discard hash events
		}
		adlogicJobSearch.discardHashChange = false;
	});

	$(document).ready(function() {
		if (!$.isEmptyObject( $.deparam.fragment() )) {
			$.each(adlogicJobSearch.registered_search_results, function(index,searchObj) {
				searchObj.adlogicJobSearch('hashOptions', $.deparam.fragment())
			});
			if (typeof($.deparam.fragment().page) != 'undefined') {
				$.each(adlogicJobSearch.registered_pagination_bars, function(index,searchObj) {
					searchObj.adlogicSearchPagination('setPage', $.deparam.fragment().page);
				});
			}
		}
	});
})(jQuery);

(function($) {
	var defaults = {
			searchParams: {},
			template: null
		}

	var options = $.extend(defaults, options);
	var originalSearchParams = {};
	var savedJobsArray;

	var methods = {
			init : function( options ) {
				var options = $.extend(defaults, options);
				if (options.embedded_search == true) {
					adlogicJobSearch.unregistered_search_results = adlogicJobSearch.unregistered_search_results.concat(this);
				} else {
					adlogicJobSearch.registered_search_results = adlogicJobSearch.registered_search_results.concat(this);
				}
				if (typeof(options.searchParams) == 'object') {
					originalSearchParams = $.extend(true, {}, options.searchParams);
				}


				// Initialise Save Jobs Code
				// Get Saved Jobs
				methods.getSavedJobs.apply(this, Array({'refresh': true}));


				// Setup Saved Jobs events
				$(document).on('click', '.ajb-save-job', function(event) {
					jobAdId = parseInt($(this).attr('id').replace('save_job_id_',''));

					if (adlogicJobSearch.sessionManager.adlogicSessionManager('isLoggedIn')) {
						if ($(this).hasClass('saved')) {
							// Remove from saved jobs
							$.get(adlogicJobSearch.ajaxurl + '?action=removeSavedJob&jobAdId=' + jobAdId, function(data){
								$.each(savedJobsArray, function(idx,obj) {
									if (obj == jobAdId) {
										savedJobsArray.splice(idx);
										return true;
									}
								});
							});
						} else {
							$.get(adlogicJobSearch.ajaxurl + '?action=addSavedJob&jobAdId=' + jobAdId, function(data){
								savedJobsArray.push(jobAdId);
							});
						}

						$(this).toggleClass('saved');
					} else {
						adlogicJobSearch.sessionManager.adlogicSessionManager('showDialog');
					}
				});

				$(document).trigger('adlogicJobSearch.searchPage.init', [$(this), options]);
			},
			getSavedJobs : function(opt) {
				// If refresh option set, get from server, else get from cache
				if ((typeof(opt) != 'undefined') && (opt.refresh == true)) {
					if (options.ajaxServer.search('action=searchAllRecruiters') > 0) {
						method = 'getSavedJobIdsAllRecruiters';
					} else if (options.ajaxServer.search('action=searchArchiveAds') > 0) {
						method = 'getArchivedSavedJobIds';
					} else {
						method = 'getSavedJobIds';
					}
					$.ajax(adlogicJobSearch.ajaxurl + '?action=' + method, {
								beforeSend: function(xhr) {
									$(window).bind('beforeunload', function() {
										xhr.abort();
									});
								},
								complete: function(data) {
									if ($.isArray(data)) {
										$.each(data, function (idx,obj) {
											$('#save_job_id_' + obj).toggleClass('saved', true);
										});
										// set to variable
										savedJobsArray = data;
									}
								}
					});
					/*$.get(adlogicJobSearch.ajaxurl + '?action=' + method, function(data) {
						if ($.isArray(data)) {
							$.each(data, function (idx,obj) {
								$('#save_job_id_' + obj).toggleClass('saved', true);
							});
							// set to variable
							savedJobsArray = data;
						}
					});*/
				} else {
					if ($.isArray(savedJobsArray)) {
						$.each(savedJobsArray, function (idx,obj) {
							$('#save_job_id_' + obj).toggleClass('saved', true);
						});
					}
				}
			},
			hashOptions : function (opt) {
				if ($.isEmptyObject(opt)) {
					options.searchParams = $.extend(true, {}, originalSearchParams);
					$.each(adlogicJobSearch.registered_pagination_bars, function(index,searchObj) {
						searchObj.adlogicSearchPagination('setPage', originalSearchParams.currentPage);
					});
					methods.update.apply(this, Array({force_update: false, hash_update: false}));
				} else {
					if (typeof(opt.locId) != 'undefined') {
						options.searchParams.location_id = opt.locId;
					}

					if (typeof(opt.indId) != 'undefined') {
						options.searchParams.industry_id = opt.indId;
						options.searchParams.industry_name = opt.industry_name;
					}

					if (typeof(opt.wtId) != 'undefined') {
						options.searchParams.work_type_id = opt.wtId;
					}

					if (typeof(opt.salType) != 'undefined') {
						options.searchParams.salary_type = opt.salType;
					}

					if (typeof(opt.salMin) != 'undefined') {
						options.searchParams.salary_min = opt.salMin;
					}

					if (typeof(opt.salMax) != 'undefined') {
						options.searchParams.salary_max = opt.salMax;
					}

					if (typeof(opt.keyword) != 'undefined') {
						options.searchParams.keywords = opt.keyword;
					}

					if (typeof(opt.page) != 'undefined') {
						options.searchParams.currentPage = parseInt(opt.page);
						options.searchParams.from = parseInt(opt.page*options.items_per_page)+1;
						options.searchParams.to = ((parseInt(opt.page)+1)*options.items_per_page);
					}

					if (typeof(opt.costCenter) != 'undefined') {
						options.searchParams.cost_center_id = opt.costCenter;
					}

					if (typeof(opt.orgUnit) != 'undefined') {
						options.searchParams.org_unit_id = opt.orgUnit;
					}

					if (typeof(opt.listingType) != 'undefined') {
						options.searchParams.internalExternal = opt.listingType;
					}

					if (typeof(opt.geoLocationJson) != 'undefined') {
						options.searchParams.geoLocationJson = opt.geoLocationJson;
					}

					methods.update.apply(this, Array({force_update: false, hash_update: false}));
				}
			},
			options : function( opt ) {
				if (opt) {
					options = $.extend(true, options, opt);
				}

				return options;
			},
			update : function(opts) {
				var self = this;
				var loadingElement = $("#adlogic_job_loading");

				//var searchUrl = '/jobs/ajaxServer.php?action=searchJobs&indId=' + $('#indId').val() + '&locId=' + $('#locId').val() + '&wtId='  + $('#wtId').val() + '&keyword=' + $('#keywords').val() + '&from=' + fromPage + '&to=' + toPage;

				var searchUrl = options.ajaxServer;
				var fragmentObject = {};

				// Build Search Url
				if (typeof(options.searchParams.location_id) != 'undefined') {
					searchUrl += '&locId=' + options.searchParams.location_id;
					fragmentObject.locId = options.searchParams.location_id;
				}

				if (typeof(options.searchParams.childrenRecruiterIds) != 'undefined') {
					searchUrl += '&childrenRecruiterIds=' + options.searchParams.childrenRecruiterIds;
					fragmentObject.childrenRecruiterIds = options.searchParams.childrenRecruiterIds;
				}

				if (typeof(options.searchParams.industry_id) != 'undefined') {
					searchUrl += '&indId=' + options.searchParams.industry_id;
					fragmentObject.indId = options.searchParams.industry_id;
				}

				if (typeof(options.searchParams.work_type_id) != 'undefined') {
					searchUrl += '&wtId=' + options.searchParams.work_type_id;
					fragmentObject.wtId = options.searchParams.work_type_id;
				}

				if (typeof(options.searchParams.salary_type) != 'undefined') {
					searchUrl += '&salaryType=' + options.searchParams.salary_type;
					fragmentObject.salType = options.searchParams.salary_type;
				}

				if (typeof(options.searchParams.salary_min) != 'undefined') {
					searchUrl += '&salaryMin=' + options.searchParams.salary_min;
					fragmentObject.salMin = options.searchParams.salary_min;
				}

				if (typeof(options.searchParams.salary_max) != 'undefined') {
					searchUrl += '&salaryMax=' + options.searchParams.salary_max;
					fragmentObject.salMax = options.searchParams.salary_max;
				}

				if (typeof(options.searchParams.keywords) != 'undefined') {
					// 15th January 2015: Fix to allow & pass through the keyword search
					searchUrl += '&keyword=' + encodeURIComponent(options.searchParams.keywords);
					fragmentObject.keyword = options.searchParams.keywords;
				}

				if (typeof(options.searchParams.from) != 'undefined') {
					searchUrl += '&from=' + options.searchParams.from;
				}

				if (typeof(options.searchParams.to) != 'undefined') {
					searchUrl += '&to=' + options.searchParams.to;
				}

				if (typeof(options.searchParams.cost_center_id) != 'undefined') {
					searchUrl += '&costCenter=' + options.searchParams.cost_center_id;
					fragmentObject.costCenter = options.searchParams.cost_center_id;
				}

				if (typeof(options.searchParams.orgUnit) != 'undefined') {
					searchUrl += '&orgUnit=' + options.searchParams.orgUnit;
					fragmentObject.orgUnit = options.searchParams.orgUnit;
				}

				if (typeof(options.searchParams.internalExternal) != 'undefined') {
					searchUrl += '&internalExternal=' + options.searchParams.internalExternal;
					fragmentObject.listingType = options.searchParams.internalExternal;
				}
				if(typeof options.searchParams.geoLocationJson != "undefined") {
					searchUrl += '&geoLocationJson='+($.isEmptyObject(options.searchParams.geoLocationJson)?"":options.searchParams.geoLocationJson);
					fragmentObject.geoLocationJson = ($.isEmptyObject(options.searchParams.geoLocationJson)?"":options.searchParams.geoLocationJson);
				}

				fragmentObject.page = options.searchParams.currentPage;
				loadingElement.prepend('<span class="adlogic_search_loading_div"></span>');
				self.css('opacity', '0.35');
				$.blockUI.defaults.css = {};
				var customSearchMessage = 'Searching for all ';
				// Custom messages!
				if (typeof(options.searchParams.industry_name) != 'undefined') {
					customSearchMessage += options.searchParams.industry_name + ' jobs';
				} else {
					customSearchMessage += 'jobs';
				}
				if (typeof(options.searchParams.location_name) != 'undefined') {
					customSearchMessage += ' in ' + options.searchParams.location_name;
				}

				//self.block({ message: customSearchMessage});

				$.each(adlogicJobSearch.registered_pagination_bars, function(index, paginationObj) {
					paginationObj.css('opacity', '0.35');
					//paginationObj.block({ message: null });
				});

				// Trigger prior to ajax job search
				$(document).trigger('adlogicJobSearch.searchPage.pre_job_search', [self, options]);

				$.post(searchUrl, {'template': options.template}, function(data) {
					if (typeof(data.search_results_html) != 'undefined') {
						self.empty();
						self.append(data.search_results_html);

						// Discard hash changes so as to not force a repeated update of search
						if ((typeof(opts) == 'undefined') || (opts.hash_update == true)) {
							window.location.href = $.param.fragment(window.location.href, fragmentObject);
						}

						$.each(adlogicJobSearch.registered_pagination_bars, function(index,searchObj) {
							paginationOptions = {
								results_count: data.search_results_attributes['@attributes'].count,
								current_page: options.searchParams.currentPage,
								items_per_page: data.search_results_attributes['@attributes'].resultsPerPage
							};

							searchObj.adlogicSearchPagination('options', paginationOptions);
							searchObj.adlogicSearchPagination('setPage', options.searchParams.currentPage);

							if ((typeof(Cufon) != 'undefined') && ($.isFunction(Cufon))) {
								Cufon.refresh();
							}
						});
					} else {
						alert('No results received - please refresh page');
					}

					if (typeof(stButtons) == 'object') {
						stButtons.locateElements();
					}

					// Update saved jobs list
					methods.getSavedJobs.apply(this);
					//self.unblock();
					loadingElement.children('.adlogic_search_loading_div').remove();
					self.css('opacity', '1');
					$.each(adlogicJobSearch.registered_pagination_bars, function(index, paginationObj) {
						//paginationObj.unblock();
						paginationObj.css('opacity', '1');
					});

					// Trigger after to job search results
					$(document).trigger('adlogicJobSearch.searchPage.post_job_search', [self, options, data]);
				});

				self.remove('.adlogic_loading_div');
			}

	};

	$.fn.adlogicJobSearch = function( method ) {

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicJobSearch' );
		}
	};
})(jQuery);

// Search Pagination

(function($) {
	var defaults = {
			'bound_search': 'null', // Search pagination is bound to
			'results_count': '',
			'current_page': 0,
			'items_per_page': 10,
			'num_display_pages': 10
		}

	var options = $.extend(defaults, options);

	var changePage = false;

	var callbackFunction = function(pageNum, paginationObj) {
		//$(window).scrollTop(0);
		if (changePage == false) {
			if (options.bound_search == 'null') {
				$.each(adlogicJobSearch.registered_search_results, function(index, obj) {
					adlogicJobSearch.discardHashChange = true;
					fromPage = (pageNum) * options.items_per_page+1;
					toPage = ((pageNum+1) * options.items_per_page);
					obj.adlogicJobSearch('options', {searchParams: {'from': fromPage, 'to': toPage, 'currentPage': pageNum }});
					obj.adlogicJobSearch('update');
				});

			} else {
				fromPage = (pageNum) * options.items_per_page+1;
				toPage = ((pageNum+1) * options.items_per_page);
				$(options.bound_search).adlogicJobSearch('options', {searchParams: {'from': fromPage, 'to': toPage, 'currentPage': pageNum }});
				$(options.bound_search).adlogicJobSearch('update');
			}
		} else {
			changePage = false;
		}
	}

	var methods = {
			init : function( options ) {
				adlogicJobSearch.registered_pagination_bars = adlogicJobSearch.registered_pagination_bars.concat(this);
				var options = $.extend(defaults, options);
				changePage = true;
				this.pagination(options.results_count, {
					current_page: options.current_page,
					items_per_page: options.items_per_page ,
					num_display_entries: options.num_display_pages,
					num_edge_entries: 2,
					callback: callbackFunction
					}
				);
			},
			options : function( opt ) {
				if (opt) {
					options = $.extend(true, options, opt);
				}

				return options;
			},
			setPage : function( pageNum ) {
				changePage = true;
				this.pagination(options.results_count, {
					current_page: pageNum,
					items_per_page: options.items_per_page ,
					num_display_entries: options.num_display_pages,
					num_edge_entries: 2,
					callback: callbackFunction
					}
				);
			}
	};

	$.fn.adlogicSearchPagination = function( method ) {

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicSearchPagination' );
		}
	};
})(jQuery);

// Search Breadcrumbs
(function($) {

	var defaults = {
		'bound_search': 'null'
	}

	var options = $.extend(defaults, options);

	var content_replacements_array = new Array(
		'{classification}',
		'{location}',
		'{worktype}',
		'{keywords}',
		'{salary_minimum}',
		'{salary_maximum}',
		'{salary_type}',
		'{current_page}',
		'{total_pages}',
		'{results_per_page}',
		'{from_results}',
		'{to_results}',
		'{count_results}',
		'{total_results}'
	);

	var translationsRegExp = new RegExp ('{(classification|worktype|location) translation="([^"]*)"}', 'igm');

	var methods = {
		init : function( params ) {

			adlogicJobSearch.registered_breadcrumbs = adlogicJobSearch.registered_breadcrumbs.concat(this);
			options = $.extend(options, params);

			self = $(this);

			$(document).bind('adlogicJobSearch.searchPage.post_job_search', function(e, searchPageObj, options, data) {
				methods.update.apply(self, Array({'searchPageObj': searchPageObj, 'data': data}));
			});
		},
		update : function ( params ) {
			// Check if Breadcrumbs object is bound to a specific search object, and update bound search only if it matches the one triggered
			if (options.bound_search != 'null') {
				// Only update if search object is bound to this breadcrumbs object
				if (params.searchPageObj.attr('id') != $(options.bound_search).attr('id')) {
					return false;
				}
			}

			// Update Breadcrumbs object

			// Clear existing data
			this.empty();

			new_content = content = options.template;

			// Get search results query data
			search_query = params.data.search_results_query;

			// Prepare data replacement array

			// Classifications
			classificationsString = '';
			$.each(search_query.parameters.classifications, function(idx, cla) {
				if (idx != 0) { classificationsString += ', '}
				classificationsString += '<a href="">' + cla.displayName + '</a>';
			});
			// Locations
			locationsString = '';
			$.each(search_query.parameters.locations, function(idx, loc) {
				if (idx != 0) { locationsString += ', '}
				locationsString += '<a href="">' + loc.displayName + '</a>';
			});

			// Work Types
			worktypesString = '';
			$.each(search_query.parameters.worktypes, function(idx, wt) {
				if (idx != 0) { worktypesString += ', '}
				worktypesString += '<a href="">' + wt.displayName + '</a>';
			});

			data_replacements_array = new Array(
				classificationsString ? classificationsString : 'All Classifications',
				locationsString ? locationsString : 'All Locations',
				worktypesString ? worktypesString : 'All Work Types',
				search_query.parameters.keywords ? search_query.parameters.keywords : 'N/A',
				(search_query.parameters.salary.min ? '$' + (search_query.parameters.salary.type == 'HourlyRate' ?  search_query.parameters.salary.min : (search_query.parameters.salary.min/1000) + 'K') : 'N/A'),
				(search_query.parameters.salary.max ? '$' + (search_query.parameters.salary.type == 'HourlyRate' ?  search_query.parameters.salary.max : (search_query.parameters.salary.max/1000) + 'K') : 'N/A'),
				(search_query.parameters.salary.type ? (search_query.parameters.salary.type == 'HourlyRate' ? 'hour' : 'year') : 'N/A'),
				(search_query.pagination.from ? ((search_query.pagination.from > 0) ? (((search_query.pagination.from-1) / search_query.pagination.results_per_page)+1) : 1) : 1),
				Math.ceil(search_query.pagination.total_results/search_query.pagination.results_per_page),
				search_query.pagination.results_per_page,
				search_query.pagination.from,
				(search_query.pagination.to > search_query.pagination.total_results ? search_query.pagination.total_results : search_query.pagination.to),
				search_query.pagination.results_returned,
				search_query.pagination.total_results
			);

			// Translation Replacements
			while (translations = translationsRegExp.exec(content)) {
				switch (translations[1]) {
					case 'classification':
						translations[1] = classificationsString ? classificationsString : 'All ' + translations[2];
						break;
					case 'location':
						translations[1] = locationsString ? locationsString : 'All ' + translations[2];
						break;
					case 'worktype':
						translations[1] = worktypesString ? worktypesString : 'All ' + translations[2];
						break;
				}
				new_content = str_replace(translations[0], translations[1], new_content);
			}

			// Append breadcrumbs html to object
			this.append(str_replace(content_replacements_array, data_replacements_array, new_content));
		}
	}

	$.fn.adlogicSearchBreadcrumbs = function( method ) {

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicSearchPagination' );
		}
	};

})(jQuery);
