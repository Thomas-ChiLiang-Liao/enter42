<!DOCTYPE html>
<?php
/*
$_SESSION['classTitle']
$_SESSION['studentId']  
$_SESSION['first']
$_SESSION['studentName']
$_SESSION['examSortID'] => 51 
$_SESSION['examSort'] => 電機與電子群 
$_SESSION['examSols'] => 03,04 
$_SESSION['examID']
$_SESSION['preChinese']
$_SESSION['preEnglish']
$_SESSION['preMath']
$_SESSION['preProf1']
$_SESSION['preProf2']
$_SESSION['preDeps']  
$_SESSION['simInterView'] 
$_SESSION['phone1'] 
$_SESSION['phone2']
$_SESSION['projectRoot'] => https://photo.taivs.tp.edu.tw/enter42/preSelect 
$_SESSION['browserTimezoneOffset'] => -28800 
$_SESSION['serverTimezoneOffset'] => 28800
*/
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['studentName'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../menu.php';
    include '../../config.ini.php';

    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $stuId, $stuPw);
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="../../images/logo.icon.png" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
  <script>
    let sw = <?php echo $_SESSION['simInterView']; ?>;
    let phone1 = "<?php echo $_SESSION['phone1']; ?>";
    let phone2 = "<?php echo $_SESSION['phone2']; ?>";
  </script>
  <script src="scripts.js"></script>
</head>
<body onload="onload()">
  <?php menu('interviewSetting'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12 col-md-8 offset-md-2 col-xl-6 offset-xl-3">
        <div class="card">
          <div class="card-header bg-primary text-center text-white">
            <h3>專業問題模擬面試登記</h3>
          </div>
          <div class="card-body">
            <form action="writeToMariaDB.php" method="post" onsubmit="setInput()">
              <input type="hidden" id="swValue" name="swValue">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="sw" name="sw" onclick="onOff(this)">
                <label class="form-check-label" for="sw"></label>
              </div>
              <div class="row" id="phonePanel">
                <div class="col-12 col-md-6">
                  <div class="input-group mt-2">
                    <span class="input-group-text">電話一</span>
                    <input type="tel" class="form-control" id="phone1" name="phone1" pattern="[0]{1}[1-9]{1}[0-9]{8}">
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="input-group mt-2">
                    <span class="input-group-text">電話二</span>
                    <input type="tel" class="form-control" id="phone2" name="phone2" pattern="[0]{1}[1-9]{1}[0-9]{8}">
                  </div>
                </div>
              </div>
              <button class="btn btn-primary mt-2" type="submit">送出</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } } ?>