<?php
ini_set('display_errors',1); // for the development PC only
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // ALWAYS

$name     = $_POST['email'];
if (isset($_POST['passowrd'])) {
	$password    = $_POST['password'];
} else {
	$password    = "";
}

/*
 * TODO: connect to mysql database, check user password.
 */

$json = array(
	"error" => false,
	"user" => $name,
	"login" => true
);

return json_encode($json);
?>
