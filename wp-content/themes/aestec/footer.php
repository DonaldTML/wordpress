<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Careers_v1
 */

?>

</div>

<!-- Footer Elements -->

<style>
#sticky-footer {
    background-color: #f0f0f0;
  margin-top:5em !important;
  flex-shrink: none;

}
#footer-text {
  color: #d7282e;
}

</style>

<!-- Footer -->
<footer id="sticky-footer" class="py-4">
    <div class="container text-center">
    <small id="footer-text">2021 Copyright © <a href="https://martianlogic.com/" target="_blank" style="color:#283B50;">HRIS</a> by<a href="https://martianlogic.com/" target="_blank" style="color:#283B50;margin-left: 5px;">Martian Logic</a></small>
    </div>
 
    <script>
  window.addEventListener('load',function(){ 
    jQuery('button:contains(Apply for this job )').click(function(){
      gtag('event', 'conversion', {'send_to': 'AW-10991606789/WvEHCMDlrIEYEIW4mvko'});
    });
  }); 
</script>
</footer>
<!-- Footer -->
</div><!-- #page -->

<?php wp_footer(); ?>
<style>
  .mrp-apply-button {
    background: #d7282e !important;
    box-shadow: none !important;
  }
  .mrp-email-button {
    background: #FFFFFF !important;
    box-shadow: none !important;
    color: #d7282e !important;
    border: 1px solid #d7282e !important;
  }
  .mrp-tr-single-box, .mrp-icon-box-text {
    font-family: 'Ubuntu-Regular', sans-serif !important;
    
  }
  .mrp-tr-single-box {
    background: #f0f0f0 !important;
  }
  .mrp-tr-single-detail {
    background-color: #FFFFFF !important;
  }
</style>
</body>
</html>
