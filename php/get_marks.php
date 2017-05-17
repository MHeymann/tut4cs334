<?php
ini_set('display_errors',1); // for the development PC only
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // ALWAYS

$email   = $_POST['user'];

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


$multi = array();
$written = array();
foreach ($_POST['multi'] as $m_q => $m_val) {
	$multi[($m_q)] = array();
	$cor_ans = get_ans($m_q, $connect, "multi_q");

	$ask_count = select_current_system_count($connect, "multi_q", $m_q, 'ask_count');
	$right_count = select_current_system_count($connect, "multi_q", $m_q, 'right_count');
	if ($ask_count == NULL) {
		$ask_count = 1;
		$right_count = 0;
	} else {
		$ask_count += 1;
	}

	if (trim($m_val) == trim($cor_ans)) {
		$multi[($m_q)]['user'] = true;
		$right_count += 1;
	} else {
		$multi[($m_q)]['user'] = false;
	}
	$multi[($m_q)]['answer'] = $cor_ans;
	update_system_counts($connect, 'multi_q', $m_q, $ask_count, $right_count);
}

foreach ($_POST['written'] as $w_q => $w_val) {
	$written[($w_q)] = array();
	$cor_ans = get_ans($w_q, $connect, "written_q");

	$ask_count = select_current_system_count($connect, "written_q", $w_q, 'ask_count');
	$right_count = select_current_system_count($connect, "written_q", $w_q, 'right_count');
	if ($ask_count == NULL) {
		$ask_count = 1;
		$right_count = 0;
	} else {
		$ask_count += 1;
	}

	if (trim($w_val) == trim($cor_ans)) {
		$written[($w_q)]['user'] = true;
		$right_count += 1;
	} else {
		$written[($w_q)]['user'] = false;
	}
	$written[($w_q)]['answer'] = $cor_ans;
	update_system_counts($connect, 'written_q', $w_q, $ask_count, $right_count);
}

$connect->close();


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

function try_use($db_name, $connect) {
	$sql = "USE `$db_name`;";
	if ($connect->query($sql) !== TRUE) {
		echo_error("Failed to use new database $db_name.\n");
	}
}

function get_ans($m_q, $connect, $table) {
	$sql = "SELECT ans FROM $table ";
	$sql .= "WHERE qID = $m_q;";
	$result = $connect->query($sql);
	if ($result === FALSE) {
		$error = $connect->error;
		echo_error("Could not perform select query on $table. Error: $error");
	}
	if ($result->num_rows == 0) {
		$result->close();
		echo_error("question $m_q in $table not found.\n");
	}
	$row = $result->fetch_assoc();
	$result->close();

	return $row['ans'];
}

function select_current_system_count($connect, $table, $qID, $col) {
	$sql = "SELECT $col FROM $table ";
	$sql .= "WHERE qID = $qID ";
	$result = $connect->query($sql);
	if ($result === FALSE) {
		$error = $connect->error;
		echo_error("Could not perform select query on $table. Error: $error");
	}
	if ($result->num_rows == 0) {
		$result->close();
		echo_error("question $qID in $table not found.\n");
	}
	$row = $result->fetch_assoc();
	$result->close();

	return $row[$col];
}

function update_system_counts($connect, $table, $qID, $ask_count, $right_count) {
	$sql = "UPDATE $table ";
	$sql .= "SET right_count = $right_count, ask_count = $ask_count ";
	$sql .= "WHERE qID = $qID;";
	$result = $connect->query($sql);
	if ($result === FALSE) {
		$error = $connect->error;
		echo_error("Could not update $table. Error: $error. SQL: $sql.");
	}
}

?>
