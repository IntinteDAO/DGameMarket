<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('functions/oracle.php');

if(!empty($_SESSION['id'])) {
	$id_user = $_SESSION['id'];
} else {
	$id_user = 0;
}

if(!empty($_GET['id'])) {

	if(preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $_GET['id'])) {
		$title = $_GET['id'];
		$decoded_title = base64_decode($title);
		$db_games_get_count_of_title = pg_fetch_array(pg_query("SELECT COUNT(id) AS count FROM games WHERE (status = 1 AND title = '$title' AND id_seller != $id_user)"))[0];
		$filename = md5(strtolower($decoded_title));
		if(file_exists('database/'.$filename.'.json')) {
			$file = file('database/'.$filename.'.json')[0];
			$data = json_decode($file, TRUE);
			$steamid = $data['steamid'];
			$image = "https://steamcdn-a.akamaihd.net/steam/apps/$steamid/header.jpg";
			$description = $data['description'];

			if(!empty($data['metacritic'])) {
				$metacritic = '<b>Metacritic score: <a target="_blank" href="'.$data['metacritic']['url'].'">'.$data['metacritic']['score'].' / 100</a></b><br>';
			} else {
				$metacritic = "";
			}

			$platforms_integer = $data['platforms'];
			$platforms_string = '';
			if($platforms_integer >= 4) {
				$platforms_string = $platforms_string.' '.'Windows';
				$platforms_integer = $platforms_integer - 4;
			}

			if($platforms_integer >= 2) {
				$platforms_string = $platforms_string.' '.'MacOS';
				$platforms_integer = $platforms_integer - 2;
			}

			if($platforms_integer >= 1) {
				$platforms_string = $platforms_string.' '.'Linux';
				$platforms_integer = $platforms_integer - 1;
			}

			$platforms_string = str_replace(' ', ', ', trim($platforms_string));
			$platforms = '<b>Platforms:</b> '.$platforms_string.'<br>';
			$link = "<a target='_blank' href='https://store.steampowered.com/app/$steamid'>Go to the game profile on Steam</a>";

		} else {
			$image = "template/noimage.jpg";
			$metacritic = "";
			$description = "No description, Sorry! :-(";
			$platforms = '';
			$link = '';
		}


		echo 	'<div class="col-lg-5">
				<img src="'.$image.'">
			</div>';

		echo 	'<div class="col-lg-7">
				<h2>'.$decoded_title.'</h2>
				<b>There are keys left: '.$db_games_get_count_of_title.'</b><br>
				'.$metacritic.'
				'.$platforms.'
				<div class="text-justify">'.$description.'</div>
				'.$link.'
			</div>';
	}

	$game_info = pg_fetch_array(pg_query("SELECT id, price FROM games WHERE (status = 1 AND title = '$title' AND id_seller != $id_user) ORDER BY price asc"));

	if(!empty($game_info)) {
		$bitcoin_price = crypto_price('bitcoin');
		$game_price = floor((1000000 / $bitcoin_price) * ($game_info['price'] + $fee));
		echo '<div class="col-12"><br><center><a target="_blank" href="buy_game.php?game_id='.$game_info['id'].'"><button class="btn btn-primary">Buy Now! ('.$game_price.' Satoshi)</button></a></center></div>';
	}

}

include('footer.php');