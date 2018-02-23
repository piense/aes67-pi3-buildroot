<html>
<head>

<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="jquery-3.2.1.js"></script>
<script src="popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script language="javascript">
$(document).ready(function(){
	if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var SerReturn = xmlhttp.responseText;
			var settings = JSON.parse(SerReturn);

			if(settings['HideSplash'])
				$("#SplashScreenChk").prop('checked', true);
			else
				$("#SplashScreenChk").prop('checked', false);

			if(settings['Quiet'])
				$("#quietChk").prop('checked', true);
			else
				$("#quietChk").prop('checked', false);

			if(settings['HideRaspberries'])
				$("#RaspberriesChk").prop('checked', true);
			else
				$("#RaspberriesChk").prop('checked', false);
		}
	}
	
	xmlhttp.open("GET", "getBootConfig.php", true);
	xmlhttp.send();

});

function UpdateConfig(){
	// Fire off the request to /form.php
	request = $.ajax({
		url: "/updateBootConfig.php",
		type: "post",
		data: {splash: $("#SplashScreenChk").is(':checked'), quiet: $("#quietChk").is(':checked'), raspberries:$("#RaspberriesChk").is(':checked') }
	});
	
	// Callback handler that will be called on success
    	request.done(function (response, textStatus, jqXHR){
        // Log a message to the console
	console.log(response);
        console.log("Hooray, it worked!");
    });
	
}

</script>
</head>

<?php
include 'navbar.php';
?>

<br>

<div class="row">

<div class="col-sm-3">
	<form>
	  <div class="form-group">
		<label><h5>Boot Settings</h5></label><br>
		<input type="checkbox" id="SplashScreenChk"><label>Hide Boot Loader Splash Screen</label><br>
		<input type="checkbox" id="quietChk"><label>Hide System Startup</label><br>
		<input type="checkbox" id="RaspberriesChk"><label>Hide Raspberries</label><br>
	  </div>
	</form>

<button type="button" class="btn btn-primary" onClick="UpdateConfig()">Save All</button>
</div>

</div>

</html>
