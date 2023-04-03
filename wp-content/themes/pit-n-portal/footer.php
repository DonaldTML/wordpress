<section id="footer">
	<div class="container">
		<div class="row footer">
			<div class="col-lg-12">
			<p><strong><a id="ml-link" href="https://martianlogic.com/" target="_blank">HRIS</a></strong> by <strong><a id="ml-link" href="https://martianlogic.com/" target="_blank">Martian Logic</a></strong></strong></p>
</div>
		</div>
	</div>
</section>

<?php wp_footer(); ?>

<script src="<?php print get_template_directory_uri(); ?>/js/scrollReveal.js"></script>
<script>
	window.scrollReveal = new scrollReveal();
	jQuery(document).ready(function($) {

		$(".position").each(function(i) {
			if ($(this).attr("standOutUrl") != '') {
				$(this).addClass('hasLogo');
				var standOutHtml = '<div class="mobileStandoutLogo" style="display:none;">' +
					'<img src="' + $(this).attr("standOutUrl") + '" />' +
					'</div>';
				$(this).append(standOutHtml);
			}
		});
	});
</script>
<script src="<?php print get_template_directory_uri(); ?>/js/smoothscroll.js"></script>
<script src="<?php print get_template_directory_uri(); ?>/js/jquery-1.11.2.min.js"></script>
<script src="<?php print get_template_directory_uri(); ?>/js/bootstrap.min.js"></script>
<script src="<?php print get_template_directory_uri(); ?>/js/modernizr.custom.js"></script>

</body>
<style>
	#jobTemplateApplyButtonId, #jobTemplateEmailButtonId {
		background: #f0d443 !important; 
		border: none !important;
	}
	div#mrp-main-wrapper {
    background: transparent !important;
}
.mrp-apply-button, .mrp-email-button {
	background: #d44a5e !important;
	background-image: none !important;
	border-radius: 0px !important;
	font-weight: bold !important;
	color: #FFFFFF !important;
	border: none !important;
	box-shadow: 4px 4px 8px 0 rgb(0 0 0 / 60%) !important;
    text-shadow: 1px 1px 3px rgb(0 0 0 / 45%) !important;
    font-size: 1.2vw !important;
}
.mrp-apply-button:hover, .mrp-email-button:hover  {
	color: #FFFFFF !important;
	text-decoration: none !important;
}
/* .mrp-email-button {
	color: #FFFFFF !important;
	border-color: #FFFFFF !important;
	border-radius: 25px !important;
	border-width: 2px !important;
    font-weight: bold !important;
}
.mrp-email-button:hover {
	color: #FFFFFF !important;
	border-color: #FFFFFF !important;
	background: #FFFFFF !important;
	border-radius: 25px !important;
} */
.ajb_social_sharing_site {
margin: 0px 5px 0px 5px !important;
}

.mrp-icon-box-text {
    color: #FFFFFF !important;
    font-weight: 300 !important;
    font-family: Montserrat, sans-serif !important
}
</style>
<script>
	// var applybtn = document.getElementById('jobTemplateApplyButtonId');
	// applybtn.style.setProperty('background', '#f0d443', 'important'); 
	// applybtn.style.setProperty('border-color', '#f0d443', 'important'); 
	// applybtn.style.setProperty('color', '#FFFFFF', 'important'); 
	// applybtn.style.setProperty('pointer-events', 'none', 'important'); 
	// var emailbtn = document.getElementById('jobTemplateEmailButtonId');
	// emailbtn.style.setProperty('border-color', '#f0d443', 'important'); 
	// emailbtn.style.setProperty('color', '#f0d443', 'important'); 
	// emailbtn.style.setProperty('pointer-events', 'none', 'important'); 
	
</script>
</html>