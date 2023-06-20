  
<?php
    require('header.php');
?>



<section id="job-board" class="background-life-ags_01">
    <div class="container-fluid">
        <div class="row-fluid job-board">
            <div class="col-md-12 page-central text-center">
                    <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) : the_post(); ?>
                        <h2 class="text-center"></h2>
                        <img src="<?php print get_template_directory_uri(); ?>/images/hr.jpg" width="" class="center-block">
                        <?php  the_content(); ?>
                    <?php

                            // Your loop code
                        endwhile;
                    else :
                        echo wpautop( 'The page cannot be found' );
                    endif;
                    ?>               
                
            </div>
        
        </div>
    </div>

    <br/><br/><br/><br/>

    <div class="container">
        <div class="row-fluid job-board">
            <div class="col-md-6"><?php dynamic_sidebar('search_widget'); ?></div>
            <div class="col-md-1"></div>
            <div class="col-md-4"><?php dynamic_sidebar('jobalerts'); ?></div>
        </div>

    </div>
</section>



<?php
    require('footer.php');
?>