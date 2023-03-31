(function() {
	tinymce.create('tinymce.plugins.ajbSearchBreadcrumbs', {
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
			ed.addCommand('AJB_Add_Search_Breadcrumbs', function() {
				ed.windowManager.open({
					id : 'ajb-search-breadcrumbs',
					width : 480,
					height : "auto",
					wpDialog : true,
					title : 'Insert/edit Search Breadcrumbs shortcode'
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('ajbSearchBreadcrumbs', {
				title : 'Insert/Edit Search Breadcrumbs Shortcode',
				cmd : 'AJB_Add_Search_Breadcrumbs'
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
				longname : 'Adlogic Job Board - Search Breadcrumbs',
				author : 'adlogic',
				authorurl : 'http://www.adlogic.com.au/',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ajbSearchBreadcrumbs', tinymce.plugins.ajbSearchBreadcrumbs);
})();

(function($) {
	$('#ajb-search-breadcrumbs .ajb-dialog-cancel').click(function() {
		var ed = tinyMCE.activeEditor;
		ed.windowManager.close();
	});

	$('#ajb-search-breadcrumbs #ajb-search-breadcrumbs-shortcode').click(function() {
		var ed = tinyMCE.activeEditor;
		if ($('#ajb-search-breadcrumbs #ajb-search-breadcrumbs-template').val() == '') {
			ed.execCommand('mceInsertContent', false, '[adlogic_search_breadcrumbs]');
		} else if ($('#ajb-search-breadcrumbs #ajb-search-breadcrumbs-template').val() == 'custom') {
			ed.execCommand('mceInsertContent', false, '[adlogic_search_breadcrumbs template="' + $('#ajb-search-breadcrumbs #ajb-search-breadcrumbs-template').val() + '"]' + "<br/>" + '[/adlogic_search_breadcrumbs]');		} else {
			ed.execCommand('mceInsertContent', false, '[adlogic_search_breadcrumbs template="' + $('#ajb-search-breadcrumbs #ajb-search-breadcrumbs-template').val() + '"]');
		}
		ed.windowManager.close();
	});

})(jQuery);