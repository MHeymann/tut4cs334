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
$i = 0;
foreach ($_POST['multi'] as $m_q => $m_val) {
	$multi[($m_q)] = array();
	if ($i % 2) {
		$multi[($m_q)]['user'] = true;
	} else {
		$multi[($m_q)]['user'] = false;
	}
	$multi[($m_q)]['answer'] = "$m_q meg";
	$i++;
}

foreach ($_POST['written'] as $w_q => $w_val) {
	$written[($w_q)] = array();
	if ($i % 2) {
		$written[($w_q)]['user'] = true;
	} else {
		$written[($w_q)]['user'] = false;
	}
	$written[($w_q)]['answer'] = "$w_q meg";
	$i++;
}



$json = array(
	"error" => false,
	"user" => $email,
	"answers" => array(
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
