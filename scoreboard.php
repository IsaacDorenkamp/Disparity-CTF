<?php
	session_start();
	
	require('php-bin/db.inc.php');
	
	$logged_in = isset($_SESSION['User']);
	
	if( !$logged_in ){
		header('Location: index.php');
		exit();
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
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Disparity CTF -- Scoreboard</title>
		<link rel="stylesheet" href="style.css" />
		<script src="home.js"></script>
		<script src="scoreboard.js"></script>
	</head>
	<body>
		<div id="head">
			<span class="title">
				Disparity CTF
			</span>
			<ul id="nav">
				<?php
					if( !$logged_in ){
				?>
					<li onclick="Data.ShowDialog('Register')"><span class="text">Register</span></li>
					<li onclick="Data.ShowDialog('Login')"><span class="text">Log In</span></li>
				<?php
					}else{
				?>
					<?php
						if( load_user_data('role') === "admin" ){
					?>
						<li onclick="location.assign('dashboard.php')"><span class="text">Dashboard</span></li>
					<?php
						}
					?>
					<li onclick="location.assign('account.php')"><span class="text">Account</span></li>
					<li onclick="location.assign('challenges.php')"><span class="text">Challenges</span></li>
					<li onclick="location.assign('index.php')"><span class="text">Home</span></li>
					<li onclick="Data.SignOut()"><span class="text">Log Out</span></li>
				<?php
					}
				?>
			</ul>
		</div>
		<div id="main">
			<div class="table">
				<div>
					<div class="theader">Team Name</div>
					<div class="theader">Points</div>
					<div class="theader">Solved</div>
				</div>
				<hr />
				<div id="tbody">
					<?php
						require('php-bin/db.inc.php');
						
						$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, 'users' );
						
						$tquery = "SELECT * FROM `teams` ORDER BY points LIMIT 50";
						$res = $conn -> query( $tquery );
						
						if( !$res ){
							die('Could not fetch score data.');
						}
						
						$row = $res -> fetch_assoc();
						if( !$row ){
							die('No teams registered yet!');
						}
						
						$counter = 1;
						
						do{ //Second effective use of do-while loop!
							$name = $row['name'];
							$points = $row['points'];
							$asolved = explode(',', $row['solved']);
							$solved = 0;
							if( !(count($asolved) == 1 && empty($asolved[0])) ){
								$solved = count($asolved);
							}
							
							echo <<<HTML
<div>
	<div class="cell">$counter. $name</div>
	<div class="cell">$points</div>
	<div class="cell">$solved</div>
</div>
HTML;
							$counter += 1;
						}while( ($row = $res -> fetch_assoc()) );
					?>
				</div>
			</div>
			<footer>
				Designed 2015 by IS44CQU4RK of M350N Studios.
			</footer>
		</div>
		<div id="dialogs" class="modal">
			<div class="popup" data-dlg="Login">
				<div class="title">
					Log In
					<img src="x.png" onclick="Data.HideDialog('Login')" alt="" class="closer" />
				</div>
				<form onsubmit="Data.SignIn(event)">
					<table>
						<tbody>
							<tr>
								<td>Username:</td>
								<td><input type="text" name="user" placeholder="Username" /></td>
							</tr>
							<tr>
								<td>Password:</td>
								<td><input type="password" name="pwd" placeholder="Password123" /></td>
							</tr>
						</tbody>
					</table>
					<button type="submit" class="submit">Log In</button>
				</form>
			</div>
			<div class="popup" data-dlg="Register">
				<div class="title">
					Register
					<img src="x.png" onclick="Data.HideDialog('Register')" alt="" class="closer" />
				</div>
				<form onsubmit="Data.SignUp(event)">
					<table>
						<tbody>
							<tr>
								<td>Username:</td>
								<td><input type="text" name="user" placeholder="Username" /></td>
							</tr>
							<tr>
								<td>Password:</td>
								<td><input type="password" name="pwd" placeholder="Password123" /></td>
							</tr>
							<tr>
								<td>Email:</td>
								<td><input type="email" name="email" placeholder="example@example.org" /></td>
							</tr>
							<tr>
								<td>
									Why are you signing up?
								</td>
								<td>
									<select name="role">
										<option value="competitor" selected>To compete as a Student</option>
										<option value="spectator">To spectate</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<button type="submit" class="submit">Register</button>
				</form>
			</div>
			<div class="popup" data-dlg="Success">
				<div class="title">
					Success
				</div>
				<span id="success-msg">Operation succeeded.</span>
				<button onclick="location.reload()" class="submit">OK</button>
			</div>
			<div class="popup" data-dlg="Failure">
				<div class="title">
					Failure
				</div>
				<span id="failure-msg">Operation failed.</span>
				<button onclick="location.reload()" class="submit">OK</button>
			</div>
		</div>
	</body>
</html>