<?php
	get_header();
	get_post();
	// Template Name: Job Search 3
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
.ajb-search-widget p {
    position: static !important;
    width: 220px;
    float: left;
    margin: 1rem .2rem;
    color: #333;
}
div#adlogic_search_widget-6 {
    align-items: center;
    justify-content: center;
    display: flex;
}
.ajb-search-widget-buttons {
    position: relative;
    margin: 3rem 0 !important;
    width: 160px !important;
    float: right !important;
}
.searchbar-area {
    background: #f8f8f8;
    width: 70%;
    margin: auto;
    padding: 2.5rem 0;
    position: absolute;
    right: 0;
    left: 0;
}
.responsive-spec{
    height:10vh;
}

input.ajb-search-for-jobs-button {
    width: 150px;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #2980b9 !important;
    float: left;
    background: #2980b9 !important;
}
input.ajb-view-all-jobs-button {
    width: 150px;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    border: 1px solid #2980b9 !important;
    float: right;
    background: #FFF !important;
    color: #2980b9 !important;
}
.ajb-search-widget label {color:#333 !important;}
.ajb-search-widget label.loading {background-image:none !important;}
.ajb-search-widget select {margin-top: 0 !important;}
.current-job-text-kind-of-job a {
    position: absolute;
    top: 20px;
    right: -15px;
    background: #2980b9 !important;}
    .current-job-text-kind-of-job a:after {
    position: absolute;
    top: 100%;
    right: 0;
    content: " ";
    width: 0;
    height: 0;
    z-index: 9999;
    border-style: solid;
    border-width: 15px 15px 0 0;
    border-color: #074c7a transparent;
    }
    .current-job-box-text.boxes1 {
    height: auto;
    background: #FFF;
    border: 1px solid #2980b9 !important;
    }
    input.ajb-view-all-jobs-button {
    display: none;
}
h4.job_title.w-75.d-inline-block.px-2.mx-2 {font-weight: 700; color: #2980b9;}
</style>
<section id="job-board special-bg" class="search-bg">
    <div class="container z-index-up">
        <div class="searchbar-area">
            <?php dynamic_sidebar('slider_search_widget'); ?>
        </div>
    </div>
</section>
<div class="container">
<div class="row head-banner m-0"></div>
<section id="job-board special-bg">
    <div class="container">
    <h5 class="text-dark text-center my-4" style="    padding-top: 3em;">Current Opportunities</h5>
        <div class="row job-board">
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
<script>
jQuery('#adlogic_search_widget-6-keywords').attr('placeholder', 'Keywords');
</script>