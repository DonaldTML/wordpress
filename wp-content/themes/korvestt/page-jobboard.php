<?php
get_header();
get_post();
// Template Name: Front Page
?>
<div class="banner">
	<div class="fullscreen">
    <img src="<!-- Weblink https://careers.korvest.com.au -->/careers/wp-content/themes/korvest/images/Railyards5.jpg" class="w-100">
    </div>
</div>

<div class="job-listing">

    <div class="container">
        <div class="row job-board">
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 mx-auto">
                <div class="box-sidebar shadow">
                    <h4 class="regint">Register your interest</h4>
                    <p class="abril"> If your area of interest is not currently listed, but you would like to be considered for a future position with us, then submit an application.</p>
                    <a class="reg-btn" type="button" href="<!-- EOI Link -->" target="_blank">Register</a>
                </div>
            </div>

            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 mx-auto">
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
</div>

<?php
get_footer();
?>