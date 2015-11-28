<?php
	require('db.inc.php');
	
	echo "Warning: Broken";
	
	$conn = new mysqli( DB_HOST, DB_USER, DB_PASS );
	
	$query = "CREATE DATABASE IF NOT EXISTS users";
	$conn -> query($query);
	$conn -> select_db( 'users' );
	if( !$conn -> error) echo "Created Database.\n";
	else echo "Error creating Database.\n";
	$query = <<<QUERY
CREATE TABLE IF NOT EXISTS users(
	name TEXT,
	password TEXT,
	email TEXT,
	team VARCHAR(45),
	user_id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (user_id)
)
QUERY; //Reuse variable
	exit();
	$conn -> query( $query );
	if( !$conn -> error ) echo "Created users table.\n";
	else echo "Error creating users table.\n";
	$query = <<<QUERY
CREATE TABLE IF NOT EXISTS teams(
	name VARCHAR(45),
	solved TEXT,
	points INT,
	teamcode TEXT,
	team_id int AUTO_INCREMENT,
	PRIMARY KEY (team_id)
)
QUERY;
	$conn -> query( $query );
	if( !$conn -> error ) echo "Created teams table.\n";
	else echo "Error creating teams table.\n";
	
	$query = <<<QUERY
CREATE TABLE IF NOT EXISTS challenges(
	qtext TEXT,
	answer TEXT,
	category VARCHAR(45),
	points INT,
	challenge_id INT AUTO_INCREMENT,
	PRIMARY KEY (challenge_id)
)
QUERY;
	$conn -> query( $query );
	if( !$conn -> error ) echo "Created challenges table.\n";
	else echo "Error challenges teams table.\n";
	
	echo "Finished Configuration.\n";
?>