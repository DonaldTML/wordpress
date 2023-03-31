<?php
class Adlogic_Account_Management_Widget extends WP_Widget {
	function init() {
		$Adlogic_Job_Board = new Adlogic_Job_Board();
		if (($Adlogic_Job_Board->check_setup() == true) && (Adlogic_Job_Board_Users::isLoginEnabled() == true)) {
			add_action( 'widgets_init', create_function( '', 'return register_widget( "Adlogic_Account_Management_Widget" );' ) );
		}
	}

	function __construct() {
		parent::__construct('adlogic_account_management_widget', $name = 'Adlogic Account Management', array( 'description' => 'A widget to allow users to login/logout and manage their account information.' ) );
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
			$searchSettings = get_option('adlogic_search_settings');
			$saved_jobs_page_id = 0;
			if ( $instance ) {
				$title = esc_attr( $instance[ 'title' ] );

				if (isset($instance['saved_jobs_page_id'])) {
					$saved_jobs_page_id = $instance['saved_jobs_page_id'];
				}
			} else {
				$title = __( 'Account Management', 'text_domain' );
			}
		?>
		<p>
			<label for="<?php print $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php print $this->get_field_id('title'); ?>" name="<?php print $this->get_field_name('title'); ?>" type="text" value="<?php print $title; ?>" />
		</p>
		<p>
			<label><strong><?php _e('Saved Jobs Page:'); ?></strong></label><br />
			<?php wp_dropdown_pages(array('selected' => $saved_jobs_page_id, 'name' => $this->get_field_name('saved_jobs_page_id'), 'show_option_none' => '- None (Disables Option) -')); ?>
		</p>
		<?php 
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance							= $old_instance;
		$instance['title']					= strip_tags($new_instance['title']);
		$instance['saved_jobs_page_id']		= $new_instance['saved_jobs_page_id'];
		$instance							= array_merge($instance, $new_instance);
		return $instance;
	}

