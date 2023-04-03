<?php
get_header();
get_post();
// Template Name: Front Page
?>
<div class="banner">
<div class="banner-content">
    <h1>Mining Jobs</h1>
    <hr>
    <h3>With a proud history of hard work, accomplishment and success, Pit N Portal is a recognised industry leader in the provision of agile and diverse whole-of-mine solutions to the hard-rock mining sector nationally. Joining Pit N Portal means joining an organisation that is serious about investing in its most important asset, its people, to best support their individual professional development and career progression goals, as well as satisfy the growing demand for its business. Search below for existing opportunities with Pit N Portal or check out opportunities at <a href="https://careers.emecogroup.com/">Emeco</a>, <a href="https://careers.forceequipment.com.au/">Force by Emeco</a> and <a href="https://careers.borex.com.au/">Borex</a></h3>
    <h3>Browse Our Jobs Below</h3>
</div>
</div>
<div class="job-listing">

    <div class="container">
        <div class="row job-board">
            <div class="col-md-8 page-central">



                <?php echo do_shortcode("[adlogic_filtered_search_results childrenRecruiterIds='recruiterId: 10856' template='job-template']"); ?>
                <?php echo do_shortcode("[adlogic_filtered_search_pagination childrenRecruiterIds='recruiterId: 10856']"); ?>
                <div class="eoi">
                    <h2 class="bebas" style="font-size: 22px;">Register your interest</h2>
                    <p class="abril"> If your area of interest is not currently listed, but you would like to be considered for a future position with us, then submit an application.</p>
                    <a class="ajb-subscribe-job-alerts" style="float:left; text-decoration: none;" href="https://form.myrecruitmentplus.com/applicationform?jobId=1658923&source=10000&subSourceId=9999" target="_blank">Register</a>
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