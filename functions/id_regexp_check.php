<?php

function id_verify($id) {

if (preg_match("/^[a-zA-Z0-9]{16}$/", $id)) {
	return true;
} else {
	return false;
}

}