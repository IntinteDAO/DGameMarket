<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('template/index_table.php');
include('functions/oracle.php');


	if(empty($_SESSION['id'])) { $id_user = 0; } else { $id_user = $_SESSION['id']; }

	$db_games_data_count = $sqlite_games_db->querySingle("SELECT COUNT(DISTINCT(title)) AS price FROM games WHERE (id_seller != $id_user AND status = 1)");
	$max_pages = ceil($db_games_data_count / $limit_games_per_page);

	if(!isset($_GET['page'])) {
		$page = 1;
	} else if((is_numeric($_GET['page'])) && $_GET['page'] > 0 && $_GET['page'] <= $max_pages) {
		$page = $_GET['page'];
	} else {
		die('Wrong page index');
	}




	if($db_games_data_count > 0) {
		$db_games_data = $sqlite_games_db->query("SELECT id, title, min(price) AS price FROM games WHERE (id_seller != $id_user AND status = 1) GROUP BY title LIMIT $limit_games_per_page OFFSET ($limit_games_per_page * ($page-1))");
		$bitcoin_price = crypto_price('bitcoin');
	} else {
		echo '<div class="col-12">No games found in this node! :-(</div>';
	}

		while($db_game_data = $db_games_data->fetchArray(SQLITE3_ASSOC)) {
			$game_price = floor(((($db_game_data['price'] + $fee) / 100000000 / 100) * $bitcoin_price) * 100000000);
			echo create_card(base64_decode($db_game_data['title']),  number_format((($db_game_data['price']+$fee)/100), 2, '.', ' '), $game_price, $db_game_data['id']);
		}

// Pagination
if($max_pages > 1) {

	echo '<div class="col-12">
		<nav>
			<ul class="pagination justify-content-center">';

	if($page != 1) {
		echo '<li class="page-item"><a class="page-link" href="?page='.($page-1).'">Previous</a></li>';
	} else {
		echo '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>';
	}

	for($i=1; $i<=$max_pages; $i++) {

		if($page == $i) {
			echo '<li class="page-item active"><a class="page-link" href="#">'.$i.'</a></li>';
		} else {
			echo '<li class="page-item"><a class="page-link" href="?page='.$i.'">'.$i.'</a></li>';
		}

	}

	if($page < $max_pages) {
		echo '<li class="page-item"><a class="page-link" href="?page='.($page + 1).'">Next</a></li>';
	} else {
		echo '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
	}

	echo '</ul></nav></div>';
}

include('footer.php');