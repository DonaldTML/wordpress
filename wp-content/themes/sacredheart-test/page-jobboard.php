<?php
get_header();
get_post();
// Template Name: Job Board
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
<nav class="navbar navbar-default" role="navigation">

  <div class="row" style="width: 100%">

    <div class="col-md-12" style="padding: 0 !important;">
      <div class="nav-wrapper">
        <div class="nav-logo">
          <a class="navbar-brand n-default" href="http://www.shcgeelong.catholic.edu.au/">
            <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Napco">
          </a>
        </div>
        <div class="nav-links">
          <a href="https://www.shcgeelong.catholic.edu.au/">Home</a>
        </div>
      </div>
    </div>

  </div>
  <div class="header-bottom-line"> </div>
</nav>

<div class="row page-con">

  <div class="col-md-8">
    <section id="job-board">
      <h2 class="ttline">CURRENT OPPORTUNITIES</h2>
      <div class="page-jobs-default-inside">
        <?php /*echo do_shortcode("[adlogic_search_results template='job-layout']");*/ ?>
      </div>
    </section>
  </div>


  <div class="col-md-4 sider-con">




    <div style="margin-left:20px;">
      <h4>Register Your Interest</h4>
      <p>If you would like to register your interest for future employment opportunities, please submit an application via the link below.</p>
      <a class="ajb-register-button" target="_blank" href="https://form.myrecruitmentplus.com/applicationform?jobId=1422632&source=10000&subSourceId=9999">
        Register
      </a>
      <a onclick="window.open ('http://www.google.com', ''); return false" href="javascript:void(0);"></a>
      <h4>Subscribe to Job Alerts</h4>
      <p>Subscribe to job alerts to stay informed on new employment opportunities.</p>
      <a class="ajb-subscribe-button" href="<?php print get_template_directory_uri(); ?>/job-alert/">Subscribe</a>
    </div>
  </div>

</div>


<?php
get_footer();
?>