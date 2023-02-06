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

/*
getUserId() jQuery 版

$(document).ready(function() {
  // <input> <datalist> 中將選到的姓名的 id 查出，一併送到伺服器
  $("button").click(function() {
    $("#userPw").val(  SHA1( "%" + $("#userPw").val() + "&"  ) );
    for (i=0; i<$("#operators option").length; i++) {
      if ($("#operators option").eq(i).val() == $("#userName").val()) {
        $("#userId").val( $("#operators option").eq(i).attr("data-value") );
        break;
      }
    }
    // 設定瀏覽器的 Timezone Offset
    var d = new Date();
    $("#secondsBrowserTimezoneOffset").val(d.getTimezoneOffset() * (-60));
    $(this).parent().submit();
  })
})
*/
