<?php
	session_start();
	
	require('php-bin/db.inc.php');
	require('php-bin/global.php');
	
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
	if( load_user_data('role') !== 'admin' ){
		header('Location: index.php');
		exit();
	}

	$conn = new mysqli( DB_HOST, DB_USER, DB_PASS, 'users' );
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo htmlentities(CTF_NAME); ?> -- Scoreboard</title>
		<link href="http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="style.css" />
		<link rel="stylesheet" href="dashboard.css" />
		<script src="home.js"></script>
		<script src="dashboard.js"></script>
	</head>
	<body>
		<div id="head">
			<span class="title">
				<?php echo htmlentities(CTF_NAME); ?>
			</span>
			<ul id="nav">
				<li onclick="location.assign('account.php')"><span class="text">Account</span></li>
				<li onclick="location.assign('challenges.php')"><span class="text">Challenges</span></li>
				<li onclick="location.assign('scoreboard.php')"><span class="text">Scoreboard</span></li>
				<li onclick="location.assign('index.php')"><span class="text">Home</span></li>
				<li onclick="Data.SignOut()"><span class="text">Log Out</span></li>
			</ul>
		</div>
		<div id="main">
			<div class="zone">
				<h2>Challenges</h2>
				<hr />
				<ul>
					<li><a href="challenges_admin.php">Edit Challenges</a></li>
					<li><a href="challenges.php">View Challenges</a></li>
				</ul>
			</div>
			<div class="zone">
				<h2>Teams</h2>
				<hr />
				<ul>
					<li><a href="#" onclick="Data.ShowDialog('CreateTeam')">Create Team</a></li>
				</ul>
				<h2>Team List</h2>
				<hr />
				<ul class="exempt">
					<?php
						$teamquery = "SELECT * FROM `teams`";
						$res = $conn -> query( $teamquery );

						if( !$res ){
							echo "<li><i>No teams created.</i></li>";
						}else{
							$row = $res -> fetch_assoc();
							if( !isset($row) ){
								echo "<li><i>No teams created.</i></li>";
							}else{
								$counter = 1;
								do{
									$steam = htmlentities($row["name"]);
									echo "<li>${counter}. ${steam}</li>";
									$counter += 1;
								}while( ($row = $res -> fetch_assoc()) );
							}
						}
					?>
				</ul>
			</div>
			<div class="zone">
				<h2>Users</h2>
				<hr />
				<div class="table">
					<div>
						<div class="theader">Username</div>
						<div class="theader">Email</div>
						<div class="theader">Role</div>
					</div>
					<hr />
					<div id="tbody">
						<?php
							$tquery = "SELECT * FROM `users`";
							$res = $conn -> query( $tquery );
							
							$skip = false;
							$fail = "";
							if( !$res ){
								$skip = true;
								$fail = "Could not load user data.";
							}

							if( !$skip ){
							
								$row = $res -> fetch_assoc();
								if( !$row ){
									$skip = true;
									$fail = "No users registered yet!";
								}
								
								$counter = 1;
								
								do{ //Second effective use of do-while loop!
									if( $skip ) break;
									$name = htmlentities($row['name']);
									$email = htmlentities($row['email']);
									$rawrole = htmlentities($row['role']);
									$role = "&lt;NA&gt;";
									switch($rawrole){
									case 'admin':
										$role = "Administrator";
										break;
									case 'competitor':
										$role = "Competitor";
										break;
									case 'spectator':
										$role = "Spectator";
										break;
									}
									$id = htmlentities($row['user_id']);
									
									echo <<<HTML
<div>
	<div class="cell">#$id: $name</div>
	<div class="cell">$email</div>
	<div class="cell role" onclick="ManageRole($id, '$name')">$role</div>
</div>
HTML;
									$counter += 1;
								}while( ($row = $res -> fetch_assoc()) );
							}

							if( $skip ){
								echo <<<HTML
<div>
	<div class="cell">An error occurred! $fail</div>
</div>
HTML;
							}
						?>
					</div>
			</div>
			</div>
		</div>
		<footer>
			<?php echo FOOTER; ?>
		</footer>
		<div id="dialogs" class="modal">
			<div class="popup" data-dlg="ChangeRole">
				<div class="title">
					Change Role
					<img src="x.png" class="closer" onclick="Data.HideDialog('ChangeRole')" />
				</div>
				<form onsubmit="changerole(event)">
					<table>
						<tbody>
							<tr>
								<td>Username:</td>
								<td id="username-box"></td>
							</tr>
							<tr>
								<td>Role:</td>
								<td>
									<select id="role-box">
										<option value="admin">Administrator</option>
										<option value="competitor">Competitor</option>
										<option value="spectator">Spectator</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<button type="submit" class="submit">Change Role</button>
				</form>
			</div>
			<div class="popup" data-dlg="CreateTeam">
				<div class="title">
					Create Team
					<img src="x.png" class="closer" onclick="Data.HideDialog('CreateTeam')" />
				</div>
				<form onsubmit="DashboardData.Dialogs.CreateTeam.create(event)" id="create_team">
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