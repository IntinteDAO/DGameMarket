<?php

function create_card($title, $priceusd, $sats, $id) {

$get_data = md5(strtolower(trim($title)));
$get_name_base64 = base64_encode(trim($title));

if(file_exists('database/'.$get_data.'.json')) {
	$readfile = file('database/'.$get_data.'.json')[0];
	$json = json_decode($readfile, TRUE);
	$description = $json['description'];
	$steamid = $json['steamid'];
	$imgurl = "https://steamcdn-a.akamaihd.net/steam/apps/$steamid/header.jpg";
} else {
	$description = "No description :(";
	$imgurl = "template/noimage.jpg";
}

return '
          <div class="col-lg-3 col-md-5 mb-3">
            <div class="card h-100">
              <a href="game.php?id='.$get_name_base64.'"><img class="card-img-top" src="'.$imgurl.'" alt=""></a>
              <div class="card-body">
                <h4 class="card-title">
                  <small><a href="game.php?id='.$get_name_base64.'">'.$title.'</a></small>
                </h4>
                <h5>Prices start from $'.$priceusd.'</h5>
                <p class="card-text"><small><div class="text-justify">'.$description.'</div></small></p>
              </div>
              <div class="card-footer">
                <small class="text-muted"><a target="_blank" href="buy_game.php?game_id='.$id.'">Quick buy ('.$sats.' Satoshi)</a></small>
              </div>
            </div>
          </div>

';

}

?>