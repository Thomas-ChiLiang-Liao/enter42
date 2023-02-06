function beforeSubmit() {
  // 設定 browserTimezoxneOffset
  let d = new Date();

  document.getElementById('browserTimezoneOffset').value = d.getTimezoneOffset();
  document.getElementById('pw').value = SHA1('%' + document.getElementById('pw').value + '&');

  return true;
}