<nav class="navbar navbar-light bg-light">
  <a class="navbar-brand" href="index.php"><?php echo $website_title; ?></a>
</nav>

<div class="container">
  <div class="row">

<?php

if(!empty($xor_password) && $xor_password=="CHANGE ME!") {
	echo '<div class="col-12"><div class="alert alert-danger" role="alert"><b>REMEMBER TO CHANGE THE PASSWORD IN CONFIG.PHP TO STRONG ON PRODUCTION.</b></div></div>';
}

if(!empty($sqlite_database_file) && $sqlite_database_file=="database.sqlite3") {
	echo '<div class="col-12"><div class="alert alert-warning" role="alert">If the project is released on production, remember to <b>rename</b> the database file and block it on the web server.</div></div>';
}

if(!file_exists($sqlite_database_file)) {
	echo '<div class="col-12"><div class="alert alert-primary" role="alert">Database not found! Copy the database file and modify settings in config.php</div></div>';
}

?>