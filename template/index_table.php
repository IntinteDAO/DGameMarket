<?php

function start_table() {
return '	
	<div class="col-12">
		<table class="table">
			<thead>
				<tr>
					<th scope="col">Title:</th>
					<th scope="col">Quick Buy / Price:</th>
				</tr>
			</thead>

';
}

function content_table($title, $game_price, $id) {
	return '

		<tr>
			<td>
				'.$title.'
			</td>
			<td>
				<a href="buy_game.php?game_id='.$id.'">'.$game_price.' sat</a>
			</td>
		</tr>

';
}


function end_table() {
return '
		</table>
	</div>
';
}

?>