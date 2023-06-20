<?php /* Template Name: Home */ ?>
<?php get_header(); ?>

    <section>
      <div class="container">
        <div class="row">
          <div class="col-lg-4 order-lg-2 py-3">
            <div class="py-4">
              <div class="register-box p-3">
                <h5>Register your Interest</h5>
                <br/>
                <p>If your area of interest is not currently listed, but you would like to be considered for a position with us, then submit an application.</p>
                <br/>
                <a href="#"  target="_blank" title="Job-Board"><button class="btn btn-outline-dark">Register</button></a>
              </div>
            </div>
          </div>
          <div class="col-lg-8 order-lg-1 py-3">
            <div class="px-2">
            <br>
                  <?php echo do_shortcode("[adlogic_search_results template='synlait'] "); ?>
                  </br>
                <?php echo do_shortcode("[adlogic_search_pagination]"); ?> 
            </div>
          </div>
        </div>
      </div>
    </section> 


<?php get_footer(); ?>
