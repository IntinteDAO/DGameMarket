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

function content_table($title, $game_price) {
	return '

		<tr>
			<td>
				'.$title.'
			</td>
			<td>
				'.$game_price.' sat
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