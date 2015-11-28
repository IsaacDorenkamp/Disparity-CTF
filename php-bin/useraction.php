<?php
	session_start();
	
	error_reporting(0);
	@ini_set('display_errors', 0);
	
	define( 'SIGNUP_ROLES', ['spectator', 'competitor'] );

	require('db.inc.php');
	
	if( !isset($_POST['action']) ){
		die('Error: No user action provided.');
	}
	
	$action = $_POST['action'];
	
	$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, 'users' );
	
	if( $conn -> error ){
		die("Error: Could not connect to database.");
	}
	
	function load_user_data( $type ){
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, 'users' );
		$user = $conn -> real_escape_string( $_SESSION['User'] );
		$query = "SELECT * FROM `users` WHERE name='$user'";
		$res = $conn -> query($query);
		if( !$res ){
			return '';
		}
		
		$data = $res -> fetch_assoc();
		if( !$data ){
			return '';
		}
		
		if( isset( $data[$type] ) ){
			return $data[$type];
		}
		
		return '';
	}
	
	switch( $action ){
	case 'login':
		if( !( isset($_POST['user']) && isset($_POST['pwd']) ) ){
			die('Error: Insufficient data provided for login.');
		}
		
		$user = $conn -> real_escape_string( $_POST['user'] );
		$pass = $_POST['pwd'];
		
		$query = "SELECT * FROM `users` WHERE name='$user'";
		$res = $conn -> query($query);
		if( !$res ){
			die("Invalid username or password provided.");
		}
		$row = $res -> fetch_assoc();
		if( password_verify( $pass, $row['password'] ) ){
			$_SESSION['User'] = $_POST['user'];
			echo 'OK';
		}else{
			echo "Error: Invalid username or password provided.";
		}
		break;
	case 'signup':
		if( !( isset($_POST['user']) && isset($_POST['pwd']) && isset($_POST['email']) ) ){
			die('Error: Insufficient data provided for login.');
		}
		
		$role = "NULL";
		if( isset($_POST['role']) ){
			if( in_array( $_POST['role'], SIGNUP_ROLES ) ){
				$role = "'" . $conn -> real_escape_string($_POST['role']) . "'";
			}
		}
		
		$user = $conn -> real_escape_string( $_POST['user'] );
		$pass = $conn -> real_escape_string( password_hash($_POST['pwd'], PASSWORD_BCRYPT, ['cost' => 11]) );
		$email = $conn -> real_escape_string( $_POST['email'] );
		
		$checkquery = "SELECT * FROM `users` WHERE name='$user' OR email='$email'";
		$checkres = $conn -> query($checkquery);
		
		if( $checkres && $checkres -> fetch_assoc() ){
			die('Error: User or email address already exists.');
		}
		
		$insertquery = "INSERT INTO `users` VALUES ('$user', '$pass', '$email', NULL, $role, '')";
		$conn -> query( $insertquery );
		if( $conn -> error ){
			die( 'A database error occurred.' );
		}
		
		$_SESSION['User'] = $_POST['user'];
		
		echo "OK";
		break;
	case 'logout':
		unset( $_SESSION['User'] );
		echo 'OK';
		break;
	case 'update':
		if( !(isset($_POST['datatype']) && isset($_POST['value'])) ){
			die("Error: Insufficient data to edit user data!");
		}
		$type = $_POST['datatype'];
		$value = $_POST['value'];
		
		switch( $type ){
		case 'name':
			$name = $conn -> real_escape_string($value);
			
			if( empty($name) ){
				die("Error: Name cannot be empty.");
			}
			
			$cname = $conn -> real_escape_string( $_SESSION['User'] );
			$checkquery = "SELECT * FROM `users` WHERE name='$cname'";
			$res = $conn -> query($checkquery);
			if( $res && $res -> fetch_assoc() ){
				die("Error: Username already exists.");
			}
			$updatequery = "UPDATE `users` SET name='$name' WHERE name='$cname'";
			$conn -> query($updatequery);
			if( $conn -> error ){
				die("A database error occurred. Could not update data.");
			}
			
			$_SESSION['User'] = $value;
			
			echo "OK";
			break;
		case 'email':
			$email = $conn -> real_escape_string($value);
			
			if( empty($email) ){
				die("Error: Email cannot be empty.");
			}
			
			$checkquery = "SELECT * FROM `users` WHERE email='$email'";
			$res = $conn -> query($checkquery);
			if( $res && $res -> fetch_assoc() ){
				die("Error: Email already exists.");
			}
			$cname = $conn -> real_escape_string( $_SESSION['User'] );
			$updatequery = "UPDATE `users` SET email='$email' WHERE name='$cname'";
			$conn -> query($updatequery);
			if( $conn -> error ){
				die("A database error occurred. Could not update data.");
			}
			
			echo "OK";
			break;
		case 'password':
			$pwd = $conn -> real_escape_string(password_hash($value, PASSWORD_BCRYPT, ['cost' => 11]));
			
			if( empty($pwd) ){
				die("Error: Password cannot be empty.");
			}
			if( $value == "<redacted>" ){
				die("Error: This password is prohibited.");
			}
			
			$cname = $conn -> real_escape_string( $_SESSION['User'] );
			$updatequery = "UPDATE `users` SET password='$pwd' WHERE name='$cname'";
			$conn -> query($updatequery);
			if( $conn -> error ){
				die("A database error occurred. Could not update data.");
			}
			
			echo "OK";
			break;
		}
		break;
	case 'force_update':
		if( load_user_data('role') !== 'admin' ){
			die("Error: You don't have the authority to do this!"); //Only admins can change other users' details!
		}
		if( !(isset($_POST['datatype']) && isset($_POST['value']) && isset($_POST['target_user'])) ){
			die("Error: Insufficient data to edit user data!");
		}
		$type = $_POST['datatype'];
		$value = $_POST['value'];
		$target = $conn -> real_escape_string($_POST['target_user']);
		
		switch( $type ){
		case 'delete':
			$delquery = "DELETE FROM `users` WHERE name='$target'";
			$conn -> query( $conn );
			
			if( $conn -> error ){
				die("A database error has occurred.");
			}
			break;
		case 'role':
			$evalue = $conn -> real_escape_string($value);
			if( !($value == "admin" || $value == "competitor" || $value == "spectator") ){
				$esc = htmlentities($value);
				die("Invalid role '$esc'!");
			}
			$uquery = "UPDATE `users` SET role='$evalue' WHERE name='$target'";
			$conn -> query($uquery);
			if( $conn -> error ){
				die("A database error has occurred.");
			}
			echo "OK";
			break;
		}
		break;
	case 'delete':
		if( !isset($_SESSION['User']) ){
			die('Not logged in!');
		}
		$euser = $conn -> real_escape_string($_SESSION['User']);
		$query = "DELETE FROM `users` WHERE name='$euser'";
		$conn -> query($query);
		
		if( $conn -> error ){
			die('A database error occurred.');
		}
		
		echo "OK";
		
		unset( $_SESSION['User'] );
		break;
	}
?>
