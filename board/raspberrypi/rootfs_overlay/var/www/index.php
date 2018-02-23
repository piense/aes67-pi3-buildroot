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
			console.log(SerReturn);
			var networkInfo = JSON.parse(SerReturn);
			for(var i =0;i<networkInfo['networks'].length;i++)
			{
				$("#wifiNetworks").append("<option value='"+networkInfo['networks'][i]+"'>"+networkInfo['networks'][i]+"</option>")
			}
		}
	}
	
	xmlhttp.open("GET", "availableWifiNetworks.php", true);
	xmlhttp.send();

});
</script>
</head>

<?php
include 'navbar.php';
?>

<br>


</html>
