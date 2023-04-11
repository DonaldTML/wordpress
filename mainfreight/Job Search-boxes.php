<?php
	get_header();
	get_post();
	// Template Name: Large Companies
?>
<style>

body {
	margin: 0;
	background:#f5f5f5a3 !important;
	background-position: center;
    background-repeat: no-repeat;
}
@media (min-width: 768px){
.col-md-6 {
    -ms-flex: 0 0 50%;
    flex: 0 0 50%;
    max-width: 50%;
    float: left;
}
}
.current-job-box-text.boxes1 {
    height: 400px;
    text-align: center;
    padding-top: 2rem !important;
}
.current-job-text-kind-of-job a {
    top: -20px;background: #00a;
}

h4.special-header {
    position: absolute;
    bottom: 0;
    width: 100%;
    color: #FFF;
    font-size:14px;
    left: 0;
    text-align: center;
    margin-bottom: 0;
    background: #0e387f;
    padding: 12px;
}
h4.job_title {
    margin-top: 2rem;
}
p.current-job-text-close {
    color: #00a;
    font-weight: 500;
}
</style>
<section id="job-board special-bg" class="search-bg">
</section>






<div class="container">
    <div class="row head-banner m-0"></div>
        <section id="job-board special-bg">
            <div class="container">
            <h5 class="services-title my-4 mt-5">Current Opportunites</h5>

                <div class="row job-board">
            

                    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 mx-auto">
                        <?php echo do_shortcode("[adlogic_search_results template='mrp-spec']"); ?>
                        <?php echo do_shortcode("[adlogic_search_pagination]"); ?>

                    </div>
                    <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12 mx-auto">
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
                                <button type="button" class="mrp-btn-pos w-100 mb-2 shadow rounded">Register</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php
	get_footer();
?>