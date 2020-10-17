<?php

function withdraw_confirmation($description, $satoshi, $lninvoice) {

if(empty($description)) { $description = ""; }

return '
<div class="col-12">
	<h2>Do you really want to withdraw funds?</h2>
	Invoice description: '.$description.'<br>
	Payout of Satoshi: '.$satoshi.'<br>
	<a href="withdraw.php?lninvoice='.$lninvoice.'&confirm=1"><button class="btn btn-primary">Withdraw</button></a>
</div>
';

}