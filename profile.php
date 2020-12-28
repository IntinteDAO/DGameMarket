<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('template/profile_table.php');
include('functions/get_game_status.php');
include('functions/encryption.php');

if(empty($_SESSION['login'])) {
	echo '<div class="col-12">Sorry, you have to log in to use this feature</div>';
} else if(isset($_GET['gameedit'])) {

	$id_user = $_SESSION['id'];
	$db_fetch_games = pg_fetch_all(pg_query("SELECT id, key, title, status, price FROM games WHERE id_seller='$id_user' ORDER BY status"));

	// Profile_table.php
	echo profile_table_start();

	foreach($db_fetch_games as $db_fetch_game) {

		if($db_fetch_game['status'] <= 3) { $change_price = '<input name="'.$db_fetch_game['id'].'" value="'.number_format(($db_fetch_game["price"])/100, 2, '.', '').'" type="number" min="0" max="500.00" step="0.01"/>'; } else { $change_price = '---'; }

		echo '<tr><td>'.$db_fetch_game['id'].'</td><td>'.base64_decode($db_fetch_game['title']).'</td><td>$'.number_format($db_fetch_game["price"]/100, 2, '.', '').'</td><td>'.get_game_status($db_fetch_game['status']).'</td><td>'.$change_price.' +'.number_format($fee/100, 2, '.', '').' fee</td></tr>';
	}
	
	echo '</table><button type="submit" id="right" class="btn btn-primary">Change price</button></form></div><div class="col-12"><p id="right">Prices will be increased by a fee '.$fee.' cents</p></div></div>';

} else if(!empty($_POST)) {

	foreach($_POST as $id => $argument) {

		$id_user = $_SESSION['id'];
		$db_game_data = pg_fetch_array(pg_query("SELECT id, price, status FROM games WHERE (id_seller = $id_user AND id = $id)"));

		if($db_game_data['status'] == 0 || $db_game_data['status'] == 1) {
		// Good

			if(is_numeric($argument)) {

				$price = (str_replace(',', '.', $argument) * 100);
				if($db_game_data['price'] == $price) { continue; }

				if($argument * 100 > 0) {
					pg_query("UPDATE games SET price = $price WHERE (id_seller = $id_user AND id = $id)");
					pg_query("UPDATE games SET status = 1 WHERE (id_seller = $id_user AND id = $id)");
					echo '<div class="col-12">The key value has been modified correctly - '.$db_game_data['id'].'</div>';
				} else {
					echo '<div class="col-12">The key value is negative - '.$db_game_data['id'].'</div>';
				}

			} else {
				echo '<div class="col-12">The value is not a numerical value - '.$db_game_data['id'].'</div>';
			}

		} else {
			echo '<div class="col-12">The rules prohibit modification of this key - '.$db_game_data['id'].'</div>';
		}

	}

echo '<div class="col-12"><center><a href="?gameedit"><button class="btn btn-primary">Go back</button></a></center></div>';
} else {
	$id_user = $_SESSION['id'];
	$db_user_data = pg_fetch_array(pg_query("SELECT * FROM users WHERE id = $id_user"));
	echo '<div class="col-12"><h2>Hello '.$_SESSION['login'].'!</h2>Your balance: '.$db_user_data['balance'].' satoshi ';
	if($db_user_data['balance']>0) { echo '<a href="withdraw.php">( Withdraw funds )</a>';}
	echo '</div>';
	echo '<div class="col-12"><a href="?gameedit">Key modification service</a></div>';

	$db_games_count = pg_fetch_array(pg_query("SELECT COUNT(id) AS count FROM games WHERE (id_buyer = $id_user AND status = 3)"))['count'];

	if(!empty($db_games_count)) {

		echo '<div class="col-12"><h2>Games bought:</h2></div>';
		$db_games_data = pg_fetch_all(pg_query("SELECT id, title, key FROM games WHERE (id_buyer = $id_user AND status = 3)"));

		echo profile_table_bought_games_start();

		foreach($db_games_data as $db_game_data) {
		    echo '<tr><td>'.$db_game_data['id'].'</td><td>'.base64_decode($db_game_data['title']).'</td><td>'.decrypt($db_game_data['key'], $hashed_db_password, $iv).'</td></tr>';
		}
	}

		echo profile_table_bought_games_end();

	$db_withdraws_count = pg_fetch_array(pg_query("SELECT COUNT(id) AS count FROM withdraws WHERE (id_user = $id_user)"))['count'];

	if(!empty($db_withdraws_count)) {
		echo '<div class="col-12"><h2>Withdraws:</h2>';
		$db_withdraws_data = pg_fetch_all(pg_query("SELECT id, sats, timestamp, status FROM withdraws WHERE (id_user = $id_user) ORDER by timestamp desc"));
		echo profile_withdraw_table_start();
		foreach($db_withdraws_data as $db_withdraw_data) {
			echo '<tr><td>'.$db_withdraw_data['id'].'</td><td>'.$db_withdraw_data['sats'].'</td><td>'.date('Y-m-d H:i:s', $db_withdraw_data['timestamp']).'</td><td>'.$db_withdraw_data['status'].'</td></tr>';
		}
		echo profile_table_bought_games_end().'</div>';
	}



}

include('footer.php');

?>