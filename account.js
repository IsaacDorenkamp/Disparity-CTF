var select;
var data_target;
var data_type="name";

var UData = {
	name: '',
	password: ''
}

var Confirm = {
	callback: function(){},
	show: function(cb){
		this.callback = cb;
		Data.ShowDialog('Confirm');
	}
}

function update_user_data(){
	if(select && data_target){
		var val = select.value;
		data_target.value = val;
		data_type = select.options[select.selectedIndex].getAttribute('data-attr');
		if( data_type == "password" ){
			data_target.type = "password";
		}else if( data_type == "email" ){
			data_target.type = "email";
		}else{
			data_target.type = "text"
		}
	}
}

function update_app_data(){
	UData[data_type] = data_target.value;
}

function getXHR(){
	if( window.XMLHttpRequest ){
		return new XMLHttpRequest();
	}else{
		return new ActiveXObject('Microsoft.XMLHTTP');
	}
}

function update_user_data_server(evt){
	if( evt.preventDefault ){
		evt.preventDefault();
	}else if( evt.stopPropagation ){
		evt.stopPropagation();
	}else{
		evt.cancelBubble = true;
	}
	update_app_data();
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			if( xhr.responseText == 'OK' ){
				Data.SetSuccessMessage("Updated data!");
				Data.ShowDialog('Success');
			}else{
				Data.SetFailureMessage(xhr.responseText);
				Data.ShowDialog('Failure');
			}
		}else{
			if( xhr.readyState == 4 && xhr.status != 200 ){
				Data.SetFailureMessage("Server error occurred! Could not update data.");
				Data.ShowDialog('Failure');
			}
		}
	};
	
	xhr.open( 'POST', 'php-bin/useraction.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	xhr.send( 'action=update&' + 'datatype=' + encodeURIComponent(data_type) + '&value=' + encodeURIComponent(UData[data_type] || '') );
}

function join_team(evt){
	if( evt.preventDefault ){
		evt.preventDefault();
	}else if( evt.stopPropagation ){
		evt.stopPropagation();
	}else{
		evt.cancelBubble = true;
	};
	
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			if( xhr.responseText == 'OK' ){
				Data.SetSuccessMessage("Joined team!");
				Data.ShowDialog('Success');
			}else{
				Data.SetFailureMessage(xhr.responseText);
				Data.ShowDialog('Failure');
			}
		}else{
			if( xhr.readyState == 4 && xhr.status != 200 ){
				Data.SetFailureMessage("Server error occurred! Could not update data.");
				Data.ShowDialog('Failure');
			}
		}
	};
	
	var code = document.getElementById('join_team').elements['code'].value;
	
	xhr.open( 'POST', 'php-bin/team_api.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	xhr.send( 'action=join&code=' + encodeURIComponent(code) );
}
function create_team(evt){
	if( evt.preventDefault ){
		evt.preventDefault();
	}else if( evt.stopPropagation ){
		evt.stopPropagation();
	}else{
		evt.cancelBubble = true;
	};
	
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			if( xhr.responseText == 'OK' ){
				Data.SetSuccessMessage("Joined team!");
				Data.ShowDialog('Success');
			}else{
				Data.SetFailureMessage(xhr.responseText);
				Data.ShowDialog('Failure');
			}
		}else{
			if( xhr.readyState == 4 && xhr.status != 200 ){
				Data.SetFailureMessage("Server error occurred! Could not update data.");
				Data.ShowDialog('Failure');
			}
		}
	};
	
	var frm = document.getElementById('create_team');
	var code = frm['code'].value;
	var name = frm['name'].value;
	
	xhr.open( 'POST', 'php-bin/team_api.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	xhr.send( 'action=create&code=' + encodeURIComponent(code) + '&name=' + encodeURIComponent(name) );
}
function leave_team(){
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			if( xhr.responseText == 'OK' ){
				Data.SetSuccessMessage("Left team.");
				Data.ShowDialog('Success');
			}else{
				Data.SetFailureMessage(xhr.responseText);
				Data.ShowDialog('Failure');
			}
		}else{
			if( xhr.readyState == 4 && xhr.status != 200 ){
				Data.SetFailureMessage("Server error occurred! Could not leave team.");
				Data.ShowDialog('Failure');
			}
		}
	};
	
	xhr.open( 'POST', 'php-bin/team_api.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	xhr.send( 'action=leave' );
}
function delete_account(){
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			if( xhr.responseText == 'OK' ){
				Data.SetSuccessMessage("Deleted account.");
				Data.ShowDialog('Success');
			}else{
				Data.SetFailureMessage(xhr.responseText);
				Data.ShowDialog('Failure');
			}
		}else{
			if( xhr.readyState == 4 && xhr.status != 200 ){
				Data.SetFailureMessage("Server error occurred! Could not leave team.");
				Data.ShowDialog('Failure');
			}
		}
	};
	
	xhr.open( 'POST', 'php-bin/useraction.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	xhr.send( 'action=delete' );
}

function InitAcct(){
	select = document.getElementById('account_attribute');
	data_target = document.getElementById('data_target');
}

addEventListener( 'load', InitAcct );
