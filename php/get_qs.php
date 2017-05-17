<?php
ini_set('display_errors',1); // for the development PC only
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // ALWAYS

$email = $_POST['user'];

/*
 * TODO: connect to mysql database, check user password.
 */

$settings = parse_ini_file(__DIR__."/../.my.cnf", true);
$connect = new mysqli(
	$settings['client']['mysql_server'],
	$settings['client']['user'],
	$settings['client']['password']
);

if ($connect->connect_errno) {
	$error = $connect->connect_error;
	echo_error("Failed to connect to mysql server.  Error: $error.\n");
}

try_use("tut4_db", $connect);

$sql = "SELECT * FROM multi_q ";
$sql .= "ORDER BY RAND() ";
$sql .= "LIMIT 5;";
$result = $connect->query($sql);
if ($result === FALSE) {
	$error = $connect->error;
	echo_error("Could not perform select query for multi quersions. Error: $error");
}
if ($result->num_rows == 0) {
	$result->close();
	echo_error("Table multi_q seems to not be populated. \n");
}

$multi = array();
$i = 0;
while($row = $result->fetch_assoc()) {
	$multi[$i] = array();
	$multi[$i]["qID"] = $row["qID"];
	$multi[$i]["question"] = $row["question"];
	$multi[$i]["opts"] = array();
	for ($j = 0; $j < 4; $j++) {
		$multi[$i]["opts"][$j] = $row["opt" . ($j + 1)];
	}
	$multi[$i]["answer"] = $row["ans"];

	if (($row["ask_count"] === NULL) || ($row["ask_count"] === 0)) {
		$multi[$i]["stats"] = array("user" => NULL, "system" => NULL);
	} else {
		$ask_c = $row["ask_count"];
		$ans_c = $row["right_count"];
		if ($ans_c === NULL) {
			$ans_c = 0;
		}

		$multi[$i]["stats"] = array("system" => ($ans_c / $ask_c * 100));
		/* TODO: query for user
		 */
		$multi[$i]["stats"]["user"] = NULL;
	}

	$i++;
}
$result->close();


$sql = "SELECT * FROM written_q ";
$sql .= "ORDER BY RAND() ";
$sql .= "LIMIT 5;";
$result = $connect->query($sql);
if ($result === FALSE) {
	$error = $connect->error;
	echo_error("Could not perform select query for written quersions. Error: $error");
}
if ($result->num_rows == 0) {
	$result->close();
	echo_error("Table written_q seems to not be populated. \n");
}



$written = array();
$i = 0;
while($row = $result->fetch_assoc()) {
	$written[$i] = array();
	$written[$i]["qID"] = $row["qID"];
	$written[$i]["question"] = $row["question"];
	$written[$i]["answer"] = $row["ans"];

	if (($row["ask_count"] === NULL) || ($row["ask_count"] === 0)) {
		$written[$i]["stats"] = array("user" => NULL, "system" => NULL);
	} else {
		$ask_c = $row["ask_count"];
		$ans_c = $row["right_count"];
		if ($ans_c === NULL) {
			$ans_c = 0;
		}

		$written[$i]["stats"] = array("system" => ($ans_c / $ask_c * 100));
		/* TODO: query for user
		 */
		$written[$i]["stats"]["user"] = NULL;
	}

	$i++;
}
$result->close();

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

function try_use($db_name, $connect) {
	$sql = "USE `$db_name`;";
	if ($connect->query($sql) !== TRUE) {
		echo_error("Failed to use new database $db_name.\n");
	}
}

?>
