<?php get_header(); the_post(); 
	// Template Name: 404
?>

   <!-- Page Content -->
<div class="container jobdetail-container">
    <div class="row">


        <div class="col-md-3">
        </div>

        <div class="col-md-6 box-jobdetail text-center" style="padding-left: 10px;padding-right: 10px;">
            <h1><?php the_title(); ?></h1>
            <h2>Page Not Found</h2>
            <br>
            <p>The page you were looking for could not be found.</p>
            <p><strong><a href="<?php echo esc_url( home_url( '' ) ); ?>">Click here</a></strong> to return to job board.</p>
                
        </div>

        <div class="col-md-3">
        </div>



    </div>
</div>

<?php get_footer(); ?>



