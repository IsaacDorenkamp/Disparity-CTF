<?php
	session_start();
	require('php-bin/db.inc.php');
	$is_logged = isset( $_SESSION['User'] );
	
	if( !$is_logged ){
		header( 'Location: index.php' );
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
		<title>Disparity CTF -- Account</title>
		<link href="http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="style.css" />
		<link rel="stylesheet" href="account.css" />
		<script src="home.js"></script>
		<script src="account.js"></script>
	</head>
	<body>
		<div id="head">
			<span class="title">
				Disparity CTF
			</span>
			<ul id="nav">
				<?php
					if( !$is_logged ){
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
					<li onclick="location.assign('index.php')"><span class="text">Home</span></li>
					<li onclick="location.assign('challenges.php')"><span class="text">Challenges</span></li>
					<li onclick="location.assign('scoreboard.php')"><span class="text">Scoreboard</span></li>
					<li onclick="Data.SignOut()"><span class="text">Log Out</span></li>
				<?php
					}
				?>
			</ul>
		</div>
		<div id="main">
			<div id="acct-details">
				<table class="account-details">
					<tbody>
						<tr>
							<td>Account Username:</td>
							<td onclick="Data.ShowDialog('AccountData')"><?php echo htmlentities($_SESSION['User']) ?></td>
						</tr>
						<tr>
							<td>Account Password:</td>
							<td onclick="Data.ShowDialog('AccountData')">*********</td>
						</tr>
						<tr>
							<td>Account Email:</td>
							<td onclick="Data.ShowDialog('AccountData')"><?php echo htmlentities(load_user_data('email')); ?></td>
						</tr>
					</tbody>
				</table>
				<hr />
				<h2>
					Team
					<?php 
						if( load_user_data('team') ){ 
							echo ' -- ' . htmlentities(load_user_data('team'));
						}else{
							echo ' -- ' . '<a href="javascript:Data.ShowDialog(\'JoinTeam\')">&lt;None&gt;</a>';
						} 
					?>
				</h2>
				<div id="danger_zone">
					<h2 class="danger">Danger Zone</h2>
					<button onclick="Confirm.show(leave_team)" class="ui">Leave Team</button>
					<br />
					<button onclick="Confirm.show(delete_account)" class="ui">Delete Account</button>
				</div>
			</div>
		</div>
		<div id="dialogs" class="modal">
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
			<div class="popup" data-dlg="AccountData">
				<div class="title">
					Account Data
					<img src="x.png" class="closer" onclick="Data.HideDialog('AccountData')" />
				</div>
				Attribute: 
				<select id="account_attribute" onchange="update_user_data()">
					<option value="<?php if( $is_logged ) echo htmlentities($_SESSION['User']); ?>" data-attr="name">Username</option>
					<option value="<redacted>" data-attr="password">Password</option>
					<option value="<?php if( $is_logged ) echo htmlentities(load_user_data( 'email' )); ?>" data-attr="email">E-Mail</option>
				</select>
				<form onsubmit="update_user_data_server(event)" id="acct_data">
					<table>
						<tbody>
							<tr>
								<td>Value:</td>
								<td><input id="data_target" type="text" value="<?php if( $is_logged ) echo htmlentities($_SESSION['User']); ?>" onchange="update_app_data()" /></td>
							</tr>
						</tbody>
					</table>
					<button type="submit" class="submit">Update</button>
				</form>
			</div>
			<div class="popup" data-dlg="JoinTeam">
				<div class="title">
					Join Team
					<img src="x.png" class="closer" onclick="Data.HideDialog('JoinTeam')" />
				</div>
				<form onsubmit="join_team(event)" id="join_team">
					<table>
						<tbody>
							<tr>
								<td>Team Code:</td>
								<td><input type="text" name="code" /></td>
							</tr>
							<tr>
								<td colspan="2" class="alt">
									<a href="javascript:Data.ShowDialog('CreateTeam')">Create your own team!</a>
								</td>
							</tr>
						</tbody>
					</table>
					
					<button type="submit" class="submit">OK</button>
				</form>
			</div>
			<div class="popup" data-dlg="CreateTeam">
				<div class="title">
					Create Team
					<img src="x.png" class="closer" onclick="Data.HideDialog('CreateTeam')" />
				</div>
				<form onsubmit="create_team(event)" id="create_team">
					<table>
						<tbody>
							<tr>
								<td>Team Code:</td>
								<td><input type="text" name="code" /></td>
							</tr>
							<tr>
								<td>Team Name:</td>
								<td><input type="text" name="name" ></td>
							</tr>
						</tbody>
					</table>
					<button type="submit" class="submit">Create</button>
				</form>
			</div>
			<div class="popup" data-dlg="Confirm">
				<div class="title">
					Confirm
				</div>
				<div class="message">
					Are you SURE you want to do this?
				</div>
				<button onclick="Confirm.callback()" class="yes">Yes</button>
				<button onclick="Data.HideDialog('Confirm')" class="no">No</button>
			</div>
		</div>
	</body>
</html>
