<?php

function get_game_status($status) {

switch ($status) {
    case 0:
        return "OK; Set the price";
        break;
    case 1:
        return "OK; Key ready to sell";
        break;
    case 2:
        return "OK; Key ready to remove";
        break;
    case 3:
        return "OK; Key sold";
        break;
    case 997:
        return "Fail; Key used";
        break;
    case 998:
        return "Fail; Key not exists";
        break;
    case 999:
        return "Fail; Wrong key ID";
        break;
}



}