<?php
	session_start();
	
	error_reporting(0);
	@ini_set('display_errors', 0);
	
	require_once('db.inc.php');
	
	function load_user_data( $type ){
		if( !isset($_SESSION['User']) ){
			return '';
		}
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
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
	
	function create_team($name, $code){
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		
		if( isset($_SESSION['User']) ){
			$team = load_user_data('team');
			$role = load_user_data('role');
			if( !empty($team) && $role != "admin" ){
				return 'You are already on a team! You can only create a team if you are not currently on one.';
			}
		}else{
			return 'You are not logged in! You must be logged in to create a team.';
		}
		
		$ename = $conn -> real_escape_string($name);
		$ecode = $conn -> real_escape_string($code);
		
		$checkquery = "SELECT * FROM `teams` WHERE name='$ename' OR teamcode='$ecode'";
		$checkres = $conn -> query($checkquery);
		
		if( $checkres ){
			if( $checkres -> fetch_assoc() ){
				return 'Team with that name or team code already exists.';
			}
		}
		
		$query = "INSERT INTO `teams` VALUES('$ename', '', 0, '$ecode', '')";
		
		$conn -> query( $query );
		if( $conn -> error ){
			return 'A database error occurred.';
		}
		
		$euser = $conn -> real_escape_string($_SESSION['User']);
		$joinquery = "UPDATE `users` SET team='$ename' WHERE name='$euser'";
		$conn -> query( $joinquery );
		
		if( $conn -> error ){
			return 'Created team, but could not join it.';
		}
		
		return 'OK';
	}
	function join_team($code){
		if( !isset( $_SESSION['User'] ) ){
			return 'You must be logged in to join a team.';
		}
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		
		$ecode = $conn -> real_escape_string($code);
		$tquery = "SELECT * FROM `teams` WHERE teamcode='$ecode'";
		$res = $conn -> query( $tquery );
		
		if( !$res ){
			return 'Team with code does not exist.';
		}
		
		$data = $res -> fetch_assoc();
		
		if( !$data ){
			return 'Team with code does not exist.';
		}
		
		$name = $data['name'];
		$ename = $conn -> real_escape_string( $name );
		$user = $_SESSION['User'];
		$euser = $conn -> real_escape_string( $user );
		$joinquery = "UPDATE `users` SET team='$ename' WHERE name='$euser'";
		
		$conn -> query( $joinquery );
		
		if( $conn -> error ){
			return 'Failed to join team.';
		}
		
		return 'OK';
	}
	
	if( !isset($_POST['action']) ){
		exit();
	}
	
	$action = $_POST['action'];
	
	switch($action){
	case 'create':
		if( !( isset($_POST['name']) && isset($_POST['code']) ) ){
			die('Error: Team name or code must not be empty');
		}
		if( empty($_POST['name']) || empty($_POST['code']) ){
			die('Error: Team name or code must not be empty');
		}
		
		$name = $_POST['name'];
		$code = $_POST['code'];
		$output = create_team($name, $code);
		echo $output;
		break;
	case 'join':
		if( !isset($_POST['code']) ){
			die('Error: Team code must not be empty');
		}
		if( empty( $_POST['code']) ){
			die('Error: Team code must not be empty');
		}
		
		$code = $_POST['code'];
		$output = join_team($code);
		
		echo $output;
		break;
	case 'leave':
		if( !isset($_SESSION['User']) ){
			die("You are not logged in! You cannot leave a team!!");
		}
		if( !load_user_data('team') ){
			die("You are not on a team.");
		}
		$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		$user = $_SESSION['User'];
		$euser = $conn -> real_escape_string($user);
		$query = "UPDATE `users` SET team=NULL WHERE name='$euser'";
		$conn -> query( $query );
		
		if( $conn -> error ){
			die("A database error occurred.");
		}
		
		echo "OK";
		break;
	default:
		echo 'OK';
	}
?>
