<?php
	get_header();
	get_post();
	// Template Name: Job Search 4
?>
<style>.search-bg {
    background: #004d40 !important;
    padding: 3rem 0;
    height: 25vh;
    display: flex;
    align-items: center;
    position: relative;
}
.ajb-search-widget p {
    position: static !important;
    width: 225px;
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
    background: #002620 !important;
    width: 80%;
    margin: auto;
    padding: 1rem 0;
    position: relative;
}
.search-bg:after {
    content: "";
    background: #f8f8f8;
    position: absolute;
    width: 50px;
    height: 50px;
    border: 1px solid #f8f8f8;
    bottom: -25px;
    right:0;
    margin-right:auto;
    margin-left:auto;
    left:0;
    z-index: 9999;
    transform: rotate(45deg);
}
h5.text-dark.text-center.my-4 {
    margin-top: 3rem !important;
}
.responsive-spec{
    height:10vh;
}

input.ajb-search-for-jobs-button {
    width: 150px;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #4CAF50 !important;
    float: left;
    background: #4CAF50 !important;
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
.ajb-search-widget label {color: #FFF !important;}
.ajb-search-widget label.loading {background-image:none !important;}
.ajb-search-widget select {margin-top: 0 !important;height:45px !important;}
.ajb-keywords {height:45px !important;}
input.ajb-search-for-jobs-button {height:45px !important;}
.current-job-text-kind-of-job a {
    position: absolute;
    top: 20px;
    right: -15px;
    background: #004d40 !important;
    }
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
    border-color: #004036 transparent;
    }
    .current-job-box-text.boxes1 {
    height: auto;
    background: #FFF;
    border: 1px solid #004d40 !important;
    }
    input.ajb-view-all-jobs-button {
    display: none;
}
h4.job_title.w-75.d-inline-block.px-2.mx-2 {font-weight: 700;color: #004d40;}
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
    <h5 class="text-dark text-center my-4">Current Opportunities</h5>
        <div class="row job-board">
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
<script>
jQuery('#adlogic_search_widget-6-keywords').attr('placeholder', 'Keywords');
</script>