(function() {
	tinymce.create('tinymce.plugins.ajbJobApplication', {
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
			ed.addCommand('AJB_Add_Job_Application', function() {
				// Insert shortcode here
				ed.execCommand('mceInsertContent', false, '[adlogic_job_application]');
			});

			// Register example button
			ed.addButton('ajbJobApplication', {
				title : 'Insert/Edit Job Application Shortcode',
				cmd : 'AJB_Add_Job_Application'
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
				longname : 'Adlogic Job Board - Job Application',
				author : 'adlogic',
				authorurl : 'http://www.adlogic.com.au/',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ajbJobApplication', tinymce.plugins.ajbJobApplication);
})();