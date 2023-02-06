function checkPasswordMatch(objAId, objBId) {
  document.getElementById("addOperatorButton").disabled = !(checkPasswordsConsistence(objAId, objBId));
}

function checkFormA() {
  var operatorName = document.getElementById('operatorName');
  var operatorPassword = document.getElementById('operatorPassword');
  var confirmPassword = document.getElementById('confirmPassword');
  var operatorSex = document.getElementsByName('operatorSex');
  var operatorType = document.getElementsByName('operatorType');
  if (operatorName.value == null || operatorName.value == '') { alert('操作人員姓名未輸入！');}
  else if (operatorPassword.value == null || operatorPassword.value == '') alert('請設定密碼！');
  else if (!operatorSex[0].checked && !operatorSex[1].checked) { alert('未勾選操作人員性別！');}
  else if (!operatorType[0].checked && !operatorType[1].checked) { alert('未勾選操作人員操作等級！');}
  else {
    operatorPassword.value = SHA1( '%' + operatorPassword.value + '&');
    confirmPassword.value = SHA1( '%' + confirmPassword.value + '&');
    document.getElementById("addOperatorForm").submit();
  }
  
}

function checkSelected() {
  if (document.getElementById("operatorSelector").selectedIndex == 0) document.getElementById("deleteOperatorButton").disabled = true;
  else document.getElementById("deleteOperatorButton").disabled = false;
}