/**
 * Search Page code
 */

adlogicJobSearch.registered_saved_jobs = [];
adlogicJobSearch.registered_saved_jobs_pagination_bars = [];
adlogicJobSearch.discardHashChange = false;

(function($) {
	//Bind a callback that executes when document.location.hash changes.
	$(window).bind( "hashchange", function(e) {
		if (adlogicJobSearch.discardHashChangeSavedJobs == false) {
			$.each(adlogicJobSearch.registered_saved_jobs, function(index,searchObj) {
				searchObj.adlogicSavedJobs('hashOptions', $.deparam.fragment())
			});

			if (typeof($.deparam.fragment().page) != 'undefined') {
				$.each(adlogicJobSearch.registered_saved_jobs, function(index,searchObj) {
					searchObj.adlogicSavedJobsPagination('setPage', $.deparam.fragment().page);
				});
			}
		} else {
			// Discard hash events
		}
		adlogicJobSearch.discardHashChangeSavedJobs = false;
	});
	
	$(document).ready(function() {
		if (!$.isEmptyObject( $.deparam.fragment() )) {
			$.each(adlogicJobSearch.registered_saved_jobs, function(index,searchObj) {
				searchObj.adlogicSavedJobs('hashOptions', $.deparam.fragment())
			});
			if (typeof($.deparam.fragment().page) != 'undefined') {
				$.each(adlogicJobSearch.registered_saved_jobs, function(index,searchObj) {
					searchObj.adlogicSavedJobsPagination('setPage', $.deparam.fragment().page);
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
	var initialized;

	var methods = {
			init : function( options ) { 
				var options = $.extend(defaults, options);
				// Check if Saved Jobs shortcode hasn't already been instantiated - if it has, return false
				$.each(adlogicJobSearch.registered_saved_jobs, function (idx, shortcode){
					if (shortcode == this) { initialized = true; }
				});

				if (initialized == true) { return false; }

				// If it hasn't been instantiated, let's activate shortcode
				adlogicJobSearch.registered_saved_jobs = adlogicJobSearch.registered_saved_jobs.concat(this);
				
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
							$.get(adlogicJobSearch.ajaxurl + '?action=removeSavedJob&jobAdId=' + jobAdId, function(data) {
								window.location.reload();
							});
						}

						$(this).toggleClass('saved');
					} else {
						adlogicJobSearch.sessionManager.adlogicSessionManager('showDialog');
					}
				});
			},
			getSavedJobs : function(opt) {
				$('.ajb-save-job').toggleClass('saved', true);
			},
			hashOptions : function (opt) {
				if ($.isEmptyObject(opt)) {
					options.searchParams = $.extend(true, {}, originalSearchParams);
					$.each(adlogicJobSearch.adlogicJobSearch.registered_saved_jobs_pagination_bars, function(index,paginationObj) {
						paginationObj.adlogicSavedJobsPagination('setPage', originalSearchParams.currentPage);
					});
					methods.update.apply(this, Array({force_update: false, hash_update: false}));
				} else {
					if (typeof(opt.page) != 'undefined') {
						options.searchParams.currentPage = parseInt(opt.page);
						options.searchParams.from = parseInt(opt.page*options.items_per_page)+1;
						options.searchParams.to = ((parseInt(opt.page)+1)*options.items_per_page);
					}

					if (typeof(opt.costCenter) != 'undefined') {
						options.searchParams.cost_center_id = opt.costCenter;
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

				//var searchUrl = '/jobs/ajaxServer.php?action=searchJobs&indId=' + $('#indId').val() + '&locId=' + $('#locId').val() + '&wtId='  + $('#wtId').val() + '&keyword=' + $('#keywords').val() + '&from=' + fromPage + '&to=' + toPage;

				var searchUrl = options.ajaxServer;
				var fragmentObject = {};

				if (typeof(options.searchParams.from) != 'undefined') {
					searchUrl += '&from=' + options.searchParams.from;
				}

				if (typeof(options.searchParams.to) != 'undefined') {
					searchUrl += '&to=' + options.searchParams.to;
				}

				fragmentObject.page = options.searchParams.currentPage;
				self.prepend('<span class="adlogic_saved_jobs_loading_div"></span>');
				$.blockUI.defaults.css = {};
				self.block({ message: '&nbsp;'});

				$.each(adlogicJobSearch.registered_saved_jobs_pagination_bars, function(index, paginationObj) {
					paginationObj.block({ message: null });
				});

				$.post(searchUrl, {'template': options.template}, function(data) {
					if (typeof(data.search_results_html) != 'undefined') {
						self.empty();
						self.append(data.search_results_html);

						// Discard hash changes so as to not force a repeated update of search
						if ((typeof(opts) == 'undefined') || (opts.hash_update == true)) {
							window.location.href = $.param.fragment(window.location.href, fragmentObject);
						}

						$.each(adlogicJobSearch.registered_saved_jobs_pagination_bars, function(index,searchObj) {
							paginationOptions = {
								results_count: data.search_results_attributes['@attributes'].count,
								current_page: options.searchParams.currentPage,
								items_per_page: data.search_results_attributes['@attributes'].resultsPerPage
							};

							searchObj.adlogicSavedJobsPagination('options', paginationOptions);
							searchObj.adlogicSavedJobsPagination('setPage', options.searchParams.currentPage);

							if ((typeof(Cufon) != 'undefined') && ($.isFunction(Cufon))) {
								Cufon.refresh();
							}
						});
					} else {
						//alert('No results received - please refresh page');
					}

					if (typeof(stButtons) == 'object') {
						stButtons.locateElements();
					}

					// Update saved jobs list
					methods.getSavedJobs.apply(this);

					self.unblock();
					$.each(adlogicJobSearch.registered_saved_jobs_pagination_bars, function(index, paginationObj) {
						paginationObj.unblock();
					});
				});
				self.remove('.adlogic_loading_div');
			}
	};

	$.fn.adlogicSavedJobs = function( method ) {

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicSavedJobs' );
		}
	};
})(jQuery);

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
		if (changePage == false) {
			if (defaults.bound_search == 'null') {
				$.each(adlogicJobSearch.registered_saved_jobs, function(index, obj) {
					adlogicJobSearch.discardHashChangeSavedJobs = true;
					fromPage = (pageNum) * options.items_per_page+1;
					toPage = ((pageNum+1) * options.items_per_page);
					obj.adlogicSavedJobs('options', {searchParams: {'from': fromPage, 'to': toPage, 'currentPage': pageNum }});
					obj.adlogicSavedJobs('update');
				});
				
			} else {
				fromPage = (pageNum) * options.items_per_page+1;
				toPage = ((pageNum+1) * options.items_per_page);
				$(defaults.bound_search).adlogicSavedJobs('options', {searchParams: {'from': fromPage, 'to': toPage, 'currentPage': pageNum }});
				$(defaults.bound_search).adlogicSavedJobs('update');
			}
		} else {
			changePage = false;
		}
	}

	var methods = {
			init : function( options ) { 
				adlogicJobSearch.registered_saved_jobs_pagination_bars = adlogicJobSearch.registered_saved_jobs_pagination_bars.concat(this);
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

	$.fn.adlogicSavedJobsPagination = function( method ) {

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicSavedJobsPagination' );
		}
	};
})(jQuery);