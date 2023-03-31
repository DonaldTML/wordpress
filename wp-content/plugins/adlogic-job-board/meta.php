<?php

	/* Define the custom box */
	
	add_action( 'add_meta_boxes', 'adlogic_add_meta_box' );
	
	// backwards compatible (before WP 3.0)
	add_action( 'admin_init', 'adlogic_add_meta_box', 1 );
	
	/* Do something with the data entered */
	add_action( 'save_post', 'adlogic_save_post' );

	function adlogic_add_meta_box() {
		add_meta_box('adlogic_job_board', 'Adlogic Job Board', 'adlogic_meta_box', 'page', 'side', 'low');
		add_meta_box('adlogic_job_board', 'Adlogic Job Board', 'adlogic_meta_box', 'post', 'side');
	}
	
	function adlogic_meta_box() {
		?>
			<h1>Short Code Reference</h1>
			<p><h2>Job Search Results</h2><br />
			<h4>Code:</strong> [adlogic_search_results]</h4>
			<strong>Description:</strong> <br />
			<p>This tag allows you to embed the adlogic search results section anywhere on your website.</p>
			<p>Options - </p>
			template=template_name<br/>
			(template_name - base, custom, template-x, template-y, etc.)<br />
			eg. [adlogic_search_results template=base]
			</p>
			<p>Setting template to custom allows building your own template for the search results using our template tags as below:</p>
			<p>
			<pre>
	{job_id} - Job unique Id
	{job_title} - Job title
	{job_description} - Job description
	{job_bulletpoints} - Job bullet point standouts
	{job_classification_breadcrumbs} - Job classification breadcrumb links
	{job_location_breadcrumbs} - Job location breadcrumb links
	{job_worktype_link} - Job work type link
			</pre>
			</p>
			<p>You can also use HTML in conjunction with your tags (nb: We recommend using html editor above, rather than visual edit to ensure proper code insertion).</p>
			<p>eg.
			<pre>
	[adlogic_search_results]
	[adlogic_search_results template=custom]
	&lt;h2&gt;&lt;a href="{job_link}"&gt;{job_title}&lt;/a&gt;&lt;/h2&gt;
	&lt;em&gt;{job_description}&lt;/em&gt;
	&lt;ul&gt;{job_bulletpoints}&lt;/ul&gt;
	Location:
	{job_location_breadcrumbs}
	Classification:
	{job_classification_breadcrumbs}
	Work Type:
	{job_worktype_link}
	[/adlogic_search_results]
			</pre>
			</p>
			
		<?php 
	}
	function adlogic_save_post() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	}
?>