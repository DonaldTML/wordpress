<?php
	get_header();
	get_post();
	// Template Name: Front Page
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
</style>
<section id="job-board special-bg" class="search-bg">
   
</section>
<div class="row head-banner m-0"></div>
<section id="job-board special-bg">
    <div class="container">
        <div class="row job-board">
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 mx-auto">
                <h5 class="text-dark text-center my-4">Current Opportunities</h5>
                <?php echo do_shortcode("[adlogic_search_results template='synlait']"); ?>
            </div>
        </div>
    </div>
</section>

<?php
	get_footer();
?>
