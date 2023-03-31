(function() {
	tinymce.create('tinymce.plugins.ajbSearchPagination', {
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
			ed.addCommand('AJB_Add_Search_Pagination', function() {
				ed.execCommand('mceInsertContent', false, '[adlogic_search_pagination]');
				// Not yet implemented
				/*ed.windowManager.open({
					id : 'ajb-search-pagination',
					width : 480,
					height : "auto",
					wpDialog : true,
					title : 'Insert/edit Search Pagination Page shortcode'
				}, {
					plugin_url : url // Plugin absolute URL
				});*/
			});

			// Register example button
			ed.addButton('ajbSearchPagination', {
				title : 'Insert/Edit Search Pagination Shortcode',
				cmd : 'AJB_Add_Search_Pagination'
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
				longname : 'Adlogic Job Board - Search Pagination',
				author : 'adlogic',
				authorurl : 'http://www.adlogic.com.au/',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ajbSearchPagination', tinymce.plugins.ajbSearchPagination);
})();