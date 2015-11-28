<?php
	session_start();
	
	require('php-bin/db.inc.php');
	$logged_in = isset( $_SESSION['User'] );
	
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
		<title>Disparity CTF</title>
		<link href="http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="style.css" />
		<script src="home.js"></script>
		<script src="dates.js"></script>
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
					<li onclick="location.assign('scoreboard.php')"><span class="text">Scoreboard</span></li>
					<li onclick="Data.SignOut()"><span class="text">Log Out</span></li>
				<?php
					}
				?>
			</ul>
		</div>
		<div id="main">
			<div id="countdown">
				<span id="days"></span> DAYS,
				<span id="hours"></span> HOURS,
				<span id="minutes"></span> MINUTES,
				<span id="seconds"></span> SECONDS
			</div>
			<hr />
			<div id="user">
				<?php
					if( $logged_in ){
						echo 'Welcome, ' . $_SESSION['User'] . '.';
					}else{
						echo 'Please sign in to participate in Disparity.';
					}
				?>
			</div>
			<div id="disp-msg"></div>
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
