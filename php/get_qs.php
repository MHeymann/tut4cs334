<?php
ini_set('display_errors',1); // for the development PC only
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // ALWAYS

$email   = $_POST['user'];

/*
 * TODO: connect to mysql database, check user password.
 */

$json = array(
	"error" => false,
	"user" => $email,
	"questions" => array(
		"q" => "Liewe heksie"
	)
);

echo json_encode($json);
?>
