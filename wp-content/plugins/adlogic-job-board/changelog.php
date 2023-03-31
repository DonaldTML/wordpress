<html>
<head>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<style>
body {
	background: #ADDDE7 url(http://www.adlogic.com.au/site/wp-content/themes/myrecruitmentplus-v2/images/background/clouds-bkg.png) repeat-x 30px 24px;
	font-family: 'Open Sans', sans-serif;
}
.container {
	width: 650px;
	margin: 61px auto;
	border-radius: 10px;
	background: #FFF;
	padding: 15px 20px;
	box-shadow: 1px 1px 1px #fff;
	font-size: 13px;
}
ul {
    padding-left: 15px;
}
ul ul {
    padding-left: 20px;
}
div.logo {
    margin: 30px auto;
    width: 300px;
}
span.subheading {
    font-size: 21px;
    font-weight: bold;
    line-height: 40px;
}
a.changelog-link {
    color: #ADDDE7;
    text-decoration: none;
}
div.button-bar {
    width: 650px;
    margin: 0 auto;
}
div.tab {
    width: 100px;
    float: left;
    background: #f1f1f1;
    padding: 7px 5px;
    text-align: center;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    font-size: 12px;
    border: 1px solid #e2e2e2;
    border-bottom: none;
    cursor: pointer;
}
div.tab.active {
	background: #FFF;
}
</style>
<?php 
 $changelogFile = 'http://updates.adlogic.com.au/files/adlogic_job_board/wordpress/update.xml';
 $oChangeLog = simplexml_load_file($changelogFile);
 $oUpdatesAttributes = $oChangeLog->updates->version;

 ?>
 <script type="text/javascript">
 	$(document).ready(function($){
	 	$("#loadUpdates").click(function(){
		 	$(".instructions").fadeOut(500);
		 	$(".details").fadeOut(500);
		 	$(".updates").fadeIn(1000);
		 	$("#loadInstructions").removeClass("active");
		 	$("#loadDetails").removeClass("active");
		 	$("#loadUpdates").addClass("active");
	 	});
	 	$("#loadInstructions").click(function(){
		 	$(".details").fadeOut(500);
		 	$(".updates").fadeOut(500);
		 	$(".instructions").fadeIn(1000);
		 	$("#loadUpdates").removeClass("active");
		 	$("#loadDetails").removeClass("active");
		 	$("#loadInstructions").addClass("active");
	 	});
	 	$("#loadDetails").click(function(){
		 	$(".instructions").fadeOut(500);
		 	$(".updates").fadeOut(500);	
		 	$(".details").fadeIn(1000);
		 	$("#loadInstructions").removeClass("active");
		 	$("#loadUpdates").removeClass("active");
		 	$("#loadDetails").addClass("active");
	 	});
 	});
 </script>
</head>
<body>
	<div class="logo">
		<img src="http://www.adlogic.com.au/site/wp-content/themes/myrecruitmentplus-v2/images/header/myrecruitment-logo.png"><br />
		<span class="subheading">Agile Recruitment Software</span>
	</div>
	<div class="button-bar">
		<div id="loadUpdates" class="tab active">
			Changelog
		</div>
		<div id="loadInstructions" class="tab">
			Instructions
		</div>
		<div id="loadDetails" class="tab">
			Details
		</div>
	</div>
	<div class="container">
		<div class="meta">
			<span style="float: right;">Latest version is: <strong><?php print $oUpdatesAttributes->attributes()->number; ?></strong></span> 
		</div>
		<div class="updates">
			<?php print $oChangeLog->updates->version->sections->changelog; ?>
		</div>
		<div class="instructions" style="display: none;">
			<?php print $oChangeLog->updates->version->sections->instructions; ?>
		</div>
		<div class="details" style="display: none;">
			<?php print $oChangeLog->updates->version->sections->description; ?>
		</div>
	</div>
</body>
</html>