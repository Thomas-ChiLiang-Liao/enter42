function onload() {
  const obj = document.getElementById('sw');
  if (sw == 1) {
    obj.checked = true;
    obj.nextElementSibling.style.color = 'red';
    obj.nextElementSibling.innerHTML = '我要登記參加專業問題模擬面試，且下面的電話一定聯絡得到我。';
    document.getElementById('phonePanel').style.display = 'flex';
  } else {
    obj.checked = false;
    obj.nextElementSibling.style.color = 'black';
    obj.nextElementSibling.innerHTML = '不參加！';
    document.getElementById('phonePanel').style.display = 'none';
  }

  // 設定電話號碼
  document.getElementById('phone1').value = phone1;
  document.getElementById('phone2').value = phone2;
}

function onOff(o) {
  if (o.checked) {
    o.nextElementSibling.style.color = 'red';
    o.nextElementSibling.innerHTML = '我要登記參加專業問題模擬面試，且下面的電話一定聯絡得到我。'; 
    document.getElementById('phonePanel').style.display = 'flex';
  } else {
    o.nextElementSibling.style.color = 'black';
    o.nextElementSibling.innerHTML = '不參加！';
    document.getElementById('phonePanel').style.display = 'none';
  }
}

function setInput() {
  if (document.getElementById('sw').checked) {
    document.getElementById('swValue').value = 1;
  } else {
    document.getElementById('swValue').value = 0;
    document.getElementById('phone1').value = $_SESSION['phone1'];
    document.getElementById('phone2').value = $_SESSION['phone2'];
  }
  return true;
}