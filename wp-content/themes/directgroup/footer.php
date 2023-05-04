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
  margin-top:5em !important;
  flex-shrink: none;
}

</style>

<!-- Footer -->
<footer id="sticky-footer" class="py-4 bg-blue text-white">
    <div class="container text-center">
      <small> <?php echo date('Y'); ?> Copyright &copy; <a href="https://martianlogic.com/" target="_blank" style="color:#f26524;">HRIS</a> by<a href="https://martianlogic.com/" target="_blank" style="color:#f26524;margin-left: 5px;">Martian Logic</a></small>
    </div>
  </footer>

</footer>
<!-- Footer -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
