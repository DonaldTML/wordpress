<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Careers_v1
 */
$needCareerButton = false;
if(strpos($_SERVER['REQUEST_URI'], 'job-details/query')){
	$needCareerButton = true;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
	
    <!-- Favicon -->   
	<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" type="image/x-icon" />       
	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  	<meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/images/og.jpg" />
	<!-- CSS only -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css" integrity="sha384-VCmXjywReHh4PwowAiWNagnWcLhlEJLA5buUprzK8rxFgeH0kww/aWY76TfkUoSX" crossorigin="anonymous">
	<title>Careers at Mainfreight</title>
	<!-- JS, Popper.js, and jQuery -->
	<script rel="preload" src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script rel="preload" src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script rel="preload" src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/js/bootstrap.min.js" integrity="sha384-XEerZL0cuoUbHE4nZReLT7nx9gQrQreJekYhJD9WNWhH8nEW+0c5qq7aIo2Wl30J" crossorigin="anonymous"></script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="navbar navbar-expand-lg text-white" id="ice-cream-nav">
<div class="container nav-container">

		<a href="<?php echo get_home_url(); ?>" class="logo d-flex justify-content-center align-items-center" style=" margin-top: 18px; margin-bottom: 18px; max-width: 410px;">
			<img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" />
		</a>


  <?php if(!$needCareerButton) { ?>
    <form class="form-inline my-2 my-lg-0" action="https://www.mainfreight.com/">
      <a href="https://www.mainfreight.com/" class="main-site-btn-link"><button id="main-site-btn"  class="btn-searchTop my-2 my-sm-0" type="submit">Home</button></a>
    </form>
	<?php } else { ?>
		<form class="form-inline my-2 my-lg-0" action="https://jobboards.adlogic.com.au/mainfreight/">
      <a href="https://jobboards.adlogic.com.au/mainfreight/" class="main-site-btn-link"><button id="main-site-btn"  class="btn-searchTop my-sm-0" type="submit">Job Search</button></a>
    </form>	
	<?php } ?>
  </div>
</nav>
<style>
@media only screen and (max-width: 800px){
	.logo{
		max-width: 100% !important;
	}
	.nav-container{
		display: block !important;
	}
	#main-site-btn{
		width: 100% !important;
		max-width: 100% !important;
	}
	.main-site-btn-link{
		width: 100% !important;
		max-width: 100% !important;
	}
}

#ice-cream-nav{
    background: #FFF !important;
	height: 84px !important;
}

}
button.btn.btn-searchTop.my-2.my-sm-0 {
    color: #0091DA !important;
}
nav.navbar.navbar-expand-lg.navbar-white.text-white.bg-white {
    background: transparent !important;
}
.col-xs-12.col-sm-12.col-md-6.hidden-xs.hidden-sm img {
    display: block;
}
.col-xs-12.col-sm-12.col-md-6.hidden-xs.hidden-sm {
    display: inline-block;
    width: 600px;
}

.links {
    width: 49%;
    display: inline-block;
    padding-top: 1rem;
}
.dropdown-toggle::after{
  display:none;
}
a.nav-link {
    font-size: 19px;
}
.btn-searchTop{
    font-weight: bold;
    width: 150px !important;
    max-width: 150px;
    padding: 10px !important;
    color: #FFFFFF !important;
    border: none !important;
    background: #e4002b;
	border-radius: 0 !important;  
	font-size: 16px !important;
}
.btn-searchTop:focus {
	outline: none;
}
a.btn.btn-success.w-100.mb-2.shadow.text-white {
	color: #FFFFFF !important;
    font-weight: 900!important;
    background-color: #e4002b !important;
	border: 1px solid #005eb8;
    border-radius: 0px;
    height: 38px;
    margin-bottom: 10px;
    width: 100% !important;
    border-radius: none !important;
	line-height: 1.4;
    -webkit-appearance: none;
    font-size: 1rem !important;
}





	
	/* background-color: #0091DA  !important;
	border: 1px solid #0091DA  !important;
	background-size: 250px;
	color: #FFFFFF  !important;
	font-weight: bold !important;
	border-radius: 2px !important; */

}
.icon-phone-nav{
    padding: 10px;
    position: relative;
    width: 50px;
}
span.phone {
    color: #f0e623;
	font-size: 25px;
	top:5px;
	position:relative;
    margin-right: 1rem;
}
@media all and (min-width: 992px) {
	.navbar .nav-item .dropdown-menu{ display: none; }
	.navbar .nav-item:hover .nav-link{ color: #fff;  }
	.navbar .nav-item:hover .dropdown-menu{ display: block; }
	.navbar .nav-item .dropdown-menu{ margin-top:0; }
}
@media (max-width: 1746px){
	.ajb-search-for-jobs-button {
    	width: 100% !important;
    	margin-bottom: 5px;
	}
	.ajb-view-all-jobs-button{
		width: 100% !important;
	}
}
</style>