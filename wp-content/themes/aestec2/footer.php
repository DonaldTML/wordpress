<section id="footer">
	<div class="container">
		<div class="row footer">
			<div class="col-lg-12">
			<p><strong><a id="ml-link" href="https://martianlogic.com/" target="_blank">HRIS by </a></strong><strong><a id="ml-link" href="https://martianlogic.com/" target="_blank">Martian Logic</a></strong></strong></p>
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
	background: transparent  !important;
	background-image: none !important;
	font-weight: bold !important;
	border: 2px solid #3DAF2C !important;
	color: #3DAF2C !important;
	text-transform: uppercase !important;
	font-size: 16px !important;
	font-family: 'NexaBlack' !important;
}
.mrp-apply-button:hover, .mrp-email-button:hover {
	background: transparent !important;
	color: #000000  !important;
	border: 2px solid #3DAF2C  !important;
}
@media (max-width: 1000px){
    #footer {
        display: none !important;
    }
	.mrp-apply-button {
		background-color: #3DAF2C !important;
		color: #FFFFFF !important;
	}
	.mrp-apply-button:hover {
		background-color: #000000 !important;
	}
	.mrp-row > .mrp-col-sm-6 {
		flex: 0 0 100% !important; 
		max-width: 100% !important;
	}
	.apply-wrap-buttons > .mrp-row:last-child {
		flex-direction: column !important;
		width: 100% !important;
		margin-left: 0 !important;
		margin-right: 0 !important;
	}
}
/* .mrp-email-button {
	color: #583494 !important;
	border-color: #583494 !important;
	border-radius: 25px !important;
	border-width: 2px !important;
    font-weight: bold !important;
}
.mrp-email-button:hover {
	color: #FFFFFF !important;
	border-color: #583494 !important;
	background: #583494 !important;
	border-radius: 25px !important;
} */
.ajb_social_sharing_site {
margin: 0px 5px 0px 5px !important;
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