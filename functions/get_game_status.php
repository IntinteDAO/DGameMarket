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
    case 900:
        return "<i>Key in the verification queue</i>";
        break;
    case 997:
        return "<b>Fail; Key used</b>";
        break;
    case 998:
        return "<b>Fail; Key not exists</b>";
        break;
    case 999:
        return "<b>Fail; Wrong key ID</b>";
        break;
}



}