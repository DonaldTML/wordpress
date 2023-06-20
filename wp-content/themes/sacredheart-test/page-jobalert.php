<?php
  get_header();
  get_post();
	// Template Name: Job alert
?>




<style type="text/css">
  .page-jobs-default {
    padding: 10px;
  }

  .page-jobs-default-inside {
    padding: 40px;
    background: #fff;
  }

  .page-sidebar {
    padding: 10px;
  }

  .page-sidebar-inside {
    padding: 40px;
    background: #fff;
  }
</style>

<div class="row page-con">
<div class="col-md-1"></div>
<div class="col-md-7">
<section id="job-board">  
  <div class="page-jobs-default-inside">
          <?php
          if ( have_posts() ) :
            while ( have_posts() ) : the_post(); ?>
          <h2 class="text-center"></h2>
          <?php  the_content(); ?>
          <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6"><?php dynamic_sidebar('jobalerts'); ?></div>
            <div class="col-md-3"></div>
          </div>
          

          <?php
          endwhile;
          else :
            echo wpautop( 'Sorry! Nothing Here!' );
          endif;
          ?>
  </div>





</section>
</div>
 <div class="col-md-3 sider-con">

  <?php dynamic_sidebar('search_widget'); ?>
    

<div style="margin-left:20px;">
    <h4>Register Your Interest</h4>
    <p>If you would like to register your interest for future employment opportunities, please submit an application via the link below.</p>
    <a class="ajb-register-button" target="_blank" href="http://clientapplications.myrecruitmentplus.com/applicationform/?jobAdId=7093329">
    Register
    </a>
    <a onclick="window.open ('http://www.google.com', ''); return false" href="javascript:void(0);"></a>
    <h4>Subscribe to Job Alerts</h4>
    <p>Subscribe to Job Alerts to receive an email when an opportunity becomes available that matches your criteria.</p>
    <a class="ajb-subscribe-button" href="<?php print get_template_directory_uri(); ?>/job-alert/">Subscribe</a>
    </div>
  </div>
  <div class="col-md-1"></div>

</div>
<?php
  get_footer();
?>