	function widget( $args, $instance ) {
		global $wp_rewrite;

		$searchSettings = get_option('adlogic_search_settings');

		// Enqueue Javascript
		wp_enqueue_script( 'jquery-adlogic-acccountManagementWidget' );

		// Enqueue Stylesheet
		wp_enqueue_style( 'adlogic-account-management-widget' );

		if ( $instance ) {
			if (!empty($args['before_title']) && !empty($args['after_title'])) {
				$title = $args['before_title'] . esc_attr( $instance[ 'title' ] ) . $args['after_title'];
			} else {
				$title = esc_attr( $instance[ 'title' ] );
			}

			if (isset($instance['saved_jobs_page_id'])) {
				$saved_jobs_page_id = $instance['saved_jobs_page_id'];
			} else {
				$saved_jobs_page_id = 0;
			}

			if (!isset($args['widget_id']) || empty($args['widget_id'])) {
				if (isset($args['id']) && !empty($args['id'])) {
					$args['widget_id'] = $args['id'];
				} else {
					 throw new InvalidArgumentException('No ID for hot jobs');
				}
			}

			// Get details from current sessions if any
			$oFacebookUser = Adlogic_Job_Board_Users::$oFacebookUser;
			$oLinkedInUser = Adlogic_Job_Board_Users::$oLinkedInUser;
			$oGooglePlusUser = Adlogic_Job_Board_Users::$oGooglePlusUser;

			// Build list of logged in accounts and accounts not logged in
			$aLoggedInAccounts = array();
			$aLoggedOutAccounts = array();

			if ($oLinkedInUser) { 
				$aLoggedInAccounts[] = array('name' => 'LinkedIn', 'class' => 'linkedin', 'url' => Adlogic_Job_Board_Users::$sLinkedInAuthUrl); 
			} else if (Adlogic_Job_Board_Users::isLinkedInEnabled() && !$oLinkedInUser) { 
				$aLoggedOutAccounts[] = array('name' => 'LinkedIn', 'class' => 'linkedin', 'url' => Adlogic_Job_Board_Users::$sLinkedInAuthUrl);
			}
			if ($oFacebookUser) { 
				$aLoggedInAccounts[] = array('name' => 'Facebook', 'class' => 'facebook', 'url' => Adlogic_Job_Board_Users::$sFacebookAuthUrl);
			} else if (Adlogic_Job_Board_Users::isFacebookEnabled() && !$oFacebookUser) { 
				$aLoggedOutAccounts[] = array('name' => 'Facebook', 'class' => 'facebook', 'url' => Adlogic_Job_Board_Users::$sFacebookAuthUrl);
			}
			if ($oGooglePlusUser) {
				$aLoggedInAccounts[] = array('name' => 'Google+', 'class' => 'google-plus', 'url' => Adlogic_Job_Board_Users::$sGooglePlusAuthUrl);
			} else if (Adlogic_Job_Board_Users::isGooglePlusEnabled() && !$oGooglePlusUser) {
				$aLoggedOutAccounts[] = array('name' => 'Google+', 'class' => 'google-plus', 'url' => Adlogic_Job_Board_Users::$sGooglePlusAuthUrl);
			}
			?>
			<script type="text/javascript">
					jQuery(document).ready(function($) {
						$('#<?php print $args['widget_id']; ?> .ajb-account-management-widget').adlogicAccountManagement();
					});
			</script>
			<?php if( !empty($args['before_widget']) ) { print $args['before_widget']; }?>
			<div id="<?php print $args['widget_id']; ?>" class="ajb-account-management-widget">
				<?php print $title;?>
				<?php if (Adlogic_Job_Board_Users::isLoggedIn()): // Content to display if user is logged in ?>
						<p><?php if ($oLinkedInUser) : ?>
							Welcome <?php print $oLinkedInUser->{'formatted-name'}; ?>!
						<?php elseif ($oFacebookUser) : ?>
							Welcome <?php print $oFacebookUser->name; ?>!
						<?php elseif ($oGooglePlusUser) : ?>
							Welcome <?php print $oGooglePlusUser->displayName; ?>!
						<?php endif; ?>
						<br />
						You're signed in with:
						<?php 
							foreach ($aLoggedInAccounts as $loggedInAccount) {
								print '<span class="login-status active ' . $loggedInAccount['class'] . '">' . $loggedInAccount['name'] . '</span>';
							}
							foreach ($aLoggedOutAccounts as $loggedOutAccount) {
								print '<a href="' . $loggedOutAccount['url'] . '"><span class="login-status inactive ' . $loggedOutAccount['class'] . '">' . $loggedOutAccount['name'] . '</span></a>';
							}
						?>
						</p>
						<ul class="ajb-account-management-links">
							<?php if (!empty($saved_jobs_page_id)) : ?>
								<li><a href="<?php print get_permalink($saved_jobs_page_id); ?>" class="saved-jobs">View Saved Jobs</a></li>
							<?php endif; ?>
							<li><a href="javascript:void(0);" class="logout">Logout</a></li>
						</ul>
				<?php else: // Content to display if user is not logged in ?>
					<?php if ((!$oFacebookUser) && (!$oLinkedInUser) && (!$oGooglePlusUser)) : ?>
						<div class="status"><p>You are currently not signed in.<br />Please sign in using the options below</p></div>
						<ul class="login-options">
							<?php 
							if (Adlogic_Job_Board_Users::isFacebookEnabled()) {
								print '<li><a href="' . Adlogic_Job_Board_Users::$sFacebookAuthUrl . '" class="social-media-login facebook">Login Using Facebook</a></li>';
							}
							if (Adlogic_Job_Board_Users::isLinkedInEnabled()) {
								print '<li><a href="' . Adlogic_Job_Board_Users::$sLinkedInAuthUrl . '" class="social-media-login linkedin">Login Using LinkedIn</a></li>';
							}
							if (Adlogic_Job_Board_Users::isGooglePlusEnabled()) {
								print '<li><a href="' . Adlogic_Job_Board_Users::$sGooglePlusAuthUrl . '" class="social-media-login google-plus">Login Using Google+</a></li>';
							}
							?>
						</ul>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php
			if( !empty($args['after_widget']) ) {
				print $args['after_widget'];
			}
		} 
	}

}
?>