var DashboardData = {
	UID: -1,
	USR: '',
	
	//Dialogs
	Dialogs: {
		Role: {
			Username: null,
			Box: null,
			Selected: "admin"
		}
	}
};

function ManageRole(id, usrnm){
	DashboardData.UID = id;
	DashboardData.USR = usrnm;
	
	update_role_dialog();
	Data.ShowDialog('ChangeRole');
}
function update_role_dialog(){
	DashboardData.Dialogs.Role.Username.innerHTML = DashboardData.USR;
}

function getXHR(){
	if( window.XMLHttpRequest ){
		return new XMLHttpRequest();
	}else{
		return new ActiveXObject('Microsoft.XMLHTTP');
	}
}
function changerole(evt){
	if( evt.preventDefault ){
		evt.preventDefault();
	}else if( evt.stopPropagation ){
		evt.stopPropagation();
	}else{
		evt.cancelBubble = true;
	}
	var xhr = getXHR();
	
	xhr.onreadystatechange = function(){
		if( xhr.readyState == 4 && xhr.status == 200 ){
			if( xhr.responseText == "OK" ){
				Data.SetSuccessMessage("Updated role of user " + DashboardData.USR);
				Data.ShowDialog('Success');
			}else{
				Data.SetFailureMessage(xhr.responseText);
				Data.ShowDialog('Failure');
			}
		}else{
			Data.SetFailureMessage('A server error occurred.');
			Data.ShowDialog('Failure');
		}
	};
	
	xhr.open( 'POST', 'php-bin/useraction.php' );
	xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
	xhr.send( 'action=force_update&datatype=role&target_user=' + encodeURIComponent( DashboardData.USR ) + '&value=' + encodeURIComponent( DashboardData.Dialogs.Role.Selected ) );
}

function InitDashboard(){
	DashboardData.Dialogs.Role.Username = document.getElementById('username-box');
	DashboardData.Dialogs.Role.Box = document.getElementById('role-box');
	DashboardData.Dialogs.Role.Box.onchange = function(){
		DashboardData.Dialogs.Role.Box.blur();
		DashboardData.Dialogs.Role.Selected = DashboardData.Dialogs.Role.Box.value;
	};
}

addEventListener('load', InitDashboard);