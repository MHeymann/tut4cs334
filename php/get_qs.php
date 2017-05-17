<?php
ini_set('display_errors',1); // for the development PC only
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // ALWAYS

$email   = $_POST['user'];

/*
 * TODO: connect to mysql database, check user password.
 */

$multi = array();
$written = array();
for ($i = 1; $i <= 5; $i++) {
	$multi[($i-1)] = array();
	$written[($i-1)] = array();
	$multi[($i-1)]["qID"] = $i + 509;
	$written[($i-1)]["qID"] = $i + 403;
	$multi[($i-1)]["question"] = "some question, multi number $i";
	$written[($i-1)]["question"] = "some question, written number $i";
	$multi[($i-1)]["stats"] = array("user" => 30.3, "system" => 20.7);
	$written[($i-1)]["stats"] = array("user" => null, "system" => 20);

	$multi[($i-1)]["opts"] = array();
	$multi[($i-1)]["opts"][0] = "some option 1 for question $i";
	$multi[($i-1)]["opts"][1] = "some option 2 for question $i";
	$multi[($i-1)]["opts"][2] = "some option 3 for question $i";
	$multi[($i-1)]["opts"][3] = "some option 4 for question $i";

	$multi[($i-1)]["answer"] = "answer for multiple choice question number $i";
	$written[($i-1)]["answer"] = "answer for written question number $i";
}



$json = array(
	"error" => false,
	"user" => $email,
	"questions" => array(
		"multi" => $multi,
		"written" => $written
	)
);

echo json_encode($json);


function echo_error($error_message) {
	echo json_encode(array(
		"error" => true,
		"error_message" => $error_message
	));
	die();
}
?>
