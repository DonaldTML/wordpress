<?php
get_header();
get_post();
// Template Name: Job Detail
?>

<div class="flex-wrap">
<div class="row page-con" style="padding: 0 !important">

  <div class="col-12">
    <section id="job-board det">

      <div class="page-jobs-default-inside dispn">
        <?php
        if (have_posts()) :
          while (have_posts()) : the_post(); ?>
            <h2 class="text-center"><?php the_title(); ?></h2>
            <?php the_content(); ?>
        <?php
          endwhile;
        else :
          echo wpautop('Sorry! Nothing Here!');
        endif;
        ?>
      </div>


    </section>
  </div>
</div>


<script>
  var boxes = document.getElementsByClassName('mrp-tr-single-box');
  var detailsBox = boxes[1];
  detailsBox.style.setProperty('display', 'none', 'important');
</script>
<?php
get_footer();
?>
<style>
  .navbar {
    display: none !important;
  }
  .mrp-apply-button, .mrp-email-button {
    background: #13294B !important;
    color: #FFFFFF !important;
    border: 1px solid #13294B !important;
  }
  .mrp-apply-button:hover, .mrp-email-button:hover {
    background: #FFFFFF !important;
    color: #13294B !important;
    border: 1px solid #13294B !important;
  }

</style>