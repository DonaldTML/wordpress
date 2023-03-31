(function() {
	tinymce.create('tinymce.plugins.ajbJobDetails', {
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
			ed.addCommand('AJB_Add_Job_Details', function() {
				ed.windowManager.open({
					id : 'ajb-job-details',
					width : 480,
					height : "auto",
					wpDialog : true,
					title : 'Insert/edit Job Details Page shortcode'
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('ajbJobDetails', {
				title : 'Insert/Edit Job Details Shortcode',
				cmd : 'AJB_Add_Job_Details'
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
				longname : 'Adlogic Job Board - Job Details',
				author : 'adlogic',
				authorurl : 'http://www.adlogic.com.au/',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ajbJobDetails', tinymce.plugins.ajbJobDetails);
})();

(function($) {
	$('#ajb-job-details .ajb-dialog-cancel').click(function() {
		var ed = tinyMCE.activeEditor;
		ed.windowManager.close();
	});

	$('#ajb-job-details #ajb-job-details-shortcode').click(function() {
		var ed = tinyMCE.activeEditor;
		if ($('#ajb-job-details #ajb-job-details-template').val() == '') {
			ed.execCommand('mceInsertContent', false, '[adlogic_job_details]');
		} else if ($('#ajb-job-details #ajb-job-details-template').val() == 'custom') {
			ed.execCommand('mceInsertContent', false, '[adlogic_job_details template="' + $('#ajb-job-details #ajb-job-details-template').val() + '"]' + "<br/>" + '[/adlogic_job_details]');		} else {
			ed.execCommand('mceInsertContent', false, '[adlogic_job_details template="' + $('#ajb-job-details #ajb-job-details-template').val() + '"]');
		}
		ed.windowManager.close();
	});

})(jQuery);