<?php
ini_set('display_errors',1); // for the development PC only
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // ALWAYS

if (!isset($_POST['submit_action'])) {
	echo json_encode(array(
		"error" => true,
		"error_message" => "Action not specified.",
	));
	die();
}

if (($_POST['submit_action'] == "register") && !isset($_POST['password2'])) {
	echo json_encode(array(
		"error" => true,
		"error_message" => "Password needs to be provided twice for correctness.",
	));
	die();
}

if (!isset($_POST['password'])) {
	echo json_encode(array(
		"error" => true,
		"error_message" => "No password provided.",
	));
	die();
}

if (isset($_POST['password2'])) {
	if ($_POST['password2'] !== $_POST['password']) {
		echo json_encode(array(
			"error" => true,
			"error_message" => "Provided passwords don't match.",
		));
		die();
	}

	$pass_pattern = '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{5,15}$/';
	$preg_result = preg_match($pass_pattern, $_POST['password']);
	if ($preg_result === FALSE) {
		echo json_encode(array(
			"error" => true,
			"error_message" => "Error while testing password regular expression",
		));
		die();
	} else if ($preg_result === 0) {
		echo json_encode(array(
			"error" => true,
			"error_message" => "Password invalid." .
			"  Must be between 5 and 15 characters with lowercase, " .
			"uppercase and numeric",
		));
		die();

	}

}

if (!isset($_POST['email'])) {
	echo json_encode(array(
		"error" => true,
		"error_message" => "Please provide an email for the user",
	));
	die();
}

$password = $_POST['password'];
$email  = $_POST['email'];
$action = $_POST['submit_action'];


$settings = parse_ini_file(__DIR__."/../.my.cnf", true);
$connect = new mysqli(
	$settings["client"]["mysql_server"],
	$settings["client"]["user"],
	$settings["client"]["password"]
);

if ($connect->connect_errno) {
	$error = $connect->connect_error;
	echo json_encode(array(
		"error" => true,
		"error_message" => "mysqli error: $error"
	));
	die();
}

if ($connect->query("USE tut4_db;") !== TRUE) {
	echo json_encode(array(
		"error" => true,
		"error_message" => "Could not use database tut4_db."
	));
	die();
}


if ($action == "register") {
	$sql = "SELECT email FROM user WHERE email LIKE '$email'";
	$result = $connect->query($sql);
	if ($result->num_rows > 0) {
		$result->close();
		echo json_encode(array(
			"error" => true,
			"error_message" => "User with email '$email' already exists"
		));
		die();
	}
	$result->close();
	$sql = "INSERT INTO user ";
	$sql .= "(email, password) ";
	$sql .= "VALUES ";
	$sql .= "('$email', '$password');";
	if (!$connect->query($sql)) {
		echo json_encode(array(
			"error" => true,
			"error_message" => "Error entering user with email '$email' into database: " .
			$connect->error
		));
		die();
	}

} else if ($action == "login") {
	$sql = "SELECT password FROM user WHERE email LIKE '$email'";
	$result = $connect->query($sql);
	if ($result === FALSE) {
		echo json_encode(array(
			"error" => true,
			"error_message" => "Failed when looking up password for user."
		));
		die();
	}
	// TODO update error message to just say problems with username or
	// password
	if ($result->num_rows == 0) {
		$result->close();
		echo json_encode(array(
			"error" => true,
			"error_message" => "User with email '$email' does not exist"
		));
		die();
	}
	while($row = $result->fetch_assoc()) {
		$db_password = $row["password"];
	}
	$result->close();
	if ($password !== $db_password) {
		echo json_encode(array(
			"error" => true,
			"error_message" => "Incorrect password was provided"
		));
		die();
	}
/*
 * TODO: connect to mysql database, check user password.
 */
} else {
	echo json_encode(array(
		"error" => true,
		"error_message" => "Requested action not recognized"
	));
	die();

}

$connect->close();

$json = array(
	"error" => false,
	"user" => $email,
	"action" => $action,
);


echo json_encode($json);
?>
