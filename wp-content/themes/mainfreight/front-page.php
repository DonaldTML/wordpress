<?php
get_header();
get_post();
// Template Name: Front Page Test
?>
<style>
    .search-bg {
        /* background: url(https://jobboards.adlogic.com.au/rib-software/wp-content/themes/rib-software/images/banner.png) center; */
        padding: 0;
        height: 100% !important;
        background-size: cover !important;
        display: flex;
        align-items: center;
        overflow: hidden;
        background-position-x: right;
    }

    .search-bg img {
        width: 100% !important;
        position: relative;
    }

    input.ajb-search-for-jobs-button {

        color: #FFFFFF !important;
        font-weight: 900 !important;
        background-color: #e4002b !important;
        border: 1px solid #005eb8;
        border-radius: 0px;
        height: 38px;
        margin-bottom: 10px;
        width: 100% !important;
        border-radius: none !important;
        transition: all .25s ease;
        font-weight: 600;
        line-height: 1.4;
        -webkit-appearance: none;
        font-size: 1rem !important;

    }

    /* .input.ajb-search-for-jobs-button:hover{
    background:#0091DA  !important;
    color:#FFFFFF !important;
    border-radius: 2px !important;
    
} */
    input.ajb-view-all-jobs-button {
        color: #FFFFFF !important;
        font-weight: 900 !important;
        background-color: #e4002b !important;
        border: 1px solid #005eb8;
        border-radius: 0px;
        height: 38px;
        margin-bottom: 10px;
        width: 100% !important;
        border-radius: none !important;
        transition: all .25s ease;
        font-weight: 600;
        line-height: 1.4;
        -webkit-appearance: none;
        font-size: 1rem !important;
    }

    /* .input.ajb-view-all-jobs-button:hover{
    background:#0091DA  !important;
    color:#FFFFFF !important;
} */
    button.btn.btn-success.w-100.mb-2.shadow.rounded {
        clip-path: polygon(80% 0, 90% 50%, 80% 100%, 0% 100%, 0% 0%);
        padding: 0.3em 3em 0.3em 1.5em !important;
        color: #FFFFFF !important;
        font-weight: 900 !important;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-blend-mode: normal;
        background-image: url(https://www.richdataco.com/wp-content/uploads/2021/04/abstract-2.jpg);
        background-color: #000000;
        transform: scale3d(0.964, 0.964, 0.964);
        opacity: 0.880;
        transition: transform 200ms linear, opacity 200ms linear;
        border: 2px solid transparent;
        border-radius: 0px;
    }

    /* @media (max-width: 1500px){
    .search-bg {
        height: 400px;
        background-position-y: -250px;
    }
}
@media (max-width: 1170px){
    .search-bg {
        height: 350px;
        background-position-y: -100px;
    }
}
@media (max-width: 800px){
    .search-bg {
        height: 280px;
        background-position-y: -100px;
    }
}
@media (max-width: 675px){
    .search-bg {
        height: 200px;
        background-position-y: -18px;
    }
}
@media (max-width: 390px){
    .search-bg {
        height: 177px;
        background-position-y: -18px;
    }
} */
    img.banner-img {
        width: 100%;
        margin: auto;
        display: block;
    }

    #privacy-link {
        color: #1a2847 !important;

    }

    #privacy-link:hover {
        color: #fbb03b !important;
    }

    #row-info-list>li {
        font: 14px/1.55em Roboto, Arial, Sans-serif !important;
    }

    #banner-slogan {
        background: #FFFFFF;
        position: absolute;
        z-index: 99;
        margin-left: 376px;
        /* margin-right: 862px; */
        width: 545px;
        padding: 1.875rem;

    }

    @media (max-width:1600px) {

        #banner-slogan {
            margin-left: 200px !important;
        }
    }

    @media (max-width: 1300px) {

        #banner-slogan {
            width: 472px !important;
            margin-left: 120px !important;
        }
    }

    @media (max-width: 1089px) {
        .search-bg {
            height: 350px !important;
        }

        #slogan-header {
            font-size: 1.5rem !important;
        }

        #slogan-content {
            font-size: 0.875rem !important;
        }

        #banner-slogan {
            width: 307px !important;
        }
    }

    @media (max-width: 800px) {
        #ice-cream-nav {
            height: 100% !important;
            padding: 56px 16px 0px 16px !important;
        }
    }

    @media (max-width: 880px) {
        #banner-slogan {
            display: none !important;
        }
    }

    @media (max-width: 762px) {
        .search-bg {
            height: 100% !important;
        }
    }
