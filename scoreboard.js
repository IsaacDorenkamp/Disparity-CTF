function getXHR(){
	if( window.XMLHttpRequest ){
		return new XMLHttpRequest();
	}else{
		return new ActiveXObject('Microsoft.XMLHTTP');
	}
}

var table;
function check_scores(){
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			table.innerHTML = xhr.responseText;
		}else{
		
		}
	};
	
	xhr.open( 'GET', 'php-bin/score.php' );
	xhr.send();
	
	setTimeout( check_scores, 10000 );
}

function InitScoreboard(){
	table = document.getElementById('tbody');
	
	check_scores();
}

addEventListener( 'load', InitScoreboard );