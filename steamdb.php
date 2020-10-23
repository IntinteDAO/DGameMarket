<?php

if(php_sapi_name()!="cli") { die(); }

if (!file_exists('database')) {
	mkdir('database');
}

$getAppList = file("https://api.steampowered.com/ISteamApps/GetAppList/v2/")[0];
$getAppList_array = json_decode($getAppList, true);
$count_all_games = count($getAppList_array['applist']['apps']);

for($i=0; $i<=$count_all_games-1; $i++) {
	unset($game);
	sleep(1);
	if($i%10==0) { sleep(2); }
	$j = $i + 1;
	$is_exists = md5(strtolower(trim($getAppList_array['applist']['apps'][$i]['name'])));
	if(!file_exists('database/'.$is_exists.'.json')) {

		$id = $getAppList_array['applist']['apps'][$i]['appid'];
		$getApp = file('https://store.steampowered.com/api/appdetails?l=english&cc=en&appids='.$id)[0];
		$getApp_array = json_decode($getApp, true);
		if(empty($getApp_array[$id]['success'])) { echo 'This game is not available ('.$j.' / '.$count_all_games.')'.PHP_EOL; continue; }
		if(!empty($getApp_array[$id]['data']['is_free'])) { echo 'This game is free ('.$j.' / '.$count_all_games.')'.PHP_EOL; continue; }
		if($getApp_array[$id]['data']['type']=="demo" || $getApp_array[$id]['data']['type']=="soundtrack" || $getApp_array[$id]['data']['type']=="music") { echo 'This is Demo or soundtrack ('.$j.' / '.$count_all_games.')'.PHP_EOL; continue; }
		$game['type'] = $getApp_array[$id]['data']['type'];
		$game['name'] = $getApp_array[$id]['data']['name'];
		$game['steamid'] = $getApp_array[$id]['data']['steam_appid'];
		$game['description'] = $getApp_array[$id]['data']['short_description'];
		$game['platforms'] = $getApp_array[$id]['data']['platforms']['windows']*4 + $getApp_array[$id]['data']['platforms']['mac']*2 + $getApp_array[$id]['data']['platforms']['linux'];

		if(!empty($getApp_array[$id]['data']['price_overview']['final'])) {
			$game['price'] = $getApp_array[$id]['data']['price_overview']['final'];
		} else if (!empty($getApp_array[$id]['data']['price_overview']['initial'])) {
			$game['price'] = $getApp_array[$id]['data']['price_overview']['initial'];
		} else {
			$game['price'] = 0;
		}

		if(!empty($getApp_array[$id]['data']['categories'])) {
			for($k=0; $k<=count($getApp_array[$id]['data']['categories'])-1; $k++) {
				$categories[$k] = $getApp_array[$id]['data']['categories'][$k]['id'];
			}

			$game['categories'] = $categories; 
		}

		if(!empty($getApp_array[$id]['data']['genres'])) {
			for($l=0; $l<=count($getApp_array[$id]['data']['genres'])-1; $l++) {
				$genres[$l] = $getApp_array[$id]['data']['genres'][$l]['id'];
			}

			$game['genres'] = $genres;
		}

		if(!empty($getApp_array[$id]['data']['metacritic']['score'])) {
			$game['metacritic']['score'] = $getApp_array[$id]['data']['metacritic']['score'];
			$game['metacritic']['url'] = explode("?", $getApp_array[$id]['data']['metacritic']['url'])[0];
		}

		$filename = md5(strtolower(trim($game['name'])));
		if (!file_exists('database/'.$filename.'.json')) {
			$fp = fopen('database/'.$filename.'.json', 'w');
			fwrite($fp, json_encode($game));
			fclose($fp);
			echo 'Created game '.$game['name'].' ('.$j.' / '.$count_all_games.')'.PHP_EOL;
		} else {
			echo 'Ignoring - already exist ('.$j.' / '.$count_all_games.')'.PHP_EOL;
		}

	} else {
		echo 'Skip - Name collision ('.$j.' / '.$count_all_games.')'.PHP_EOL;
	}
}




