<?php
require_once ('framework/wp_bootstrap_navwalker.php');

// Custom Widgets
require_once ('framework/widgets.php');


// Registra os Menus
register_nav_menus( array(
    'primary-menu' => __( 'Primary Menu', 'bootstrapwp' ),
  ) );


/* START MENU BLOCK */

	// Register Custom Navigation Walker
	require_once('framework/wp_bootstrap_navwalker.php');

	// Add the 'top_menu' location in a theme setup function.
	function bootpress_setup() {
		register_nav_menus(
			array(
				'top_menu' => 'Top Menu'
			)
		);
	}

	// Add setup function to the 'after_setup_theme' hook
	add_action( 'after_setup_theme', 'bootpress_setup' );

/* END MENU BLOCK */



		register_sidebar ( array (
				'name' => 'Search Widget',
				'id' => 'search_widget',
				'description' => 'This sidebar is located on the job board pages',
				'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
				'after_widget' => '</div>',
				'before_title' => '<h2>',
				'after_title' => '</h2>' 
		) );

		register_sidebar ( array (
				'name' => 'Featured Jobs Widget',
				'id' => 'featured-jobs',
				'description' => 'This sidebar is located on the job board pages',
				'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
				'after_widget' => '</div>',
				'before_title' => '<h2>',
				'after_title' => '</h2>' 
		) );

		register_sidebar ( array (
				'name' => 'Job Alerts Widget',
				'id' => 'jobalerts',
				'description' => 'This sidebar is located on the job board pages',
				'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
				'after_widget' => '</div>',
				'before_title' => '<h2>',
				'after_title' => '</h2>' 
		) );
		
		register_sidebar ( array (
				'name' => 'Job Alerts Popup Widget',
				'id' => 'job-alerts-popup',
				'description' => 'This sidebar is located on the job board pages',
				'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
				'after_widget' => '</div>',
				'before_title' => '<h2>',
				'after_title' => '</h2>' 
		) );
		
		register_sidebar ( array (
				'name' => 'Job Alerts Dialog Window',
				'id' => 'jobalerts-widget',
				'description' => 'This sidebar is located on the job board pages',
				'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
				'after_widget' => '</div>',
				'before_title' => '<h2>',
				'after_title' => '</h2>' 
		) );
		add_filter( 'show_admin_bar', '__return_false' );
		
		



