i="0"

while [ $i -lt 1 ]
do
	torify php buy_game.php
	torify php withdraws.php
	torify php verify_keys.php
	sleep 10s
done