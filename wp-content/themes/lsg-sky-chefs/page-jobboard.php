<?php
get_header();
get_post();
// Template Name: Front Page
?>
<div class="banner-container">
	<img src="http://localhost/wordpress/wp-content/themes/lsg-sky-chefs/images/banner_chef.jpg"/>
	<div class="banner-image"></div>
    <div class="banner">
    </div>
    <!-- <div class="banner-info-wrapper">
        <div class="banner-info">
            <h1>Careers</h1>
        </div>
    </div> -->

</div>
<div class="job-listing">

    <div class="container">
        <h1>Current Vacancies</h1>
        <div class="row job-board">
            <div class="col-md-8 page-central">



                <?php echo do_shortcode("[adlogic_search_results template='job-template']"); ?>
                <?php echo do_shortcode("[adlogic_search_pagination]"); ?>
                <div class="eoi">
                    <h2 class="bebas" style="font-size: 22px;">Register your interest</h2>
                    <div class="eoi-wrapper">
                        <p class="abril"> If your area of interest is not currently listed, but you would like to be considered for a future position with us, then submit an application.</p>
                        <a class="ajb-subscribe-job-alerts" style="float:left; text-decoration: none;" href="https://form.myrecruitmentplus.com/applicationform?jobId=1681499&source=10000&subSourceId=9999" target="_blank">Register</a>
                    </div>
                </div>

            </div>


            <div class="col-md-4 sidebar"><?php dynamic_sidebar('search_widget'); ?><br />

            </div>

        </div>
    </div>
</div>



<style>
    .ajb_social_sharing_sites,
    .share-text {
        display: none !important;

    }

    .position .date {
        margin: 0px 0px 5px 0px !important;
    }
</style>

<?php
get_footer();
?>