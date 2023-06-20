<?php
	get_header();
	get_post();
?>




<section id="job-board">
    <div class="container">
        <div class="row job-board">
            <div class="col-md-9 page-central">
                
                
                    <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) : the_post(); ?>
                        <h2><?php the_title(); ?> </h2>
                        <?php  the_content(); ?>
                    <?php

                            // Your loop code
                        endwhile;
                    else :
                        echo wpautop( 'Sorry! Nothing Here!' );
                    endif;
                    ?>               
                
            </div>
            
            
            
            
            
            
            <div class="col-md-3 sidebar"><?php dynamic_sidebar('search_widget'); ?></div>
            
            
            
            
            
        </div>
    </div>
</section>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Email this job to a friend</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>



<?php
	get_footer();
?>