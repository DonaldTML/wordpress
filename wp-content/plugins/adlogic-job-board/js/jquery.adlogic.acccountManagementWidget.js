(function($) {
	$.fn.extend({
		adlogicAccountManagement: function(options) {
			var defaults = {
				ajaxServer: '' 
			}

			var options = $.extend(defaults, options);
			
			return this.each(function() {
				var obj = $(this);
				obj.find('.logout').click(function() {
					adlogicJobSearch.sessionManager.adlogicSessionManager('logout');
				});
			});
		}
	});
})(jQuery);