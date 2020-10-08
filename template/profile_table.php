<?php

function profile_table_start() {

return '

	<div class="col-12"><form method="POST" action="profile.php"><table class="table">
		<thead>
			<tr>
				<th scope="col">ID:</th>
				<th scope="col">Title:</th>
				<th scope="col">Price without fee:</th>
				<th scope="col">Status:</th>
				<th scope="col">New price:</th>
			</tr>
		</thead>

';

}

function profile_table_bought_games_start() {

return '

	<div class="col-12"><table class="table">
		<thead>
			<tr>
				<th scope="col">ID:</th>
				<th scope="col">Title:</th>
				<th scope="col">Key:</th>
			</tr>
		</thead>

';

}

function profile_table_bought_games_end() {

return '

		</table>
	</div>

';

}