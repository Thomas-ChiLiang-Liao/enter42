<!DOCTYPE html>
<?php
/*
$_SESSION['classTitle'] =>    班級名稱
$_SESSION['studentId'] =>     報名序號(後6碼)
$_SESSION['first'] =>         第一次登入為 true
$_SESSION['studentName'] =>   學生姓名
$_SESSION['examSortID'] =>    報考類別碼
$_SESSION['examSort'] =>      報考類別
$_SESSION['examSols'] =>      可選的類別03,04
$_SESSION['examID'] =>        准考證號
$_SESSION['preChinese'] =>    落點分析國文成績 
$_SESSION['preEnglish'] =>    落點分析英文成績  
$_SESSION['preMath'] =>       落點分析數學成績 
$_SESSION['preProf1'] =>      落點分析專一成績
$_SESSION['preProf2'] =>      落點分析專二成績
$_SESSION['preDeps'] =>       落點分析 
$_SESSION['simInterView'] =>  是否參加模擬面試
$_SESSION['phone1'] =>  
$_SESSION['phone2'] => 
$_SESSION['projectRoot'] => https://yy33.us/enter42/preSelect 
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
<body>
  <?php menu(''); ?>
  <div class="container-fluid">
    <?php
			if (isset($_SESSION['msg'])) { 
				$message = explode(':',$_SESSION['msg']);
				if ( count($message) > 2) {
					foreach ($message as $i => $data) {
						if ($i <= 1) continue;
						else $message[1] .= ':' . $data;
					}
				}    
    ?>
    <div class="row mt-5">
      <div class="col-12">
        <div class="alert alert-<?php echo $message[0]; ?> text-center"><?php echo $message[1]; ?></div>
      </div>
    </div>
    <?php unset($_SESSION['msg']); } ?>
  </div>
</body>
</html>
<?php } } ?>