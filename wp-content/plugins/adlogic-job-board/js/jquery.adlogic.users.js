adlogicSessionManager = null;


(function($) {
	var defaults = {
			loggedIn: false
		}

	var options = $.extend(defaults, options);

	var self;

	var methods = {
			init : function( opt ) {
				var options = $.extend(defaults, opt);
				var self = this;
				adlogicJobSearch.sessionManager = $(this);
				$(this).dialog({ autoOpen: false, buttons: [ { text: 'Close', click: function() { $(this).dialog('close'); } } ] });
			},
			options : function( opt ) {
				if (opt) {
					options = $.extend(true, options, opt);
				}

				return options;
			},
			logout: function() {
				$.get(adlogicJobSearch.ajaxurl + '?action=logout', function(data) {
					if (data.result == true) {
						window.location.reload();
					} else {
					}
				});
			},
			showDialog : function ( opt ) {
				
				if (options.loggedIn == true) {
					$(this).dialog('option', 'buttons', [ { 
						text: 'Sign out', 
						click: function () {
							$(this).dialog('close');
							$(this).dialog('option', 'modal', true);
							$(this).dialog('open');
							$(this).dialog('option', 'buttons', [{
								text: 'Signing out ...',
								click: function() { }
							}]);
							methods.logout.apply( this );
						}
					},
					{ 
						text: 'Close', 
						click: function() { 
							$(this).dialog('close'); 
						} 
					}]);
				} else {
					$(this).dialog('option', 'buttons', [ { 
							text: 'Close', 
							click: function() { 
								$(this).dialog('close'); 
							} 
					}]);
				}

				$(this).dialog('open');
			},
			isLoggedIn : function() {
				return options.loggedIn;
			}
	};

	$.fn.adlogicSessionManager = function( method ) {
		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.adlogicSessionManager' );
		}
		/*return this.each(function() {
		})*/
	};
})(jQuery);