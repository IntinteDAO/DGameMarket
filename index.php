<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('template/index_table.php');
include('functions/oracle.php');


	if(empty($_SESSION['id'])) { $id_user = 0; } else { $id_user = $_SESSION['id']; }

	$db_games_data_count = $sqlite_games_db->querySingle("SELECT DISTINCT count(title) AS price FROM games WHERE (id_seller != $id_user AND status = 1)");

	if($db_games_data_count > 0) {
		$db_games_data = $sqlite_games_db->query("SELECT DISTINCT title, price AS price, id FROM games WHERE (id_seller != $id_user AND status = 1)");
		$bitcoin_price = crypto_price('bitcoin');

		echo start_table();
		while($db_game_data = $db_games_data->fetchArray(SQLITE3_ASSOC)) {
			$game_price = floor(((($db_game_data['price'] + $fee) / 100000000 / 100) * $bitcoin_price) * 100000000);
			echo content_table($db_game_data['title'], $game_price, $db_game_data['id']);
		}
	} else {
		echo '<div class="col-12">No games found in this node! :-(</div>';
	}

	echo end_table();

include('footer.php');