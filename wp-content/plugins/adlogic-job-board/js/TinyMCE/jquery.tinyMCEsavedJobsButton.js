(function() {
	tinymce.create('tinymce.plugins.ajbSavedJobs', {
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
			ed.addCommand('AJB_Add_Saved_Jobs', function() {
				ed.windowManager.open({
					id : 'ajb-saved-jobs',
					width : 480,
					height : "auto",
					wpDialog : true,
					title : 'Insert/edit Saved Jobs Page shortcode'
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('ajbSavedJobs', {
				title : 'Insert/Edit Saved Jobs Shortcode',
				cmd : 'AJB_Add_Saved_Jobs'
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
				longname : 'Adlogic Job Board - Saved Jobs',
				author : 'adlogic',
				authorurl : 'http://www.adlogic.com.au/',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ajbSavedJobs', tinymce.plugins.ajbSavedJobs);
})();

(function($) {
	$('#ajb-saved-jobs .ajb-dialog-cancel').click(function() {
		var ed = tinyMCE.activeEditor;
		ed.windowManager.close();
	});

	$('#ajb-saved-jobs #ajb-saved-jobs-shortcode').click(function() {
		var ed = tinyMCE.activeEditor;
		if ($('#ajb-saved-jobs #ajb-saved-jobs-template').val() == '') {
			ed.execCommand('mceInsertContent', false, '[adlogic_saved_jobs]');
		} else if ($('#ajb-saved-jobs #ajb-saved-jobs-template').val() == 'custom') {
			ed.execCommand('mceInsertContent', false, '[adlogic_saved_jobs template="' + $('#ajb-saved-jobs #ajb-saved-jobs-template').val() + '"]' + "<br/>" + '[/adlogic_saved_jobs]');		} else {
			ed.execCommand('mceInsertContent', false, '[adlogic_saved_jobs template="' + $('#ajb-saved-jobs #ajb-saved-jobs-template').val() + '"]');
		}
		ed.windowManager.close();
	});

})(jQuery);