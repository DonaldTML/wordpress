jQuery(document).ready(function($){
	var emailToFriendButton = $(".adlogic_job_details_email_friend");
	var emailToFriendPopup = $("#emailFriendPopup");
	
	// Add Bootstrap dialog attributes
	if(window.innerWidth > 500) {
		$(emailToFriendButton).attr("data-toggle", "modal");
		$(emailToFriendButton).attr("data-target", "#myModal");
	}
	
	$(emailToFriendButton).click(function(e) {
		e.preventDefault();
		openCustomEmailToFriendForm();
	});
	
	///////
	///////	FUNCTIONS
	///////
	
	
	// Override the default email popup function
	function openEmailToFriendForm(){};
	
	function openCustomEmailToFriendForm() {
		if(window.innerWidth <= 500) {
			var emailForm = $("#mailFrameId").attr("src");
			window.open(emailForm);
		} else {
			$("#myModal .modal-body").html(emailToFriendPopup);
			$("#mailFrameId").attr("width", "100%");
			$("#mailFrameId").attr("style", "");
			$(emailToFriendPopup).show();
		}
	}
	
	
});