</style>
<section id="job-board special-bg" class="search-bg">
	<!-- <div id="banner-slogan">
      <h5 id="slogan-header" style="font-size: 2.5rem; color: #214874; font-weight: bold;">Greater Bank Careers</h5>
      <p id="slogan-content" style="font-size: 1.125rem;
    line-height: 1.6875rem;
    color: #000000; ">At Greater Bank, we are on a journey that is being driven by our people. Become part of the team that is helping to transform the way we do banking.<br /><br />Join us today to create tomorrow.</p>  
    </div> -->
	<img src="https://jobboards.adlogic.com.au/mainfreight/wp-content/themes/mainfreight/images/banner.jpg"/>
</section>
<div class="container">
	<div class="row head-banner m-0"/>
	<section id="job-board special-bg">
		<div class="container">
			<!-- <div class="row info-board">
        <div class="col-12">
    
</div>
</div>
    </div> -->
			<h5 class="text-dark text-center my-4" style="font-size: 3rem;">Current Opportunities</h5>
			<div class="row job-board">
				<div class="col-md-4 col-sm-12">
					<div class="box-sidebar shadow">
						<div class="box-sidebar-title">
							<h4>Expression of Interest- Owner Driver</h4>
						</div>
						<div class="box-sidebar-content">
							<p>Are you looking to own your own business, while having the security of being supported by a successful global company?</p>
							<p>The continued growth of Mainfreight across New Zealand means we are looking for more hungry drivers to support our supply chain, as we continue on our 100 year journey.</p>
							<p>
								<b>Becoming a Mainfreight Owner Driver could be for you!</b>
							</p>
						</div>
						<hr>
							<a type="button" href="https://form.myrecruitmentplus.com/applicationform?jobId=1631381&source=10000&subSourceId=9999" style="-webkit-appearance: none;" class="btn btn-success w-100 mb-2 shadow text-white" style="border: none !important;">Apply</a>
						</div>
					</div>
					<div class="col-md-4 col-sm-12">
						<div class="box-sidebar shadow">
							<div class="box-sidebar-title">
								<h4>Expression of Interest- Looking For a Change</h4>
							</div>
							<div class="box-sidebar-content">
								<p>If you are looking for a change and your area of interest is not currently listed, but you would like to be considered for a future position in the Mainfreight team then apply now!</p>
							</div>
							<hr>
								<a type="button" href="https://form.myrecruitmentplus.com/applicationform?jobId=1631380&source=10000&subSourceId=9999" style="-webkit-appearance: none;" class="btn btn-success w-100 mb-2 shadow text-white" style="border: none !important;">Apply</a>
							</div>
						</div>
						<div class="col-md-4 col-sm-12">
							<div class="box-sidebar shadow">
								<div class="box-sidebar-title">
									<h4>Mainfreight Development Programme</h4>
								</div>
								<div class="box-sidebar-content">
									<p>This programme is for School leavers and Graduates.</p>
									<p>The Mainfreight Development Programme seeks those willing to get stuck in and who are keen to embrace all kinds of challenges and new experiences.</p>
									<p>Roll up your sleeves for a practical and hands-on learning experience at the core of our business.</p>
								</div>
								<hr>
									<a type="button" href="https://form.myrecruitmentplus.com/applicationform?jobId=1629966&source=10000&subSourceId=9999" style="-webkit-appearance: none;" class="btn btn-success w-100 mb-2 shadow text-white" style="border: none !important;">Apply</a>
								</div>
							</div>
							<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
								<div class="search-sidebar">
<?php get_sidebar(); ?>
								</div>
								<!-- <div class="box-sidebar shadow">
                    <div class="box-sidebar-title">
                        <h4>Register your interest</h4>
                    </div>
                    <div class="box-sidebar-content">
                        <p>If your area of interest is not currently listed, but you would like to be considered for a future position with us, then submit an application.</p>
                    </div>
                    <hr>
                    <a type="button" href="https://form.myrecruitmentplus.com/applicationform?jobId=1629966&source=10000&subSourceId=9999" style="-webkit-appearance: none;" class="btn btn-success w-100 mb-2 shadow text-white" style="border: none !important;">Registration</a>
               </div> -->
							</div>
							<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
<?php echo do_shortcode("[adlogic_search_results template='synlait']"); ?>
<?php echo do_shortcode("[adlogic_search_pagination]"); ?>
							</div>
						</div>
					</div>
				</section>

<?php
    get_footer();
    ?>