(function() {
	tinymce.create('tinymce.plugins.ajbJobSearch', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {

			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('AJB_Add_Job_Search', function() {
				ed.windowManager.open({
					id : 'ajb-job-search',
					width : 480,
					height : "auto",
					wpDialog : true,
					title : 'Insert/edit Job Search Page shortcode'
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('ajbJobSearch', {
				title : 'Insert/Edit Job Search Shortcode',
				cmd : 'AJB_Add_Job_Search'
			});
		},
		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Adlogic Job Board - Job Search',
				author : 'adlogic',
				authorurl : 'http://www.adlogic.com.au/',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ajbJobSearch', tinymce.plugins.ajbJobSearch);
})();

(function($) {
	$('#ajb-job-search .ajb-dialog-cancel').click(function() {
		var ed = tinyMCE.activeEditor;
		ed.windowManager.close();
	});

	$('#ajb-job-search #ajb-job-search-shortcode').click(function() {
		var ed = tinyMCE.activeEditor;
		if ($('#ajb-job-search #ajb-job-search-template').val() == '') {
			ed.execCommand('mceInsertContent', false, '[adlogic_search_results]');
		} else if ($('#ajb-job-search #ajb-job-search-template').val() == 'custom') {
			ed.execCommand('mceInsertContent', false, '[adlogic_search_results template="' + $('#ajb-job-search #ajb-job-search-template').val() + '"]' + "<br/>" + '[/adlogic_search_results]');	
		} else {
			ed.execCommand('mceInsertContent', false, '[adlogic_search_results template="' + $('#ajb-job-search #ajb-job-search-template').val() + '"]');
		}
		ed.windowManager.close();
	});

})(jQuery);