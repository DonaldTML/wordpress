<?php
	get_header();
	get_post();
	// Template Name: Job Search 2
?>
<style>
.search-bg {
    background: #333 url(/wp-content/themes/Andy-Testing-Template/images/main-banner-1.png) !important;
    padding: 3rem 0;
    height: 35vh;
    display: flex;
    align-items: center;
    position: relative;
}
input.ajb-search-for-jobs-button {
    background:#00a7e1 !important;
}
input.ajb-view-all-jobs-button { 
     background:#00a7e1 !important;
}
button.btn.btn-success.w-100.mb-2.shadow.rounded {
    background: #FFF !important;
    color: #00a7e1  !important;
    border: 1px solid #00a7e1;
}
</style>
<section id="job-board special-bg" class="search-bg">
   
</section>
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
                        <h4>Register for interest</h4>
                    </div>
                    <div class="box-sidebar-content">
                        <p>Martian Logic is a CLOUD based platform that enables HR and recruitment agencies to manage their recruitment and onboarding activities from one location.</p>
                    </div>
                    <hr>
                    <button type="button" class="btn btn-success w-100 mb-2 shadow rounded">Register</button>
               </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 mx-auto">
                <?php echo do_shortcode("[adlogic_search_results template='synlait']"); ?>
                <?php echo do_shortcode("[adlogic_search_pagination]"); ?>

            </div>
            
        </div>
    </div>
</section>

<?php
	get_footer();
?>