<?php
	require('db.inc.php');
	
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
		$name = htmlentities($row['name']);
		$points = htmlentities($row['points']);
		$asolved = explode(',', $row['solved']);
		$solved = 0;
		if( !(count($asolved) == 1 && empty($asolved[0])) ){
			$solved = htmlentities(count($asolved));
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