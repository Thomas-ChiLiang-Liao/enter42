function init() {
  // 依資料庫開關設定網頁介面
  if (sw == 1) document.getElementById('sw').checked = true;
  else         document.getElementById('sw').checked = false;
  checkSwitch(document.getElementById('sw'));

  // 依資料庫截止時間，設定輸入框
  document.getElementById('expireDate').value = expire.split(' ')[0];
  document.getElementById('expireTime').value = expire.split(' ')[1];
}

function checkSwitch(obj) {
  if ( obj.checked ) {
    obj.nextElementSibling.innerHTML = '網頁開啟'; 
    document.getElementById('timePanel').style.display = 'flex';
  }
  else {
    obj.nextElementSibling.innerHTML = '網頁關閉';
    document.getElementById('timePanel').style.display = 'none';
  }
}