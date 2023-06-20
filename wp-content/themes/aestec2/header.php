<?php
$needCareerButton = false;
if (strpos($_SERVER['REQUEST_URI'], 'job-details/query')) {
	$needCareerButton = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<!--[if IE 7]><html lang="en" class="ie7"><![endif]-->
<!--[if IE 8]><html lang="en" class="ie8"><![endif]-->
<!--[if IE 9]><html lang="en" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><html lang="en"><![endif]-->
<!--[if !IE]><html lang="en"><![endif]-->

<head>
	<meta charset="utf-8">


	<link rel="stylesheet" type="text/css" href="<?php print get_template_directory_uri(); ?>/css/bootstrap.min.css" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php print get_template_directory_uri(); ?>/css/bootstrap-theme.css" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php print get_template_directory_uri(); ?>/css/adlogic-custom.css" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php print get_template_directory_uri(); ?>/fonts/stylesheet.css" type="text/css" media="screen" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">

	<title>Careers - Aestec Services<?php the_title(); ?></title>


	<!-- Favicon -->
	<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.png">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">


	

	<!-- Essential viewport settings -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">


	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<?php
	wp_head();
	?>


</head>

<body>





	<!-- START menu -->
	<nav class="jobboard-nav">
		<div class="navigation-container">
			<div class="nav-logo">
				<a href="https://www.aestec.com.au/"><img src="<?php echo get_template_directory_uri(); ?>/images/Aestec-Services-Logo.png" alt="" id="nav-logo"></a>
			</div>
			<div class="nav-buttons">
				<?php if (!$needCareerButton) { ?>
					<a href="https://www.aestec.com.au/" class="nav-button">Home Page</a>
				<?php } else { ?>
					<a href="https://jobboards.adlogic.com.au/aesteccareers/" class="nav-button">Job Search</a>
				<?php } ?>

			</div>
		</div>
	</nav>
	<!-- END menu -->