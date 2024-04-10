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
</head>
<body onload="onload()">
  <?php menu('refData'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12 col-md-10 offset-md-1 col-xl-9 offset-xl-2">
        <div class="card">
          <div class="card-header bg-primary text-center text-white">
            <h3>落點分析</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <thead>
                <tr class="bg-secondary text-white">
                  <th></th>
                  <th class="text-center large">國文<br><span class="small">括弧內為轉換分數</span></th>
                  <th class="text-center large">英文<br><span class="small">括弧內為轉換分數</span></th>
                  <th class="text-center large">數學<br><span class="small">括弧內為轉換分數</span></th>
                  <th class="text-center large">專一<br><span class="small">括弧內為轉換分數</span></th>
                  <th class="text-center large">專二<br><span class="small">括弧內為轉換分數</span></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th class="text-center bg-secondary text-white">成績</th>
                  <td class="text-center"><?php echo $_SESSION['preChinese']; ?></td>
                  <td class="text-center"><?php echo $_SESSION['preEnglish']; ?></td>
                  <td class="text-center"><?php echo $_SESSION['preMath']; ?></td>
                  <td class="text-center"><?php echo $_SESSION['preProf1']; ?></td>
                  <td class="text-center"><?php echo $_SESSION['preProf2']; ?></td>
                </tr>
                <tr>
                  <th class="text-center bg-secondary text-white align-middle">預測落點</th>
                  <td colspan="5" class="pt-3">
                    <pre><?php echo $_SESSION['preDeps']; ?></pre>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } } ?>