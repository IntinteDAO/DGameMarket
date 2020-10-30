<?php

function withdraw_confirmation($description, $satoshi, $lninvoice) {

if(empty($description)) { $description = ""; }

return '
<div class="col-12">
	<h2>Do you really want to withdraw funds?</h2>
	Invoice description: '.htmlspecialchars($description).'<br>
	Payout of Satoshi: '.$satoshi.'<br>

	<form method="POST">
		<input type="hidden" name="lninvoice" value="'.$lninvoice.'">
		<input type="hidden" name="confirm" value="1">
		<button class="btn btn-primary">Withdraw</button>
	</form>
</div>
';

}