function getXHR(){
	  if( window.XMLHttpRequest ){
	  	return new XMLHttpRequest();
	  }else{
	  	return new ActiveXObject('Microsoft.XMLHTTP');
	  }
}

var QData = {
	QBox: null,
	SetQuestion:  function(q){
		this.QBox.innerHTML = q;
	},
	ShowQuestionDialog: function(){
		Data.ShowDialog('Question');
	},
	
	SubmitButton: null,
	
	ID: -1
};

function load_question(id){
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			QData.SetQuestion( xhr.responseText );
			QData.ID = id;
			QData.ShowQuestionDialog();
		}else{
			if( xhr.readyState == 4 && xhr.status != 200 ){
				Data.SetFailureMessage( "A server error occurred." );
				Data.ShowDialog('Failure');
			}
		}
	};
	
	xhr.open( 'POST', 'php-bin/challenge_api.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	xhr.send( 'action=fetch&id=' + encodeURIComponent(id) );
}

function verify_question(evt){
	if( evt.preventDefault ){
		evt.preventDefault();
	}else if( evt.stopPropagation ){
		evt.stopPropagation();
	}else{
		evt.cancelBubble = true;
	}
	
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 ) QData.SubmitButton.disabled = false;
		if( xhr.readyState == 4 && xhr.status == 200 ){
			if(xhr.responseText == 'OK'){
				Data.SetSuccessMessage('Correct Answer!');
				Data.ShowDialog( 'Success' );
			}else{
				Data.SetFailureMessage( xhr.responseText );
				Data.ShowDialog( 'Failure' );
			}
		}else{
			if( xhr.readyState == 4 && xhr.status != 200 ){
				Data.SetFailureMessage( "A server error occurred." );
				Data.ShowDialog('Failure');
			}
		}
	};
	
	xhr.open( 'POST', 'php-bin/challenge_api.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	var ans = document.forms[2].elements['answer'].value;
	QData.SubmitButton.disabled = true;
	xhr.send( 'action=verify&id=' + encodeURIComponent(QData.ID) + '&answer=' + encodeURIComponent(ans) );
}

function InitChallenges(){
	QData.QBox = document.getElementById('question-box');
	QData.SubmitButton = document.getElementById('submit-button');
}

addEventListener('load', InitChallenges);