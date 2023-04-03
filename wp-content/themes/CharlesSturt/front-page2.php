<?php
	get_header();
	get_post();
	// Template Name: Front Page 2
?>
<style>
.search-bg {
    background: #333 url(/careers/wp-content/themes/CharlesSturt/images/Main_banner_1920x1282.jpg) !important;
    padding: 3rem 0;
    background-size: cover !important;
    background-position-y: center !important;
    height: 1260px;
    display: flex;
    align-items: center;
    position: relative;
}
input.ajb-search-for-jobs-button {
    background:#00a7e1 !important;
}
.input.ajb-search-for-jobs-button:hover{
    background:#FFF !important;
    color:#00a7e1 !important;
}
input.ajb-view-all-jobs-button { 
     background:#00a7e1 !important;
}
.input.ajb-view-all-jobs-button:hover{
    background:#FFF !important;
    color:#00a7e1 !important;
}
button.btn.btn-success.w-100.mb-2.shadow.rounded {
    background: #FFF !important;
    color: #00a7e1  !important;
    border: 1px solid #00a7e1;
}
</style>

<div class="wrapper">
    <div class="fullscreen">
    <img src="https://careers.charlessturt.sa.gov.au/careers/wp-content/themes/CharlesSturt/images/Main_banner_1920x500.jpg" class="w-100">
    </div>
</div>

<div class="container">
<div class="row head-banner m-0"></div>
<section id="job-board special-bg">
    <div class="container">
    <h5 class="text-dark text-center my-4">Current Opportunities</h5>

        <div class="row job-board">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 mx-auto">
               <div class="search-sidebar">
                    <?php get_sidebar(); ?>
                </div>
                <div class="box-sidebar shadow">
                    <div class="box-sidebar-title">
                        <h4>Register your interest</h4>
                    </div>
                    <div class="box-sidebar-content">
                        <p>If your area of interest is not currently listed, but you would like to be considered for a future position with us, then submit an application.</p>
                    </div>
                    <hr>
                    <a href=" https://clientapplications.myrecruitmentplus.com/applicationform/?adlogic_job_id=1560178"><button type="button" class="btn btn-success w-100 mb-2 shadow rounded">Register</button></a>
               </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 mx-auto">
            <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) : the_post(); ?>
                        
                        <?php  the_content(); ?>
                    <?php

                            // Your loop code
                        endwhile;
                    else :
                        echo wpautop( 'Sorry! Nothing Here!' );
                    endif;
                    ?>            

            </div>
            
        </div>
    </div>
</section>

<?php
	get_footer();
?>