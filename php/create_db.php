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
	} else if ($verbose) {
		echo "Successfully connected to mysql server.\n";
	}

	$sql = "DROP DATABASE `$db_name`;";
	if (($connect->query($sql) === true) && $verbose) {
		echo "Deleted database $db_name.\n";
	} else if ($verbose) {
		echo "Could not delete database $db_name.\n";
		echo "This might be because thi database doesn't exist.\n";
	}

	/* create new database */
	$sql = "CREATE DATABASE `$db_name`";
	if ($connect->query($sql) !== TRUE) {
		echo "Failed to create new database $db_name.\n";
		die();
	} else if ($verbose) {
		echo "Created new database $db_name.\n";
	}
	
	/* select the new database for querying */
	$sql = "USE `$db_name`";
	if ($connect->query($sql) !== TRUE) {
		echo "Failed to use new database $db_name.\n";
		die();
	} else if ($verbose) {
		echo "Using new database $db_name.\n";
	}

	/* create a table with the required fields */
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

	$sql = "CREATE INDEX `user_index` ON `user` (";
	$sql .= "email";
	$sql .= ");";
	if ($connect->query($sql) !== TRUE) {
		echo "Failed to create index user_index in $db_name.\n";
		die();
	} else if ($verbose) {
		echo "Created index user_index in $db_name.\n";
	}


	$connect->close();
}
?>
