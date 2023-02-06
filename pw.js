function checkPasswordsConsistence(objAId, objBId) {
	var objA = document.getElementById(objAId);
	var objB = document.getElementById(objBId);
	
	if ((objA.value != objB.value) || (objA.value == "" && objB.value == "")) {
		objA.style.background = "#FF8080";
		objB.style.background = "#FF8080";
		var rValue = false;
	} else {
		objA.style.background = "#80FF80";
		objB.style.background = "#80FF80";
		var rValue = true;
	}
	return rValue;
}

function checkNewPassword(objAId, objBId) {
  if ( !(checkPasswordsConsistence(objAId, objBId)) || document.getElementById("oldPw").value == '' ) 
    document.getElementById("setNewPasswordButton").disabled = true;
  else
    document.getElementById("setNewPasswordButton").disabled = false;
}

function encryptPw(){
  if ( document.getElementById('oldPw').value == document.getElementById('newPw').value ) { 
    alert('新舊密碼相同，請重新輸入。'); 
    return false; 
  } else {
    document.getElementById('confirmPw').value = SHA1('%'+document.getElementById('confirmPw').value+'&');
    document.getElementById('newPw').value = SHA1('%'+document.getElementById('newPw').value+'&');
    document.getElementById('oldPw').value = SHA1('%'+document.getElementById('oldPw').value+'&');
    return true;
  } 
}