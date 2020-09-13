<nav class="navbar navbar-light bg-light">
  <a class="navbar-brand" href="index.php"><?php echo $website_title; ?></a>
</nav>

<div class="container">
  <div class="row">

<?php

if(!empty($xor_password) && $xor_password=="CHANGE ME!") {
	echo '<div class="col-12"><div class="alert alert-danger" role="alert"><b>REMEMBER TO CHANGE THE PASSWORD IN CONFIG.PHP TO STRONG ON PRODUCTION.</b></div></div>';
}

?>
