<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('template/index_table.php');
include('functions/oracle.php');


	if(empty($_SESSION['id'])) { $id_user = 0; } else { $id_user = $_SESSION['id']; }

	$db_games_data = $sqlite_games_db->query("SELECT DISTINCT title, min(price) AS price FROM games WHERE (id_seller != $id_user AND status = 1)");
	$bitcoin_price = crypto_price('bitcoin');

	echo start_table();
	while($db_game_data = $db_games_data->fetchArray(SQLITE3_ASSOC)) {
		$game_price = floor((($db_game_data['price'] / 100000000 / 100) * $bitcoin_price) * 100000000);
		echo content_table($db_game_data['title'], $game_price);
	}

	echo end_table();

include('footer.php');