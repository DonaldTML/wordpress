<?php
	get_header();
	get_post();
    // Template Name: Job Board
?>




<section id="job-board">
    <div class="container">
        <div class="row job-board">
            <div class="col-md-12 page-central">
                
                
                    <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) : the_post(); ?>
                        <?php  the_content(); ?>
                    <?php

                            // Your loop code
                        endwhile;
                    else :
                        echo wpautop( 'Sorry! Nothing Here!' );
                    endif;
                    ?>               
                
            </div>
            
            
            
            
    
            
            
            
            
            
        </div>
    </div>
</section>

<script>
document.getElementById('mrp-main-wrapper').style.background = 'transparent';
</script>


<?php
	get_footer();
?>