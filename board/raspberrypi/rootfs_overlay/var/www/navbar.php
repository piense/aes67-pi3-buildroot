<head>
<script language="javascript">

function reboot(){
	request = $.ajax({
		url: "/Reboot.php",
		type: "post",
		data: { }
	});
	
}

</script>
</head>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Navbar</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Configuration
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
			<a class="dropdown-item" href="BootConfig.php">Boot</a>
			<a class="dropdown-item" href="NetworkConfig.php">Network</a>
			<a class="dropdown-item" href="#"><div onClick="reboot();">Reboot Now!</div></a>
        </div>
      </li>
	  
    </ul>
  </div>
</nav>
