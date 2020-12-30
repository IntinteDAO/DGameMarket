<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href="index.php"><?php echo $website_title; ?></a>

	<input type="checkbox" id="lua-navbar-toggler" class="d-none" />

	<label for="lua-navbar-toggler" class="navbar-toggler" data-toggle="collapse" data-target="#lua-navbar-content" aria-controls="lua-navbar-content" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</label>


	<div class="collapse navbar-collapse" id="lua-navbar-content">
		<ul class="navbar-nav">

			<li class="nav-item">
				<form class="form-inline my-2 my-lg-0" action="game.php">
					<select class="form-control" name="id">

					<?php
						if(empty($_SESSION['id'])) { $id_user = 0; } else { $id_user = $_SESSION['id']; }
						$db_search = pg_fetch_all(pg_query("SELECT title FROM games WHERE (id_seller != $id_user AND status = 1) GROUP BY title ORDER BY decode(title, 'base64')::text"));

						for($i=0; $i<=count($db_search)-1; $i++) {
							echo '<option value="'.$db_search[$i]['title'].'">'.base64_decode($db_search[$i]['title']).'</option>';
						}

					?>
					</select>

					<button type="submit" class="btn btn-success">🔍 Find it!</button>
				</form>
			</li>
		</ul>

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

	</div>
</nav>

<div class="container">
  <div class="row">

<?php

if(!empty($db_password) && $db_password=="CHANGE ME!") {
	echo '<div class="col-12"><div class="alert alert-danger" role="alert"><b>REMEMBER TO CHANGE THE PASSWORD IN CONFIG.PHP TO STRONG ON PRODUCTION.</b></div></div>';
}

echo '<div class="col-12"><div class="alert alert-info" role="alert"><center><b>Haven\'t found the game? Check on <a target="_blank" href="//joltfun.com/">Joltfun!</a></b></center></div></div>';

?>
