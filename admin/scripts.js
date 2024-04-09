function getUserId() {
  document.getElementById('userPw').value = SHA1("%" + document.getElementById('userPw').value + "&");
  let obj = document.getElementsByTagName('option');
  for (var i = 0; i < obj.length; i++) {
    if (obj[i].value == document.getElementById('userName').value) {
      document.getElementById('userId').value = obj[i].getAttributeNode('data-value').value;
      break;
    }
  }
  // 設定瀏覽器的 Timezone Offset
  var d = new Date();
  document.getElementById('secondsBrowserTimezoneOffset').value = d.getTimezoneOffset() * (-60);
  document.getElementById('loginForm').submit();
}

function checkFileExtension(obj,extString) {
  let ext = obj.value.split('.').pop();
  let extArray = extString.split('_');
  if ( extArray.indexOf( ext ) == -1 ) alert("上傳檔格式應為：" + extArray.toString() + "，請重新選擇。");
}

function beforeSubmit(obj, extString) {
  let ext = obj.value.split('.').pop();
  let extArray = extString.split('_');
  if (obj.value != null && extArray.indexOf(ext) != -1 ) return true;
  else {
    alert("未指定檔案或檔案格式錯誤。")
    return false;
  }
}