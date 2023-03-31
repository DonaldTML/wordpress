<?php
	get_header();
	get_post();
	// Template Name: New Job Board
?>
	<link href="<?php echo get_template_directory_uri(); ?>/style-second.css" rel="stylesheet">

<script>
var andy = document.getElementById('careers-v1-style-css');
console.log(andy);
andy.remove();</script>
<style>
html,body{height:100%;}
.search-bg {
    background: #333 url(/careers/wp-content/themes/Andy-Testing-Template/images/main-banner-1.png) !important;
    padding: 3rem 0;
    height: 60%;
    display: block !important;
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
    background: #F26525 !important;
    color: #FFF  !important;
    border: 1px solid #F26525;
}
span.current-job-box-text-social-icons {
    position: absolute;
    right: 10px;
    top: 20px;
}
span.current-job-box-text-social-icons a{
    margin:0 5px;
}
h4.slogan-heading {
    text-align: center;
    display: block;
    position: relative;
    color: #FFF;
    font-size: 3em;
    z-index: 9;
    font-weight:600;
}

p.slogan-text {
    color: #FFF !important;
    font-size: 1.4em;
    z-index: 9;
    top: 20px;
    text-align: center;
    position: relative;
    display: block;
}
.slogan-area{
    position:relative;
    display:block;
    top:25%;
}
.current-job-box-text.boxes1:before {
    position: absolute;
    bottom: -100%;
    left: 0;
    right: 0;
    width: 100%;
    height: 100%;
    margin: 0 auto;
    background: rgba(0, 0, 0, 0.045);
    content: "";
    -webkit-border-radius: 100% 100% 0 0;
    -ms-border-radius: 100% 100% 0 0;
    border-radius: 100% 100% 0 0;
    -webkit-transition: all 500ms cubic-bezier(0.645, 0.045, 0.095, 1.08);
    -ms-transition: all 500ms cubic-bezier(0.645, 0.045, 0.095, 1.08);
    transition: all 500ms cubic-bezier(0.645, 0.045, 0.095, 1.08);
    z-index: 0;
}

.current-job-box-text.boxes1:hover:before {
    bottom: -75%;
}

.current-job-box-text.boxes1 {  
  overflow: hidden;
  padding: 26px 30px 30px;
  border: 1px solid #f6f8f9;
  position: relative;
  -webkit-border-radius: 3px;
  -ms-border-radius: 3px;
  border-radius: 3px;
  -webkit-transition: all ease .4s;
  -ms-transition: all ease .4s;
  transition: all ease .4s;
  -webkit-box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
  -ms-box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
}
.alert-box-mrp{
    height: auto;
    border-radius: 5px;
    background: #F26525;
    color:#FFF;
    font-size:27px;
    font-weight:700;
    padding: 2rem;
    margin: 3rem 1rem;
    transition: .3s ease-in-out;
    position: relative;
    overflow: hidden;
    padding: 26px 30px 30px;
    position: relative;
    -webkit-border-radius: 3px;
    -ms-border-radius: 3px;
    border-radius: 3px;
    -webkit-transition: all ease .4s;
    -ms-transition: all ease .4s;
    transition: all ease .4s;
    -webkit-box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
    -ms-box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
}
.alert-box-mrp h4{
    display:block;
    position:relative;
    z-index:999 !important;
    margin-bottom:0 !important;
}
.alert-box-mrp:hover h4{
    color:#F26525;
}
.registerInterest{
    padding: 10px;
    background: #FFF;
    color: #F26525;
    position: absolute;
    border: 1px solid #f26525;
    transition: .5s ease;
    float: right;
    border-radius: 5px;
    top: 25%;
    font-size: 16px;
    right: 5%;
    -webkit-box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
    -ms-box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
}
.alert-box-mrp:before {
    position: absolute;
    bottom: -100%;
    left: 0;
    right: 0;
    width: 100%;
    height: 100%;
    margin: 0 auto;
    background: #FFF;
    content: "";
    -webkit-border-radius: 100% 100% 0 0;
    -ms-border-radius: 100% 100% 0 0;
    border-radius: 100% 100% 0 0;
    -webkit-transition: all 500ms cubic-bezier(0.645, 0.045, 0.095, 1.08);
    -ms-transition: all 500ms cubic-bezier(0.645, 0.045, 0.095, 1.08);
    transition: all 500ms cubic-bezier(0.645, 0.045, 0.095, 1.08);
    z-index: 0;
}
.alert-box-mrp:hover:before {
    bottom:0%;
    height:150%;
}

</style>

<section id="job-board special-bg" class="search-bg">
    <div class="slogan-area">
        <h4 class="slogan-heading">Does your career allow you to <br> bring your purpose to life?</h4>
        <p class="slogan-text">We are committed to high quality work, learning <br>from each other, and living by our values.</p>
    </div>
</section>
<div class="container">
<div class="row head-banner m-0"></div>
<section id="job-board special-bg">
    <div class="container">
        <div class="row job-board">
             <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 mx-auto">
               <div class="search-sidebar">
                    <?php get_sidebar(); ?>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 mx-auto">
                <div class="col-md-12 current-job-box m-auto d-block" id="7978060">
                    <div class="alert-box-mrp">
                        <h4>Register for Interest</h4>
                        <button class="registerInterest">REGISTER NOW!</button>
                    </div>
                </div>
                <?php echo do_shortcode("[adlogic_search_results template='simple']"); ?>
                <?php echo do_shortcode("[adlogic_search_pagination]"); ?>

            </div>
            
        </div>
    </div>
</section>

<?php
	get_footer();
?>