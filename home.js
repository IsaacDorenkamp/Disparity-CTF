function Modal(id){
	this.element = document.getElementById(id);
	this.configured = "";
	var self = this;
	this.show = function(dlg){
		//First, deconfigure current dialog
		if( self.configured )
			self.hide(self.configured);
		for( var i = 0; i < self.element.children.length; i++ ){
			var curel = self.element.children[i];
			if( curel.getAttribute('data-dlg') == dlg ){
				self.element.style.visibility = "visible";
				curel.style.visibility = "visible";
				self.Configure( curel );
				self.configured = dlg;
			}
		}
	};
	this.hide = function(dlg){
		var closed = false;
		for( var i = 0; i < self.element.children.length; i++ ){
			var curel = self.element.children[i];
			if( curel.getAttribute('data-dlg') == dlg ){
				self.element.style.visibility = "visible";
				curel.style.visibility = "hidden";
				self.Deconfigure( curel );
				self.configured = dlg;
				closed = true;
				break;
			}
		}
		if( closed ) self.element.style.visibility = "hidden";
	};
	this.Configure = function(dlg){
		if( dlg.className.split(" ").indexOf("lower") != -1 ){
			console.log('lower');
			dlg.style.top = "40%";
		}else{
			dlg.style.top = "35%";
		}
		dlg.style.opacity = "1";
	};
	this.Deconfigure = function(dlg){
		if( dlg.className.split(" ").indexOf("lower") != -1 ){
			dlg.style.top = "35%";
		}else{
			dlg.style.top = "30%";
		}
		dlg.style.opacity = "0";
	}
}

function GetXHR(){
	if( window.XMLHttpRequest ){
		return new XMLHttpRequest();
	}else{
		return new ActiveXObject( 'Microsoft.XMLHTTP' );
	}
}

var Data = {
	Modal: null,
	ShowDialog: function(dlg){
		this.Modal.show(dlg);
	},
	HideDialog: function(dlg){
		this.Modal.hide(dlg);
	},
	SetSuccessMessage: function(msg){
		document.getElementById('success-msg').innerHTML = msg;
	},
	SetFailureMessage: function(msg){
		document.getElementById('failure-msg').innerHTML = msg;
	},
	
	SignOut: function(){
		var xhr = GetXHR();
		
		xhr.onreadystatechange = function(){
			if( xhr.readyState == 4 && xhr.status == 200 ){
				Data.SetSuccessMessage('Successfully logged out.');
				Data.ShowDialog('Success');
			}else{
				if( xhr.readyState == 4 && xhr.status != 200 ){
					Data.SetFailureMessage('Failed to log out.');
					Data.ShowDialog('Failure');
				}
			}
		};
		
		xhr.open( 'POST', 'php-bin/useraction.php' );
		xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
		xhr.send( 'action=logout' );
	},
	SignIn: function(evt){
		if( evt.preventDefault ){
			evt.preventDefault();
		}else if( evt.stopPropagation ){
			evt.stopPropagation();
		}else{
			evt.cancelBubble = true;
		}
		
		var form = document.getElementById('login-form');
		if( !form ){
			return;
		}
		
		var xhr = GetXHR();
		
		xhr.onreadystatechange = function(){
			if( xhr.readyState == 4 && xhr.status == 200 ){
				console.log( xhr.responseText );
				if( xhr.responseText == 'OK' ){
					Data.SetSuccessMessage('Successfully logged in!');
					Data.ShowDialog('Success');
				}else{
					Data.SetFailureMessage( xhr.responseText );
					Data.ShowDialog('Failure');
				}
			}else{
				if( xhr.readyState == 4 && xhr.status != 200 ){
					Data.SetFailureMessage('Failed to log in.');
					Data.ShowDialog('Failure');
				}
			}
		}
		var user = form.elements['user'].value;
		var pass = form.elements['pwd'].value;
		
		xhr.open( 'POST', 'php-bin/useraction.php' );
		xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
		xhr.send( 'action=login&user=' + encodeURIComponent(user) + '&pwd=' + encodeURIComponent(pass) )
	},
	
	SignUp: function(evt){
		if( evt.preventDefault ){
			evt.preventDefault();
		}else if( evt.stopPropagation ){
			evt.stopPropagation();
		}else{
			evt.cancelBubble = true;
		}
		
		var form = document.getElementById('signup-form');
		if( !form ){
			return;
		}
		
		var xhr = GetXHR();
		
		xhr.onreadystatechange = function(){
			if( xhr.readyState == 4 && xhr.status == 200 ){
				console.log( xhr.responseText );
				if( xhr.responseText == 'OK' ){
					Data.SetSuccessMessage('Successfully signed up!');
					Data.ShowDialog('Success');
				}else{
					Data.SetFailureMessage( xhr.responseText );
					Data.ShowDialog('Failure');
				}
			}else{
				if( xhr.readyState == 4 && xhr.status != 200 ){
					Data.SetFailureMessage('Failed to sign up.');
					Data.ShowDialog('Failure');
				}
			}
		}
		var user = form.elements['user'].value;
		var pass = form.elements['pwd'].value;
		var email = form.elements['email'].value;
		var role = form.elements['role'].value;
		
		xhr.open( 'POST', 'php-bin/useraction.php' );
		xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
		xhr.send( 'action=signup&role=' + encodeURIComponent(role) + '&user=' + encodeURIComponent(user) + '&pwd=' + encodeURIComponent(pass) + '&email=' + encodeURIComponent(email) )
	}
}

function Init(){
	images = document.getElementById('images');
	
	Data.Modal = new Modal( 'dialogs' );
}

addEventListener( 'load', Init );