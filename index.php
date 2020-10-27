<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('template/index_table.php');
include('functions/oracle.php');


	if(empty($_SESSION['id'])) { $id_user = 0; } else { $id_user = $_SESSION['id']; }

	$db_games_data_count = pg_fetch_array(pg_query("SELECT COUNT(DISTINCT(title)) AS count FROM games WHERE (id_seller != $id_user AND status = 1)"))[0];
	$max_pages = ceil($db_games_data_count / $limit_games_per_page);

	if(!isset($_GET['page'])) {
		$page = 1;
	} else if((is_numeric($_GET['page'])) && $_GET['page'] > 0 && $_GET['page'] <= $max_pages) {
		$page = $_GET['page'];
	} else {
		die('Wrong page index');
	}

	if($db_games_data_count > 0) {

		$db_games_data = pg_fetch_all(pg_query("SELECT DISTINCT title FROM games WHERE (id_seller != $id_user AND status = 1) GROUP BY title LIMIT $limit_games_per_page OFFSET ($limit_games_per_page * ($page-1))"));
		for($i=0; $i<=count($db_games_data)-1; $i++) {
			$game[$i]['title'] = $db_games_data[$i]['title'];
			$title = $game[$i]['title'];
			$game[$i]['price'] = pg_fetch_array(pg_query("SELECT min(price) FROM games WHERE title = '$title' AND (id_seller != $id_user AND status = 1)"))[0];
			$price = $game[$i]['price'];
			$game[$i]['id'] = pg_fetch_array(pg_query("SELECT id FROM games WHERE title = '$title' AND price = $price AND (id_seller != $id_user AND status = 1)"))[0];
		}

		$bitcoin_price = crypto_price('bitcoin');

		for($i=0; $i<=count($game)-1; $i++) {
			$game_price = floor((1000000 / $bitcoin_price) * ($game[$i]['price'] + $fee));
			echo create_card(base64_decode($game[$i]['title']),  number_format((($game[$i]['price']+$fee)/100), 2, '.', ' '), $game_price, $game[$i]['id']);
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

	} else {
		echo '<div class="col-12">No games found in this node! :-(</div>';
	}

include('footer.php');