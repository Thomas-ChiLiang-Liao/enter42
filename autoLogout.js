let timeoutTimer;
const timer = 10*60*1000;

function autoLogout() {
  alert('連線已逾時，自動登出！\n請重新連線。');
  window.open('../logout.php','_top');
}

function resetTimeoutTimer() {
  clearTimeout(timeoutTimer);
  timeoutTimer = setTimeout(autoLogout,timer);
}

document.onmousedown=resetTimeoutTimer;
document.onmousemove=resetTimeoutTimer;
resetTimeoutTimer();