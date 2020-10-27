<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="index.php"><?php echo $website_title; ?></a>

    <ul class="navbar-nav ml-auto"><li class="nav-item">

		<?php
		if(!empty($_SESSION['login'])) {
			echo '
			<a class="btn btn-link" href="add_keys.php">Add keys</a>
			<a class="btn btn-link" href="profile.php">'.$_SESSION['login'].'</a>
			<a class="btn btn-link" href="logout.php">Logout</a>
			';
		} else { echo '
			<a class="btn btn-link" href="register.php">Register</a>
			<a class="btn btn-link" href="login.php">Login</a>';
		} ?>
    </li></ul>

</nav>

<div class="container">
  <div class="row">

<?php

if(!empty($db_password) && $db_password=="CHANGE ME!") {
	echo '<div class="col-12"><div class="alert alert-danger" role="alert"><b>REMEMBER TO CHANGE THE PASSWORD IN CONFIG.PHP TO STRONG ON PRODUCTION.</b></div></div>';
}

?>
