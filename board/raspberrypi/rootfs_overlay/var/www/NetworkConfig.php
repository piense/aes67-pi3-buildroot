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
			var networkInfo = JSON.parse(SerReturn);
			for(var i =0;i<networkInfo['networks'].length;i++)
			{
				$("#wifiNetworks").append("<option value='"+networkInfo['networks'][i]+"'>"+networkInfo['networks'][i]+"</option>")
			}
		}
	}
	
	xmlhttp.open("GET", "availableWifiNetworks.php", true);
	xmlhttp.send();
	
	if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp2 = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp2.onreadystatechange = function () {
		if (xmlhttp2.readyState == 4 && xmlhttp2.status == 200) {
			var SerReturn = xmlhttp2.responseText;

			var networkInfo = JSON.parse(SerReturn);
			$("#eth0IP").html(networkInfo['eth0IP']);
			$("#eth0Mask").html(networkInfo['eth0Mask']);
			$("#eth0Gateway").html(networkInfo['eth0Gateway']);
			if(networkInfo['eth0Online'])
				$("#eth0Connected").html("Connected");
			else
				$("#eth0Connected").html("Disconnected");
			
			$("#wlan0IP").html(networkInfo['wlan0IP']);
			$("#wlan0Mask").html(networkInfo['wlan0Mask']);
			$("#wlan0Gateway").html(networkInfo['wlan0Gateway']);
			if(networkInfo['wlan0Online'])
				$("#wlan0Connected").html("Connected: " + networkInfo['wlan0ESSID']);
			else
				$("#wlan0Connected").html("Disconnected");
		}
	}
	
	xmlhttp2.open("GET", "getNetworkStatus.php", true);
	xmlhttp2.send();
	
	if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp3 = new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp3 = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
		xmlhttp3.onreadystatechange = function () {
		if (xmlhttp3.readyState == 4 && xmlhttp3.status == 200) {
			var SerReturn = xmlhttp3.responseText;
			var networkInfo = JSON.parse(SerReturn);
			$("#wiredIP").val(networkInfo['eth0IP']);
			$("#wiredSubnet").val(networkInfo['eth0Mask']);
			$("#wiredGateway").val(networkInfo['eth0Gateway']);
			if(networkInfo['eth0DHCP'])
				$("#wiredDHCPChk").prop('checked', true);
			else
				$("#wiredDHCPChk").prop('checked', false);
			
			$("#wirelessIP").val(networkInfo['wlan0IP']);
			$("#wirelessSubnet").val(networkInfo['wlan0Mask']);
			$("#wirelessGateway").val(networkInfo['wlan0Gateway']);
			if(networkInfo['wlan0DHCP'])
				$("#wirelessDHCPChk").prop('checked', true);
			else
				$("#wirelessDHCPChk").prop('checked', false);
			
			$("#wirelessNetwork").val(networkInfo['networks'][0]['ssid']);
			$("#wirelessKey").val(networkInfo['networks'][0]['psk']);
		}
	}
	
	xmlhttp3.open("GET", "getNetworkConfig.php", true);
	xmlhttp3.send();
});

function updateConfig()
{
	var returnData = new Object();
	returnData['eth0DHCP'] = $("#wiredDHCPChk").is(':checked');
	returnData['eth0IP'] = $("#wiredIP").val();
	returnData['eth0Mask'] = $("#wiredSubnet").val();
	returnData['eth0Gateway'] = $("#wiredGateway").val();
	
	returnData['wlan0DHCP'] = $("#wirelessDHCPChk").is(':checked');
	returnData['wlan0IP'] = $("#wirelessIP").val();
	returnData['wlan0Mask'] = $("#wirelessSubnet").val();
	returnData['wlan0Gateway'] = $("#wirelessGateway").val();
	returnData['wlan0SSID'] = $("#wirelessNetwork").val();
	returnData['wlan0PSK'] = $("#wirelessKey").val();
	
	request = $.ajax({
        url: "/updateNetworkConfig.php",
        type: "post",
        data: returnData
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

<div class="col-sm-6">

<label><h5>Wired Network Status</h5></label><br>
<label id="eth0Connected"></label></br>
<label>IP:</label>
<label id="eth0IP">x.x.x.x</label></br>
<label>Subnet Mask:</label>
<label id="eth0Mask">x.x.x.x</label></br>
<label>Gateway:</label>
<label id="eth0Gateway">x.x.x.x</label></br>

	<form>
	  <div class="form-group">
		<label><h5>Wired Network Settings</h5></label><br>
		<input type="checkbox" id="wiredDHCPChk"><label>Use DHCP</label><br>
		<label>IP:</label>
		<input class="form-control" id="wiredIP" placeholder="IP Address" value=""><br>
		<label>Subnet Mask:</label>
		<input class="form-control" id="wiredSubnet" placeholder="Subnet Address" value=""><br>
		<label>Gateway:</label>
		<input class="form-control" id="wiredGateway" placeholder="Gateway Address" value=""><br>
	  </div>
	</form>
</div>

<div class="col-sm-6">

<label><h5>Wireless Network Status</h5></label><br>
<label id="wlan0Connected"></label></br>
<label>IP:</label>
<label id="wlan0IP">x.x.x.x</label></br>
<label>Subnet Mask:</label>
<label id="wlan0Mask">x.x.x.x</label></br>
<label>Gateway:</label>
<label id="wlan0Gateway">x.x.x.x</label></br>

	<form>
	  <div class="form-group">
		<label><h5>Wireless Network Settings</h5></label><br>
		<label><h6>Detected Networks:  </h6></label>
		<select id="wifiNetworks" onchange="$('#wirelessNetwork').val( $('#wifiNetworks').find(':selected').text());">
		</select><br><br>
		<label>SSID:</label>
		<input class="form-control" id="wirelessNetwork" placeholder="Wireless Network" value=""><br>
		<label>Key:</label>
		<input class="form-control" id="wirelessKey" placeholder="Key" value=""><br>
		<br>
		<input type="checkbox" id="wirelessDHCPChk"><label>Use DHCP</label><br>
		<label>IP:</label>
		<input class="form-control" id="wirelessIP" placeholder="IP Address" value=""><br>
		<label>Subnet Mask:</label>
		<input class="form-control" id="wirelessSubnet" placeholder="Subnet Address" value=""><br>
		<label>Gateway:</label>
		<input class="form-control" id="wirelessGateway" placeholder="Gateway Address" value=""><br>
	  </div>

	</form>
	
	
</div>

<div class="col-sm-3">
<button type="button" class="btn btn-primary" onClick="updateConfig()">Write Network Config</button>
</div>

</div>

</html>
