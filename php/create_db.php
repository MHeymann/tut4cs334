<?php

/**
 * Create a database with the required fields for this project, and an
 * index to help with queries on large databases.
 *
 * @param $db_name The name of the database to create
 * @param $target_file The path to the pgn file for getting information on
 * the longest move sequence.
 */
function create_database($db_name, $verbose=true) {
	/*
	 * Create connection to the local mysql server
	 * as provided by the LAMP/MAMP stack.
	 */
	$settings = parse_ini_file(__DIR__."/../.my.cnf", true);
	$connect = new mysqli(
		$settings['client']['mysql_server'],
		$settings['client']['user'],
		$settings['client']['password']
	);

	if ($connect->connect_errno) {
		$error = $connect->connect_error;
		echo "Failed to connect to mysql server.  Error: $error.\n";
		die();
	} else if ($verbose) {
		echo "Successfully connected to mysql server.\n";
	}

	/* Delete database if exists */
	try_drop($db_name, $connect, $verbose);

	/* Create new database */
	try_create($db_name, $connect, $verbose);

	/* Select the new database for querying */
	try_use($db_name, $connect, $verbose);

	/* create a table for users to log in with */
	try_create_user_table($db_name, $connect, $verbose);

	/* create index on user table just created */
	try_create_user_index($db_name, $connect, $verbose);

	try_create_question_tables($db_name, $connect, $verbose);

	$connect->close();
	echo "Closed connection to mysql server.\n";
}

function try_drop($db_name, $connect, $verbose) {
	$sql = "DROP DATABASE `$db_name`;";
	if (($connect->query($sql) === true) && $verbose) {
		echo "Deleted database $db_name.\n";
	} else if ($verbose) {
		echo "Could not delete database $db_name.\n";
		echo "This might be because thi database doesn't exist.\n";
	}
}

function try_create($db_name, $connect, $verbose) {
	$sql = "CREATE DATABASE `$db_name`";
	if ($connect->query($sql) !== TRUE) {
		echo "Failed to create new database $db_name.\n";
		die();
	} else if ($verbose) {
		echo "Created new database $db_name.\n";
	}
}

function try_use($db_name, $connect, $verbose) {
	$sql = "USE `$db_name`";
	if ($connect->query($sql) !== TRUE) {
		echo "Failed to use new database $db_name.\n";
		die();
	} else if ($verbose) {
		echo "Using new database $db_name.\n";
	}
}

function try_create_user_table($db_name, $connect, $verbose) {
	$sql = "CREATE TABLE `user` (";
	$sql .= "uID INT(10) NOT NULL AUTO_INCREMENT,";
	$sql .= "email VARCHAR(254) NOT NULL,";
	$sql .= "password VARCHAR(15) NOT NULL,";
	$sql .= "PRIMARY KEY (uID)";
	$sql .= ");";
	if ($connect->query($sql) !== TRUE) {
		echo "Failed to create user table in $db_name.\n";
		die();
	} else if ($verbose) {
		echo "Created user table in $db_name.\n";
	}
}

function try_create_user_index($db_name, $connect, $verbose) {
	$sql = "CREATE INDEX `user_index` ON `user` (";
	$sql .= "email";
	$sql .= ");";
	if ($connect->query($sql) !== TRUE) {
		echo "Failed to create index user_index in $db_name.\n";
		die();
	} else if ($verbose) {
		echo "Created index user_index in $db_name.\n";
	}
}

function try_create_question_tables($db_name, $connect, $verbose) {
	$sql = "CREATE TABLE `multi_q` (";
	$sql .= "qID INT(10) NOT NULL AUTO_INCREMENT,";
	$sql .= "question VARCHAR(100) NOT NULL,";
	$sql .= "opt1 VARCHAR(40) NOT NULL,";
	$sql .= "opt2 VARCHAR(40) NOT NULL,";
	$sql .= "opt3 VARCHAR(40) NOT NULL,";
	$sql .= "opt4 VARCHAR(40) NOT NULL,";
	$sql .= "ans VARCHAR(40) NOT NULL,";
	$sql .= "ask_count INT(10),";
	$sql .= "right_count INT(10),";
	$sql .= "PRIMARY KEY (qID)";
	$sql .= ");";
	if ($connect->query($sql) !== TRUE) {
		$error = $connect->error;
		echo "Failed to create multiple question table in $db_name. Error: $error\n";
		die();
	} else if ($verbose) {
		echo "Created multiple question table in $db_name.\n";
	}

	$sql = "CREATE TABLE `multi_qu_count` (";
	$sql .= "qID INT(10) NOT NULL,";
	$sql .= "uID INT(10) NOT NULL,";
	$sql .= "q_count INT(10) NOT NULL,";
	$sql .= "u_count INT(10) NOT NULL,";
	$sql .= "PRIMARY KEY (qID, uID),";
	$sql .= "FOREIGN KEY (qID) REFERENCES multi_q(qID),";
	$sql .= "FOREIGN KEY (uID) REFERENCES user(uID)";
	$sql .= ");";
	if ($connect->query($sql) !== TRUE) {
		$error = $connect->error;
		echo "Failed to create multiple question answer count table in $db_name. Error: $error\n";
		die();
	} else if ($verbose) {
		echo "Created multiple question answer count table in $db_name.\n";
	}

	$sql = "CREATE TABLE `written_q` (";
	$sql .= "qID INT(10) NOT NULL AUTO_INCREMENT,";
	$sql .= "question VARCHAR(100) NOT NULL,";
	$sql .= "ans VARCHAR(40) NOT NULL,";
	$sql .= "ask_count INT(10),";
	$sql .= "right_count INT(10),";
	$sql .= "PRIMARY KEY (qID)";
	$sql .= ");";
	if ($connect->query($sql) !== TRUE) {
		$error = $connect->error;
		echo "Failed to create multiple question table in $db_name. Error: $error\n";
		die();
	} else if ($verbose) {
		echo "Created multiple question table in $db_name.\n";
	}

	$sql = "CREATE TABLE `written_qu_count` (";
	$sql .= "qID INT(10) NOT NULL,";
	$sql .= "uID INT(10) NOT NULL,";
	$sql .= "q_count INT(10) NOT NULL,";
	$sql .= "u_count INT(10) NOT NULL,";
	$sql .= "PRIMARY KEY (qID, uID),";
	$sql .= "FOREIGN KEY (qID) REFERENCES written_q(qID),";
	$sql .= "FOREIGN KEY (uID) REFERENCES user(uID)";
	$sql .= ");";
	if ($connect->query($sql) !== TRUE) {
		$error = $connect->error;
		echo "Failed to create multiple question answer count table in $db_name. Error: $error\n";
		die();
	} else if ($verbose) {
		echo "Created multiple question answer count table in $db_name.\n";
	}

}

?>
