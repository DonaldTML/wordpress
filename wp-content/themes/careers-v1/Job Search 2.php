<?php
	get_header();
	get_post();
	// Template Name: Job Search 2
?>
<style>
.search-bg {
    background: #333 url(/wp-content/themes/careers-v1/images/main-banner-1.png) !important;
    padding: 3rem 0;
    height: 35vh;
    display: flex;
    align-items: center;
    position: relative;
}
input.ajb-search-for-jobs-button {
    background:#0e387f !important;
}
input.ajb-view-all-jobs-button { 
     background:#0e387f !important;
}
button.btn.btn-success.w-100.mb-2.shadow.rounded {
    background: #FFF !important;
    color: #0e387f  !important;
    border: 1px solid #0e387f;
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
                        <h4>Register your interest</h4>
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


<section id="job-board special-bg">
    <div class="row my-5 py-5">
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mx-auto">
            <div class="branding-box box-1">
                <h5>Lorem ipsum</h5>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mx-auto">
            <div class="branding-box box-2">
                 <h5>Lorem ipsum</h5>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            </div>
        </div>
    </div>
</section>
<?php
	get_footer();
?>